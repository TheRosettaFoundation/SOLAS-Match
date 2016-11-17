#!/usr/bin/env bash

export DEBIAN_FRONTEND=noninteractive

apt-get update

echo "mysql-server-5.5 mysql-server/root_password password root" | sudo debconf-set-selections
echo "mysql-server-5.5 mysql-server/root_password_again password root" | sudo debconf-set-selections

apt-get install -y curl build-essential vim-nox git
apt-get install -y apache2 libapache2-mod-xsendfile
apt-get install -y php5 php5-mcrypt php5-curl php5-mysql php5-gd php5-dev php-apc
apt-get install -y mysql-server mysql-client

php5enmod mcrypt
a2enmod rewrite

# Add the virtual domain
if [ ! -f '/etc/apache2/sites-enabled/001-match.conf' ]; then
    rm /etc/apache2/sites-enabled/000-default.conf
    ln -s /vagrant/assets/001-match.conf /etc/apache2/sites-enabled/001-match.conf
fi

# Link the source code
if [ ! -d '/var/www/html/match' ]; then
    ln -s /opt/match /var/www/html/match
fi


# Install composer
if [ ! -e '/usr/local/bin/composer' ]; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
fi

composer self-update

# Add PHP error_log
if [ ! -f '/etc/php5/apache2/conf.d/30-error_log.ini' ]; then
    echo 'error_log=/tmp/php_error.log' > /etc/php5/apache2/conf.d/30-error_log.ini
fi

# Install php-protocolbuffers
if [ ! -f '/etc/php5/apache2/conf.d/40-protocolbuffer.ini' ]; then
    cd /tmp; git clone https://github.com/chobie/php-protocolbuffers.git
    cd /tmp/php-protocolbuffers
    phpize
    ./configure
    make
    make install
    echo "extension=protocolbuffers.so" > /etc/php5/apache2/conf.d/40-protocolbuffer.ini
fi

# Create database
mysql -uroot -proot -e 'create database SolasMatch'

# Import database
mysql --default-character-set=utf8 -u root -proot SolasMatch < /opt/match/api/vendor/league/oauth2-server/sql/mysql.sql
mysql --default-character-set=utf8 -u root -proot SolasMatch < /opt/match/db/schema.sql
mysql --default-character-set=utf8 -u root -proot SolasMatch < /opt/match/db/languages.sql
mysql --default-character-set=utf8 -u root -proot SolasMatch < /opt/match/db/country_codes.sql

# Add OAuth client
mysql -uroot -proot SolasMatch -e 'insert into oauth_clients (id, secret) values ("yub78q7gabcku73FK47A4AIFK7GAK7UGFAK4", "sfvg7gir74bi7ybawQFNJUMSDCPOPi7u238OH88r");'
mysql -uroot -proot SolasMatch -e 'insert into oauth_client_endpoints (client_id) VALUES ("yub78q7gabcku73FK47A4AIFK7GAK7UGFAK4");'

if [ ! -f '/var/www/html/match/Common/conf/conf.ini' ]; then
    ln -s /vagrant/assets/conf.ini /var/www/html/match/Common/conf/conf.ini
fi

service apache2 restart

cd /var/www/html/match/api; sudo -u vagrant -H sh -c "composer install"
cd /var/www/html/match/ui; sudo -u vagrant -H sh -c "composer install"

# Install Backend

apt-get -y install rabbitmq-server
apt-get -y install cmake qt5-default qt5-qmake libqt5sql5-mysql libctemplate-dev

cd /tmp
wget https://github.com/alanxz/rabbitmq-c/archive/v0.7.0.tar.gz
tar zxvf v0.7.0.tar.gz
cd rabbitmq-c-0.7.0/
cmake .
cmake --build .
cmake --build . --target install
cd /tmp
git clone https://github.com/TheRosettaFoundation/amqpcpp.git
cd /tmp/amqpcpp
make
cp -p libamqpcpp.so /usr/local/lib/
cp -p libamqpcpp.a /usr/local/lib/
cp -p include/AMQPcpp.h /usr/local/include/

cd /opt
git clone https://github.com/TheRosettaFoundation/SOLAS-Match-Backend.git
cd /opt/SOLAS-Match-Backend/
git checkout qt521

mkdir /etc/SOLAS-Match
ln -s /opt/SOLAS-Match-Backend/templates/ /etc/SOLAS-Match/templates
ln -s /opt/SOLAS-Match-Backend/schedule.xml /etc/SOLAS-Match/schedule.xml
ln -s /vagrant/assets/conf_back.ini /opt/SOLAS-Match-Backend/conf.ini
qmake
make
cd PluginHandler
ln -s ../conf.ini conf.ini
ln -s ../schedule.xml schedule.xml
ln -s ../templates templates
cd ..
ln -s /vagrant/assets/run_daemon.sh /opt/SOLAS-Match-Backend
chmod 755 run_daemon.sh
