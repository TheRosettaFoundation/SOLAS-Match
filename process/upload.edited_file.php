<?php
require('../private/includes/smarty.php');
/* Process submitted form data to create a new task. 
	Simple mockup functionality. Therefore, not much error checking happening. 
*/

$task_id = intval($s->io->post('task_id'));
$file_id = intval($s->io->post('file_id'));

$task_file = new TaskFile($s, $task_id, $file_id);

if (!$s->io->saveUploadedEditedFile('edited_file', $task_file))
{
	echo "Failed to upload file :("; die;
}

// Return to the task view.
$task = new Task($s, $task_id);
header('Location: '.$task->url());
