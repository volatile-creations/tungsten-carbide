ARG PHP_VERSION=8.3

FROM mlocati/php-extension-installer:latest as php-ext-installer
FROM php:${PHP_VERSION}-fpm-alpine as php-fpm

COPY --from=php-ext-installer --link /usr/bin/install-php-extensions /usr/local/bin/

ARG PHP_EXTENSIONS=""
ARG APK_PACKAGES=""
RUN set -eux; \
    if [[ " $APK_PACKAGES " == *" symfony-cli "* ]]; then \
      apk add --no-cache bash curl \
      && curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash; \
    fi; \
    if [[ "$PHP_EXTENSIONS" != "" ]]; then set -eux && install-php-extensions $PHP_EXTENSIONS; fi; \
    if [[ "$APK_PACKAGES" != "" ]]; then apk add --no-cache $APK_PACKAGES; fi;

WORKDIR /var/www/html