FROM php:8.2-fpm-alpine AS base

# Dpendencias varias
RUN apk add --no-cache \
        bash \
        git \
        unzip \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
    && docker-php-ext-install \
        pdo_mysql \
        mysqli \
        intl \
        opcache \
        zip \
        mbstring \
    && docker-php-ext-enable opcache

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Instalar composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

FROM base AS vendor

COPY composer.json composer.lock ./

# Instalar dependencias
RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --no-interaction

COPY . .

RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

FROM base AS dev

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/php/php-dev.ini /usr/local/etc/php/conf.d/php-dev.ini
COPY docker/php/entrypoint-dev.sh /usr/local/bin/entrypoint-dev.sh
RUN chmod +x /usr/local/bin/entrypoint-dev.sh

WORKDIR /var/www/html

# Pre-crear var/ con el propietario correcto para que php-fpm (www-data) pueda escribir
RUN mkdir -p var/cache var/log && chown -R www-data:www-data var

ENTRYPOINT ["entrypoint-dev.sh"]
CMD ["php-fpm"]