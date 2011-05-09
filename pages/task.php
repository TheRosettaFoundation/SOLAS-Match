<?php
require('../private/includes/smarty.php');
/*
 * Input: task_id
 * Page responsible for showing all the details of this particular task.
 */

$task_id = $s->io->get('task_id');
$task = new Task($s, $task_id);
$task_files = $task->files();

if (!$task->isInit())
{
	// Make sure that we've been passed a correct task.
	header('HTTP/1.0 404 Not Found');
}

$s->assign('task', $task);
if ($task_files)
{
	$s->assign('task_files', $task_files);
}
$s->assign('body_class', 'task_page');
$s->display('task.tpl');
