FROM node:22 AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:8.4-fpm-alpine

RUN apk add --no-cache nginx supervisor libzip-dev sqlite-dev sqlite && \
    docker-php-ext-install -j$(nproc) zip pdo_sqlite && \
    mkdir -p /etc/nginx/http.d /run/nginx

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /app
COPY --from=assets /app/public/build /app/public/build
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/www.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /app

RUN set -ex && \
    composer install --no-dev --optimize-autoloader --no-interaction && \
    cp -n .env.example .env 2>/dev/null || true && \
    php artisan key:generate --force && \
    touch database/database.sqlite && \
    chmod 666 database/database.sqlite && \
    php artisan migrate --force && \
    php artisan db:seed --class=DatabaseSeeder --force && \
    php artisan storage:link --force && \
    chown -R www-data:www-data /app/storage /app/database /app/bootstrap/cache /app/public/build

EXPOSE 8080

CMD supervisord -n -c /etc/supervisord.conf
