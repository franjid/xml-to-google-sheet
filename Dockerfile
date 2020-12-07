FROM php:7.4-fpm-alpine

RUN apk add --update \
    composer \
    git \
    && rm -rf /var/cache/apk/*

COPY . /push-xml-google-sheet
WORKDIR /push-xml-google-sheet

RUN composer install --prefer-source --no-interaction --optimize-autoloader
