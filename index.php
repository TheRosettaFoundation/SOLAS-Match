<?php
require('private/includes/smarty.php');

if ($tasks = $s->stream->getStream(10))
{
	$s->assign('tasks', $tasks);
}
$s->display('index.tpl');
