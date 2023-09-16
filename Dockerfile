ARG NODE_VERSION=20
ARG ALPINE_VERSION=3.17
ARG PHP_VERSION=8.2

FROM node:${NODE_VERSION}-alpine${ALPINE_VERSION} as node_builder

COPY ./frontend /app
WORKDIR /app
RUN npm ci
RUN npm run prod

######################

FROM php:${PHP_VERSION}-apache as php

ARG USER_ID
ARG GROUP_ID

COPY ./app /var/www
COPY ./httpd.conf /etc/apache2/sites-available/000-default.conf
COPY --from=node_builder /app/dist/js /var/www/public/js
COPY --from=node_builder /app/dist/css /var/www/public/css
COPY --from=node_builder /app/dist/img /var/www/public/img

## Update package information
RUN apt-get update

## Configure Apache
RUN a2enmod rewrite \
    && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf \
    && mv /var/www/html /var/www/public

## Install Composer
RUN curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/local/bin --filename=composer

## Install zip libraries and extension
RUN apt-get install --yes git zlib1g-dev libzip-dev \
    && docker-php-ext-install zip

## Install unzip
RUN apt-get install --yes unzip

## Install intl library and extension
RUN apt-get install --yes libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

## Install xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

## PostgreSQL PDO support
RUN apt-get install --yes libpq-dev \
    && docker-php-ext-install pdo_pgsql

WORKDIR /var/www  

## Install dependencies
RUN composer install

## Add user and group
RUN addgroup --gid $GROUP_ID user
RUN adduser --disabled-password --gecos '' --uid $USER_ID --gid $GROUP_ID user

USER user
