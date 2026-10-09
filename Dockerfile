FROM php:8.3-fpm-alpine

RUN apk add --no-cache --virtual .build-deps \
        autoconf g++ make linux-headers \
        freetype-dev libjpeg-turbo-dev libpng-dev \
        icu-dev libzip-dev postgresql-dev \
    && apk add --no-cache \
        freetype libjpeg-turbo libpng icu libzip libpq \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pgsql pdo_pgsql bcmath gd zip intl \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/*

# Align www-data to uid/gid 1000: deploy-stack chowns the state bind-mounts to
# uid 1000, so php-fpm must run as 1000 to write files/, store/, cache/ and avatars.
RUN apk add --no-cache --virtual .user-deps shadow \
    && groupmod -g 1000 www-data \
    && usermod -u 1000 -g 1000 www-data \
    && apk del .user-deps

WORKDIR /var/www/html
COPY site ./site
COPY forum ./forum
RUN chown -R www-data:www-data /var/www/html

RUN sed -i 's|^listen = .*|listen = 9000|' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's|^;\?clear_env = .*|clear_env = no|' /usr/local/etc/php-fpm.d/www.conf

EXPOSE 9000
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s \
  CMD php -r "exit(0);"
CMD ["php-fpm", "--nodaemonize"]
