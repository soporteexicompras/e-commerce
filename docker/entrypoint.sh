#!/bin/sh
# ════════════════════════════════════════════════════════════════════════════
#  Exicompras — entrypoint del container app
#  Se ejecuta en cada arranque. Idempotente: se puede reiniciar sin romper.
# ════════════════════════════════════════════════════════════════════════════
set -e

# ── 1. Esperar a que la base de datos esté lista ───────────────────────────
if [ -n "$DB_HOST" ] && [ "$DB_CONNECTION" != "sqlite" ]; then
    echo "⏳ Esperando a la base de datos ${DB_HOST}:${DB_PORT:-3306}..."
    ATTEMPTS=0
    MAX_ATTEMPTS=30
    until php -r "
        try {
            new PDO(
                'mysql:host='.getenv('DB_HOST').';port='.getenv('DB_PORT', 3306),
                getenv('DB_USERNAME'),
                getenv('DB_PASSWORD')
            );
            exit(0);
        } catch (Exception \$e) {
            exit(1);
        }
    " 2>/dev/null; do
        ATTEMPTS=$((ATTEMPTS + 1))
        if [ $ATTEMPTS -ge $MAX_ATTEMPTS ]; then
            echo "❌ No se pudo conectar a la BD tras ${MAX_ATTEMPTS} intentos"
            exit 1
        fi
        sleep 2
    done
    echo "✅ Base de datos lista."
fi

# ── 2. Generar APP_KEY si falta ───────────────────────────────────────────
if [ -z "$APP_KEY" ]; then
    echo "🔑 APP_KEY no definida — generando..."
    php artisan key:generate --force --no-interaction
fi

# ── 2.5 Asegurar subdirs de storage/framework ────────────────────────────
# Cuando storage/ se monta como volumen Docker, los subdirs cache/sessions/
# views/ que Laravel necesita para tmp files desaparecen. Sin esto,
# tempnam() falla con "file created in the system's temporary directory"
# y todas las requests devuelven 500. Ver DEPLOYMENT.md §12.5.
mkdir -p \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/testing \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs

# ── 3. Storage symlink (idempotente) ─────────────────────────────────────
if [ ! -L /var/www/html/public/storage ]; then
    echo "🔗 Creando storage symlink..."
    php artisan storage:link || true
fi

# ── 4. Permisos runtime (importante en volúmenes montados) ───────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# ── 5. Migraciones automáticas (solo si se habilita explícitamente) ─────
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "🗄️  Ejecutando migraciones Laravel..."
    php artisan migrate --force --no-interaction
    echo "🗄️  Ejecutando setup Aimeos (crea tablas mshop_*, índices, etc.)..."
    php artisan aimeos:setup --no-interaction 2>&1 | tail -5 || echo "⚠️ aimeos:setup fallo (puede ser normal si ya estaba aplicado)"
fi

# ── 6. Aimeos: publicar assets del paquete si no existen ───────────────
if [ ! -d /var/www/html/public/vendor/shop/themes/default/assets ] && [ -d /var/www/html/vendor/aimeos ]; then
    echo "🎨 Publicando assets de Aimeos..."
    php artisan vendor:publish --tag=public --force --no-interaction || true
fi

# ── 7. Optimización de cachés en producción ──────────────────────────────
if [ "${APP_ENV}" = "production" ]; then
    echo "⚡ Optimizando cachés para producción..."
    php artisan config:cache   --no-interaction || true
    php artisan route:cache    --no-interaction || true
    php artisan view:cache     --no-interaction || true
    php artisan event:cache    --no-interaction || true
fi

echo "🚀 Arrancando: $@"
exec "$@"
