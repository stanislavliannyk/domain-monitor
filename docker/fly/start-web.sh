#!/bin/sh
set -e

# Права на storage и cache (на случай если volume примонтирован позже)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Прогреваем кэш конфигурации/маршрутов
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec /usr/bin/supervisord -c /etc/supervisord.conf
