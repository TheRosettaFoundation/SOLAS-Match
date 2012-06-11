# Translation eXchange

## What is this?

The Rosetta Foundation Translation eXchange is a web application. It
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

The Translation eXchange is written in PHP 5.3+ code and makes use of
a MySQL 5+ database.

## System Requirements

HTTP server (Apache, for example) with PHP 5.3+ interpreter MySQL 5+ 
database

Several additional libraries also need to be installed alongside 
Translation eXchange. See the following installation instructions.

# Contact

Contact:

  Reinhard Schäler <reinhard.schaler@ul.ie>

Coded by:

  Eoin Ó Conchúir <eoin.oconchuir@ul.ie>


# Installation

Several components and directories need to be set up.

Further below in this document, there are also several resources for our work model for git.

## Configure Apache

 * Ensure that RewriteEngine is installed. If not:
   $ sudo a2enmod rewrite

## Install Smarty PHP templating engine

 * In Ubuntu: 
	$ sudo apt-get install smarty3

**Or**

 * Clone the git repository at https://github.com/eocdev/Smarty (git@github.com:eocdev/Smarty.git)

## Set up the MySQL database

1. Set up a MySQL database.
2. Create a user with all permissions.
3. Import ./app/setup.sql (using phpMyAdmin, for example.)

## Configuration file

1. Copy ./private/includes/conf.template.php to ./private/includes/conf.php
2. Edit conf.php with your configurations.
3. Under database, enter your MySQL connection settings.
4. Under the site section, enter the URL of the installation.
5. Under user session control, enter a long random string.
6. Note the value of $files['max_upload_file_size'] for configuring PHP (see below).
7. Under Smarty, set the appropriate value for $smarty['lib'].

## Configure PHP

 * In php.ini set appropriate values for upload_max_filesize and post_max_size (such as 20M).
   php.ini is often found under /etc/php5/apache2/php.ini

## Set file/folder permissions

    chmod 777 ./app/uploads
    chmod 777 ./app/templating/templates_compiled

## Install 960.css Grid System

This step is probably redundant, being replaced by Twitter Bootstrap. If you're working
through these instructions, please review the generaly header template and correct these 
nstructions as appropricate.

1. Download 960 Grid System from http://960.gs/
2. Extract just the file 960.css to ./assets/css/

# Resources for future work

[Twitter's Active Reputation System](https://github.com/twitter/activerecord-reputation-system)

# Collaborative source code management

## git primers

* [git - the simple guide](http://nvie.com/posts/a-successful-git-branching-model/)
* [git - a primer](http://danielmiessler.com/study/git/)

## Collaborative workflow

We generally follow [A successful Git branching model](http://nvie.com/posts/a-successful-git-branching-model/).

In this model, most of the work is done off the `develop` branch.

For example, if you want to start working on a new feature, checkout the `develop` branch,
and then branch from their a new feature branch. For example:

    git checkout develop
    git checkout -b my-new-feature-name
    
Only when a branch is finished and ready for testing, we merge it into the `develop` branch.

Once the feature is ready for production, it can be finally merged into the `master` branch.
