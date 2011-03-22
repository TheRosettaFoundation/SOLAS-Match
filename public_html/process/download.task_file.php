<?php
/* Process submitted form data to create a new task. 
	Simple mockup functionality. Therefore, not much error checking happening. 
*/
require($_SERVER['DOCUMENT_ROOT'].'/../includes/smarty.php');

$task_id = intval($s->io->get('task_id'));
$file_id = intval($s->io->get('file_id'));

$task_file = new TaskFile($s, $task_id, $file_id);
$s->io->downloadOriginalFile($task_file->absoluteFilePath(), $task_file->contentType());
$task_file->recordDownload(0);

die;
