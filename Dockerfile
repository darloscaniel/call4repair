FROM php:8.2-fpm

LABEL maintainer="darloscaniel"

RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libpng-dev libonig-dev zip \
    && docker-php-ext-install pdo_mysql zip mbstring bcmath gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
