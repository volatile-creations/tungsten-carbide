ARG PHP_VERSION=8.2

FROM mlocati/php-extension-installer:latest as php-ext-installer
FROM php:${PHP_VERSION}-fpm-alpine as php-fpm

COPY --from=php-ext-installer --link /usr/bin/install-php-extensions /usr/local/bin/

ARG PHP_EXTENSIONS
RUN if [[ "$PHP_EXTENSIONS" != "" ]]; then set -eux && install-php-extensions $PHP_EXTENSIONS; fi

RUN set -eux \
    && apk add --no-cache fcgi \
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