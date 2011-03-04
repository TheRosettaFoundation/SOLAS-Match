<?php
/*
 * Input: task_id
 * Page responsible for showing all the details of this particular task.
 */
require($_SERVER['DOCUMENT_ROOT'].'/../includes/smarty.php');
$task_id = $s->io->get('task_id');
$task = new Task($s, $task_id);

if (!$task->isInit())
{
	// Make sure that we've been passed a correct task.
	header('HTTP/1.0 404 Not Found');
}

$s->assign('task', $task);
$s->display('task.tpl');
