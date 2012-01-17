<?php

class TaskFile
{
	var $task_id;
	var $file_id;
	
	function TaskFile($task_id, $file_id)
	{
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
		$task = new Task($this->task_id);
		return $task->organisationID();
	}
	
	function absoluteFilePath($version = 0)
	{
		return TaskFile::absolutePath($this->organisationId(), $this->task_id, $version).DIRECTORY_SEPARATOR.$this->filename($version);
	}
	
	static function absolutePath($org_id, $task_id, $version = 0)
	{
		// Not necessarily an existing path, but what it should be for this file.
		$settings = new Settings();
		return $settings->setting('files.upload_path').'org-'.intval($org_id).DIRECTORY_SEPARATOR.'task-'.intval($task_id).DIRECTORY_SEPARATOR.'v-'.intval($version);
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
		$latest_version = $this->latestVersion();
		if ($latest_version !== false)
		{
			$ret = $latest_version + 1;
		}
		return $ret;
	}
	
	function latestVersion()
	{
		$ret = false;
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT max(version_id) as latest_version
		 		FROM task_file_version
		 		WHERE task_id ='.$db->cleanse($this->taskID()).'
		 		AND file_id ='.$db->cleanse($this->fileID());
		if ($r = $db->Select($q))
		{
			if ($r[0]['latest_version'] != null)
			{
				$ret = intval($r[0]['latest_version']);
			}
		}
		return $ret;
	}
		
	function timesDownloaded()
	{
		$ret = 0;
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT count(*) times_downloaded
				FROM task_file_version_download
				WHERE task_id='.$db->cleanse($this->taskID()).'
				AND file_id='.$db->cleanse($this->fileID());
		if ($r = $db->Select($q))
		{
			$ret = $r[0]['times_downloaded'];
		}
		return $ret;
	}
	
	function recordDownload($version)
	{
		$down = array();
		$db = new MySQLWrapper();
		$db->init();
		$down['task_id'] = $db->cleanse($this->taskID());
		$down['file_id'] = $db->cleanse($this->fileID());
		$down['version_id'] = $db->cleanse($version);
		$down['user_id'] = 'NULL';
		$down['time_downloaded'] = 'NOW()';
		return $db->Insert('task_file_version_download', $down);
	}

	public function recordNewlyUploadedVersion($version, $filename, $content_type)
	{
		// Save file version
		$db = new MySQLWrapper();
		$db->init();
		$task_file_version = array();
		$task_file_version['task_id'] = intval($this->taskID());
		$task_file_version['file_id'] = intval($this->fileID());
		$task_file_version['version_id'] = intval($version);
		$task_file_version['filename'] = '\''.$db->cleanse($filename).'\'';
		$task_file_version['content_type'] = '\''.$db->cleanse($content_type).'\'';
		$task_file_version['user_id'] = 'NULL';
		$task_file_version['upload_time'] = 'NOW()';
		$ret = $db->Insert('task_file_version', $task_file_version);
		return $ret;
	}
	
	/*
	 * Check in the database the stored content type of this file.
	 * Return false if not found.
	 */
	function contentType($version)
	{
		$ret = false;
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT content_type
				FROM task_file_version
				WHERE task_id = '.$db->cleanse($this->task_id).'
				AND file_id = '.$db->cleanse($this->file_id).'
				AND version_id ='.$db->cleanse($version);
		if ($r = $db->Select($q))
		{
			$ret = $r[0]['content_type'];			
		}
		return $ret;
	}
	
	function filename($version = 0)
	{
		$ret = false;
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT filename
				FROM task_file_version
				WHERE task_id = '.$db->cleanse($this->task_id).'
				AND file_id = '.$db->cleanse($this->file_id).'
				AND version_id ='.$db->cleanse($version);
		if ($r = $db->Select($q))
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
	
	function urlVersion($version)
	{
		return '/process/download.task_file.php?task_id='.$this->task_id.'&file_id='.$this->file_id.'&version_id='.intval($version); // Not secure and should be improved.
	}	
}
