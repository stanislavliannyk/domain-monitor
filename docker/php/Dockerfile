FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    bash \
    curl \
    git \
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

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
