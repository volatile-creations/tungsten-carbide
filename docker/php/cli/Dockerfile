ARG PHP_VERSION=8.3

FROM mlocati/php-extension-installer:latest as php-ext-installer
FROM php:${PHP_VERSION}-cli-alpine as php-fpm

COPY --from=php-ext-installer --link /usr/bin/install-php-extensions /usr/local/bin/

ARG PHP_EXTENSIONS=""
ARG APK_PACKAGES=""
RUN set -eux; \
    if [[ " $APK_PACKAGES " == *" symfony-cli "* ]]; then \
      apk add --no-cache bash curl \
      && curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash; \
    fi; \
    if [[ "$APK_PACKAGES" != "" ]]; then apk add --no-cache $APK_PACKAGES; fi; \
    if [[ "$PHP_EXTENSIONS" != "" ]]; then install-php-extensions $PHP_EXTENSIONS; fi

WORKDIR /var/www/html