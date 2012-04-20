<?php
require_once 'Task.class.php';
//require_once 'TaskTags.class.php';

/**
 * Task Document Access Object for manipulating tasks.
 *
 * @package default
 * @author eoin.oconchuir@ul.ie
 **/
class TaskDao {
	/**
	 * Get a Task object, save to databse.
	 *
	 * @return Task object
	 * @author
	 **/
	public function create($params) {
		$task = new Task($params);
		$this->save($task);
		return $task;
	}

/*
New requirement:

    $my_tasks           = $task_dao->find(array(
        'user_id'           => $current_user->getUserId(),
        'organisation_ids'  => $my_organisations
    ));
*/

	public function findTasks($params, $sort_column = NULL, $sort_direction = NULL) {
		$permitted_params = array(
			'organisation_ids'
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

		$tasks = null;

		$organisation_ids = implode(',', $params['organisation_ids']);
		$db = new MySQLWrapper();
		$db->init();
		$query = 'SELECT id
					FROM task
					WHERE organisation_id IN (' . $db->cleanse($organisation_ids) . ')';
		if (!empty($sort_column)) {
			$query .= ' ORDER BY ' . $db->cleanse($sort_column);
			if (!empty($sort_direction)) {
				$query .= ' ' . $db->cleanse($sort_direction);
			}
		}
		if ($result = $db->Select($query)) {
			$tasks = array();
			foreach ($result as $row) {
				$task = $this->find(array('task_id' => $row['id']));
				if (is_object($task)) {
					$tasks[] = $task;
				}
			}
		}

		return $tasks;
	}

	public function find($params) {
		$permitted_params = array(
			'task_id',
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

			if ($tags = $this->_fetchTags($params['task_id'])) {
				$task_data['tags'] = $tags;
			}

			$ret = new Task($task_data);
		}
		return $ret;
	}

	private function _fetchTags($tag_id) {
		$db = new MySQLWrapper();
		$db->init();
		$query = 'SELECT t.label
					FROM task_tag AS tt, tag AS t
					WHERE tt.task_id = ' . $db->cleanse($tag_id) . '
						AND tt.tag_id = t.tag_id';
		$ret = null;
		if ($result = $db->Select($query)) {
			$ret = array();
			foreach ($result as $row) {
				$ret[] = $row['label'];
			}
		}

		if (is_array($ret) && count($ret) > 0) {
			return $ret;
		}
		else {
			return null;
		}
	}

	/**
	 * Save task object to database (either insert of update)
	 *
	 * @return void
	 * @author 
	 **/
	public function save(&$task)
	{
		if (is_null($task->getTaskId())) {
			$this->_insert($task);
		}
		else {
			$this->_update($task);
		}
	}

	private function _update($task) {
		$task_dao = new TaskDao;
		$existing_task = $task_dao->find(array('task_id' => $task->getTaskId()));

		if (!is_object($existing_task)) {
			throw new InvalidArgumentException('Cannot update task, as the provided task was not found in the database.');
		}

		$to_update = array();
		$db = new MySQLWrapper();
		$db->init();
		if ($task->getSourceId() != $existing_task->getSourceId()) {
			$to_update['source_id'] = $db->cleanse($task->getSourceId());
		}
		if ($task->getTargetId() != $existing_task->getTargetId()) {
			$to_update['target_id'] = $db->cleanse($task->getTargetId());
		}
		if ($task->getTitle() != $existing_task->getTitle()) {
			$to_update['title'] = $db->cleanseWrapStr($task->getTitle());
		}
		if ($task->getWordCount() != $existing_task->getWordCount()) {
			$to_update['word_count'] = $db->cleanse($task->getWordCount());
		}

		if (count($to_update) > 0) {
			$set = array();
			foreach ($to_update as $key => $value) {
				$set[] = $key . ' = ' . $value;
			}
			$q = 'UPDATE task
					SET ' . implode(', ', $set) . '
					WHERE id = ' . $db->cleanse($task->getTaskId()) . '
					LIMIT 1';

			$db->Update($q);
		}

		$this->_updateTags($task);
	}

	private function _updateTags($task) {
		$this->_unlinkStoredTags($task);
		$this->_storeTagLinks($task);
	}

	private function _unlinkStoredTags($task) {
		$db = new MySQLWrapper();
		$db->init();
		$query = 'DELETE FROM task_tag
					WHERE task_id = ' . $db->cleanse($task->getTaskId());
		$db->Delete($query);
	}

	private function _storeTagLinks($task) {
		if ($tags = $task->getTags()) {
			if ($tag_ids = $this->_tagsToIds($tags)) {
				$db = new MySQLWrapper;
				$db->init();
				foreach ($tag_ids as $tag_id) {
					$ins = array();
					$ins['task_id'] = $db->cleanse($task->getTaskId());
					$ins['tag_id'] = $db->cleanse($tag_id);
					$db->Insert('task_tag', $ins);
				}
			}
		}
	}

	private function _tagsToIds($tags) {
		$tag_ids = array();
		foreach ($tags as $tag) {
			if ($tag_id = $this->getTagId($tag)) {
				$tag_ids[] = $tag_id;
			}
			else {
				$tag_ids[] = $this->_createTag($tag);
			}
		}

		if (count($tag_ids) > 0) {
			return $tag_ids;
		}
		else {
			return null;
		}
	}

	public function getTagId($tag) {
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT tag_id
				FROM tag
				WHERE label = ' . $db->cleanseWrapStr($tag);

		if ($r = $db->Select($q)) {
			return $r[0]['tag_id'];
		}
		else {
			return null;
		}		
	}

	private function _createTag($tag) {
		$db = new MySQLWrapper;
		$db->init();
		$ins = array();
		$ins['label'] = $db->cleanseWrapStr($tag);
		return $db->Insert('tag', $ins);
	}

	private function _insert(&$task) {
		$db = new MySQLWrapper();
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
		if ($task_id = $db->insert('task', $insert)) {
			$task->setTaskId($task_id);
		}
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
	public function getTaggedTasks($tag, $nb_items = 10) {
		$task_dao = new TaskDao;
		$tag_id = $task_dao->getTagId($tag);

		if (is_null($tag_id)) {
			throw new InvalidArgumentException('Cannot get tasks tagged with ' . $tag . ' because no such tag is in the system.');
		}

		$db = new MySQLWrapper();
		$db->init();
		$ret = false;
		$q = 'SELECT id
				FROM task
				WHERE id IN (
					SELECT task_id
					FROM task_tag
					WHERE tag_id = ' . $db->cleanse($tag_id) . '
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

	function getTopTags($limit = 30) {
		$ret = false;
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT t.label AS label, COUNT( tt.tag_id ) AS frequency
				FROM task_tag AS tt, tag AS t
				WHERE tt.tag_id = t.tag_id
				GROUP BY tt.tag_id
				ORDER BY frequency DESC
				LIMIT '.intval($limit);
		if ($r = $db->Select($q)) {
			$ret = array();
			foreach ($r as $row) {
				$ret[] = $row['label'];
			}
		}
		return $ret;
	}

	public function recordFileUpload($task, $path, $filename, $content_type) {
		$next_version = $this->nextFileVersionNumber($task);
		$db = new MySQLWrapper();
		$db->init();

		$task_file_version = array();
		$task_file_version['task_id'] 		= $db->cleanse($task->getTaskId());
		$task_file_version['version_id'] 	= $db->cleanse($next_version);
		$task_file_version['filename'] 		= $db->cleanseWrapStr($filename);
		$task_file_version['content_type'] 	= $db->cleanseWrapStr($content_type);
		$task_file_version['user_id'] 		= 'NULL'; // TODO record user
		$task_file_version['upload_time'] 	= 'NOW()';
		$ret = $db->Insert('task_file_version', $task_file_version);
		return $ret;
	}

	/*
	 * Return an integer value. Give the next version number when creating a file.
	 * In other words, if there are versions 1-5 stored now, return 6, as that's
	 * the next available value.
	 */
	public function nextFileVersionNumber($task) {
		/* I realise this code is dangerous and may cause problems futher down the line.
		 * The code returns the next available version. However, if a second person
		 * was also editing the file in parallel, it's possible that their 
		 * version numbers will get mixed up, or that they get the same version number.
		 * If that conflict happens, we'll simply reject the commit, or do something
		 * more user friendly than that.
		 */
		$latest_version = self::getLatestFileVersion($task);
		if ($latest_version !== false) {
			return $latest_version + 1;
		}
		else {
			return 0;
		}
	}

	public function getLatestFileVersion($task) {
		$db = new MySQLWrapper();
		$db->init();

		$q = 'SELECT max(version_id) as latest_version
		 		FROM task_file_version
		 		WHERE task_id = ' . $db->cleanse($task->getTaskId());
		$ret = false;
		if ($r = $db->Select($q)) {
			if (is_numeric($r[0]['latest_version'])) {
				$ret =  intval($r[0]['latest_version']);
			}
		}
		return $ret;
	}

	public function logFileDownload($task, $version) {
		$db = new MySQLWrapper();
		$db->init();
		$down = array();
		$down['task_id'] = $db->cleanse($task->getTaskId());
		$down['version_id'] = $db->cleanse($version);
		$down['user_id'] = 'NULL'; // TODO record user
		$down['time_downloaded'] = 'NOW()';
		return $db->Insert('task_file_version_download', $down);
	}

	private function getFilename($task, $version) {
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT filename
				FROM task_file_version
				WHERE task_id = ' . $db->cleanse($task->getTaskId())
				 . ' AND version_id =' . $db->cleanse($version)
				 . ' LIMIT 1';
		if ($r = $db->Select($q)) {
			return $r[0]['filename'];
		}
		else {
			return null;			
		}
	}

	public function getTaskFileInfo($task, $version = 0) {
		$ret = null;
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT *
				FROM task_file_version
				WHERE task_id = ' . $db->cleanse($task->getTaskId()) . '
				AND version_id = ' . $db->cleanse($version) . '
				LIMIT 1';
		$ret = false;
		if ($r = $db->Select($q)) {
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
}