<?php
/* Process submitted form data to create a new task. 
	Simple mockup functionality. Therefore, not much error checking happening. 
*/
require($_SERVER['DOCUMENT_ROOT'].'/../includes/smarty.php');

$content = $s->io->post('content');
$tags = $s->io->post('tags');
$organisation_id = $s->io->post('organisation_id');

// Put the task in the database.
$task_id = $s->tasks->create($content, $organisation_id, $tags);
$task = new Task($s, $task_id);

// Forward the person to the task page.
Header('Location: '.$task->url());
die;
