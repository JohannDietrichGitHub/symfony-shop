FROM composer:2 AS dependencies

WORKDIR /app
COPY composer.json composer.lock ./

FROM dependencies AS dependencies-development
RUN composer install \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

FROM dependencies AS dependencies-production
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

FROM php:8.4-fpm-bookworm AS php-base

RUN apt-get update \
    && apt-get install -y --no-install-recommends libicu-dev libpq-dev \
    && docker-php-ext-install intl opcache pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

FROM php-base AS development
COPY --from=dependencies-development /app/vendor ./vendor
COPY --chown=www-data:www-data . .
RUN mkdir -p var/cache var/log && chown -R www-data:www-data var
USER www-data
CMD ["php-fpm", "-F"]

FROM php-base AS production
COPY --from=dependencies-production /app/vendor ./vendor
COPY --chown=www-data:www-data . .
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var \
    && APP_ENV=prod APP_DEBUG=0 APP_SECRET=build-time-placeholder \
       DATABASE_URL=postgresql://build:build@localhost:5432/build \
       php bin/console cache:clear --env=prod
USER www-data
CMD ["php-fpm", "-F"]
