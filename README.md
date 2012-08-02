SOLAS Match
====================

What is this?
-------------

The Rosetta Foundation SOLAS Match is a web application. It
is designed to be accessed by two groups: NGOs seeking to have content
translated, and volunteer transators who may complete such tasks.
Accessed through the browser, the application displays a list of
translation tasks previously uploaded to the system. A task may have
one resource file attached to it which can be downloaded, translated,
and re-uploaded. Development began in February 2011.

Copyright notice
----------------

© 2011 University of Limerick. All rights reserved. This material may 
not be reproduced, displayed, modified or distributed without the 
express prior written permission of the copyright holder.

The copyright notice applies to all code in this distribution, unless
explicitly stated otherwise.

Technical Requirements
----------------------

SOLAS Match is written in PHP 5.3+ code and makes use of
a MySQL 5+ database.

### System Requirements

HTTP server (Apache, for example) with PHP 5.3+ interpreter MySQL 5+ 
database

Several additional libraries also need to be installed alongside 
SOLAS Match. See the following installation instructions.

Contact
-------

Contact:

  Reinhard Schäler <reinhard.schaler@ul.ie>

Coded by:

  Eoin Ó Conchúir <eoin.oconchuir@ul.ie>


Installation
------------

Several components and directories need to be set up.

### Configure Apache

 * Ensure that RewriteEngine is installed. If not:
   $ sudo a2enmod rewrite

### Install Smarty PHP templating engine

 * In Ubuntu:
    If subversion is not install run:
    
        $ sudo apt-get install subversion

	In the root of the directory structure run:

        $ curl -s https://getcomposer.org/installer | php

    Then run 
        
        $ php composer.phar install

### Set up the MySQL database

1. Set up a MySQL database.
2. Create a user with all permissions.
3. Import ./app/setup.sql (using phpMyAdmin, for example.)

### Configuration file

1. Copy ./private/includes/conf.template.php to ./private/includes/conf.php
2. Edit conf.php with your configurations.
3. Under database, enter your MySQL connection settings.
4. Under the site section, enter the URL of the installation.
5. Under user session control, enter a long random string.
6. Note the value of $files['max_upload_file_size'] for configuring PHP (see below).
7. Under Smarty, set the appropriate value for $smarty['lib'].

### Configure PHP

 * In php.ini set appropriate values for upload_max_filesize and post_max_size (such as 20M).
   php.ini is often found under /etc/php5/apache2/php.ini

### Set file/folder permissions

    chmod 777 ./app/uploads
    chmod 777 ./app/templating/templates_compiled

### Install 960.css Grid System

1. Download 960 Grid System from http://960.gs/
2. Extract just the file 960.css to ./assets/css/

### Install sub modules on git

1. Once the github version has been cloned locally the libs directory must be populated with 
submodules
2. in the root of the git tree run:
	
	git submodule init

3. Then run:

	git submodule update

NOTE: Step 3 clones the repos into the correct place locally. You must share your public key 
with github in order to complete the download. keys can be generated using ssh-keygen.
NOTE: The error "Fatal: needed single revision" means that there is a file/files in the target 
directory. Remove the offending files and rerun.
