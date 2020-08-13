FROM hhvm/hhvm:3.30.12

COPY . /bench
WORKDIR /bench

ENTRYPOINT ["hhvm", "run.php"]
