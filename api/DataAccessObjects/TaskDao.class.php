<?php

require_once __DIR__."/../../Common/Requests/UserTaskScoreRequest.php";
require_once __DIR__."/../../Common/lib/PDOWrapper.class.php";
require_once __DIR__."/../../Common/models/Task.php";
require_once __DIR__."/../../api/lib/Upload.class.php";
require_once __DIR__."/../lib/Notify.class.php";
require_once __DIR__."/../lib/NotificationTypes.class.php";
require_once __DIR__."/../lib/APIWorkflowBuilder.class.php";
require_once __DIR__."/../lib/Upload.class.php";


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
        
    public static function getTask($id=null, $projectId=null, $title=null, $wordCount=null, $sourceLanguageCode=null,
            $targetLanguageCode=null, $createdTime=null, $sourceCountryCode=null, $targetCountryCode=null, $comment=null,
            $taskTypeId=null, $taskStatusId=null, $published=null, $deadline=null)
    {
        $tasks = array();
        $result = PDOWrapper::call("getTask", PDOWrapper::cleanseNull($id).",".PDOWrapper::cleanseNull($projectId).",".
                PDOWrapper::cleanseNullOrWrapStr($title).",".PDOWrapper::cleanseNull($wordCount).",".PDOWrapper::cleanseNullOrWrapStr($sourceLanguageCode).",".
                PDOWrapper::cleanseNullOrWrapStr($targetLanguageCode).",".PDOWrapper::cleanseNullOrWrapStr($createdTime).",".
                PDOWrapper::cleanseNullOrWrapStr($sourceCountryCode).",".PDOWrapper::cleanseNullOrWrapStr($targetCountryCode).",".
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

    public static function submitReview($review)
    {
        $ret = null;

        $args = '';
        $args .= PDOWrapper::cleanseNull($review->getProjectId()).', ';
        $args .= PDOWrapper::cleanseNull($review->getTaskId()).', ';
        $args .= PDOWrapper::cleanseNull($review->getUserId()).', ';
        $args .= PDOWrapper::cleanseNull($review->getCorrections()).', ';
        $args .= PDOWrapper::cleanseNull($review->getGrammar()).', ';
        $args .= PDOWrapper::cleanseNull($review->getSpelling()).', ';
        $args .= PDOWrapper::cleanseNull($review->getConsistency()).', ';
        $args .= PDOWrapper::cleanseNullOrWrapStr($review->getComment());

        $result = PDOWrapper::call('submitTaskReview', $args);
        if ($result) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }

    public static function getTaskReviews($params)
    {
        $args = "";

        if (isset($params['project_id'])) {
            $args .= PDOWrapper::cleanseNull($params['project_id']);
        } else {
            $args .= 'null, ';
        }
        if (isset($params['task_id'])) {
            $args .= PDOWrapper::cleanseNull($params['task_id']).", ";
        } else {
            $args .= "null, ";
        }
        if (isset($params['user_id'])) {
            $args .= PDOWrapper::cleanseNull($params['user_id']).", ";
        } else {
            $args .= "null, ";
        }
        if (isset($params['corrections'])) {
            $args .= PDOWrapper::cleanseNull($params['corrections']).", ";
        } else {
            $args .= "null, ";
        }
        if (isset($params['grammar'])) {
            $args .= PDOWrapper::cleanseNull($params['grammar']).", ";
        } else {
            $args .= "null, ";
        }
        if (isset($params['spelling'])) {
            $args .= PDOWrapper::cleanseNull($params['spelling']).", ";
        } else {
            $args .= "null, ";
        }
        if (isset($params['consistency'])) {
            $args .= PDOWrapper::cleanseNull($params['consistency']).", ";
        } else {
            $args .= "null, ";
        }
        if (isset($params['comment']) && $params['comment'] != '') {
            $args .= PDOWrapper::cleanseNullOrWrapStr($params['comment']).", ";
        } else {
            $args .= "null";
        }

        $reviews = NULL;
        $result = PDOWrapper::call("getTaskReviews", $args);
        if ($result) {
            $reviews = array();
            foreach ($result as $row) {
                $reviews[] = ModelFactory::buildModel('TaskReview', $row);
            }
        }

        return $reviews;
    }

    /*
     * Add an identicle entry with a different ID and target Language
     * Used for bulk uploads
     */
    public static function duplicateTaskForTarget($task, $languageCode, $countryCode, $userID)
    {
        //Get the file info for original task
        $task_file_info = self::getTaskFileInfo($task->getId());
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
        self::recordFileUpload($task->getId(), $task_file_info['filename'], $task_file_info['content-type'], $userID);
     
        $task_file_info['filename'] = '"'.$task_file_info['filename'].'"';

        //Get the new path the file can be found at
        $file_info = self::getTaskFileInfo($task);
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
        $sourceLocale = $task->getSourceLocale();
        $targetLocale = $task->getTargetLocale();
        $result= PDOWrapper::call("taskInsertAndUpdate", PDOWrapper::cleanseNull($task->getId())
                                                .",".PDOWrapper::cleanseNull($task->getProjectId())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($task->getTitle())
                                                .",".PDOWrapper::cleanseNull($task->getWordCount())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getLanguageCode())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($targetLocale->getLanguageCode())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($task->getCreatedTime())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($task->getComment())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getCountryCode())
                                                .",".PDOWrapper::cleanseNullOrWrapStr($targetLocale->getCountryCode())
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


    private static function insert(&$task)
    {
        $sourceLocale = $task->getSourceLocale();
        $targetLocale = $task->getTargetLocale();
        
        $result = PDOWrapper::call("taskInsertAndUpdate", "null"
            .",".PDOWrapper::cleanseNull($task->getProjectId())
            .",".PDOWrapper::cleanseNullOrWrapStr($task->getTitle())
            .",".PDOWrapper::cleanseNull($task->getWordCount())
            .",".PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getLanguageCode())
            .",".PDOWrapper::cleanseNullOrWrapStr($targetLocale->getLanguageCode())
            .",".PDOWrapper::cleanseNullOrWrapStr($task->getCreatedTime())
            .",".PDOWrapper::cleanseNullOrWrapStr($task->getComment())
            .",".PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getCountryCode())
            .",".PDOWrapper::cleanseNullOrWrapStr($targetLocale->getCountryCode())
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

    public static function getUserTopTasks($user_id, $limit, $filter=" and 1")
    {
        $ret = false;
        if ($result = PDOWrapper::call("getUserTopTasks", PDOWrapper::cleanse($user_id)
                                        .",".PDOWrapper::cleanseNullOrWrapStr($limit).
                                        ",'$filter'")) {

            $ret = array();
            foreach ($result as $row) {
                 $ret[] = ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }

        
    public static function getTasksWithTag($tag_id, $limit = 15)
    {
        $ret = null;
        if ($result= PDOWrapper::call("getTaggedTasks", PDOWrapper::cleanse($tag_id).",".PDOWrapper::cleanse($limit))) {
            $ret = array();
            foreach ($result as $row) {
                    $ret[] = ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }

    public static function moveToArchiveByID($taskId, $userId) 
    {
        $ret = false;
        $task = self::getTask($taskId);
        $task = $task[0];

        $graphBuilder = new APIWorkflowBuilder();
        $graph = $graphBuilder->buildProjectGraph($task->getProjectId());

        if ($graph) {

            $index = $graphBuilder->find($taskId, $graph);
            $node = $graph->getAllNodes($index);
            $ret = self::archiveTaskNode($node, $graph, $userId);
        }

        // UI is expecting output to be 0 or 1
        if ($ret) {
            $ret = 1;
        } else {
            $ret = 0;
        }

        return $ret;
    }


    public static function archiveTaskNode($node, $graph, $userId)
    {
        $ret = true;
        $task = self::getTask($node->getTaskId());
        $dependantNodes = $node->getNextList();
        if (count($dependantNodes) > 0) {
            $builder = new APIWorkflowBuilder();
            foreach ($dependantNodes as $dependantId) {
                $dTask = $this->find(array('id' => $dependantId));
                $index = $builder->find($dependantId, $graph);
                $dependant = $graph->getAllNodes($index);
                $preReqs = $dependant->getPreviousList();
                if ((count($preReqs) == 2 && $dTask->getTaskType() == TaskTypeEnum::DESEGMENTATION) ||
                        count($preReqs) == 1) {
                    $ret = $ret && (self::archiveTaskNode($dependant, $graph, $userId));
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
        self::delete($taskId);
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

        
        if ($result = PDOWrapper::call('getSubscribedUsers', "$task_id")) {
            foreach ($result as $row) {
               ModelFactory::buildModel("User", $row);
            }
        }

        return $ret;
    }

    /*
    * Check to see if a translation for this task has been uploaded before
    */
    public static function hasBeenUploaded($task_id, $user_id)
    {
        return self::checkTaskFileVersion($task_id, $user_id);
    }

    public static function getTaskStatus($task_id)
    {
        if (self::checkTaskFileVersion($task_id)) {
            return "Your translation is under review";
        } else {
            return "Awaiting your translation";
        }
    }
 
    public static function downloadTask($taskID, $version = 0)
    {
        $task = self::getTask($taskID);
        $task=$task[0];

        if (!is_object($task)) {
            header('HTTP/1.0 500 Not Found');
            die;
        }
        
        $task_file_info = self::getTaskFileInfo($taskID, $version);

        if (empty($task_file_info)) {
            throw new Exception("Task file info not set for.");
        }

        $absolute_file_path = Upload::absoluteFilePathForUpload($task, $version, $task_file_info['filename']);
        $file_content_type = $task_file_info['content-type'];
        //self::logFileDownload($task, $version);
        IO::downloadFile($absolute_file_path, $file_content_type);
    }
    
    public static function downloadConvertedTask($taskID, $version = 0)
    {
        $task = self::getTask($taskID);

        if (!is_object($task)) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        
        $task_file_info = self::getTaskFileInfo($taskID, $version);

        if (empty($task_file_info)) {
            throw new Exception("Task file info not set for.");
        }

        $absolute_file_path = Upload::absoluteFilePathForUpload($task, $version, $task_file_info['filename']);
        $file_content_type = $task_file_info['content-type'];
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
    
    public static function checkTaskFileVersion($task_id, $user_id = null)
    {
        $result = PDOWrapper::call("getLatestFileVersion", PDOWrapper::cleanse($task_id)
                                    .",".PDOWrapper::cleanseNull($user_id));
        return $result[0]['latest_version'] > 0;
    }
    
    public static function recordFileUpload($taskId, $filename, $content_type, $user_id) 
    {
        $args = "";
        $args .= PDOWrapper::cleanseNull($taskId);
        $args .= ",".PDOWrapper::cleanseWrapStr($filename);
        $args .= ",".PDOWrapper::cleanseWrapStr($content_type);
        $args .= ",".PDOWrapper::cleanseNull($user_id);
        if($result = PDOWrapper::call("recordFileUpload", $args)) {
            return $result[0]['version'];
        } else {
            return null;
        }        
    }
    
    public static function getTaskFileInfo($taskID, $version = 0)
    {
        $ret = false;
        if ($r = PDOWrapper::call("getTaskFileMetaData", PDOWrapper::cleanse($taskID)
                                                    .",".PDOWrapper::cleanse($version)
                                                    .",null, null, null, null")) {
            $file_info = array();
            foreach ($r[0] as $key => $value) {
                if (!is_numeric($key)) {
                    $file_info[$key] = $value;
                }
            }
            $ret = $file_info;
        }
        return $ret;
    }
    
    public static function getFilename($taskId, $version)
    {
        if ($r = PDOWrapper::call("getTaskFileMetaData", PDOWrapper::cleanse($taskId)
                                                    .",".PDOWrapper::cleanse($version)
                                                    .",null, null, null, null")) {
            return $r[0]['filename'];
        } else {
            return null;			
        }
    }  

    public static function getLatestFileVersion($task_id, $user_id=null)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getLatestFileVersion", PDOWrapper::cleanse($task_id)
                                    .",".PDOWrapper::cleanseNull($user_id))) {
            if (is_numeric($result[0]['latest_version'])) {
                $ret = intval($result[0]['latest_version']);
            }
        }
        return $ret;
    }
    
    public static function uploadFile($task,$convert,&$file,$version,$userId,$filename)
    {
        Notify::sendEmailNotifications($task->getId(), NotificationTypes::UPLOAD);
            
        if($convert){
            Upload::apiSaveFile($task, $userId, 
            FormatConverter::convertFromXliff($file), $filename,$version);
        }else{
            //touch this and you will die painfully sinisterly sean :)
            Upload::apiSaveFile($task, $userId, $file, $filename,$version);
        }
    }
    
    public static function uploadOutputFile($task,$convert,&$file,$userId,$filename){
        self::uploadFile($task,$convert,$file,null,$userId,$filename);
        $graphBuilder = new APIWorkflowBuilder();
            $graph = $graphBuilder->buildProjectGraph($task->getProjectId());
            if ($graph->hasRootNode()) {
                $currentLayer = $graph->getRootNodeList();
                $nextLayer = array();
                $found = false;

                $dependants = array();
                while(!$found && count($currentLayer) > 0) {
                    foreach ($currentLayer as $node) {
                        if ($node->getTaskId() == $task->getId()) {
                            $found = true;
                            foreach ($node->getNextList() as $nextNode) {
                                $dependants[] = $nextNode->getTaskId();
                            }
                        }
                        foreach ($node->getNextList() as $nextNode) {
                            if(!in_array($nextNode, $nextLayer)) {
                                $nextLayer[] = $nextNode;
                            }
                        }
                    }
                    $currentLayer = $nextLayer;
                    $nextLayer = array();
                }

                foreach ($dependants as $nextTask) {
                    $dTask = TaskDao::getTask($nextTask);
                    self::uploadFile($dTask ,$convert,$file,0,$userId,$filename);
                }
            }
    }
    
}
