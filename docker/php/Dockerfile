ARG PHP_VERSION=8.2
ARG COMPOSER_VERSION=2

FROM composer:${COMPOSER_VERSION} as composer
FROM mlocati/php-extension-installer:latest as php-ext-installer
FROM php:${PHP_VERSION}-fpm-alpine as php-fpm

COPY --from=composer --link /usr/bin/composer /usr/bin/composer
COPY --from=php-ext-installer --link /usr/bin/install-php-extensions /usr/local/bin/

RUN apk add --no-cache fcgi \
    && set -eux \
    && install-php-extensions \
        apcu \
        intl \
        opcache \
        zip \
	&& { \
		echo '[www]'; \
		echo 'ping.path = /ping'; \
	} | tee /usr/local/etc/php-fpm.d/docker-healthcheck.conf

ARG XDEBUG=0
RUN if [[ $XDEBUG -eq 1 ]]; then install-php-extensions xdebug; fi

ARG MYSQL=0
RUN if [[ $MYSQL -eq 1 ]]; then install-php-extensions pdo_mysql; fi

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD [ \
    "env", \
    "REQUEST_METHOD=GET", \
    "SCRIPT_NAME=/ping", \
    "SCRIPT_FILENAME=/ping", \
    "cgi-fcgi", \
    "-bind", \
    "-connect", \
    "127.0.0.1:9000" \
]

WORKDIR /var/www/html