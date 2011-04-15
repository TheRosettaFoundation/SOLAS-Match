<?php
require($_SERVER['DOCUMENT_ROOT'].'/../includes/smarty.php');

// Check permissions
if (!$s->isLoggedIn())
{
	header('Location: '.$s->url->login());
	die;
}

$s->display('task.create.tpl');
