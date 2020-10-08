### 1
FROM php:7.4-cli AS php-builder

RUN apt-get update && apt-get install -yqq unzip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet

ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /app

COPY ./composer.json ./composer.lock ./

RUN composer install --no-dev --optimize-autoloader

#### 2
FROM node:alpine AS node-builder

COPY ./ ./app
WORKDIR /app

RUN yarn install && npm rebuild node-sass

RUN npm run build

### 3
FROM php:7.4-fpm

RUN apt-get update && apt-get install -yqq \
    unzip \
    zip \
    && docker-php-ext-install pdo_mysql

WORKDIR /app

COPY ./docker/prod/php/php.ini /usr/local/etc/php/conf.d/default.ini
COPY --from=php-builder /app /app
COPY --from=node-builder /app/public/build /app/public/build
COPY ./ ./

ENV APP_ENV prod

RUN php bin/console cache:warmup
