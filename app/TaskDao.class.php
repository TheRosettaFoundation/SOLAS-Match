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

	public function findTasksByOrg($params, $sort_column = NULL, $sort_direction = NULL) {
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

            $organisation_ids = $params['organisation_ids'];

                    // We're assuming that organisation_ids is always being provided.
            if(count($organisation_ids) > 1) {
                    $organisation_ids = implode(',', $organisation_ids);
            }
            $db = new PDOWrapper();
            $db->init();
            $args = $db->cleanse($organisation_ids);
            $args .= empty($sort_column)?",null":"{$db->cleanse($sort_column)}";
            $args .= (!empty($sort_column)&&empty($sort_direction))?" ":" {$db->cleanse($sort_direction)}";
            if ($result = $db->call("getTasksByOrgIDs", $args)) {
                    $tasks = array();
                    foreach ($result as $row) {
                        $task_data = array();
                        foreach($row as $col_name => $col_value) {
                            if ($col_name == 'id') {
                                    $task_data['task_id'] = $col_value;
                                }
                            else if (!is_numeric($col_name) && !is_null($col_value)) {
                                    $task_data[$col_name] = $col_value;
                            }
                        }

                        if ($tags = $this->_fetchTags($row['id'])) {
                            $task_data['tags'] = $tags;
                        }

                        $task = new Task($task_data);
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
                
                $result = self::getTask($params);
                return $result[0];
	}
        
        public function getTask($params){
            $db=new PDOWrapper();
            $db->init();
            $args ="";
            $args .= isset ($params['task_id'])?"{$db->cleanseNull($params['task_id'])}":"null";
            $args .= isset ($params['org_id'])?"{$db->cleanseNull($params['org_id'])}":",null";
            $args .= isset ($params['title'])?"{$db->cleanseNullOrWrapStr($params['title'])}":",null";
            $args .= isset ($params['word_count'])?"{$db->cleanseNull($params['word_count'])}":",null";
            $args .= isset ($params['source_id'])?"{$db->cleanseNull($params['source_id'])}":",null";
            $args .= isset ($params['target_id'])?"{$db->cleanseNull($params['target_id'])}":",null";
            $args .= isset ($params['created_time'])?"{$db->cleanseNull($params['created_time'])}":",null";
            $args .= isset ($params['impact'])?"{$db->cleanseNullOrWrapStr($params['impact'])}":",null";
            $args .= isset ($params['reference_page'])?"{$db->cleanseNullOrWrapStr($params['reference_page'])}":",null";
            
            $tasks = array();
            foreach($db->call("getTask", $args) as $row){
                $task_data = array();
                        foreach($row as $col_name => $col_value) {
                            if ($col_name == 'id') {
                                    $task_data['task_id'] = $col_value;
                                }
                            else if (!is_numeric($col_name) && !is_null($col_value)) {
                                    $task_data[$col_name] = $col_value;
                            }
                        }

                        if ($tags = $this->_fetchTags($row['id'])) {
                            $task_data['tags'] = $tags;
                        }

                        $task = new Task($task_data);
                        if (is_object($task)) {
                            $tasks[] = $task;
                        }
            }
            return $tasks;
        }


        private function _fetchTags($task_id) {
		$db = new PDOWrapper();
		$db->init();
		$ret = null;
		if ($result = $db->call("getTaskTags", "{$db->cleanseNull($task_id)}")) {
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
        $this->calculateTaskScore($task->getTaskId());
	}

    /*
     * Add an identicle entry with a different ID and target Language
     * Used for bulk uploads
     */
    public function duplicateTaskForTarget($task, $language_id)
    {
        //Get the file info for original task
        $task_file_info = $this->getTaskFileInfo($task);
        //Get the file path to original upload
        $old_file_path = Upload::absoluteFilePathForUpload($task, 0, $task_file_info['filename']);

        //Remove ID so a new one will be created
        $task->setTaskId(null);
        $task->setTargetId($language_id);
        //Save the new Task
        $this->save($task);

        //Generate new file info and save it
        $task_file_info['task_id'] = $task->getTaskId();
        $task_file_info['upload_time'] = "\"".$task_file_info['upload_time']."\"";
        $task_file_info['filename'] = "\"".$task_file_info['filename']."\"";
        $task_file_info['content_type'] = "\"".$task_file_info['content_type']."\"";
        $this->saveTaskFileInfo($task_file_info);

        //Get the new path the file can be found at
        $file_info = $this->getTaskFileInfo($task);
        $new_file_path = Upload::absoluteFilePathForUpload($task, 0, $file_info['filename']);

        Upload::createFolderPath($task);
        if(!copy($old_file_path, $new_file_path)) {
            $error = "Failed to copy file to new location";
        }
    }

    private function _update($task) {
        $db = new PDOWrapper();
        $db->init();
        $result= $db->call("taskInsertAndUpdate", "{$db->cleanseNull($task->getTaskId())},{$db->cleanseNull($task->getOrganisationId())},{$db->cleanseNullOrWrapStr($task->getTitle())},{$db->cleanseNull($task->getWordCount())},{$db->cleanseNull($task->getSourceId())},{$db->cleanseNull($task->getTargetId())},{$db->cleanseNullOrWrapStr($task->getCreatedTime())},{$db->cleanseNullOrWrapStr($task->getImpact())},{$db->cleanseNullOrWrapStr($task->getReferencePage())}");
        $this->_updateTags($task);
    }


    private function calculateTaskScore($task_id)
    {
        $exec_path = __DIR__."/scripts/calculate_scores.py $task_id";
        echo shell_exec($exec_path . "> /dev/null 2>/dev/null &");

    }

    private function _updateTags($task) {
            $this->_unlinkStoredTags($task);
            $this->_storeTagLinks($task);
    }

    private function _unlinkStoredTags($task) {
            $db = new PDOWrapper();
            $db->init();
            $db->call("unlinkStoredTags", "{$db->cleanse($task->getTaskId())}");
    }

    private function _storeTagLinks($task) {
            if ($tags = $task->getTags()) {
                    if ($tag_ids = $this->_tagsToIds($tags)) {
                            $db = new PDOWrapper();
                            $db->init();
                            foreach ($tag_ids as $tag_id) {
                                    $ins = array();
                                    $ins['task_id'] = $db->cleanse($task->getTaskId());
                                    $ins['tag_id'] = $db->cleanse($tag_id);
                                    $db->call("storeTagLinks", "{$db->cleanse($task->getTaskId())},{$db->cleanse($tag_id)}");
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
            $tDAO = new TagsDao();
            return $tDAO->tagIDFromLabel($tag);
    }

    private function _createTag($tag) {
            $tDAO = new TagsDao();
            return $tDAO->create($tag);
    }

    private function _insert(&$task) {
        $db = new PDOWrapper();
        $db->init();
        $result= $db->call("taskInsertAndUpdate", "null,{$db->cleanseNull($task->getOrganisationId())},{$db->cleanseNullOrWrapStr($task->getTitle())},{$db->cleanseNull($task->getWordCount())},{$db->cleanseNull($task->getSourceId())},{$db->cleanseNull($task->getTargetId())},null,{$db->cleanseNullOrWrapStr($task->getImpact())},{$db->cleanseNullOrWrapStr($task->getReferencePage())}");
        $task->setTaskId($result[0]['id']);
        $this->_updateTags($task);

    }

    public function getLatestAvailableTasks($nb_items = 10) {
            $db = new PDOWrapper();
            $db->init();
            $ret = false;
            if ($r = $db->call("getLatestAvailableTasks", "{$db->cleanse($nb_items)}")) {
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
     * Returns an array of tasks ordered by the highest score related to the user
     */
    public function getUserTopTasks($user_id, $nb_items) {
        $db = new PDOWrapper();
        $db->init();
        $ret = false;
        if ($result = $db->call("getUserTopTasks", "{$db->cleanse($user_id)},{$db->cleanse($nb_items)}")) {
            $ret = array();
            foreach($result as $row) {
                $task = self::find(array('task_id' => $row['id']));
                if(!$task->getTaskId()) {
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

		$db = new PDOWrapper();
		$db->init();
		$ret = false;
		if ($r = $db->call("getTaggedTasks", "{$db->cleanse($tag_id)},{$db->cleanse($nb_items)}")) {
			$ret = array();
			foreach($r as $row)	{
				$ret[] = self::find(array('task_id' => $row['id']));
			}
		}
		return $ret;
	}

	function getTopTags ($limit = 30) {
		$ret = false;
		$db = new PDOWrapper();
		$db->init();
		if ($r = $db->call("getTopTags", "{$db->cleanse($limit)}")) {
			$ret = array();
			foreach ($r as $row) {
				$ret[] = $row['label'];
			}
		}
		return $ret;
	}

	public function recordFileUpload($task, $filename, $content_type, $user_id) {
                $db = new PDOWrapper();
		$db->init();
                $args = "";
                $args .= "{$db->cleanse($task->getTaskId())}";
                $args .= ",{$db->cleanseWrapStr($filename)}";
                $args .= ",{$db->cleanseWrapStr($content_type)}";
                $args .= ",{$db->cleanse($user_id)}";
                return $db->call("recordFileUpload", $args);
        }

	public function getLatestFileVersion($task) {
		$db = new PDOWrapper();
		$db->init();
		$ret = false;
		if ($r = $db->call("getLatestFileVersion", "{$db->cleanse($task->getTaskId())}")) {
			if (is_numeric($r[0]['latest_version'])) {
				$ret =  intval($r[0]['latest_version']);
			}
		}
		return $ret;
	}

	public function moveToArchive($task) {
		$db = new PDOWrapper();
		$db->init();
                $db->call("archiveTask", "{$db->cleanse($task->getTaskId())}");
	}

	public function logFileDownload($task, $version) {
		$db = new PDOWrapper();
		$db->init();
                $db->call("logFileDownload", "{$db->cleanse($task->getTaskId())},{$db->cleanse($version)},null");
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

    private function saveTaskFileInfo($task_file_info)
    {
        $ret = null;
        $db = new MySQLWrapper();
        $db->init();
        $db->Insert('task_file_version', $task_file_info);
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

	public function claimTask($task, $user) {
		$db = new MySQLWrapper();
		$db->init();

		$task_claim = array();
		$task_claim['task_id'] 			= $db->cleanse($task->getTaskId());
		$task_claim['user_id'] 			= $db->cleanse($user->getUserId());
		$task_claim['claimed_time']	= 'NOW()';

		$ret = $db->Insert('task_claim', $task_claim);
		return $ret;
	}

	public function hasUserClaimedTask($user_id, $task_id) {
		$db = new MySQLWrapper();
		$db->init();
		$query = 'SELECT user_id
					FROM task_claim
					WHERE task_id = ' . $db->cleanse($task_id) . '
					AND user_id = ' . $db->cleanse($user_id);
		if ($result = $db->Select($query)) {
			return true;
		}
		else {
			return false;
		}
	}

	public function taskIsClaimed($task_id) {
		$db = new MySQLWrapper();
		$db->init();
		$query = 'SELECT user_id
					FROM task_claim
					WHERE task_id = ' . $db->cleanse($task_id);
		if ($result = $db->Select($query)) {
			return true;
		}
		else {
			return false;
		}
	}

    public function getUserTasks($user, $limit = 0)
    {
        $db = new MySQLWrapper();
        $db->init();
        $query = 'SELECT * 
                    FROM task JOIN task_claim ON task_claim.task_id = task.id
                    WHERE user_id = '.$db->cleanse($user->getUserId()).'
                    ORDER BY created_time DESC';
        if($limit != 0) {
            $query .= ' LIMIT 0, 10';
        }
        return $this->_parse_result_for_user_task($db->Select($query));
    }

    public function getUserArchivedTasks($user, $limit = 0)
    {
        $db = new MySQLWrapper();
        $db->init();
        $query = 'SELECT *
                    FROM archived_task as a JOIN task_claim as c ON a.task_id = c.task_id
                    WHERE user_id = '.$db->cleanse($user->getUserID()).'
                    ORDER BY created_time DESC';
        if($limit != 0) {
            $query .= ' LIMIT 0, 10';
        }
        return $this->_parse_result_for_user_task($db->Select($query));
    }


    private function _parse_result_for_user_task($sqlResult)
    {
        $ret = NULL;
        $ret = array();
        if($sqlResult) {
            foreach($sqlResult as $row) {
                $params = array();
                $params['task_id'] = $row['task_id'];
                $params['title'] = $row['title'];
                $params['impact'] = $row['impact'];
                $params['reference_page'] = $row['reference_page'];
                $params['organisation_id'] = $row['organisation_id'];
                $params['source_id'] = $row['source_id'];
                $params['target_id'] = $row['target_id'];
                $params['word_count'] = $row['word_count'];
                $params['created_time'] = $row['created_time'];
                $task = new Task($params);
                $task->setStatus($this->getTaskStatus($task->getTaskId()));
                $ret[] = $task;
            }
        }
         
        return $ret;
    }

    /*
    * Check to see if a translation for this task has been uploaded before
    */
    public function hasBeenUploaded($task_id, $user_id)
    {
        return $this->_check_task_file_version($task_id, $user_id);
    }

    private function getTaskStatus($task_id)
    {
        
        if($this->_check_task_file_version($task_id)) {
            return "Your translation is under review";
        } else {
            return "Awaiting your translation";
        }
    }

    /*
     * A private file for check if a task has been translated by checking 
     * if a file has been uploaded for it. if user_id is null it will just 
     * check on a task basis. The inclusion of the user_id allows several 
     * people to work on the job at once
     * Returns true if the file has been translated
     */
    private function _check_task_file_version($task_id, $user_id = null)
    {
        $db = new MySQLWrapper();
        $db->init();
        $query = 'SELECT *
                FROM task_file_version
                WHERE task_id = '.$db->cleanse($task_id).'
                AND version_id > 0';
        if(!is_null($user_id)) {
            $query .= ' AND user_id = '.$db->cleanse($user_id);
        }
        if(!$db->Select($query)) {
            return false;
        } else {
            return true;
        }
    }
}
