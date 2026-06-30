# Throwaway CLI image for running the phpBB updater against the local ephemeral
# MariaDB (issue #3). Mirrors the php-fpm runtime extensions of the production
# Dockerfile so the local upgrade exercises the same PHP surface the board runs
# on. The phpBB source tree is bind-mounted at runtime, not copied in.
FROM php:7.4-cli-alpine

RUN apk add --no-cache --virtual .build-deps \
        autoconf g++ make linux-headers \
        freetype-dev libjpeg-turbo-dev libpng-dev \
        icu-dev libzip-dev oniguruma-dev sqlite-dev libxml2-dev \
    && apk add --no-cache \
        freetype libjpeg-turbo libpng icu libzip oniguruma sqlite-libs libxml2 \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        mysqli pdo_mysql pdo_sqlite mbstring tokenizer xml ctype bcmath gd zip fileinfo intl opcache \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/*

WORKDIR /var/www/html
