#!/bin/sh
set -e

# ─────────────────────────────────────────────────────────────────────────────
# Setup tasks only run when starting the main PHP-FPM process (not for the
# queue worker or scheduler containers which override CMD).
# ─────────────────────────────────────────────────────────────────────────────
if [ "$1" = "php-fpm" ]; then
    cd /var/www/html

    # Validate APP_KEY
    if [ -z "$APP_KEY" ]; then
        echo "ERROR: APP_KEY is not set. Add it to your .env file."
        echo "       Run: php artisan key:generate --show"
        exit 1
    fi

    # Install Composer dependencies if vendor is empty (first boot in dev)
    if [ ! -f "vendor/autoload.php" ]; then
        echo "==> Installing Composer dependencies..."
        composer install --no-interaction --prefer-dist --no-progress
    fi

    # Ensure writable directories have correct permissions
    chown -R www-data:www-data storage bootstrap/cache
    chmod -R 775 storage bootstrap/cache

    # Run database migrations (idempotent)
    echo "==> Running migrations..."
    php artisan migrate --force --no-interaction

    # Aimeos shop setup — safe to run on every boot, skips already-done steps
    echo "==> Running Aimeos setup..."
    php artisan aimeos:setup --option=db:defaultonly=1 --no-interaction 2>/dev/null || true

    # Create public/storage symlink (--force recreates if target changed)
    php artisan storage:link --force 2>/dev/null || true

    if [ "$APP_ENV" = "production" ]; then
        echo "==> Caching config, routes and views..."
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
    else
        php artisan config:clear  2>/dev/null || true
        php artisan route:clear   2>/dev/null || true
        php artisan view:clear    2>/dev/null || true
    fi

    # Signal to the healthcheck that setup is complete
    touch /tmp/.app-ready

    echo "==> Application ready."
fi

exec "$@"
