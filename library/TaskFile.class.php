<?php

class TaskFile
{
	var $s;
	var $task_id;
	var $file_id;
	
	function TaskFile(&$smarty, $task_id, $file_id)
	{
		$this->s = &$smarty;
		$this->task_id = $task_id;
		$this->file_id = $file_id;
	}
	
	function filename()
	{
		$ret = false;
		$q = 'SELECT filename
				FROM task_file
				WHERE task_id = '.$this->s->db->cleanse($this->task_id).'
				AND file_id = '.$this->s->db->cleanse($this->file_id);
		if ($r = $this->s->db->Select($q))
		{
			$ret = $r[0]['filename'];
		}
		return $ret;
	}
	
	/*
	 * URL to download the file.
	 */
	function url()
	{
				
	}
}
