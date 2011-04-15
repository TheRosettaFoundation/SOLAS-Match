<?php
require($_SERVER['DOCUMENT_ROOT'].'/../includes/smarty.php');

if (!empty($_POST['email']) && !empty($_POST['password']))
{
	// User is trying to register.
	$email = $s->io->post('email');
	$password = $s->io->post('password');
	if (User::userExists($s, $email))
	{
		$error = 'You have already created an account. <a href="'.$s->url->login().'">Please log in.</a>';
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
}
