<?php

class TaskFile {

	function taskID() {
		return intval($this->task_id);		
	}
	
	function timesDownloaded() {
		$db = new PDOWrapper();
		$db->init();
		if ($r = $db->call("taskDownloadCount", "{$db->cleanse($this->taskID())}")) {
                    $ret = $r[0]['times_downloaded'];
                    return $ret;
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
