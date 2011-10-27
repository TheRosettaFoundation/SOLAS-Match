<?php
require('../app/includes/smarty.php');

// User is trying to register.
$email = $s->io->post('email');
$password = $s->io->post('password');
if (User::userExists($s, $email))
{
	$error = 'You have already created an account. <a href="'.$s->url->login().'">Please log in.</a>';
	echo $error;
	die;
}

if (!User::validEmail($email))
{
	$error = 'The email address you entered was not valid. Please press back and try again.';
	echo $error;
	die;
}

if (!User::validPassword($password))
{
	$error = 'You didn\'t enter a password. Please press back and try again.';
	echo $error;
	die;
}

if (User::create($s, $email, $password) >= 1)
{
	// Success.
	if (User::login($s, $email, $password))
	{
		header('Location: '.$s->url->server());	
	}
	else
	{
		$error = 'Tried to log you in immediately, but was unable to.';
		echo $error;
		die;
	}
}
else
{
	$error = 'Unable to register.';
	echo $error;
	die;
}
