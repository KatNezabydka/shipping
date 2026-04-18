FROM php:8.4.11-fpm-alpine

RUN apk add --no-cache \
    $PHPIZE_DEPS \
    linux-headers

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN apk del $PHPIZE_DEPS

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apk add --no-cache git

WORKDIR /var/www