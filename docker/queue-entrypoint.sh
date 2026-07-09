#!/bin/sh
# ════════════════════════════════════════════════════════════════════════════
#  Exicompras — entrypoint del container queue
#  Ejecuta `php artisan queue:work` con reintentos y signal handling.
# ════════════════════════════════════════════════════════════════════════════
set -e

echo "⏳ Esperando a que la app y la BD estén listas..."
# Reutilizamos la lógica de espera del entrypoint principal
DB_HOST="$DB_HOST" DB_PORT="$DB_PORT" DB_USERNAME="$DB_USERNAME" DB_PASSWORD="$DB_PASSWORD" \
  sh -c '
        if [ -n "$DB_HOST" ] && [ "$DB_CONNECTION" != "sqlite" ]; then
            ATTEMPTS=0
            until php -r "try { new PDO(\"mysql:host=\".getenv(\"DB_HOST\").\";port=\".(getenv(\"DB_PORT\") ?: 3306), getenv(\"DB_USERNAME\"), getenv(\"DB_PASSWORD\")); exit(0); } catch (Exception \$e) { exit(1); }" 2>/dev/null; do
                ATTEMPTS=$((ATTEMPTS + 1))
                [ $ATTEMPTS -ge 30 ] && exit 1
                sleep 2
            done
        fi
    ' || { echo "❌ BD no disponible"; exit 1; }

echo "✅ Worker de colas arrancando..."
exec php artisan queue:work \
        --tries=3 \
        --max-time=3600 \
        --sleep=3 \
        --timeout=120 \
        --memory=512 \
        --queue=default,aimeos
