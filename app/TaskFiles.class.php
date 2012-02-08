<?php

class TaskFiles {
	public static function getTaskFiles($task) {
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT *
				FROM task_file
				WHERE task_id = '.$db->cleanse($task->getTaskId());
		
		$ret = false;
		if ($r = $db->Select($q)) {
			$task_files = array();
			foreach($r as $row)	{
				$task_files[] = new TaskFile($task->getTaskId(), $row['file_id']);
			}
			$ret = $task_files;
		}
		return $ret;
	}
}