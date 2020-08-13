FROM mysql:8.0.20

ENV MYSQL_ROOT_PASSWORD=password

COPY ./db.sql.gz /docker-entrypoint-initdb.d/
