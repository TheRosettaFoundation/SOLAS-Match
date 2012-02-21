<?php

class TaskFile {

	function taskID() {
		return intval($this->task_id);		
	}
	
	function fileID() {
		return intval($this->file_id);		
	}
	
	function organisationID() {
		$task = new Task($this->task_id);
		return $task->organisationID();
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
		
	/*
	 * URL to download the file.
	 */
	function url() {
		 // Not secure and should be improved.
		return '/task/id/' . $this->task_id . '/download_file/' . $this->file_id . '/';
	}
	
	function urlVersion($version) {
		return $this->url() . 'v/' . intval($version) . '/';
	}	
}
