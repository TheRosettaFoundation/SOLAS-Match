Translation eXchange
====================

Installation
------------
Several components and directories need to be set up.

### Configure Apache

1. Ensure that RewriteEngine is installed. If not:
   $ sudo a2enmod rewrite

### Install Smarty PHP templating engine

1. In Ubuntu: $ sudo apt-get install smarty3

### Set up the MySQL database

1. Set up a MySQL database.
2. Create a user with all permissions.
3. Import ./private/setup.sql (using phpMyAdmin, for example.)

### Configuration file

1. Copy ./private/includes/conf.template.php to ./private/includes/conf.php
2. Edit conf.php with your configurations.
3. Under database, enter your MySQL connection settings.
4. Under the site section, enter the URL of the installation.
5. Note the value of $files['max_upload_file_size'] for configuring PHP (see below).
6. Under Smarty, set the appropriate value for $smarty['lib'].

### Configure PHP

1. In php.ini set appropriate values for upload_max_filesize and post_max_size (such as 20M).
   php.ini is often found under /etc/php5/apache2/php.ini

### Set file/folder permissions
