#!/bin/sh
set -e

# Создать обязательные директории Laravel (bind-mount хоста может их не содержать)
mkdir -p \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache

# Выставить права для www-data — процесс PHP-FPM должен иметь доступ на запись
chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

chmod -R 775 \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

exec "$@"
