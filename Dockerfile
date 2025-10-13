FROM composer AS build
COPY . /app
WORKDIR /app
RUN composer install

FROM dunglas/frankenphp:1-php8.2-bookworm

RUN install-php-extensions \
    intl \
    opcache \
    zip \
    pgsql \
    pdo_pgsql

ENV SERVER_NAME=localhost
ENV SERVER_PORT=:80
ENV FRANKENPHP_CONFIG="worker ./public/index.php"
ENV APP_RUNTIME="Runtime\\FrankenPhpSymfony\\Runtime"

# https://github.com/php/frankenphp/blob/main/caddy/frankenphp/Caddyfile
ENV CADDY_GLOBAL_OPTIONS="admin :2019 \n metrics { \n per_host \n }"
ENV CADDY_SERVER_EXTRA_DIRECTIVES=""

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /app
COPY --from=build /app /app
