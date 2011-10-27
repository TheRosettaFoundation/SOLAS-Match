<?php
require('../app/includes/smarty.php');

if (!empty($_POST['email']) && !empty($_POST['password']))
{
	// Trying to log in...
	$email = $s->io->post('email');
	$password = $s->io->post('password');
	
	if (User::login($s, $email, $password))
	{
		header('Location: '.$s->url->server());
		die;	
	}
	else
	{
		$error = 'Unable to log in. Please check your email and password. <a href="'.$s->url->login().'">Try logging in again</a>.';
		echo $error;
		die;
	}
}
