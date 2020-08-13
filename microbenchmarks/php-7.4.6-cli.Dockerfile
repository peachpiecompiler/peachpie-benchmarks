FROM php:7.4.6-cli

COPY . /bench
WORKDIR /bench

ENTRYPOINT ["php", "run.php"]
