#!/bin/sh
set -e

cd /var/www/html

as_app() {
    su-exec app "$@"
}

[ -f .env ] || as_app cp .env.example .env
[ -f vendor/autoload.php ] || as_app composer install --no-interaction --prefer-dist
grep -q '^APP_KEY=.\+' .env || as_app php artisan key:generate --force

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R app:app storage bootstrap/cache

if [ "$1" = "php-fpm" ] && [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    as_app php artisan migrate --force
fi

exec "$@"
