<?php

class TaskFile {

	function taskID() {
		return intval($this->task_id);		
	}
	
	function timesDownloaded() {
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT count(*) times_downloaded
				FROM task_file_version_download
				WHERE task_id = ' . $db->cleanse($this->taskID());
		if ($r = $db->Select($q)) {
			$ret = $r[0]['times_downloaded'];
		}
		else {
			return null;
		}
	}
		
	/*
	 * URL to download the file.
	 */
	function url() {
		 // Not secure and should be improved.
		return '/task/id/' . $this->task_id . '/download-file/';
	}
	
	function urlVersion($version) {
		return $this->url() . 'v/' . intval($version) . '/';
	}	
}
