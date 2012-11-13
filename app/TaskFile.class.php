<?php
require_once ('PDOWrapper.class.php');
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
    
    public static function getTaskFileInfo($task, $version = 0) {
		return TaskFile::getTaskFileInfoById($task->getTaskId(),$version);
    }
    
    public static function getTaskFileInfoById($taskID, $version = 0) {
		$db = new PDOWrapper();
		$db->init();
		$ret = false;
		if ($r = $db->call("getTaskFileMetaData","{$db->cleanse($taskID)},{$db->cleanse($version)}, null, null, null, null")) {
			$file_info = array();
			foreach($r[0] as $key => $value) {
				if (!is_numeric($key)) {
					$file_info[$key] = $value;
				}
			}
			$ret = $file_info;
		}
		return $ret;
    }
    
    public static function getFilename($task, $version) {
		$db = new PDOWrapper();
		$db->init();
		if ($r = $db->call("getTaskFileMetaData","{$db->cleanse($task->getTaskId())},{$db->cleanse($version)}, null, null, null, null")) {
			return $r[0]['filename'];
		}
		else {
			return null;			
		}
	}
        
        
    public static function logFileDownload($task, $version) {
            $db = new PDOWrapper();
            $db->init();
            $db->call("logFileDownload", "{$db->cleanse($task->getTaskId())},{$db->cleanse($version)},null");
    }
    
    public static function getLatestFileVersion($task) {
	return TaskFile::getLatestFileVersionByTaskID($task->getTaskId());
    }

    public static function getLatestFileVersionByTaskID($task_id,$user_id=null) {
            $db = new PDOWrapper();
            $db->init();
            $ret = false;
            if ($r = $db->call("getLatestFileVersion", "{$db->cleanse($task_id)},{$db->cleanseNull($user_id)}")) {
                    if (is_numeric($r[0]['latest_version'])) {
                            $ret =  intval($r[0]['latest_version']);
                    }
            }
            return $ret;
    }
}
