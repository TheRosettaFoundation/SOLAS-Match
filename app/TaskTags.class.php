<?php

class TaskTags {
	public static function getTags($task) {
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT tag_id
				FROM task_tag
				WHERE task_id = ' . $db->cleanse($task->getTaskId());
		$ret = false;
		if ($r = $db->Select($q))
		{
			$ret = array();
			$tags_dao = new TagsDao();
			foreach($r as $row)	{
				if ($tag = $tags_dao->find(array('tag_id' => $row['tag_id']))) {
					$ret[] = $tag;
				}
			}
		}
		return $ret;
	}

	public static function dropTasksTags($task) {
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