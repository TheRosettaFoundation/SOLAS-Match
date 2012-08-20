<?php

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

	public static function deleteTasksTags($task) {
		if (!$task->hasTaskId()) {
			throw new InvalidArgumentException('Cannot drop tags for a task, as task ID is not set.');
		}

		$db = new MySQLWrapper();
		$db->init();
		$q = 'DELETE
				FROM task_tag
				WHERE task_id = ' . $db->cleanse($task->getTaskId());
		$db->Delete($q);
	}

	public static function setTaskTags($task, $tags) {
		$db = new MySQLWrapper();
		$db->init();
		foreach ($tags as $tag) {
			self::setTaskTag($task->getTaskId(), $tag->getTagId());
		}
	}

	public static function setTaskTag($task_id, $tag_id) {
		$insert = array();
		$db = new MySQLWrapper();
		$db->init();
		$insert['task_id'] = $db->cleanse($task_id);
		$insert['tag_id'] = $db->cleanse($tag_id);
		$db->Insert('task_tag', $insert);
	}
}