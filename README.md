SOLAS Match
====================

## What is this?

The Rosetta Foundation SOLAS Match is a web application. It
is designed to be accessed by two groups: NGOs seeking to have content
translated, and volunteer transators who may complete such tasks.
Accessed through the browser, the application displays a list of
translation tasks previously uploaded to the system. A task may have
one resource file attached to it which can be downloaded, translated,
and re-uploaded. Development began in February 2011.

# Copyright notice

© 2011 University of Limerick. All rights reserved. This material may
not be reproduced, displayed, modified or distributed without the
express prior written permission of the copyright holder.

The copyright notice applies to all code in this distribution, unless
explicitly stated otherwise.

# Technical Requirements

SOLAS Match is written in PHP 5.3+ code and makes use of
a MySQL 5+ database.

## System Requirements

HTTP server (Apache, for example) with PHP 5.3+ interpreter MySQL 5+
database

Several additional libraries also need to be installed alongside
SOLAS Match. See the following installation instructions.

# Contact

Contact:

  Reinhard Schäler <reinhard.schaler@ul.ie>

Coded by:

  Eoin Ó Conchúir <eoin.oconchuir@ul.ie>
  David O Carroll <david.ocarroll@ul.ie>


# Installation

Several components and directories need to be set up.

Further below in this document, there are also several resources for our work model for git.

## Configure Apache

 * Ensure that RewriteEngine is installed. If not:
   $ sudo a2enmod rewrite

## Alternitive Configure Lighttpd
 * Ensure that url rewritting is enabled.
    server.modules += ("mod_rewrite")

 * update lighttpd.conf with the following rewite rules
    url.rewrite-once = ( "resources/css/style.([0-9]+).css$" => "resources/css/style.css","^/?index.php/?$" => ""  )
    url.rewrite-if-not-file = ( "/?index.php/.*" => "$1" ,"(.*)" => "index.php/$1")

 * example vhost 
   $HTTP["host"] == "php-workspace" {
      server.document-root = "/home/sean/Dev/Git-Mannaged/SOLAS-Match/"
      url.rewrite-once = ( "resources/css/style.([0-9]+).css$" => "resources/css/style.css","^/?index.php/?$" => ""  )
      url.rewrite-if-not-file = ( "/?index.php/.*" => "$1" ,"(.*)" => "index.php/$1")
   }


## Install Solas Match Dependencies

 * In Ubuntu:
    If subversion is not install run:
    
        $ sudo apt-get install subversion

	In the root of the directory structure run:

        $ curl -s https://getcomposer.org/installer | php

    Then run 
        
        $ php composer.phar install

## Set up the MySQL database

1. Set up a MySQL database.
2. Create a user with all permissions.
3. Import ./app/db/schema.sql (using phpMyAdmin, for example.)
    
GRANT EXECUTE, PROCESS, SELECT, SHOW DATABASES, SHOW VIEW, DELETE, INSERT, UPDATE, LOCK TABLES  ON *.* TO 'tester'@'localhost';
FLUSH PRIVILEGES;
SHOW GRANTS FOR 'tester'@'localhost';

## Configuration file
    
1.  Copy ./app/includes/conf.template.ini to ./app/includes/conf.ini
2.  Edit conf.ini with your configurations.
3.  Under database, enter your MySQL connection settings.
4.  Under the site section, enter the URL of the installation.
4.1 Under the site section, you can choose to either set openid to 'y','n' or 'h'.
    setting openid='y' will configure the application to use openid as the login mechanisium.
    setting openid='n' will configure the applicataion to fall back to its internal login mechanisium.
    setting openid='h' will enable hybrid login.(both login options will be avaiable to the user).
5.  Under user session control, enter a long random string.

## Add Cron Job

The user's individual task stream is calculated by a python script which should be run at regular 
intervals to keep everything up to date. This is achieved by adding it as a cron job.

To edit your crontab file run:

    crontab -e

Choose your default editor and add the following line:

    10 * * * * cd /absolute/path/to/SOLAS-Match/app/scripts/ && ./calculate_scores.py

Change the path above so that it points to the correct location. This will run the python script 
every hour at 10 past the hour.

## Configure PHP

 * In php.ini set appropriate values for upload_max_filesize and post_max_size (such as 20M each).
   php.ini is often found under /etc/php5/apache2/php.ini

## Set file/folder permissions

    chmod 777 ./app/uploads
    chmod 777 ./app/templating/templates_compiled

## Install 960.css Grid System

This step is probably redundant, being replaced by Twitter Bootstrap. If you're working
through these instructions, please review the generaly header template and correct these
nstructions as appropricate.

1. Download 960 Grid System from http://960.gs/
2. Extract just the file 960.css to ./resources/css/

# Create an Org Account

Once the app has been configured and is running succesfully you must create an Organisation
on the database. Tasks are uploaded by Organisation so it is not possible to upload a file
without creating an Organisation. To do this create a new database entry in the organisation
table consisting of the org name, home page and biography. The id is automatically generated
can be left blank.

Create a user by registering with the app. This can be done from the app itself instead of in
phpmyadmin. Once a user has been created log in to phpmyadmin again and create a new entry in 
the organisation\_member table consisting of the org id from the organisation table and the user
id from the user table.

You can now upload files using the client dashboard while logged in as the organisation member

# Resources for future work

[Twitter's Active Reputation System](https://github.com/twitter/activerecord-reputation-system)

# Collaborative source code management

Please see the wiki page [Contributing code](https://github.com/TheRosettaFoundation/SOLAS-Match/wiki/Contributing-code)
for the standards followed by this project (such as git version control, and coding
style).
