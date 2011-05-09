<?php
require_once(dirname(__FILE__).'/../library/Settings.class.php');
/*
Include this script in normal site pages. It will make smarty
available as the global $s variable.
*/

############################################################
# smarty-winxp.php
#http://smarty.incutio.com/?page=SmartyFrequentlyAskedQuestions
# -- Sets up a connection with the Smarty template engine.
#
# Usage: require_once('smarty-winxp.php') in a script.
#
# Author: David Redstone
# Email: David X-AT-X inglesisimo X-DOT-X com
#
############################################################
######################################################################
# Create constants to represent Smarty's directory, and my directory
# (Notice that for security, Smarty resides outside
# of Apache's web root, which is 'Apache2\htdocs'.)
######################################################################

$settings = new Settings();
if (!defined('SMARTY_DIR'))
{
	define ("SMARTY_DIR", $settings->get('smarty.lib'));
}
if (!defined('MY_DIR'))
{
	define ("MY_DIR", $settings->get('smarty.templates'));
}

#######################################
# Use Smarty.class.php in this file.
#######################################
require_once(SMARTY_DIR.'Smarty.class.php'); // TODO not being found
require_once(dirname(__FILE__).'/../library/RosettaSmarty.class.php'); //$_SERVER['DOCUMENT_ROOT'].'/../library/

##############################
# Create a new Smarty object
##############################
$s = new RosettaSmarty;
$s->initRosettaSmarty();

###################################
# Setup some of Smarty's options.
###################################
$s->compile_check = true;
$s->debugging = false	; //Uncomment this if you want to see the debugging window pop up when you call a template!

###########################################################
# Setup the Smarty template engine's directory structure.
# (gnd is my personal project directory!)
###########################################################
$s->template_dir = MY_DIR.'templates';
$s->compile_dir = MY_DIR.'templates_compiled';
$s->config_dir = MY_DIR.'configs';

$s->assign('s', $s); 
/* Calling it just "smarty" would make Smarty access its default object, 
 * not our customised RosettaSmarty one.
 */
