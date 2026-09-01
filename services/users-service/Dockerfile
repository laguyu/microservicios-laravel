FROM composer:2 AS dependencies

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --no-scripts

FROM php:8.3-cli-bookworm

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install --yes --no-install-recommends git libicu-dev libonig-dev libzip-dev unzip \
    && docker-php-ext-install bcmath intl mbstring pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=dependencies /usr/bin/composer /usr/bin/composer
COPY --from=dependencies /app/vendor ./vendor
COPY . .

RUN composer dump-autoload --no-dev --optimize \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

CMD ["/bin/sh", "-c", "php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]