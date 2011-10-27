<?php
require('../app/includes/smarty.php');
/*
 * Input: tag_id
 * Page responsible for showing a stream of tasks related to this tag.
 */
$tag_id = $s->io->get('tag_id');
if ($tasks = $s->stream->getTaggedStream($tag_id, 10))
{
	$s->assign('tasks', $tasks);
}
/*
// Should do check if tag exists.
if (!...)
{
	// Make sure that we've been passed a correct task.
	header('HTTP/1.0 404 Not Found');
}
*/

$s->assign('tag_id', $tag_id);
$s->display('tag.tpl');
