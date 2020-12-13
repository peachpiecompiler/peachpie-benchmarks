FROM php:8.0.0-cli

COPY . /bench
WORKDIR /bench

ENTRYPOINT ["php", "run.php"]
