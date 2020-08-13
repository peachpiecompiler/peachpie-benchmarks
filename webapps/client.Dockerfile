FROM httpd:2.4.43

# Install MySQL client to enable database connectivity checks
RUN apt-get update &&\
    apt-get install -y gnupg &&\
    apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 8C718D3B5072E1F5 &&\
    echo "deb http://repo.mysql.com/apt/debian/ buster mysql-8.0" > /etc/apt/sources.list.d/mysql.list &&\
    apt-get update &&\
    apt-get install -y mysql-community-client

CMD ["bash"]
