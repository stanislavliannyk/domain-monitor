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

# ШАГ 1: зависимости (кэшируемый слой)
# --no-scripts: не запускать post-autoload-dump — код приложения ещё не скопирован
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

# ШАГ 3: entrypoint — права выставляются при каждом старте контейнера,
# а не здесь, потому что bind-mount хоста перекрывает права из образа
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
