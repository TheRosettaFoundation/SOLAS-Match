<?php
require_once 'Task.class.php';
require_once 'TaskTags.class.php';


/**
 * Task Document Access Object for manipulating tasks.
 *
 * @package default
 * @author eoin.oconchuir@ul.ie
 **/
class TaskDao
{
	/**
	 * Get a Task object, save to databse.
	 *
	 * @return Task object
	 * @author
	 **/
	public function create($params)
	{
		$task = new Task($params);
		$this->save($task);
		return $task;
	}

	public function find($params) {
		$permitted_params = array(
			'task_id'
		);

		if (!is_array($params)) {
			throw new InvalidArgumentException('Can\'t find a task if an array isn\'t provided.');
		}

		$where = array();
		foreach($params as $key => $value) {
			if (!in_array($key, $permitted_params)) {
				throw new InvalidArgumentException('Cannot search for a task with the provided paramter ' . $key . '.');
			}
		}

		$db = new MySQLWrapper();
		$db->init();
		$query = 'SELECT *
					FROM task
					WHERE id = ' . $db->cleanse($params['task_id']);
		
		$ret = null;
		if ($res = $db->Select($query)) {
			$row = $res[0];
			$task_data = array();
			foreach($row as $col_name => $col_value) {
				if ($col_name == 'id') {
					$task_data['task_id'] = $col_value;
				}
				else if (!is_numeric($col_name) && !is_null($col_value)) {
					$task_data[$col_name] = $col_value;
				}
			}

			$ret = new Task($task_data);
		}
		return $ret;
	}

	/**
	 * Save task object to database (either insert of update)
	 *
	 * @return void
	 * @author 
	 **/
	public function save($task)
	{
		if (is_null($task->getTaskId())) {
			$this->_insert($task);
		}
		else {
			$this->_update($task);
		}
	}

	/**
	 * Insert task object into database
	 *
	 * @return void
	 * @author 
	 **/
	private function _insert($task)
	{
		$db = new MySQLHandler();
		$db->init();
		$insert = array();		
		if ($title = $task->getTitle()) {
			$insert['title'] = $db->cleanseWrapStr($title);
		}
		if ($organisation_id = $task->getOrganisationId()) {
			$insert['organisation_id'] = $db->cleanse($organisation_id);
		}
		if ($source_id = $task->getSourceId()) {
			$insert['source_id'] = $db->cleanse($source_id);
		}
		if ($target_id = $task->getTargetId()) {
			$insert['target_id'] = $db->cleanse($target_id);
		}
		if ($word_count = $task->getWordCount()) {
			$insert['word_count'] = $db->cleanse($word_count);
		}
		$insert['created_time'] = 'NOW()';
		$db->insert('task', $insert);
	}

	public function getLatestTasks($nb_items = 10) {
		$db = new MySQLWrapper();
		$db->init();
		$q 	= 'SELECT id
				FROM task
				ORDER BY created_time DESC 
				LIMIT '.$db->cleanse($nb_items);
		
		$ret = false;
		if ($r = $db->Select($q)) {
			$ret = array();
			foreach($r as $row)	{
				// Add a new Job object to the array to be returned.
				$task = self::find(array('task_id' => $row['id']));
				if (!$task->getTaskId()) {
					throw new Exception('Tried to create a task, but its ID is not set.');
				}
				$ret[] = $task;
			}
		}
		return $ret;
	}
	
	/*
	 * Return an array of tasks that are tagged with a certain tag.
	 */
	public function getTaggedTasks($tag_id, $nb_items = 10)	{
		$db = new MySQLWrapper();
		$db->init();
		$ret = false;
		$q = 'SELECT id
				FROM task
				WHERE id IN (
					SELECT task_id
					FROM task_tag
					WHERE tag_id = ' . intval($tag_id) . '
				) 
				ORDER BY created_time DESC 
				LIMIT '.$db->cleanse($nb_items);
		if ($r = $db->Select($q)) {
			$ret = array();
			foreach($r as $row)	{
				$ret[] = self::find(array('task_id' => $row['id']));
			}
		}
		return $ret;
	}
} // END TaskDao class 