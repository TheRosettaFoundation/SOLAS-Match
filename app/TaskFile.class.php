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
        /*
     * A private file for check if a task has been translated by checking 
     * if a file has been uploaded for it. if user_id is null it will just 
     * check on a task basis. The inclusion of the user_id allows several 
     * people to work on the job at once
     * Returns true if the file has been translated
     */
    public static function _check_task_file_version($task_id, $user_id = null)
    {
        $db = new PDOWrapper();
        $db->init();
        $result = $db->call("getLatestFileVersion","{$db->cleanse($task_id)},{$db->cleanseNull($user_id)}");
        return $result[0]['latest_version']>0;
    }
    
    public static function recordFileUpload($task, $filename, $content_type, $user_id) {
                $db = new PDOWrapper();
		$db->init();
                $args = "";
                $args .= "{$db->cleanse($task->getTaskId())}";
                $args .= ",{$db->cleanseWrapStr($filename)}";
                $args .= ",{$db->cleanseWrapStr($content_type)}";
                $args .= ",{$db->cleanseNull($user_id)}";
                return $db->call("recordFileUpload", $args);
    }
}
