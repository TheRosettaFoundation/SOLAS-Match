SOLAS Match
===========


What is this?
-------------

The Rosetta Foundation SOLAS Match is a web application. It
is designed to be accessed by two groups: NGOs seeking to have content
translated, and volunteer translators who may complete such tasks.
Accessed through the browser, the application displays a list of
translation tasks previously uploaded to the system. A task may have
one resource file attached to it which can be downloaded, translated,
and re-uploaded. Development began in February 2011.

License notice
==============

This software is licensed under the terms of the GNU LESSER GENERAL PUBLIC LICENSE
                       Version 3, 29 June 2007
For full terms see License.txt or http://www.gnu.org/licenses/lgpl-3.0.txt

Contact
=======

**Contact:**

* Reinhard Schäler <reinhard.schaler@ul.ie>

**Coded by:**

* Eoin Ó Conchúir   <eoin.oconchuir@ul.ie>
* David O Carroll   <david.ocarroll@ul.ie>
* Sean Mooney       <Sean.Mooney@ul.ie>
* Manuel Honegger   <Manuel.Honegger@ul.ie>
* Phillip O’Duffy
* Raymond Kearney
* Mark Cummins
* Asanka Wasala
* Tadhg O’Flaherty
* Aaron Mason
* Alan Barrett


Technical Requirements
======================

SOLAS Match is written in PHP 5.4+ code and makes use of
a MySQL 5+ database.

System Requirements
-------------------

HTTP server (Apache, for example) with PHP 5.4+ (ideally 5.5+) interpreter MySQL 5+
database

Several additional libraries also need to be installed alongside
SOLAS Match. 
See the following installation instructions.



Installation
============

Several components and directories need to be set up.

Further below in this document, there are also several resources for our work model for git.

Configure Apache
----------------
 * Create a symbolic link from /var/www/ to wherever you cloned SOLAS Match to.

 * Ensure that RewriteEngine is installed. If not:
   <pre><code>sudo a2enmod rewrite</code></pre>
   
 * Enable X-Sendfile
   <pre><code>sudo apt-get install libapache2-mod-xsendfile</code></pre>    
 
  Add path to upload directory to your host apache2.conf,vHost or httpd.conf(recommended) e.g.
   
  <pre><code>
  &lt;Directory /var/www/&gt;
	    AllowOverride All
	    XSendFilePath /path/to/SOLAS-Match/uploads/
  &lt;/Directory&gt;
  </code></pre>
  
  In your "apache2/sites-available/" directory edit default as below
  
  <pre><code>
  &lt;Directory /var/www/&gt;
	        Options Indexes FollowSymLinks  
                AllowOverride All
                Order allow,deny
                allow from all
                XSendFilePath / 
  &lt;/Directory&gt;
  </code></pre>

Install Solas Match Dependencies
--------------------------------

 * In Ubuntu:
   
   If subversion is not installed run:
    
        $ sudo apt-get install subversion
   If curl is not installed run:
        $ sudo apt-get install curl

	In api/ and ui/ do the following:

        $ curl -s https://getcomposer.org/installer | php

   Then run 
        
        $ php composer.phar install  

Install Chobie Protobuf
--------------------------------

 * In Ubuntu:
   
1. cd into home or workspace folder.
2. Clone Chobie Protobuf
	$ git clone https://github.com/chobie/php-protocolbuffers.git
3. cd into php-protocolbuffers
4. run:
	$ sudo phpize
If you get an error try:
	$ sudo apt-get install php5-dev
5. Run:
	$ sudo ./configure
	$ sudo make
	$ sudo make install
6. Edit php.ini file:
	$ sudo vi /etc/php5/cgi/php.ini
Search for extension= and add the following:
	extension=protocolbuffers.so

Set up the MySQL database
-------------------------

1. Set up a MySQL database.
2. Create a user with all permissions.
3. Import ./api/vendor/league/oauth2-server/sql/mysql.sql (using phpMyAdmin, for example. This MUST be the first import as our schema.sql executes alter table statements on some oauth tables.)
4. Import path/to/repo/db/schema.sql (using phpMyAdmin, for example.)
5. Import path/to/repo/db/languages.sql (using phpMyAdmin, for example.)
6. Import path/to/repo/db/country_codes.sql (using phpMyAdmin, for example.); if using mysql command line to import use --default-character-set=utf8
7. Add the a new entry to the oauth_clients table for your web client using the client_id and client_secret defined in the conf file.
8. Add an entry to the client_endpoints table with the redirect_uri set to the login page URL for this installation.

<code>
GRANT EXECUTE, PROCESS, SELECT, SHOW DATABASES, SHOW VIEW, DELETE, INSERT, UPDATE, LOCK TABLES  ON *.* TO 'tester'@'localhost';
FLUSH PRIVILEGES;
SHOW GRANTS FOR 'tester'@'localhost';
</code>

Configuration file
------------------
    
1.  Copy /Common/conf/conf.template.ini to /Common/conf/conf.ini
2.  Edit conf.ini with your configurations.
3.  Under database, enter your MySQL connection settings.
4.  Under the site section, enter the URL of the installation.
       1. Under the site section, you can choose to either set openid to 'y','n' or 'h'.
	    - setting openid='y' will configure the application to use openid as the login mechanisim.
	    - setting openid='n' will configure the applicataion to fall back to its internal login mechanisim.
	    - setting openid='h' will enable hybrid login.(both login options will be available to the user).
       2. for more reliable openid support install php5-curl. sudo apt-get install php5-curl(fixes issue with google/yahoo connection reset).
5.  Under session, enter a long random string in the site_key field.
6.  Under Files changes upload_path to 'uploads/'
7.  Under oauth generate two random strings for the web client id and secret

Git Pre-Commit Hook
-------------------

If you are interested in developing for SOLAS Match, you will need to copy the git pre-commit file in the SOLAS Match root directory to the .git/hooks/ directory.
This will check any of your commits for harmful sql statements or unwanted debug statements.

Configure PHP
-------------

 * In php.ini set appropriate values for upload_max_filesize and post_max_size (such as 20M each).
   php.ini is often found under /etc/php5/apache2/php.ini
 * Download the php_browscap.ini file from http://tempdownloads.browserscap.com/ and save it locally.
   Suggested save location: /etc/php5/apache2/php_browscap.ini
 * Update your php.ini file to point to the browscap conf file. In php.ini uncomment browscap and update its value.
 e.g. <pre><code>
    [browscap]
    browscap = /etc/php5/apache2/php_browscap.ini
 </code></pre>
 * Make sure the following are also installed
  1. sudo apt-get install php5-cli
  2. sudo apt-get install curl
  3. sudo apt-get install php5-curl
  4. sudo apt-get install php5-mysql
  5. sudo apt-get install php-apc
  6. sudo apt-get install php5-mcrypt

Set file/folder permissions
---------------------------

    chmod 777 path/to/repo/uploads
    chmod 777 path/to/repo/ui/templating/templates_compiled
    chmod 777 path/to/repo/ui/templating/cache
    
Install Solas Match Backend
---------------------------
The Solas Match frontend sould now be fully configured.
To install the Solas Match Backend please follow the instructions at 
https://github.com/TheRosettaFoundation/SOLAS-Match-Backend/blob/master/README.md



Create an Org Account
=====================

Create a user by registering with the app. This can be done  by supplying a valid email and a password.
Once a user has been created you can create an organisation and begin to create projects and tasks.
For more infomation see the videos tab in the web UI.



Collaborative source code management
====================================

Please see the wiki page [Contributing code](https://github.com/TheRosettaFoundation/SOLAS-Match/wiki/Contributing-code)
for the standards followed by this project (such as git version control, and coding
style).
