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
	
	function taskID()
	{
		return intval($this->task_id);		
	}
	
	function fileID()
	{
		return intval($this->file_id);		
	}
	
	function organisationID()
	{
		$task = new Task($this->s, $this->task_id);
		return $task->organisationID();
	}
	
	function absoluteFilePath()
	{
		return TaskFile::absolutePath($this->s, $this->organisationId(), $this->task_id).DIRECTORY_SEPARATOR.$this->filename();
	}
	
	static function absolutePath(&$s, $org_id, $task_id, $version = 0)
	{
		// Not necessarily an existing path, but what it should be for this file.
		return $s->setting('files.upload_path').'org-'.intval($org_id).DIRECTORY_SEPARATOR.'task-'.intval($task_id).DIRECTORY_SEPARATOR.'v-'.intval($version);
	}
	
	/*
	 * Return an integer value. Give the next version number when creating a file.
	 * In other words, if there are versions 1-5 stored now, return 6, as that's
	 * the next available value.
	 */
	function nextVersion()
	{
		/* I realise this code is dangerous and may cause problems futher down the line.
		 * The code returns the next available version. However, if a second person
		 * was also editing the file in parallel, it's possible that their 
		 * version numbers will get mixed up, or that they get the same version number.
		 * If that conflict happens, we'll simply reject the commit, or do something
		 * more user friendly than that.
		 */
		$ret = 0; // Default (first) version is 0.
		$q = 'SELECT max(version_id) as next_version
		 		FROM task_file_version
		 		WHERE task_id ='.$this->s->db->cleanse($this->taskID()).'
		 		AND file_id ='.$this->s->db->cleanse($this->fileID());
		if ($r = $this->s->db->Select($q))
		{
			if ($r[0]['next_version'] != null)
			{
				$ret = intval($r[0]['next_version'])+1;
			}
		}
		return $ret;
	}
	
	function timesDownloaded()
	{
		$ret = 0;
		$q = 'SELECT count(*) times_downloaded
				FROM task_file_version_download
				WHERE task_id='.$this->s->db->cleanse($this->taskID()).'
				AND file_id='.$this->s->db->cleanse($this->fileID());
		if ($r = $this->s->db->Select($q))
		{
			$ret = $r[0]['times_downloaded'];
		}
		return $ret;
	}
	
	function recordDownload($version)
	{
		$down = array();
		$down['task_id'] = $this->s->db->cleanse($this->taskID());
		$down['file_id'] = $this->s->db->cleanse($this->fileID());
		$down['version_id'] = $this->s->db->cleanse($version);
		$down['user_id'] = 'NULL';
		$down['time_downloaded'] = 'NOW()';
		return $this->s->db->Insert('task_file_version_download', $down);
	}

	public function recordNewlyUploadedVersion($version, $filename, $content_type)
	{
		// Save file version
		$task_file_version = array();
		$task_file_version['task_id'] = intval($this->taskID());
		$task_file_version['file_id'] = intval($this->fileID());
		$task_file_version['version_id'] = intval($version);
		$task_file_version['filename'] = '\''.$this->s->db->cleanse($filename).'\'';
		$task_file_version['content_type'] = '\''.$this->s->db->cleanse($content_type).'\'';
		$task_file_version['user_id'] = 'NULL';
		$task_file_version['upload_time'] = 'NOW()';
		$ret = $this->s->db->Insert('task_file_version', $task_file_version);
		return $ret;
	}
	
	/*
	 * Check in the database the stored content type of this file.
	 * Return false if not found.
	 */
	function contentType()
	{
		$ret = false;
		$q = 'SELECT content_type
				FROM task_file
				WHERE task_id = '.$this->s->db->cleanse($this->task_id).'
				AND file_id = '.$this->s->db->cleanse($this->file_id);
		if ($r = $this->s->db->Select($q))
		{
			$ret = $r[0]['content_type'];			
		}
		return $ret;
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
		return '/process/download.task_file.php?task_id='.$this->task_id.'&file_id='.$this->file_id; // Not secure and should be improved.
	}
}
