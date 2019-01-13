FROM php:7.3-fpm-alpine

RUN set -ex && \
    apk --no-cache add autoconf build-base postgresql-dev icu-dev libpng-dev libzip-dev bzip2-dev && \
    docker-php-ext-install pdo_pgsql intl gd zip bz2 opcache && \
    pecl install apcu && \
    docker-php-ext-enable apcu

RUN curl -fsSL https://getcomposer.org/installer | \
        php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir -p /.composer && chmod 777 /.composer

WORKDIR /app
