FROM php:7.4.6-apache

RUN docker-php-ext-install mysqli

ARG app

COPY ./apps/$app /app
WORKDIR /app
RUN ["/app/install.sh", "/var/www/html"]
