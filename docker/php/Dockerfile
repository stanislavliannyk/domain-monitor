FROM php:8.3-fpm-alpine

# Системные зависимости
RUN apk add --no-cache \
    bash \
    curl \
    git \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    zip \
    unzip

# PHP-расширения
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    zip \
    bcmath \
    opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ШАГ 1: только composer-манифест → кэшируемый слой зависимостей.
# --no-scripts: не запускать post-autoload-dump (php artisan package:discover),
# который упадёт, пока код приложения ещё не скопирован.
COPY composer.json composer.lock* ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# ШАГ 2: весь код приложения
COPY . .

# ШАГ 3: права на директории Laravel.
# package:discover запускается автоматически при первом старте приложения —
# в образе его запускать не нужно (требует APP_KEY и рабочий .env).
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
