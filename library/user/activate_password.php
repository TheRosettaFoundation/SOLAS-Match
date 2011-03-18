<?php 
require($_SERVER['DOCUMENT_ROOT'].'/includes/smarty.php');

$act_password = new User($s);

// Accept a request from an email link to set a new password
// for account id.
if (isset($_GET['activate']) && isset($_GET['id']))
{
	// These two variables are required for activating/updating the
	// account/password
	$id = $s->io->get('id');
	$activation = $s->io->get('activate');
	if ($act_password->check_activation_password($activation, $id))
	{
		// the activation/validation method
		display_password_form($s, $act_password, $id, $activation);
	}
	else
	{
		display_fatal_error($s, $act_password);
	}
}
else if (isset($_POST['Submit']))
{
	// Accept the form to set a new password.
	$password = $s->io->post('password');
	$confirm = $s->io->post('confirm');
	$activation = $s->io->post('activation');
	$id = $s->io->post('id');
	if ($act_password->activate_new_password($password, $confirm, $activation, $id))
	{
		// Success. This will change the password, inserts new password only ones!
		display_confirmation($s, $act_password);
	}
	else
	{
		// There was an error with the passwords. Re-display form.
		display_password_form($s, $act_password, $id, $activation);
	}
}

/**********************************/
/******* Display Functions ********/
/**********************************/
function display_password_form(&$s, &$act_password, $id, $activation)
{
	$error = $act_password->the_msg;
	if (!empty($error))
	{
		$s->assign('error', $error);
	}
	$s->assign('page_title', 'Choose a new password');
	$s->assign('value_activation', $activation);
	$s->assign('value_id', $id);
	$s->display('account.activate-password.tpl');
}

function display_fatal_error(&$s, &$act_password)
{
	$s->assign('fatal_error', $act_password->the_msg);
	$s->display('account.activate-password.tpl');	
}

function display_confirmation(&$s, &$act_password)
{
	$s->assign('confirmation', $act_password->the_msg);
	$s->display('account.activate-password.tpl');		
}
