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
	
	function isInit()
	{
		return ( isset($this->id) && (intval($this->id)>0) );
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
		$q = 'SELECT title
				FROM task
				WHERE id = '.$this->s->db->cleanse($this->id);
		if ($r = $this->s->db->Select($q))
		{
			$text = $r[0]['title'];
			$ret = $text;
		}
		return $ret;
	}
	
	/*
	 * Return the unix time stamp of when this task was created.
	 */
	function createdTime()
	{
		$ret = '';
		$q = 'SELECT created_time
				FROM task
				WHERE id = '.$this->s->db->cleanse($this->id);
		if ($r = $this->s->db->Select($q))
		{
			$ret = strtotime($r[0]['created_time']); // Converting to unix time string 
		}
		return $ret;
	}
	
	private function organisationID()
	{
		$ret = '';
		$q = 'SELECT organisation_id
				FROM task
				WHERE id = '.$this->s->db->cleanse($this->id);
		if ($r = $this->s->db->Select($q))
		{
			$text = $r[0]['organisation_id'];
			return $text;
		}
		return $ret;	
	}
	
	/*
		Return the string of the organisation's name who owns this task.
	*/
	function organisation()
	{
		return $this->s->orgs->name($this->organisationID());
	}
	
	function url()
	{
		return '/task/'.$this->id.'/';
	}

	function tagIDs()
	{
		return $this->s->tags->taskTagIDs($this->id);
	}
	
	/*
	 * Return an array of TaskFile objects, or false if none found.
	 */
	function files()
	{
		$ret = false;
		$q = 'SELECT *
				FROM task_file
				WHERE task_id = '.$this->s->db->cleanse($this->id);
		if ($r = $this->s->db->Select($q))
		{
			$task_files = array();
			foreach($r as $row)
			{
				$task_files[] = new TaskFile($this->s, $this->id, $row['file_id']);
			}
			$ret = $task_files;
		}
		return $ret;
	}
}
