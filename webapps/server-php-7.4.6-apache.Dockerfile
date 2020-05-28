FROM php:7.4.6-apache

ARG app

COPY ./apps/$app /app
WORKDIR /app
RUN ["/app/install.sh", "/var/www/html"]
