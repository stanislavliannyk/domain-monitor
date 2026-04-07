# ── Stage 1: build frontend assets ──────────────────────────────────────────
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY resources/ resources/
COPY vite.config.js postcss.config.js tailwind.config.js ./
RUN npm run build

# ── Stage 2: production image ────────────────────────────────────────────────
FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    nginx \
    supervisor \
    libpq-dev \
    libzip-dev \
    oniguruma-dev \
    zip \
    unzip

RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    zip \
    bcmath \
    opcache

# Opcache: production settings
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.validate_timestamps=0'; \
} > /usr/local/etc/php/conf.d/opcache.ini

# PHP: производственные настройки
RUN { \
    echo 'upload_max_filesize=64M'; \
    echo 'post_max_size=64M'; \
    echo 'memory_limit=256M'; \
    echo 'max_execution_time=60'; \
    echo 'display_errors=Off'; \
    echo 'log_errors=On'; \
    echo 'error_log=/dev/stderr'; \
} > /usr/local/etc/php/conf.d/app.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Копируем код (без .env, node_modules и т.д. — см. .dockerignore)
COPY . .

# Копируем собранный фронтенд
COPY --from=frontend /app/public/build public/build

# Создаём директории до composer install (нужен bootstrap/cache для package:discover)
RUN mkdir -p \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/framework/testing \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Устанавливаем PHP-зависимости (только prod)
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader

# Nginx + Supervisor
COPY docker/fly/nginx.conf      /etc/nginx/nginx.conf
COPY docker/fly/supervisord.conf /etc/supervisord.conf
COPY docker/fly/start-web.sh    /usr/local/bin/start-web.sh
RUN chmod +x /usr/local/bin/start-web.sh

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
