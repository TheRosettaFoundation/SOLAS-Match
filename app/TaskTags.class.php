<?php
require_once ('PDOWrapper.class.php');
class TaskTags {
	public static function getTags($task_id) {
                $db = new PDOWrapper();
		$db->init();
		$ret = null;
		if ($result = $db->call("getTaskTags", "{$db->cleanseNull($task_id)}")) {
			$ret = array();
			foreach ($result as $row) {
				$ret[] = $row['label'];
			}
		}
		return $ret;
	}

	public static function deleteTaskTags($task) {
            $db = new PDOWrapper();
            $db->init();
            $db->call("unlinkStoredTags", "{$db->cleanse($task->getTaskId())}");
	}

	public static function setTaskTags($task, $tag_ids) {
		foreach ($tag_ids as $tag_id) {
			self::setTaskTag($task->getTaskId(), $tag_id);
		}
	}

	public static function setTaskTag($task_id, $tag_id) {
		$db = new PDOWrapper();
		$db->init();
		$db->call("storeTagLinks", "{$db->cleanse($task_id)},{$db->cleanse($tag_id)}");
                            
	}
}