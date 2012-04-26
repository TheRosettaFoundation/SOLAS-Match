<?php
/** COPY TO conf.php **/

/** Database **/
$db = array();
$db['server'] = '';
$db['database'] = '';
$db['username'] = '';
$db['password'] = '';

$db['show_errors'] = 'y'; // Set to n for production.
$db['show_sql'] = 'n'; // Set to n for production. Spits out queries as they are executed.
$db['log_file'] = '';

/** Site **/
$site = array();
$site['url'] = '';
$site['name'] = 'Rosetta Translation eXchange';
$site['title'] = 'Rosetta Translation eXchange'; // Default value for the <title></title> tag.
$site['meta_desc'] = 'Help translate content for organisations looking to spread their cause.';
$site['meta_key'] = 'rosetta foundation, translation, crowdsourcing, volunteer, translate';
$site['system_email_address'] = 'eoin.oconchuir.ul@gmail.com';
$site['notifications_monitor_email_address'] = 'eoin.oconchuir.ul+solasnotify@gmail.com';
$site['system_email_name'] = 'Solas Match';

/** User session control **/
$users = array();
$users['site_key'] = ''; // Fill with a string 60 to 80 characters long. Unique for each installation. Used for password encryption.

/** Files **/
$files = array();
$files['upload_path'] = dirname(__FILE__).'/../uploads/'; // No need to edit this

