#!/usr/bin/env bash

export DEBIAN_FRONTEND=noninteractive

apt-get update

echo "mysql-server-5.5 mysql-server/root_password password 123" | sudo debconf-set-selections
echo "mysql-server-5.5 mysql-server/root_password_again password 123" | sudo debconf-set-selections

apt-get install -y curl build-essential vim-nox git
apt-get install -y apache2 libapache2-mod-xsendfile
apt-get install -y php5 php5-mcrypt php5-curl php5-mysql php5-gd php5-dev
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

service apache2 restart

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
cd /tmp; git clone https://github.com/chobie/php-protocolbuffers.git
cd /tmp/php-protocolbuffers
phpize
./configure
make
make install
echo "extension=protocolbuffers.so" > /etc/php5/apache2/conf.d/40-protocolbuffer.ini

cd /var/www/html/match/api; sudo -u vagrant -H sh -c "composer install"
cd /var/www/html/match/ui; sudo -u vagrant -H sh -c "composer install"



