<?php
require_once 'Task.class.php';


/**
 * Task Document Access Object for manipulating tasks.
 *
 * @package default
 * @author eoin.oconchuir@ul.ie
 **/
class TaskDao
{
	function __construct() {
		
	}

	/**
	 * Get a Task object, save to databse.
	 *
	 * @return Task object
	 * @author
	 **/
	public function create ($params)
	{
		$task = new Task($params);
		$this->save($task);
		return $task;
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

// TODO
//'	tags'				=> 'setTags',
			
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
		if (count($insert) > 0) {
			$db->insert('task', $insert);
		}


	}
} // END TaskDao class 