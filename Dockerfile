FROM ghcr.io/roadrunner-server/roadrunner:2023.1.4 AS roadrunner
FROM php:8.2-alpine as api

ARG APP_ENV=${APP_ENV}

RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        linux-headers \
    && apk add --update --no-cache \
        git \
        openssl-dev \
        pcre-dev \
        icu-dev \
        icu-data-full \
        libzip-dev \
    && docker-php-ext-install  \
        bcmath \
        intl \
        opcache \
        zip \
        sockets \
        pdo_mysql \
    && apk del --purge .build-deps

COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

WORKDIR /usr/src/app

COPY bin bin/
COPY config config/
COPY migrations migrations/
COPY public public/
COPY src src/
COPY templates templates/

COPY .env ./.env
COPY docker/php/conf/php.ini $PHP_INI_DIR/conf.d/php.ini
COPY composer.json composer.lock symfony.lock entrypoint.sh .rr.yaml ./
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ARG COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-scripts --no-progress --no-interaction --optimize-autoloader;\
    composer clearcache; \
    composer dump-env prod; \
    chmod 755 entrypoint.sh; \
    chmod 775 bin/console;

ENTRYPOINT ["./entrypoint.sh"]
CMD ["rr", "serve"]
