# syntax=docker/dockerfile:1.7
# ════════════════════════════════════════════════════════════════════════════
#  Exicompras — Dockerfile multi-stage
#  Stages:
#    1. node-builder  → compila assets con Vite (Node 20 Alpine)
#    2. vendor        → instala dependencias PHP con Composer (sin dev)
#    3. dev           → imagen final con Xdebug (perfil: development)
#    4. app           → imagen final optimizada para producción (default)
# ════════════════════════════════════════════════════════════════════════════

# ──────────────────────────────────────────────────────────────
# Stage 1 — Compilar assets del frontend (Vite)
# ──────────────────────────────────────────────────────────────
FROM node:20-alpine AS node-builder

WORKDIR /app

# Cache de capas: si package*.json no cambia, no reinstala
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

# Copiar solo lo necesario para el build
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build


# ──────────────────────────────────────────────────────────────
# Stage 2 — Instalar dependencias PHP con Composer
# ──────────────────────────────────────────────────────────────
FROM composer:2.8 AS vendor

WORKDIR /app

# Copiamos manifests primero para cachear la instalación
COPY composer.json composer.lock ./

# --no-scripts: no ejecuta "post-autoload-dump" aquí (lo hará el entrypoint)
# --no-autoloader: lo generamos tras copiar el código (mejor para cache)
# En el stage vendor (composer:2.7) NO tenemos las extensiones PHP del runtime
# (intl, gd, zip, etc.); las extensiones estan en el stage 'app'. Por eso
# ignoramos los platform-reqs en este stage; se validan en runtime.
RUN composer update \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --with-all-dependencies \
        --no-interaction \
        --no-progress \
        --ignore-platform-req=*

# Copiamos el código y regeneramos el autoloader optimizado
COPY . .
RUN composer dump-autoload \
        --classmap-authoritative \
        --no-dev \
        --no-scripts


# ──────────────────────────────────────────────────────────────
# Stage 3 — Extensiones PHP base (compartido por dev y app)
# ──────────────────────────────────────────────────────────────
FROM php:8.4-fpm-bookworm AS php-base

# Argumentos para el usuario final (no root)
ARG WWW_USER=www-data
ARG WWW_GROUP=www-data

# Evitar que APT bloquee el build con preguntas interactivas
ENV DEBIAN_FRONTEND=noninteractive

# Dependencias de sistema para las extensiones PHP requeridas
#   - intl    → libicu-dev
#   - zip     → libzip-dev
#   - gd      → libpng-dev libjpeg62-turbo-dev libfreetype6-dev libwebp-dev
#   - bcmath, opcache, pcntl, pdo → built-in
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        curl \
        ca-certificates \
        unzip \
        supervisor \
        libicu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libwebp-dev \
        libxml2-dev \
        libonig-dev \
        libssl-dev \
        libsqlite3-dev \
        tzdata \
    && rm -rf /var/lib/apt/lists/*

# Compilar extensiones PHP requeridas por Laravel 12 + Aimeos 2025.10
#   intl     → Aimeos (obligatoria)
#   bcmath   → Laravel por defecto
#   mbstring → Laravel + Aimeos
#   opcache  → rendimiento en producción
#   pcntl    → queue:work --max-time / signals
#   pdo_mysql → driver principal MySQL/MariaDB
#   pdo_sqlite → fallback para tests y dev local
#   zip      → Aimeos
#   gd        → Aimeos (generación thumbnails)
#   exif     → Aimeos
#   fileinfo → Aimeos
RUN docker-php-ext-install -j"$(nproc)" \
        bcmath \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo_mysql \
        pdo_sqlite \
        zip \
        gd \
        exif \
        fileinfo

# Configurar y recompilar GD con soporte para JPEG, WEBP, FREETYPE
# IMPORTANTE: hay que correr configure + install en el mismo RUN, sino el configure
# no tiene efecto (configure solo actualiza flags, install recompila).
RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install -j"$(nproc)" gd

# Extensión PECL: Redis (cliente phpredis — más rápido que predis)
RUN pecl install redis \
    && docker-php-ext-enable redis

# Composer (binario) — misma version que el stage vendor para coherencia
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Zona horaria por defecto — Colombia (puede sobreescribirse con TZ en runtime)
ENV TZ=America/Bogota

# Directorio de la app
WORKDIR /var/www/html

# ──────────────────────────────────────────────────────────────
# Stage 4 — Imagen DEV (con Xdebug, código montado como volumen)
# ──────────────────────────────────────────────────────────────
FROM php-base AS dev

# Xdebug para development
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug,develop" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Permisos
RUN chown -R ${WWW_USER}:${WWW_GROUP} /var/www/html

# Variables de entorno útiles para dev
ENV APP_ENV=local \
    XDEBUG_MODE=debug


# ──────────────────────────────────────────────────────────────
# Stage 5 — Imagen de PRODUCCIÓN (default, optimizada)
# ──────────────────────────────────────────────────────────────
FROM php-base AS app

# Configs PHP personalizadas (producción)
COPY docker/php/php.ini    /usr/local/etc/php/conf.d/zz-exicompras.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/www.conf   /usr/local/etc/php-fpm.d/www.conf

# Copiar vendor (sin dev) desde el stage vendor
COPY --from=vendor --chown=${WWW_USER}:${WWW_GROUP} /app/vendor ./vendor

# Copiar código fuente de la app
COPY --chown=${WWW_USER}:${WWW_GROUP} . .
RUN chmod +x /var/www/html/docker/queue-entrypoint.sh /var/www/html/docker/entrypoint.sh 2>/dev/null || true

# Copiar assets ya compilados por Vite (sobrescribe public/build)
COPY --from=node-builder --chown=${WWW_USER}:${WWW_GROUP} /app/public/build ./public/build

# Permisos: storage y bootstrap/cache deben ser escribibles
RUN chown -R ${WWW_USER}:${WWW_GROUP} /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Entry point: prepara la app (APP_KEY, storage:link, optimize) y arranca php-fpm
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

# Healthcheck: usa el endpoint /up que ya expone Laravel 12 (configurado en bootstrap/app.php)
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -fsS http://127.0.0.1:9000/up 2>/dev/null \
        || (echo > /dev/tcp/127.0.0.1/9000) >/dev/null 2>&1 \
        || exit 1

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint"]
CMD ["php-fpm"]
