FROM php:7.4-fpm

RUN apt-get update && apt-get install -yqq \
    unzip \
    zip \
    && docker-php-ext-install pdo_mysql \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet

ENV COMPOSER_ALLOW_SUPERUSER 1

COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /app
