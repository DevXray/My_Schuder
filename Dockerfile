# syntax=docker/dockerfile:1
#
# Empat stage, tiga target yang dipakai lewat `docker compose build --target ...`:
#   - dev         -> image PHP-FPM untuk development (kode di-mount dari host)
#   - prod        -> image PHP-FPM untuk production (kode dan vendor di-bake ke image)
#   - nginx-prod  -> image nginx untuk production (static asset di-bake, tidak share filesystem dg app)
#
# Kenapa 4 stage bukan 1 Dockerfile flat? Supaya build cache efisien (composer/npm install
# cuma re-run kalau composer.json/package.json berubah, bukan tiap kali source code berubah)
# dan supaya image production tidak membawa xdebug, devDependencies, atau file .git.

############################################
# Stage: composer-deps — vendor PRODUCTION only
############################################
FROM composer:2 AS composer-deps
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction

############################################
# Stage: frontend-build — compile asset Vite
############################################
FROM node:20-alpine AS frontend-build
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js tailwind.config.js postcss.config.js ./
RUN npm run build

############################################
# Stage: base — runtime PHP + ekstensi yang dipakai bersama dev & prod
############################################
FROM php:8.2-fpm-alpine AS base

RUN apk add --no-cache \
        bash \
        curl \
        netcat-openbsd \
        libpng-dev \
        libzip-dev \
        oniguruma-dev \
        icu-dev \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]

############################################
# Stage: dev — kode di-bind-mount lewat docker-compose.yml, vendor di-install di sini
# supaya dependency PHP tidak tergantung environment host.
############################################
FROM base AS dev

RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-interaction

############################################
# Stage: prod — kode DAN vendor di-bake ke image, image ini immutable
############################################
FROM base AS prod

COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

COPY . .
COPY --from=composer-deps /app/vendor ./vendor
RUN composer dump-autoload --optimize --no-dev \
    && addgroup -g 1000 www \
    && adduser -G www -u 1000 -D www \
    && chown -R www:www /var/www/html \
    && chmod -R 775 storage bootstrap/cache

COPY --from=frontend-build /app/public/build ./public/build

USER www

############################################
# Stage: nginx-prod — web server production, static asset di-bake dari build yang SAMA
# dengan yang dipakai stage `prod`, jadi versi kode selalu konsisten antara nginx & php-fpm.
############################################
FROM nginx:1.27-alpine AS nginx-prod
COPY docker/nginx/app.conf /etc/nginx/conf.d/default.conf
COPY public /var/www/html/public
COPY --from=frontend-build /app/public/build /var/www/html/public/build
