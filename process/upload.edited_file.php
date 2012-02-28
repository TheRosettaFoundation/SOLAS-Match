<?php
require('../app/includes/smarty.php');

/*
 * Process submitted form data to create a new task. 
 * Simple mockup functionality. Therefore, not much error checking happening. 
*/

$task_dao = new TaskDao;
$task = $task_dao->find(array('task_id' => $s->io->post('task_id')));

if (!Upload::saveSubmittedFile('edited_file', $task)) {
	echo "Failed to upload file :("; die;
}

header('Location: ' . $task->url());
