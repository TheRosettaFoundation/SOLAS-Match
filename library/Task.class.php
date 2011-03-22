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
	
	function organisationID()
	{
		$ret = false;
		$q = 'SELECT organisation_id
				FROM task
				WHERE id ='.$this->s->db->cleanse($this->id);
		if ($r = $this->s->db->Select($q))
		{
			$ret = $r[0]['organisation_id'];
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
		return $this->s->url->server().'/task/'.$this->id.'/';
	}

	function tagIDs()
	{
		return $this->s->tags->taskTagIDs($this->id);
	}

	function taskID()
	{
		return $this->id;		
	}

	public function recordUploadedFile($path, $filename, $content_type)
	{
		$ret = false;
		$task_file = array();
		$task_file['task_id'] = intval($this->taskID());
		$task_file['path'] = '\''.$this->s->db->cleanse($path).'\'';
		$task_file['filename'] = '\''.$this->s->db->cleanse($filename).'\'';
		$task_file['content_type'] = '\''.$this->s->db->cleanse($content_type).'\'';
		$task_file['user_id'] = 'NULL';
		$task_file['upload_time'] = 'NOW()';
		if ($file_id = $this->s->db->Insert('task_file', $task_file))
		{
			$task_file = new TaskFile($this->s, $this->taskID(), $file_id);
			$ret = $task_file->recordNewlyUploadedVersion($task_file->nextVersion(), $filename, $content_type);
		}
		return $ret;
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
