FROM php:7.4-fpm-alpine

RUN apk add --no-cache --virtual .build-deps \
        autoconf g++ make linux-headers \
        freetype-dev libjpeg-turbo-dev libpng-dev \
        icu-dev libzip-dev oniguruma-dev sqlite-dev libxml2-dev \
    && apk add --no-cache \
        freetype libjpeg-turbo libpng icu libzip oniguruma sqlite-libs libxml2 \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        mysqli pdo_mysql pdo_sqlite mbstring tokenizer xml ctype bcmath gd zip fileinfo intl opcache \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/*

WORKDIR /var/www/html
COPY www ./
RUN chown -R www-data:www-data /var/www/html

RUN sed -i 's|^listen = .*|listen = 9000|' /usr/local/etc/php-fpm.d/www.conf

EXPOSE 9000
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s \
  CMD php -r "exit(0);"
CMD ["php-fpm", "--nodaemonize"]
