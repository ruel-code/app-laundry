FROM serversideup/php:8.3-cli

ENV APP_ENV=production \
    APP_DEBUG=false \
    DB_CONNECTION=sqlite \
    SESSION_DRIVER=database \
    CACHE_STORE=database \
    QUEUE_CONNECTION=sync \
    PHP_OPCACHE_ENABLE=1

USER root

RUN apt-get update && apt-get install -y --no-install-recommends \
    sqlite3 \
    && rm -rf /var/lib/apt/lists/*

COPY --chown=www-data:www-data . /var/www/html/

USER www-data

RUN cd /var/www/html && \
    cp .env.example .env && \
    php artisan key:generate --force && \
    composer install --no-dev --optimize-autoloader --no-interaction && \
    npm install && npm run build && \
    touch database/database.sqlite && \
    php artisan storage:link --force && \
    php artisan migrate --force && \
    php artisan db:seed --class=DatabaseSeeder --force

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000
