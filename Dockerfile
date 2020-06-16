FROM alpine:3.9.4 AS base

RUN apk update && \
    apk add \
        php-cli \
        php-json


WORKDIR /app

FROM base AS build

RUN apk add \
    curl \
    php-phar \
    php-iconv \
    php-openssl \
    php-curl


RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin --filename=composer

RUN composer global require hirak/prestissimo

COPY composer.* /app/
RUN composer install

FROM base AS prod

COPY --from=build /app /app
COPY src /app/src
COPY public /app/public

ENTRYPOINT php -S 0.0.0.0:80 -t public src/router.php
