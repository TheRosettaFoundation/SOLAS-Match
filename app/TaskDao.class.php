<?php
require_once 'models/Task.class.php';
require_once 'TaskTags.class.php';
require_once 'TaskFile.class.php';
require_once 'PDOWrapper.class.php';
require_once 'lib/Upload.class.php';
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
        if(!is_array($params)&&  is_object($params)){
            $task   = APIHelper::cast("Task", $params);
        } else {
            $task = new Task($params);
        }
		$this->save($task);
		return $task;
	}

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
                        
                        if ($tags = TaskTags::getTags($row['id'])) {
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
            $args .= isset ($params['org_id'])?",{$db->cleanseNull($params['org_id'])}":",null";
            $args .= isset ($params['title'])?",{$db->cleanseNullOrWrapStr($params['title'])}":",null";
            $args .= isset ($params['word_count'])?",{$db->cleanseNull($params['word_count'])}":",null";
            $args .= isset ($params['source_id'])?",{$db->cleanseNull($params['source_id'])}":",null";
            $args .= isset ($params['target_id'])?",{$db->cleanseNull($params['target_id'])}":",null";
            $args .= isset ($params['created_time'])?",{$db->cleanseNull($params['created_time'])}":",null";
            $args .= isset ($params['impact'])?",{$db->cleanseNullOrWrapStr($params['impact'])}":",null";
            $args .= isset ($params['reference_page'])?",{$db->cleanseNullOrWrapStr($params['reference_page'])}":",null";
            $args .= isset ($params['sourceCountry'])?",{$db->cleanseNullOrWrapStr($params['sourceCountry'])}":",null";
            $args .= isset ($params['targetCountry'])?",{$db->cleanseNullOrWrapStr($params['targetCountry'])}":",null";
            
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

                        if ($tags = TaskTags::getTags($row['id'])) {
                            $task_data['tags'] = $tags;
                        }

                        $task = new Task($task_data);
                        if (is_object($task)) {
                            $tasks[] = $task;
                        }
            }
            return $tasks;
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
            //Only calc scores for tasks with MetaData
            $this->calculateTaskScore($task->getTaskId());
		}
	}

    /*
     * Add an identicle entry with a different ID and target Language
     * Used for bulk uploads
     */
    public function duplicateTaskForTarget($task, $language_id,$countryCode)
    {
        //Get the file info for original task
        $task_file_info = TaskFile::getTaskFileInfo($task);
        //Get the file path to original upload
        $old_file_path = Upload::absoluteFilePathForUpload($task, 0, $task_file_info['filename']);

        //Remove ID so a new one will be created
        $task->setTaskId(null);
        $task->setTargetId($language_id);
        $task->setTargetCountryCode($countryCode);
        //Save the new Task
        $this->save($task);
        $this->calculateTaskScore($task->getTaskId());

        //Generate new file info and save it
        TaskFile::recordFileUpload($task,$task_file_info['filename'],$task_file_info['content_type'],$_SESSION['user_id']);
     
        $task_file_info['filename'] = '"'.$task_file_info['filename'].'"';

        //Get the new path the file can be found at
        $file_info = TaskFile::getTaskFileInfo($task);
        $new_file_path = Upload::absoluteFilePathForUpload($task, 0, $file_info['filename']);

        Upload::createFolderPath($task);
        if(!copy($old_file_path, $new_file_path)) {
            $error = "Failed to copy file to new location";
            return 0;
        }
        return 1;
    }

    private function _update($task) {
        $db = new PDOWrapper();
        $db->init();
        $result= $db->call("taskInsertAndUpdate", "{$db->cleanseNull($task->getTaskId())},{$db->cleanseNull($task->getOrganisationId())},{$db->cleanseNullOrWrapStr($task->getTitle())},{$db->cleanseNull($task->getWordCount())},{$db->cleanseNull($task->getSourceId())},{$db->cleanseNull($task->getTargetId())},{$db->cleanseNullOrWrapStr($task->getCreatedTime())},{$db->cleanseNullOrWrapStr($task->getImpact())},{$db->cleanseNullOrWrapStr($task->getReferencePage())},{$db->cleanseNullOrWrapStr($task->getSourceCountryCode())},{$db->cleanseNullOrWrapStr($task->getTargetCountryCode())}");
        $this->_updateTags($task);
    }
    
    public function delete($TaskID){
        $db = new PDOWrapper();
        $db->init();
        $result= $db->call("deleteTask", "{$db->cleanseNull($TaskID)}");
    }

    private function calculateTaskScore($task_id)
    {
        $settings = new Settings();
        $use_backend = $settings->get('site.backend');
        if(strcasecmp($use_backend, "y") == 0) {
            $mMessagingClient = new MessagingClient();
            if($mMessagingClient->init()) {
                $message = $mMessagingClient->createMessageFromString($task_id);
                $mMessagingClient->sendTopicMessage($message, $mMessagingClient->MainExchange, $mMessagingClient->TaskScoreTopic);
            } else {
                echo "Failed to Initialize messaging client";
            }
        } else {
            //use the python script
            $exec_path = __DIR__."/scripts/calculate_scores.py $task_id";
            echo shell_exec($exec_path . "> /dev/null 2>/dev/null &");
        }
    }

    public function _updateTags($task) {
            TaskTags::deleteTaskTags($task);
            if ($tags = $task->getTags()) {
                    if ($tag_ids = $this->_tagsToIds($tags)) {
                          TaskTags::setTaskTags($task, $tag_ids);
                          return 1;
                    }
                    return 0;
            }
            return 0;
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
        $result= $db->call("taskInsertAndUpdate", "null,{$db->cleanseNull($task->getOrganisationId())},{$db->cleanseNullOrWrapStr($task->getTitle())},{$db->cleanseNull($task->getWordCount())},{$db->cleanseNull($task->getSourceId())},{$db->cleanseNull($task->getTargetId())},{$db->cleanseNullOrWrapStr($task->getCreatedTime())},{$db->cleanseNullOrWrapStr($task->getImpact())},{$db->cleanseNullOrWrapStr($task->getReferencePage())},{$db->cleanseNullOrWrapStr($task->getSourceCountryCode())},{$db->cleanseNullOrWrapStr($task->getTargetCountryCode())}");
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
    public function getUserTopTasks($user_id, $limit) {
        $db = new PDOWrapper();
        $db->init();
        $ret = false;
        if ($result = $db->call("getUserTopTasks", "{$db->cleanse($user_id)},{$db->cleanse($limit)}")) {
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
	public function getTaggedTasks($tag, $limit = 10) {
		$task_dao = new TaskDao;
		$tag_id = $task_dao->getTagId($tag);
                return $this->getTasksWithTag($tag_id,$limit);
	}
        
        public function getTasksWithTag($tag_id, $limit = 10) {
		if (is_null($tag_id)) {
			throw new InvalidArgumentException('Cannot get tasks tagged with ' . $tag . ' because no such tag is in the system.');
		}

		$db = new PDOWrapper();
		$db->init();
		$ret = false;
		if ($r = $db->call("getTaggedTasks", "{$db->cleanse($tag_id)},{$db->cleanse($limit)}")) {
			$ret = array();
			foreach($r as $row)	{
				$ret[] = self::find(array('task_id' => $row['id']));
			}
		}
		return $ret;
	}

	public function moveToArchive($task) {
		$db = new PDOWrapper();
		$db->init();
                $db->call("archiveTask", "{$db->cleanse($task->getTaskId())}");
	}

	public function claimTask($task, $user) {
                return $this->claimTaskbyID($task->getTaskId(), $user->getUserId());
	}
        
        public function claimTaskbyID($task_id, $user_id) {
		$db = new PDOWrapper();
		$db->init();
                $ret = $db->call("claimTask", "{$db->cleanse($task_id)},{$db->cleanse($user_id)}");
		return $ret[0]['result'];
	}
        

	public function hasUserClaimedTask($user_id, $task_id) {
		$db = new PDOWrapper();
		$db->init();
        $result = $db->call("hasUserClaimedTask", "{$db->cleanse($task_id)},{$db->cleanse($user_id)}");
        return $result[0]['result'];
	}

	public function taskIsClaimed($task_id) {
		$db = new PDOWrapper();
		$db->init();
        $result =  $db->call("taskIsClaimed", "{$db->cleanse($task_id)}");
        return $result[0]['result'];
	}

    public function getTaskTranslator($task_id)
    {
        $ret = null;
        $db = new PDOWrapper();
        $db->init();
        if($result = $db->call('getTaskTranslator', "{$db->cleanse($task_id)}")) {
            $user_dao = new UserDao();
            $ret = $user_dao->find($result[0]);
        }
        return $ret;
    }
        
    public function getUserTasks($user, $limit = 10)
    {
         return $this->getUserTasksByID($user->getUserId(),$limit);
    }
    
    public function getUserTasksByID($user_id, $limit = 10)
    {
        $db = new PDOWrapper();
        $db->init();
        return $this->_parse_result_for_user_task($db->call("getUserTasks", "{$db->cleanse($user_id)},{$db->cleanse($limit)}"));
    }

    public function getUserArchivedTasks($user, $limit = 10)
    {
        $db = new PDOWrapper();
        $db->init();
        return $this->_parse_result_for_user_task($db->call("getUserArchivedTasks", "{$db->cleanse($user->getUserId())},{$db->cleanse($limit)}"));
        
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
       Get User Notification List for this task
    */
    public function getSubscribedUsers($task_id)
    {
        $ret = null;
        $db = new PDOWrapper();
        $db->init();
        if($result = $db->call('getSubscribedUsers', "$task_id")) {
            foreach($result as $row) {
                $user_dao = new UserDao();
                $ret[] = $user_dao->find($row);
            }
        }

        return $ret;
    }

    /*
    * Check to see if a translation for this task has been uploaded before
    */
    public function hasBeenUploaded($task_id, $user_id)
    {
        return TaskFile::_check_task_file_version($task_id, $user_id);
    }

    public function getTaskStatus($task_id)
    {
        
        if(TaskFile::_check_task_file_version($task_id)) {
            return "Your translation is under review";
        } else {
            return "Awaiting your translation";
        }
    }
 
    public static function downloadTask($taskID,$version=0){
            $task_dao = new TaskDao;
            $task = $task_dao->find(array('task_id' => $taskID));

            if (!is_object($task)) {
                header('HTTP/1.0 404 Not Found');
                die;
            }
            $task_file_info         = TaskFile::getTaskFileInfo($task, $version);

            if (empty($task_file_info)) {
                throw new Exception("Task file info not set for.");
            }

            $absolute_file_path     = Upload::absoluteFilePathForUpload($task, $version, $task_file_info['filename']);
            $file_content_type      = $task_file_info['content_type'];
            TaskFile::logFileDownload($task, $version);
            IO::downloadFile($absolute_file_path, $file_content_type);
        }

  

}
