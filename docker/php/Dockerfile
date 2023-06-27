ARG PHP_VERSION=8.2
ARG COMPOSER_VERSION=2

FROM composer:${COMPOSER_VERSION} as composer
FROM php:${PHP_VERSION}-fpm-alpine as php-fpm

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache fcgi \
    && set -eux \
	&& { \
		echo '[www]'; \
		echo 'ping.path = /ping'; \
	} | tee /usr/local/etc/php-fpm.d/docker-healthcheck.conf

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