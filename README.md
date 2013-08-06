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

# License notice

This software is licensed under the terms of the GNU LESSER GENERAL PUBLIC LICENSE
                       Version 3, 29 June 2007
For full terms see License.txt or http://www.gnu.org/licenses/lgpl-3.0.txt

# Contact

Contact:

  Reinhard Schäler <reinhard.schaler@ul.ie>

Coded by:

  Eoin Ó Conchúir   <eoin.oconchuir@ul.ie>
  David O Carroll   <david.ocarroll@ul.ie>
  Sean Mooney       <Sean.Mooney@ul.ie>
  Manuel Honegger   <Manuel.Honegger@ul.ie>	

# Technical Requirements

SOLAS Match is written in PHP 5.3+ code and makes use of
a MySQL 5+ database.

## System Requirements

HTTP server (Apache, for example) with PHP 5.3+ interpreter MySQL 5+
database

Several additional libraries also need to be installed alongside
SOLAS Match. See the following installation instructions.



# Installation

Several components and directories need to be set up.

Further below in this document, there are also several resources for our work model for git.

## Configure Apache

 * Ensure that RewriteEngine is installed. If not:
   sudo a2enmod rewrite
   
 * Enable X-Sendfile
   sudo apt-get install libapache2-mod-xsendfile
    add path to upload directoy to vour host apache2.conf,vHost or httpd.conf(recommended) eg 
    <Directory /var/www/>
		AllowOverride All
		XSendFilePath /path/to/SOLAS-Match/uploads/
    </Directory>


## Alternitive Configure Lighttpd
 * Ensure that url rewritting is enabled.
    server.modules += ("mod_rewrite")

 * update lighttpd.conf with the following rewite rules
    where deployDir is the path under the web root where Solas Match is deployed.
    url.rewrite-once = ( "deployDir/resources/css/style.([0-9]+).css$" => "deployDir/resources/css/style.css","^/?deployDir/index.php/?$" => ""  )
    url.rewrite-if-not-file = (
        "/?deployDir/index.php/.*" => "$1"
        ,"(api/.*)" => "deployDir/api/dispatcher.php/$1"
        ,"(.*)" => "deployDir/index.php/$1"
    )

 * example vHost 
  $HTTP["host"] == "127.0.0.1" {
      server.document-root = "/var/www/"
      url.rewrite-once = ( "solas-match/resources/css/style.([0-9]+).css$" => "solas-match/resources/css/style.css","^/?solas-match/index.php/?$" => ""  )
      url.rewrite-if-not-file = (
        "/?solas-match/index.php/.*" => "$1"
        ,"(api/.*)" => "solas-match/api/dispatcher.php/$1"
        ,"(.*)" => "solas-match/index.php/$1"
        )
   }



## Install Solas Match Dependencies

 * In Ubuntu:
    If subversion is not install run:
    
        $ sudo apt-get install subversion

	In api/ and ui/ do the following:

        $ curl -s https://getcomposer.org/installer | php

    Then run 
        
        $ php composer.phar install

## Set up the MySQL database

1. Set up a MySQL database.
2. Create a user with all permissions.
3. Import ./app/db/schema.sql (using phpMyAdmin, for example.)
3. Import ./app/db/languages.sql (using phpMyAdmin, for example.)
3. Import ./app/db/countries.sql (using phpMyAdmin, for example.)
    
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
4.2 for more reliable openid support install php5-curl. sudo apt-get install php5-curl(fixes issue with google/yahoo connection reset).
5.  Under user session control, enter a long random string.

## Git Pre-Commit Hook

If you are interested in developing for SOLAS Match, you will need to copy the git pre-commit file in the SOLAS Match root directory to the .git/hooks/ directory.
This will check any of your commits for harmful sql statements or unwanted debug statements.

## Configure PHP

 * In php.ini set appropriate values for upload_max_filesize and post_max_size (such as 20M each).
   php.ini is often found under /etc/php5/apache2/php.ini

## Set file/folder permissions

    chmod 777 ./app/uploads
    chmod 777 ./ui/templating/templates_compiled

## Install 960.css Grid System

This step is probably redundant, being replaced by Twitter Bootstrap. If you're working
through these instructions, please review the generaly header template and correct these
nstructions as appropricate.

1. Download 960 Grid System from http://960.gs/
2. Extract just the file 960.css to ./resources/css/

## Install solas Match Backend
The solas match front-end sould now be fully configured.
To install the Solas Match Backend please follow the instructions at 
https://github.com/TheRosettaFoundation/SOLAS-Match-Backend/blob/master/README.md



# Create an Org Account

Create a user by registering with the app. This can be done  by supplying a valid email and a password.
Once a user has been created you can create an organisation and begin to create projects and tasks.
For more infomation see the videos tab in thw web ui.



# Collaborative source code management

Please see the wiki page [Contributing code](https://github.com/TheRosettaFoundation/SOLAS-Match/wiki/Contributing-code)
for the standards followed by this project (such as git version control, and coding
style).
