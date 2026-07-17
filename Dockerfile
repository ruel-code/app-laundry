FROM node:22 AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:8.4-cli
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev zip unzip git sqlite3 libsqlite3-dev \
    && docker-php-ext-install -j$(nproc) zip pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .
COPY --from=assets /app/public/build public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction && \
    cp .env.example .env && \
    php artisan key:generate --force && \
    touch database/database.sqlite && \
    php artisan migrate --force && \
    php artisan db:seed --class=DatabaseSeeder --force && \
    php artisan storage:link --force

EXPOSE 8000
CMD php -S 0.0.0.0:${PORT:-8000} -t /app/public
