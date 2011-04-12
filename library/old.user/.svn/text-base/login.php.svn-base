<?php 
require($_SERVER['DOCUMENT_ROOT'].'/includes/smarty.php');

if ($s->u->isLoggedIn())
{
	// They shouldn't be here, forward to home page.
	header('Location: '.$s->url->server());
	die;
}

$my_access = new User($s, false);
$confirmation = '';

if (isset($_GET['activate']) && isset($_GET['ident'])) { // this two variables are required for activating/updating the account/password
	$my_access->auto_activation = true; // use this (true/false) to stop the automatic activation
	$successful = $my_access->activate_account($_GET['activate'], $_GET['ident']); // the activation method 
	$confirmation = $my_access->the_msg; // Show 'error' message as a positive confirmation
}
if (isset($_GET['validate']) && isset($_GET['id'])) { // this two variables are required for activating/updating the new e-mail address
	$my_access->validate_email($_GET['validate'], $_GET['id']); // the validation method 
}
// Log the person in.
if (isset($_POST['Submit']))
{
	$my_access->save_login = (isset($_POST['remember']) && ($_POST['remember'] == 'yes')) ? $_POST['remember'] : 'no'; // use a cookie to remember the login
	//$my_access->count_visit = true; // if this is true then the last visitdate is saved in the database (field last_visit)
	$my_access->login_user($s->io->post('email'), $s->io->post('password'), $s->io->post('redirect')); // call the login method
} 

// Show the user the login form.
$s->assign('php_self', $_SERVER['PHP_SELF']);
$s->assign('value_redirect', $s->io->formatFromURL($s->io->get('redirect')));
if (isset($_GET['email']))
{
	// Help the user by pre-filling the email address.
	$s->assign('value_email', $s->io->formatFromURL($_GET['email']));
}
else if (isset($_POST['email']))
{
	$s->assign('value_email', (isset($_POST['email'])) ? $_POST['email'] : $my_access->email);
}
$s->assign('value_password', (isset($_POST['password'])) ? $_POST['password'] : '');
$s->assign('remember_checked', ($my_access->is_cookie == true) ? ' checked="checked"' : '');

// Error handling
if (!empty($confirmation))
{
	$s->assign('confirmation', $confirmation);
}
else
{
	$error = $my_access->the_msg; // error message
	if (isset($error))
	{
		$s->assign('error', $error);
	}
}
$s->display('account.login.tpl');
