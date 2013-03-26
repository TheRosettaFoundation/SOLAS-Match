<?php

require_once __DIR__.'/../../Common/Requests/UserTaskScoreRequest.php';
require_once __DIR__.'/../../Common/lib/PDOWrapper.class.php';
require_once __DIR__.'/../../Common/models/Task.php';
require_once __DIR__.'/../../api/lib/Upload.class.php';
require_once __DIR__.'/../lib/Notify.class.php';
require_once __DIR__.'/../lib/NotificationTypes.class.php';
require_once __DIR__.'/TaskFile.class.php';

/**
 * Task Document Access Object for manipulating tasks.
 *
 * @package default
 * @author eoin.oconchuir@ul.ie
 **/

class TaskDao
{

    public static function create($task)
    {
        self::save($task);
        return $task;
    }

    public static function findTasksByOrg($params, $sort_column = null, $sort_direction = null)
    {
        $permitted_params = array(
                'organisation_ids'
        );

        if (!is_array($params)) {
            throw new InvalidArgumentException('Can\'t find a task if an array isn\'t provided.');
        }

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

                if ($tags = self::getTags($row['id'])) {
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
        
    public static function getTask($id=null, $projectId=null, $title=null, $wordCount=null, $languageIdSource=null,
            $languageIdTarget=null, $createdTime=null, $countryIdSource=null, $countryIdTarget=null, $comment=null,
            $taskTypeId=null, $taskStatusId=null, $published=null, $deadline=null)
    {
        $tasks = array();
        $result = PDOWrapper::call("getTask", PDOWrapper::cleanseNull($id).",".PDOWrapper::cleanseNull($projectId).",".
                PDOWrapper::cleanseNullOrWrapStr($title).",".PDOWrapper::cleanseNull($wordCount).",".PDOWrapper::cleanseNullOrWrapStr($languageIdSource).",".
                PDOWrapper::cleanseNullOrWrapStr($languageIdTarget).",".PDOWrapper::cleanseNullOrWrapStr($createdTime).",".
                PDOWrapper::cleanseNullOrWrapStr($countryIdSource).",".PDOWrapper::cleanseNullOrWrapStr($countryIdTarget).",".
                PDOWrapper::cleanseNullOrWrapStr($comment).",".PDOWrapper::cleanseNull($taskTypeId).",".PDOWrapper::cleanseNull($taskStatusId).",".
                PDOWrapper::cleanseNull($published).",".PDOWrapper::cleanseNullOrWrapStr($deadline));
        if ($result) {
            foreach ($result as $row) {
                $tasks[] = ModelFactory::buildModel("Task", $row);
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
    public static function save(&$task)
    {
        if (is_null($task->getId())) {
            self::insert($task);
        } else {
            self::update($task);
            //Only calc scores for tasks with MetaData
            self::calculateTaskScore($task->getId());
        }
        
        return $task;
    }

    /*
     * Add an identicle entry with a different ID and target Language
     * Used for bulk uploads
     */
    public static function duplicateTaskForTarget($task, $languageCode, $countryCode, $userID)
    {
        //Get the file info for original task
        $task_file_info = TaskFile::getTaskFileInfo($task);
        //Get the file path to original upload
        $old_file_path = Upload::absoluteFilePathForUpload($task, 0, $task_file_info['filename']);

        //Remove ID so a new one will be created
        $task->setId(null);
        $task->setTargetLanguageCode($languageCode);
        $task->setTargetCountryCode($countryCode);
        //Save the new Task
        self::save($task);
        self::calculateTaskScore($task->getId());

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

    private static function update($task)
    {
        $result= PDOWrapper::call("taskInsertAndUpdate", PDOWrapper::cleanseNull($task->getId())
                                                .",".PDOWrapper::cleanseNull($task->getProjectId())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($task->getTitle())
                                                .",".PDOWrapper::cleanseNull($task->getWordCount())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($task->getSourceLanguageCode())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($task->getTargetLanguageCode())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($task->getCreatedTime())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($task->getComment())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($task->getSourceCountryCode())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($task->getTargetCountryCode())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($task->getDeadline())
                                                .",".PDOWrapper::cleanseNull($task->getTaskType())
                                                .",".PDOWrapper::cleanseNull($task->getTaskStatus())
                                                .",".PDOWrapper::cleanseNull($task->getPublished()));
        
        if($result) {
            $task = ModelFactory::buildModel('Task', $result);
        } else {
            return null;
        }
    }
    
    public static function delete($TaskID)
    {
        $result= PDOWrapper::call("deleteTask", PDOWrapper::cleanseNull($TaskID));
        return $result[0]["result"];
    }

    private static function calculateTaskScore($task_id)
    {
        $use_backend = Settings::get('site.backend');
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
    
    public static function getTags($task_id)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getTaskTags", PDOWrapper::cleanseNull($task_id))) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Tag", $row);
            }
        }
        return $ret;
    }
    
    

    public static function updateTags($task)
    {
        TaskTags::deleteTaskTags($task);
        if ($tags = $task->getTagList()) {
            if ($tag_ids = self::tagsToIds($tags)) {
                TaskTags::setTaskTags($task, $tag_ids);
                return 1;
            }
            return 0;
        }
        return 0;
    }

//    private static function tagsToIds($tags) 
//    {
//        $tag_ids = array();
//        foreach ($tags as $tag) {
//            if ($tag_id = $tag->getId()) {
//                $tag_ids[] = $tag_id;
//            }
//        }
//
//        if (count($tag_ids) > 0) {
//            return $tag_ids;
//        } else {
//            return null;
//        }
//    }

    private static function insert(&$task)
    {
        $result = PDOWrapper::call("taskInsertAndUpdate", "null"
            .",".PDOWrapper::cleanseNull($task->getProjectId())
            .",".PDOWrapper::cleanseNullOrWrapStr($task->getTitle())
            .",".PDOWrapper::cleanseNull($task->getWordCount())
            .",".PDOWrapper::cleanseNullOrWrapStr($task->getSourceLanguageCode())
            .",".PDOWrapper::cleanseNullOrWrapStr($task->getTargetLanguageCode())
            .",".PDOWrapper::cleanseNullOrWrapStr($task->getCreatedTime())
            .",".PDOWrapper::cleanseNullOrWrapStr($task->getComment())
            .",".PDOWrapper::cleanseNullOrWrapStr($task->getSourceCountryCode())
            .",".PDOWrapper::cleanseNullOrWrapStr($task->getTargetCountryCode())
            .",".PDOWrapper::cleanseNullOrWrapStr($task->getDeadline())
            .",".PDOWrapper::cleanseNull($task->getTaskType())
            .",".PDOWrapper::cleanseNull($task->getTaskStatus())
            .",".PDOWrapper::cleanseNull($task->getPublished()));
        
        if($result) {
            $task = ModelFactory::buildModel("Task", $result[0]);           
        } else {
            $task = null;
        }
    }

    public static function getTaskPreReqs($taskId)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getTaskPreReqs", PDOWrapper::cleanseNull($taskId))) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }

    public static function addTaskPreReq($taskId, $preReqId)
    {
        $args = PDOWrapper::cleanseNull($taskId).", ";
        $args .= PDOWrapper::cleanseNull($preReqId);
        $result = PDOWrapper::call("addTaskPreReq", $args);
        return $result[0]["result"];
    }

    public static function removeTaskPreReq($taskId, $preReqId)
    {
        $args = PDOWrapper::cleanseNull($taskId).", ";
        $args .= PDOWrapper::cleanseNull($preReqId);
        $result = PDOWrapper::call("removeTaskPreReq", $args);
        return $result[0]["result"];
    }

    public static function getLatestAvailableTasks($nb_items = 15)
    {
        $ret = null;
        if ($r = PDOWrapper::call("getLatestAvailableTasks", PDOWrapper::cleanseNullOrWrapStr($nb_items))) {
            $ret = array();
            foreach ($r as $row) {
                $ret[]= ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }

    /*
     * Returns an array of tasks ordered by the highest score related to the user
     */
    public static function getUserTopTasks($user_id, $limit = 15)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getUserTopTasks", PDOWrapper::cleanse($user_id)
                                        .",".PDOWrapper::cleanseNullOrWrapStr($limit))) {
            $ret = array();
            foreach ($result as $row) {
                 $ret[]= ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }

    /*
     * Return an array of tasks that are tagged with a certain tag.
     */
    public static function getTaggedTasks($tag, $limit = 15)
    {
        $tag_id = self::getTagId($tag);
        return self::getTasksWithTag($tag_id, $limit);
    }
        
    public static function getTasksWithTag($tag_id, $limit = 15)
    {
        if (is_null($tag_id)) {
            throw new InvalidArgumentException('Cannot get tasks tagged with '
                                                . $tag_id .
                                                ' because no such tag is in the system.');
        }

        $ret = false;
        if ($r = PDOWrapper::call("getTaggedTasks", PDOWrapper::cleanse($tag_id).",".PDOWrapper::cleanse($limit))) {
            $ret = array();
            foreach ($r as $row) {
                    $ret[] = self::getTask($row['id']);
            }
        }
        return $ret;
    }

    public static function moveToArchiveByID($taskId, $userId) 
    {
        $ret = false;
        $task = self::getTask($taskId);

        $graphBuilder = new APIWorkflowBuilder();
        $graph = $graphBuilder->buildProjectGraph($task->getProjectId());

        if ($graph) {
            $currentLayer = $graph->getRootNodeList();
            $nextLayer = array();
            $found = false;
            while (count($currentLayer) > 0 && !$found) {
                foreach ($currentLayer as $node) {
                    if ($node->getTaskId() == $taskId) {
                        $found = true;
                        $ret = self::archiveTaskNode($node, $userId);
                    } else {
                        foreach ($node->getNextList() as $nextNode) {
                            if (!in_array($nextNode, $nextLayer)) {
                                $nextLayer[] = $nextNode;
                            }
                        }
                    }
                }
                $currentLayer = $nextLayer;
                $nextLayer = array();
            }
        }

        // UI us expecting output to be 0 or 1
        if ($ret) {
            $ret = 1;
        } else {
            $ret = 0;
        }

        return $ret;
    }

    public static function archiveTaskNode($node, $userId)
    {
        $ret = true;
        $task = self::getTask($node->getTaskId());
        $dependantNodes = $node->getNextList();
        if (count($dependantNodes) > 0) {
            foreach ($dependantNodes as $dependant) {
                $dTask = self::getTask($dependant->getTaskId());
                $preReqs = $dependant->getPreviousList();
                if ((count($preReqs) == 2 && $dTask->getTaskType() == TaskTypeEnum::POSTEDITING) ||
                        count($preReqs) == 1) {
                    $ret = $ret && (self::archiveTaskNode($dependant, $userId));
                }
            }
        }

        if ($ret) {
            $ret = self::archiveTask($node->getTaskId(), $userId);
        }

        return $ret;
    }

    public static function archiveTask($taskId, $userId)
    {
        Notify::sendEmailNotifications($taskId, NotificationTypes::ARCHIVE);
        $result = PDOWrapper::call("archiveTask", PDOWrapper::cleanseNull($taskId).", ".PDOWrapper::cleanseNull($userId));
        return $result[0]['result'];
    }
        
    public static function claimTask($task_id, $user_id)
    {
        $ret = PDOWrapper::call("claimTask", PDOWrapper::cleanse($task_id).",".PDOWrapper::cleanse($user_id));
        return $ret[0]['result'];
    }
    
    public static function unClaimTask($task_id, $user_id)
    {
        $ret = PDOWrapper::call("unClaimTask", PDOWrapper::cleanse($task_id).",".PDOWrapper::cleanse($user_id));
        return $ret[0]['result'];
    }
        

    public static function hasUserClaimedTask($user_id, $task_id)
    {
        $result = PDOWrapper::call("hasUserClaimedTask", PDOWrapper::cleanse($task_id)
                                    .",".PDOWrapper::cleanse($user_id));
        return $result[0]['result'];
    }

    public static function taskIsClaimed($task_id)
    {
        $result =  PDOWrapper::call("taskIsClaimed", PDOWrapper::cleanse($task_id));
        return $result[0]['result'];
    }
    
    public static function getUserTasks($user_id, $limit = 10)
    {
        $result = PDOWrapper::call("getUserTasks", PDOWrapper::cleanse($user_id).",".PDOWrapper::cleanse($limit));
        if($result) { 
            $tasks = array();
            foreach($result as $taskData) {
                $tasks[] = ModelFactory::buildModel("Task", $taskData);
            }
            return $tasks;
        } else {
            return null;
        }
    }
    
    public static function getUserArchivedTasks($user_id, $limit = 10)
    {
        $result = PDOWrapper::call("getUserArchivedTasks", PDOWrapper::cleanse($user_id).",".PDOWrapper::cleanse($limit));
        if($result) { 
            $tasks = array();
            foreach($result as $taskData) {
                $tasks[] = ModelFactory::buildModel("ArchivedTask", $taskData);
            }
            return $tasks;
        } else {
            return null;
        }
    }

    /*
       Get User Notification List for this task
    */
    public static function getSubscribedUsers($task_id)
    {
        $ret = null;

        $result = PDOWrapper::call('getSubscribedUsers', "$task_id");
        if ($result) {
            foreach ($result as $row) {
                $ret[] = UserDao::find($row);
            }
        }

        return $ret;
    }

    /*
    * Check to see if a translation for this task has been uploaded before
    */
    public static function hasBeenUploaded($task_id, $user_id)
    {
        return TaskFile::checkTaskFileVersion($task_id, $user_id);
    }

    public static function getTaskStatus($task_id)
    {
        if (TaskFile::checkTaskFileVersion($task_id)) {
            return "Your translation is under review";
        } else {
            return "Awaiting your translation";
        }
    }
 
    public static function downloadTask($taskID, $version = 0)
    {
        $task = self::getTask($taskID);

        if (!is_object($task)) {
            header('HTTP/1.0 500 Not Found');
            die;
        }
        
        $task_file_info = TaskFile::getTaskFileInfo($task, $version);

        if (empty($task_file_info)) {
            throw new Exception("Task file info not set for.");
        }

        $absolute_file_path = Upload::absoluteFilePathForUpload($task, $version, $task_file_info['filename']);
        $file_content_type = $task_file_info['content-type'];
        //TaskFile::logFileDownload($task, $version);
        IO::downloadFile($absolute_file_path, $file_content_type);
    }
    
    public static function downloadConvertedTask($taskID, $version = 0)
    {
        $task = self::getTask($taskID);

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
        IO::downloadConvertedFile($absolute_file_path, $file_content_type,$taskID);
    }
    
    
    
    public static function getUserClaimedTask($id)
    {
        $ret = null;
        if ($result = PDOWrapper::call('getUserClaimedTask', PDOWrapper::cleanse($id))) {
            
            $ret = ModelFactory::buildModel("User",$result[0] );
        }
        return $ret;
    }
    
}
