ARG PHP_VERSION=8.3

FROM mlocati/php-extension-installer:latest as php-ext-installer
FROM php:${PHP_VERSION}-fpm-alpine as php-fpm

COPY --from=php-ext-installer --link /usr/bin/install-php-extensions /usr/local/bin/

ARG PHP_EXTENSIONS=""
RUN if [[ "$PHP_EXTENSIONS" != "" ]]; then set -eux && install-php-extensions $PHP_EXTENSIONS; fi

WORKDIR /var/www/html