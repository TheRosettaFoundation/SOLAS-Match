<?php
/* Process submitted form data to create a new task. 
	Simple mockup functionality. Therefore, not much error checking happening. 
*/
require($_SERVER['DOCUMENT_ROOT'].'/../includes/smarty.php');

$title = $s->io->post('title');
$file = $_FILES['file']; // lots of error checking to do here
$tags = $s->io->post('tags');
$organisation_id = $s->io->post('organisation_id');

// Put the task in the database.
$task_id = $s->tasks->create($title, $organisation_id, $tags);
$task = new Task($s, $task_id);

// Save the file
$s->io->saveUploadedFile('original_file');

// Forward the person to the task page.
Header('Location: /');
die;
