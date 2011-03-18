<?php
require($_SERVER['DOCUMENT_ROOT'].'/includes/smarty.php');

$renew_password = new User($s);

if (isset($_POST['Submit'])) {
	$renew_password->forgot_password($s->io->post('email'));
} 
$error = $renew_password->the_msg;
if (isset($error))
{
	$s->assign('error', $error);
}

$s->assign('page_title', 'Forgotten your password');
$s->assign('site_email', $s->set->get('site.email'));

if (isset($_POST['email']))
{
	$s->assign('value_email',$s->io->post('email'));
}
else if (isset($_GET['email']))
{
	$s->assign('value_email', $s->io->formatFromURL($_GET['email']));
}

$s->display('account.forgot-password.tpl');
