FROM node:22 AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM serversideup/php:8.4-fpm-nginx

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY --chown=www-data:www-data . /var/www/html/
COPY --from=assets /app/public/build /var/www/html/public/build

WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader --no-interaction && \
    cp -n .env.example .env 2>/dev/null || true && \
    php artisan key:generate --force && \
    touch database/database.sqlite && chmod 664 database/database.sqlite && \
    php artisan storage:link --force && \
    php artisan migrate --force && \
    php artisan db:seed --class=DatabaseSeeder --force && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/database /var/www/html/bootstrap/cache
