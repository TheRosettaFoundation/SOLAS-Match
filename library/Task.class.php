<?php

/*
	One job (such as a document) will be broken into one or more tasks.
	A task simply represents something that needs to get done in the system.
	An example of a task is to translate a segment, or an entire document.
*/
class Task
{
	var $s;
	var $id;
	
	function Task(&$smarty, $task_id)
	{
		$this->s = &$smarty;
		$this->setTaskID($task_id);
	}
	
	function setTaskID($task_id)
	{
		$this->id = intval($task_id);
	}
	
	/*
		Return a short title for the task, suitable for display as a quick identifying summary.
		Retuns an empty string if nothing found.
	*/
	function title()
	{
		$ret = '';
		$q = 'SELECT text
				FROM task
				WHERE id = '.$this->s->db->cleanse($this->id);
		if ($r = $this->s->db->Select($q))
		{
			$text = $r[0]['text'];
			return $text;
		}
		return ret;
	}
}
