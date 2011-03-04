<?php
require($_SERVER['DOCUMENT_ROOT'].'/../includes/smarty.php');

if ($tasks = $s->stream->getStream(10))
{
	$s->assign('tasks', $tasks);
}
if ($top_tags = $s->tags->topTags(30))
{
	$s->assign('top_tags', $top_tags); // includes tag_id and frequency.
}
$s->display('index.tpl');
