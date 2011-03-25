<?php
/* Process submitted form data to create a new task. 
	Simple mockup functionality. Therefore, not much error checking happening. 
*/
require($_SERVER['DOCUMENT_ROOT'].'/../includes/smarty.php');

$title = $s->io->post('title');
$file = $_FILES['original_file']; // lots of error checking to do here
$word_count = $s->io->post('word_count');
$tags = $s->io->post('tags');
$organisation_id = $s->io->post('organisation_id');

// Put the task in the database.
$task_id = $s->tasks->create($title, $organisation_id, $tags, $word_count);
$task = new Task($s, $task_id);

// Save the file
if (!$s->io->saveUploadedFile('original_file', $organisation_id, $task_id))
{
	echo "Failed to upload file :("; die;
}

// Forward the person to the task page.
Header('Location: /');
die;
