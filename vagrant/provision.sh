#!/usr/bin/env bash

export DEBIAN_FRONTEND=noninteractive

apt-get update

echo "mysql-server-5.5 mysql-server/root_password password 123" | sudo debconf-set-selections
echo "mysql-server-5.5 mysql-server/root_password_again password 123" | sudo debconf-set-selections

apt-get install -y curl build-essential vim-nox git
apt-get install -y apache2
apt-get install -y php5 php5-mcrypt php5-curl php5-mysql php5-gd
apt-get install -y mysql-server mysql-client

php5enmod mcrypt
a2enmod rewrite

service apache2 restart