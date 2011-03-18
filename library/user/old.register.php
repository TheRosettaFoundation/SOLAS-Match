<?php
require($_SERVER['DOCUMENT_ROOT'].'/includes/smarty.php');

$new_member = new User($s);
// $new_member->language = "de"; // use this selector to get messages in other languages

if (isset($_POST['Submit'])) { // the confirm variable is new since ver. 1.84
	// if you don't like the confirm feature use a copy of the password variable
	$login = $s->io->post('login'); //$smarty->interface->cleanseInput($_POST['login']);
	$pw = $s->io->post('password'); //$smarty->interface->cleanseInput($_POST['password']);
	$confirm = $s->io->post('confirm'); //$smarty->interface->cleanseInput($_POST['confirm']);
	$email = $s->io->post('email'); //$smarty->interface->cleanseInput($_POST['email']);
	if ($new_member->register_user($login, $pw, $confirm, $email))
	{
		successfulRegister($s, $new_member);
	}
	else
	{
		showForm($s, $new_member);
	}
}
else
{
	showForm($s, $new_member);
}

function successfulRegister(&$s, $new_member)
{
	$confirmation = $new_member->the_msg; // The confirmation message
	$s->assign('confirmation_msg', $confirmation);
	$s->display('user_register.tpl');
}

function showForm(&$s, $new_member)
{
	$error = $new_member->the_msg; // error message
	if (isset($error))
	{
		$s->assign('error', $error);
	}

	$s->assign('user_reg_login_min_length', LOGIN_LENGTH); // TODO
	$smarty->assign('user_reg_pass_min_length', PW_LENGTH); // TODO
	$smarty->interface->assignText('display_name');
	$smarty->interface->assignText('display_name_help');
	$smarty->interface->assignText('password');
	$smarty->interface->assignText('register');
	$smarty->interface->assignText('user_reg_create');
	$smarty->interface->assignText('user_reg_confirm');
	$smarty->interface->assignText('email');
	$smarty->interface->assignText('user_already_regged');
	$smarty->assign('url_login', $new_member->login_page);
	$smarty->assign('php_self', $_SERVER['PHP_SELF']);
	$smarty->assign('value_login', (isset($_POST['login'])) ? $_POST['login'] : '');
	$smarty->assign('value_password', (isset($_POST['password'])) ? $_POST['password'] : '');
	$smarty->assign('value_confirm', (isset($_POST['confirm'])) ? $_POST['confirm'] : '');
	$smarty->assign('value_email', (isset($_POST['email'])) ? $_POST['email'] : '');

	$smarty->display('user_register.tpl');
}

?>
