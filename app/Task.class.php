<?php

/*
	One job (such as a document) will be broken into one or more tasks.
	A task simply represents something that needs to get done in the system.
	An example of a task is to translate a segment, or an entire document.
*/
class Task
{
	var $id;
	
	function Task($task_id)
	{
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
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT title
				FROM task
				WHERE id = '.$db->cleanse($this->id);
		if ($r = $db->Select($q))
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
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT created_time
				FROM task
				WHERE id = '.$db->cleanse($this->id);
		if ($r = $db->Select($q))
		{
			$ret = strtotime($r[0]['created_time']); // Converting to unix time string 
		}
		return $ret;
	}

	function createdTimeAgo() {
		return IO::timeSince($this->createdTime());
	}
	
	function wordcount()
	{
		$ret = false;
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT word_count
				FROM task
				WHERE id = '.$db->cleanse($this->id).'
				AND word_count IS NOT NULL';
		if ($r = $db->Select($q))
		{
			$ret = $r[0]['word_count'];
		}
		return $ret;
	}
	
	function target_id()
	{
		$ret = false;
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT target_id
				FROM task
				WHERE id = '.$db->cleanse($this->id).'
				AND target_id IS NOT NULL';
		if ($r = $db->Select($q))
		{
			$ret = $r[0][0];
		}
		return $ret;
	}
	
	function source_id()
	{
		$ret = false;
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT source_id
				FROM task
				WHERE id = '.$db->cleanse($this->id).'
				AND source_id IS NOT NULL';
		if ($r = $db->Select($q))
		{
			$ret = $r[0][0];
		}
		return $ret;
	}
	
	/*
	 * Return the natural language name of the target language.
	 */
	function target()
	{
		$ret = false;
		if ($target_id = $this->target_id())
		{
			$tags = new Tags();
			$ret = $tags->langName($target_id);
		}
		return $ret;
	}
	
	function source()
	{
		$ret = false;
		if ($source_id = $this->source_id())
		{
			$tags = new Tags();
			$ret = $tags->langName($source_id);
		}
		return $ret;
	}
	
	function organisationID()
	{
		$ret = false;
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT organisation_id
				FROM task
				WHERE id ='.$db->cleanse($this->id);
		if ($r = $db->Select($q))
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
		$orgs = new Organisations();
		return $orgs->name($this->organisationID());
	}
	
	function url()
	{
		$url = new URL();
		return $url->server().'/task/'.$this->id.'/';
	}

	function tagIDs()
	{
		$tags = new Tags();
		return $tags->taskTagIDs($this->id);
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
		$task_file['path'] = '\''.$db->cleanse($path).'\'';
		$task_file['filename'] = '\''.$db->cleanse($filename).'\'';
		$task_file['content_type'] = '\''.$db->cleanse($content_type).'\'';
		$task_file['user_id'] = 'NULL';
		$task_file['upload_time'] = 'NOW()';
		if ($file_id = $db->Insert('task_file', $task_file))
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
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT *
				FROM task_file
				WHERE task_id = '.$db->cleanse($this->id);
		if ($r = $db->Select($q))
		{
			$task_files = array();
			foreach($r as $row)
			{
				$task_files[] = new TaskFile($this->id, $row['file_id']);
			}
			$ret = $task_files;
		}
		return $ret;
	}
}
