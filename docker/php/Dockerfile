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

# Копируем только composer.json/composer.lock для кэширования слоя зависимостей.
# --no-scripts предотвращает запуск post-autoload-dump (php artisan package:discover),
# который падает потому что код приложения ещё не скопирован на этом шаге.
COPY composer.json composer.lock* ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# Копируем весь код приложения
COPY . .

# Запускаем post-install скрипты теперь, когда весь код на месте,
# и выставляем права на директории Laravel
RUN php artisan package:discover --ansi \
    && chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
