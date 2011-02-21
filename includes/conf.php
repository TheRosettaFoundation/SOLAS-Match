<?php

/** Database **/
$db = array();
$db['database'] = '';
$db['server'] = 'localhost';
$db['username'] = '';
$db['password'] = '';
$db['show_errors'] = 'y';
$db['show_sql'] = 'y';
$db['log_file'] = '';
$db['analyse_queries'] = 'n';

/** Email **/
$email = array();
$email['port'] = 25;
$email['host'] = '';
$email['username'] = '';
$email['password'] = '';

/** Site **/
$site = array();
$site['name'] = '';
$site['url'] = '';
$site['title'] = '';
$site['meta_desc'] = '';
$site['meta_key'] = '';

/** Smarty **/
$smarty = array();
$smarty['lib'] = '/home/eoin/sites/rosettaplatform/Rosetta-Foundation/templating/Smarty-3.0.7/libs/';
$smarty['templates'] = '/home/eoin/sites/rosettaplatform/Rosetta-Foundation/templating/';
/*
	$smarty['lib']
		The Smarty library files - downloaded from smarty.net
		Server default: /usr/local/lib/php/Smarty/
		Note: 
			You have to 'escape' backslashes such as 'C:\\http_root'
			Must end with a slash.
	$smarty['templates']
		The Smarty template files - designed for the site
		Server default: /home/focloirg/smarty/
		Note: You have to 'escape' backslashes such as 'C:\\http_root'
		Must end with a slash.
*/
