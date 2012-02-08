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
}