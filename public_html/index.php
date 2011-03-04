<?php
require($_SERVER['DOCUMENT_ROOT'].'/../includes/smarty.php');

if ($tasks = $s->stream->getStream(10))
{
	$s->assign('tasks', $tasks);
}
$s->display('index.tpl');
