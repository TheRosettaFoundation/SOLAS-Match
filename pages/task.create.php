<?php
require('../app/includes/smarty.php');

// Check permissions
if (!$s->users->isLoggedIn())
{
	header('Location: '.$s->url->login());
	die;
}

$s->display('task.create.tpl');
