ARG PHP_VERSION=8.2
ARG COMPOSER_VERSION=2

FROM composer:${COMPOSER_VERSION} as composer
FROM mlocati/php-extension-installer:latest as php-ext-installer
FROM php:${PHP_VERSION}-cli-alpine as php-fpm

COPY --from=composer --link /usr/bin/composer /usr/bin/composer
COPY --from=php-ext-installer --link /usr/bin/install-php-extensions /usr/local/bin/

ARG PHP_EXTENSIONS
RUN if [[ "$PHP_EXTENSIONS" != "" ]]; then set -eux && install-php-extensions $PHP_EXTENSIONS; fi

WORKDIR /var/www/html