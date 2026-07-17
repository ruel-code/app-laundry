FROM node:22 AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev libsqlite3-dev \
    && docker-php-ext-install -j$(nproc) zip pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /app
COPY --from=assets /app/public/build /app/public/build

WORKDIR /app

RUN set -ex && \
    composer install --no-dev --optimize-autoloader --no-interaction && \
    cp -n .env.example .env 2>/dev/null || true && \
    php artisan key:generate --force && \
    touch database/database.sqlite && chmod 666 database/database.sqlite && \
    php artisan migrate --force && \
    php artisan db:seed --class=DatabaseSeeder --force

ENV PORT=8080

EXPOSE 8080

CMD php artisan serve --host=0.0.0.0 --port=$PORT
