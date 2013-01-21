<?php

require_once '../Common/Requests/UserTaskScoreRequest.php';
require_once '../Common/lib/PDOWrapper.class.php';
require_once '../Common/models/Task.php';
require_once 'lib/Upload.class.php';
require_once 'TaskTags.class.php';
require_once 'TaskFile.class.php';

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
    public function create($params)
    {
        if (!is_array($params) && is_object($params)) {
            $task   = APIHelper::cast("Task", $params);
        } else {
            $task = ModelFactory::buildModel("Task", $params);
        }

        $this->save($task);
        return $task;
    }

    public function findTasksByOrg($params, $sort_column = null, $sort_direction = null)
    {
        $permitted_params = array(
                'organisation_ids'
        );

        if (!is_array($params)) {
            throw new InvalidArgumentException('Can\'t find a task if an array isn\'t provided.');
        }

        $where = array();
        foreach ($params as $key => $value) {
            if (!in_array($key, $permitted_params)) {
                throw new InvalidArgumentException('Cannot search for a task with the provided paramter ' . $key . '.');
            }
        }

        $tasks = null;
        $organisation_ids = $params['organisation_ids'];
        
        // We're assuming that organisation_ids is always being provided.
        if (count($organisation_ids) > 1) {
            $organisation_ids = implode(',', $organisation_ids);
        }
        
        $args = PDOWrapper::cleanse($organisation_ids);
        $args .= empty($sort_column) ? ",null" : PDOWrapper::cleanse($sort_column);
        $args .= (!empty($sort_column) && empty($sort_direction)) ? " " : PDOWrapper::cleanse($sort_direction);
        if ($result = PDOWrapper::call("getTasksByOrgIDs", $args)) {
            $tasks = array();
            foreach ($result as $row) {
                $task_data = array();
                foreach ($row as $col_name => $col_value) {
                    if ($col_name == 'id') {
                        $task_data['id'] = $col_value;
                    } else if (!is_numeric($col_name) && !is_null($col_value)) {
                        $task_data[$col_name] = $col_value;
                    }
                }

                if ($tags = TaskTags::getTags($row['id'])) {
                    $task_data['tags'] = $tags;
                }

                $task = ModelFactory::buildModel("Task", $task_data);
                if (is_object($task)) {
                    $tasks[] = $task;
                }
            }
        }

        return $tasks;
    }

    public function find($params) 
    {

        if (!is_array($params)) {
            throw new InvalidArgumentException('Can\'t find a task if an array isn\'t provided.');
        }
        
        $result = self::getTask($params);
        return $result[0];
    }
        
    public function getTask($params)
    {
        $args = "";
        $args .= isset($params['id']) ?
            PDOWrapper::cleanseNull($params['id']) : "null";
        $args .= isset($params['project_id']) ?
            PDOWrapper::cleanseNull($params['project_id']) : ", null";
        $args .= isset($params['title']) ?
            ",".PDOWrapper::cleanseNullOrWrapStr($params['title']) : ",null";
        $args .= isset($params['word-count']) ?
            ",".PDOWrapper::cleanseNull($params['word-count']) : ",null";
        $args .= isset($params['language_id-source']) ?
            ",".PDOWrapper::cleanseNull($params['language_id-source']) : ",null";
        $args .= isset($params['language_id-target']) ?
            ",".PDOWrapper::cleanseNull($params['language_id-target']) : ",null";
        $args .= isset($params['created-time']) ?
            ",".PDOWrapper::cleanseNull($params['created-time']) : ",null";
        $args .= isset($params['Country_id-source']) ?
            ",".PDOWrapper::cleanseNull($params['Country_id-source']) : ",null";
        $args .= isset($params['Country_id-target']) ?
            ",".PDOWrapper::cleanseNull($params['Country_id-target']) : ",null";
        $args .= isset($params['comment']) ?
            ",".PDOWrapper::cleanseNullOrWrapStr($params['comment']) : ",null";
        $args .= isset($params['taskType_id']) ?
            ",".PDOWrapper::cleanseNull($params['taskType_id']) : ",null";
        $args .= isset($params['taskStatus_id']) ?
            ",".PDOWrapper::cleanseNull($params['taskStatus_id']) : ",null";
        $args .= isset($params['published']) ?
            ",".PDOWrapper::cleanseNullOrWrapStr($params['published']) : ",null";
        $args .= isset($params['deadline']) ?
            ",".PDOWrapper::cleanseNull($params['deadline']) : ",null";

        $tasks = array();
        $result = PDOWrapper::call("getTask", $args);
        if ($result) {
            foreach ($result as $row) {
                $task_data = array();
                
                foreach ($row as $col_name => $col_value) {
                    if ($col_name == 'id') {
                        $task_data['id'] = $col_value;
                    } else if (!is_numeric($col_name) && !is_null($col_value)) {
                        $task_data[$col_name] = $col_value;
                    }
                }

                if ($tags = TaskTags::getTags($row['id'])) {
                    $task_data['tags'] = $tags;
                }

                $task = ModelFactory::buildModel("Task", $task_data);
                if (is_object($task)) {
                    $tasks[] = $task;
                }
            }
        }
        
        if (sizeof($tasks) == 0) {
            $tasks=null;
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
        if (is_null($task->getId())) {
            $this->insert($task);
        } else {
            $this->update($task);
            //Only calc scores for tasks with MetaData
            $this->calculateTaskScore($task->getId());
        }
    }

    /*
     * Add an identicle entry with a different ID and target Language
     * Used for bulk uploads
     */
    public function duplicateTaskForTarget($task, $language_id, $countryCode, $userID)
    {
        //Get the file info for original task
        $task_file_info = TaskFile::getTaskFileInfo($task);
        //Get the file path to original upload
        $old_file_path = Upload::absoluteFilePathForUpload($task, 0, $task_file_info['filename']);

        //Remove ID so a new one will be created
        $task->setId(null);
        $task->setTargetLanguageId($language_id);
        $task->setTargetCountryId($countryCode);
        //Save the new Task
        $this->save($task);
        $this->calculateTaskScore($task->getId());

        //Generate new file info and save it
        TaskFile::recordFileUpload($task, $task_file_info['filename'], $task_file_info['content-type'], $userID);
     
        $task_file_info['filename'] = '"'.$task_file_info['filename'].'"';

        //Get the new path the file can be found at
        $file_info = TaskFile::getTaskFileInfo($task);
        $new_file_path = Upload::absoluteFilePathForUpload($task, 0, $file_info['filename']);
        
        Upload::createFolderPath($task);
        if (!copy($old_file_path, $new_file_path)) {
            $error = "Failed to copy file to new location";
            return 0;
        }
        
        return 1;
    }

    private function update($task)
    {
        $result= PDOWrapper::call("taskInsertAndUpdate", PDOWrapper::cleanseNull($task->getId())
                                                .",".PDOWrapper::cleanseNull($task->getProjectId())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($task->getTitle())
                                                .",".PDOWrapper::cleanseNull($task->getWordCount())
                                                .",".PDOWrapper::cleanseNull($task->getSourceLanguageId())
                                                .",".PDOWrapper::cleanseNull($task->getTargetLanguageId())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($task->getCreatedTime())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($task->getComment())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($task->getSourceCountryId())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($task->getTargetCountryId())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($task->getDeadline())
                                                .",".PDOWrapper::cleanseNull($task->getTaskType())
                                                .",".PDOWrapper::cleanseNull($task->getTaskStatus())
                                                .",".PDOWrapper::cleanseNull($task->getPublished()));
        $this->updateTags($task);
    }
    
    public function delete($TaskID)
    {
        $result= PDOWrapper::call("deleteTask", PDOWrapper::cleanseNull($TaskID));
        return $result[0]["result"];
    }

    private function calculateTaskScore($task_id)
    {
        $settings = new Settings();
        $use_backend = $settings->get('site.backend');
        if (strcasecmp($use_backend, "y") == 0) {
            $mMessagingClient = new MessagingClient();
            if ($mMessagingClient->init()) {
                $request = new UserTaskScoreRequest();
                $request->setTaskId($task_id);
                $message = $mMessagingClient->createMessageFromProto($request);
                $mMessagingClient->sendTopicMessage($message, 
                                                    $mMessagingClient->MainExchange, 
                                                    $mMessagingClient->TaskScoreTopic);
            } else {
                echo "Failed to Initialize messaging client";
            }
        } else {
            //use the python script
            $exec_path = __DIR__."/../scripts/calculate_scores.py $task_id";
            echo shell_exec($exec_path . "> /dev/null 2>/dev/null &");
        }
    }

    public function updateTags($task)
    {
        TaskTags::deleteTaskTags($task);
        if ($tags = $task->getTags()) {
            if ($tag_ids = $this->tagsToIds($tags)) {
                TaskTags::setTaskTags($task, $tag_ids);
                return 1;
            }
            return 0;
        }
        return 0;
    }

    private function tagsToIds($tags) 
    {
        $tag_ids = array();
        foreach ($tags as $tag) {
            if ($tag_id = $this->getTagId($tag)) {
                $tag_ids[] = $tag_id;
            } else {
                $tag_ids[] = $this->createTag($tag);
            }
        }

        if (count($tag_ids) > 0) {
            return $tag_ids;
        } else {
            return null;
        }
    }

    public function getTagId($tag)
    {
        $tDAO = new TagsDao();
        return $tDAO->tagIDFromLabel($tag);
    }

    private function createTag($tag)
    {
        $tDAO = new TagsDao();
        return $tDAO->create($tag);
    }

    private function insert(&$task)
    {
        $result = PDOWrapper::call("taskInsertAndUpdate", "null"
            .",".PDOWrapper::cleanseNull($task->getProjectId())
            .",".PDOWrapper::cleanseNullOrWrapStr($task->getTitle())
            .",".PDOWrapper::cleanseNull($task->getWordCount())
            .",".PDOWrapper::cleanseNull($task->getSourceLanguageId())
            .",".PDOWrapper::cleanseNull($task->getTargetLanguageId())
            .",".PDOWrapper::cleanseNullOrWrapStr($task->getCreatedTime())
            .",".PDOWrapper::cleanseNullOrWrapStr($task->getComment())
            .",".PDOWrapper::cleanseNullOrWrapStr($task->getSourceCountryId())
            .",".PDOWrapper::cleanseNullOrWrapStr($task->getTargetCountryId())
            .",".PDOWrapper::cleanseNullOrWrapStr($task->getDeadline())
            .",".PDOWrapper::cleanseNull($task->getTaskType())
            .",".PDOWrapper::cleanseNull($task->getTaskStatus())
            .",".PDOWrapper::cleanseNull($task->getPublished()));
        $task->setId($result[0]['id']);
        $this->updateTags($task);
    }

    public function getLatestAvailableTasks($nb_items = 10)
    {
        $ret = false;
        if ($r = PDOWrapper::call("getLatestAvailableTasks", PDOWrapper::cleanseNullOrWrapStr($nb_items))) {
            $ret = array();
            foreach ($r as $row) {
                // Add a new Job object to the array to be returned.
                $task = self::find(array('id' => $row['id']));
                if (!$task->getId()) {
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
    public function getUserTopTasks($user_id, $limit)
    {
        $ret = false;
        if ($result = PDOWrapper::call("getUserTopTasks", PDOWrapper::cleanse($user_id)
                                        .",".PDOWrapper::cleanseNullOrWrapStr($limit))) {
            $ret = array();
            foreach ($result as $row) {
                $task = self::find(array('id' => $row['id']));
                if (!$task->getId()) {
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
    public function getTaggedTasks($tag, $limit = 10)
    {
        $task_dao = new TaskDao;
        $tag_id = $task_dao->getTagId($tag);
        return $this->getTasksWithTag($tag_id, $limit);
    }
        
    public function getTasksWithTag($tag_id, $limit = 10)
    {
        if (is_null($tag_id)) {
            throw new InvalidArgumentException('Cannot get tasks tagged with '
                                                . $tag .
                                                ' because no such tag is in the system.');
        }

        $ret = false;
        if ($r = PDOWrapper::call("getTaggedTasks", PDOWrapper::cleanse($tag_id).",".PDOWrapper::cleanse($limit))) {
            $ret = array();
            foreach ($r as $row) {
                    $ret[] = self::find(array('id' => $row['id']));
            }
        }
        return $ret;
    }

    public function moveToArchiveByID($taskID, $userId) 
    {
        $task = $this->find(array("id" => $taskID));
        Notify::sendEmailNotifications($task, NotificationTypes::ARCHIVE);
        PDOWrapper::call("archiveTask", PDOWrapper::cleanse($taskID, $userId));
    }

    public function claimTask($task, $user)
    {
        return $this->claimTaskbyID($task->getId(), $user->getUserId());
    }
        
    public function claimTaskbyID($task_id, $user_id)
    {
        $ret = PDOWrapper::call("claimTask", PDOWrapper::cleanse($task_id).",".PDOWrapper::cleanse($user_id));
        return $ret[0]['result'];
    }
        

    public function hasUserClaimedTask($user_id, $task_id)
    {
        $result = PDOWrapper::call("hasUserClaimedTask", PDOWrapper::cleanse($task_id)
                                    .",".PDOWrapper::cleanse($user_id));
        return $result[0]['result'];
    }

    public function taskIsClaimed($task_id)
    {
        $result =  PDOWrapper::call("taskIsClaimed", PDOWrapper::cleanse($task_id));
        return $result[0]['result'];
    }

    public function getTaskTranslator($task_id)
    {
        $ret = null;
        if ($result = PDOWrapper::call('getTaskTranslator', PDOWrapper::cleanse($task_id))) {
            $user_dao = new UserDao();
            $ret = $user_dao->find($result[0]);
        }
        return $ret;
    }
        
    public function getUserTasks($user, $limit = 10)
    {
        return $this->getUserTasksByID($user->getUserId(), $limit);
    }
    
    public function getUserTasksByID($user_id, $limit = 10)
    {
        return $this->parseResultForUserTask(PDOWrapper::call("getUserTasks",
                                                PDOWrapper::cleanse($user_id)
                                                .",".PDOWrapper::cleanse($limit)));
    }

    public function getUserArchivedTasks($user, $limit = 10)
    {
        return $this->getUserArchivedTasksByID($user->getUserId(), $limit);        
    }
    
    public function getUserArchivedTasksByID($user_id, $limit = 10)
    {
        return $this->parseResultForUserTask(PDOWrapper::call("getUserArchivedTasks", 
                                            PDOWrapper::cleanse($user_id).",".PDOWrapper::cleanse($limit)));
    }

    private function parseResultForUserTask($sqlResult)
    {   
        $ret = null;
        if ($sqlResult) {
            $ret = array();
            foreach ($sqlResult as $row) {
                $task = ModelFactory::buildModel("Task", $row);
                $task->setTaskStatus($this->getTaskStatus($task->getId()));
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

        if ($result = PDOWrapper::call('getSubscribedUsers', "$task_id")) {
            foreach ($result as $row) {
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
        return TaskFile::checkTaskFileVersion($task_id, $user_id);
    }

    public function getTaskStatus($task_id)
    {
        if (TaskFile::checkTaskFileVersion($task_id)) {
            return "Your translation is under review";
        } else {
            return "Awaiting your translation";
        }
    }
 
    public static function downloadTask($taskID, $version = 0)
    {
        $task_dao = new TaskDao;
        $task = $task_dao->find(array('id' => $taskID));

        if (!is_object($task)) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        
        $task_file_info = TaskFile::getTaskFileInfo($task, $version);

        if (empty($task_file_info)) {
            throw new Exception("Task file info not set for.");
        }

        $absolute_file_path = Upload::absoluteFilePathForUpload($task, $version, $task_file_info['filename']);
        $file_content_type = $task_file_info['content-type'];
        TaskFile::logFileDownload($task, $version);
        IO::downloadFile($absolute_file_path, $file_content_type);
    }
    
    public static function downloadConvertedTask($taskID, $version = 0)
    {
        $task_dao = new TaskDao;
        $task = $task_dao->find(array('id' => $taskID));

        if (!is_object($task)) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        
        $task_file_info = TaskFile::getTaskFileInfo($task, $version);

        if (empty($task_file_info)) {
            throw new Exception("Task file info not set for.");
        }

        $absolute_file_path = Upload::absoluteFilePathForUpload($task, $version, $task_file_info['filename']);
        $file_content_type = $task_file_info['content-type'];
        TaskFile::logFileDownload($task, $version);
        IO::downloadConvertedFile($absolute_file_path, $file_content_type);
    }
    
}
