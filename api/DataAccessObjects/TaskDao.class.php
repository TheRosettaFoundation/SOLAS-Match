<?php

require_once __DIR__."/../../Common/Requests/UserTaskScoreRequest.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";
require_once __DIR__."/../../Common/models/Task.php";
require_once __DIR__."/../../api/lib/Upload.class.php";
require_once __DIR__."/../lib/Notify.class.php";
require_once __DIR__."/../lib/NotificationTypes.class.php";
require_once __DIR__."/../lib/APIWorkflowBuilder.class.php";
require_once __DIR__."/../lib/Upload.class.php";
include_once __DIR__."/../../Common/lib/SolasMatchException.php";


/**
 * Task Document Access Object for manipulating tasks.
 *
 * @package default
 * @author eoin.oconchuir@ul.ie
 **/

class TaskDao
{
    public static function getTask(
        $taskId = null,
        $projectId = null,
        $title = null,
        $wordCount = null,
        $sourceLanguageCode = null,
        $targetLanguageCode = null,
        $createdTime = null,
        $sourceCountryCode = null,
        $targetCountryCode = null,
        $comment = null,
        $taskTypeId = null,
        $taskStatusId = null,
        $published = null,
        $deadline = null
    ) {
        $tasks = array();
        $args = PDOWrapper::cleanseNull($taskId)
                .",".PDOWrapper::cleanseNull($projectId)
                .",".PDOWrapper::cleanseNullOrWrapStr($title)
                .",".PDOWrapper::cleanseNull($wordCount)
                .",".PDOWrapper::cleanseNullOrWrapStr($sourceLanguageCode)
                .",".PDOWrapper::cleanseNullOrWrapStr($targetLanguageCode)
                .",".PDOWrapper::cleanseNullOrWrapStr($createdTime)
                .",".PDOWrapper::cleanseNullOrWrapStr($sourceCountryCode)
                .",".PDOWrapper::cleanseNullOrWrapStr($targetCountryCode)
                .",".PDOWrapper::cleanseNullOrWrapStr($comment)
                .",".PDOWrapper::cleanseNull($taskTypeId)
                .",".PDOWrapper::cleanseNull($taskStatusId)
                .",".PDOWrapper::cleanseNull($published)
                .",".PDOWrapper::cleanseNullOrWrapStr($deadline);
        $result = PDOWrapper::call("getTask", $args);
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
     * Save task object to database (either insert or update)
     * @return Task
     * @author 
     **/
    public static function save(&$task)
    {
        if (is_null($task->getId())) {
            self::insert($task);
            self::calculateTaskScore($task->getId());
        } else {
            self::update($task);
            self::calculateTaskScore($task->getId());
        }
        return $task;
    }

    public static function submitReview($review)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($review->getProjectId())
                .",".PDOWrapper::cleanseNull($review->getTaskId())
                .",".PDOWrapper::cleanseNull($review->getUserId())
                .",".PDOWrapper::cleanseNull($review->getCorrections())
                .",".PDOWrapper::cleanseNull($review->getGrammar())
                .",".PDOWrapper::cleanseNull($review->getSpelling())
                .",".PDOWrapper::cleanseNull($review->getConsistency())
                .",".PDOWrapper::cleanseNullOrWrapStr($review->getComment());
        $result = PDOWrapper::call('submitTaskReview', $args);
        if ($result) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }

    public static function getTaskReviews(
        $projectId = null,
        $taskId = null,
        $userId = null,
        $corrections = null,
        $grammar = null,
        $spelling = null,
        $consistency = null,
        $comment = null
    ) {
        $args = PDOWrapper::cleanseNull($projectId)
                .",".PDOWrapper::cleanseNull($taskId)
                .",".PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNull($corrections)
                .",".PDOWrapper::cleanseNull($grammar)
                .",".PDOWrapper::cleanseNull($spelling)
                .",".PDOWrapper::cleanseNull($consistency)
                .",".PDOWrapper::cleanseNullOrWrapStr($comment);
        $reviews = null;
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
    public static function duplicateTaskForTarget($task, $languageCode, $countryCode, $userId)
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
        self::recordFileUpload($task->getId(), $task_file_info['filename'], $task_file_info['content-type'], $userId);
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
        $args = PDOWrapper::cleanseNull($task->getId())
                .",".PDOWrapper::cleanseNull($task->getProjectId())
                .",".PDOWrapper::cleanseNullOrWrapStr($task->getTitle())
                .",".PDOWrapper::cleanseNull($task->getWordCount())
                .",".PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getLanguageCode())
                .",".PDOWrapper::cleanseNullOrWrapStr($targetLocale->getLanguageCode())
                .",".PDOWrapper::cleanseNullOrWrapStr($task->getComment())
                .",".PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getCountryCode())
                .",".PDOWrapper::cleanseNullOrWrapStr($targetLocale->getCountryCode())
                .",".PDOWrapper::cleanseNullOrWrapStr($task->getDeadline())
                .",".PDOWrapper::cleanseNull($task->getTaskType())
                .",".PDOWrapper::cleanseNull($task->getTaskStatus())
                .",".PDOWrapper::cleanse($task->getPublished());
        $result= PDOWrapper::call("taskInsertAndUpdate", $args);
        if ($result) {
            $task = ModelFactory::buildModel('Task', $result);
        } else {
            return null;
        }
    }
    
    public static function delete($TaskId)
    {
        $args = PDOWrapper::cleanseNull($TaskId);
        $result= PDOWrapper::call("deleteTask", $args);
        return $result[0]["result"];
    }

    private static function calculateTaskScore($taskId)
    {
        $use_backend = Settings::get('site.backend');
        if (strcasecmp($use_backend, "y") == 0) {
            $mMessagingClient = new MessagingClient();
            if ($mMessagingClient->init()) {
                $request = new UserTaskScoreRequest();
                $request->setTaskId($taskId);
                $message = $mMessagingClient->createMessageFromProto($request);
                $mMessagingClient->sendTopicMessage(
                    $message,
                    $mMessagingClient->MainExchange,
                    $mMessagingClient->TaskScoreTopic
                );
            } else {
                echo "Failed to Initialize messaging client";
            }
        } else {
            //use the python script
            $exec_path = __DIR__."/../scripts/calculate_scores.py $taskId";
            echo shell_exec($exec_path . "> /dev/null 2>/dev/null &");
        }
    }
    
    public static function getTags($taskId)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($taskId);
        
        if ($result = PDOWrapper::call("getTaskTags", $args)) {
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
        $args = "null"
                .",".PDOWrapper::cleanseNull($task->getProjectId())
                .",".PDOWrapper::cleanseNullOrWrapStr($task->getTitle())
                .",".PDOWrapper::cleanseNull($task->getWordCount())
                .",".PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getLanguageCode())
                .",".PDOWrapper::cleanseNullOrWrapStr($targetLocale->getLanguageCode())
                .",".PDOWrapper::cleanseNullOrWrapStr($task->getComment())
                .",".PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getCountryCode())
                .",".PDOWrapper::cleanseNullOrWrapStr($targetLocale->getCountryCode())
                .",".PDOWrapper::cleanseNullOrWrapStr($task->getDeadline())
                .",".PDOWrapper::cleanseNull($task->getTaskType())
                .",".PDOWrapper::cleanseNull($task->getTaskStatus())
                .",".PDOWrapper::cleanseNull($task->getPublished());
        $result = PDOWrapper::call("taskInsertAndUpdate", $args);
        if ($result) {
            $task = ModelFactory::buildModel("Task", $result[0]);
        } else {
            $task = null;
        }
    }

    public static function getTaskPreReqs($taskId)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($taskId);
        if ($result = PDOWrapper::call("getTaskPreReqs", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }
    
    public static function getTasksFromPreReq($taskId, $projectId)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($taskId).",".PDOWrapper::cleanseNull($projectId);
        if ($result = PDOWrapper::call("getTasksFromPreReq", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }

    public static function addTaskPreReq($taskId, $preReqId)
    {
        $args = PDOWrapper::cleanseNull($taskId)
                .",".PDOWrapper::cleanseNull($preReqId);
        
        $result = PDOWrapper::call("addTaskPreReq", $args);
        return $result[0]["result"];
    }

    public static function removeTaskPreReq($taskId, $preReqId)
    {
        $args = PDOWrapper::cleanseNull($taskId)
                .",".PDOWrapper::cleanseNull($preReqId);
        
        $result = PDOWrapper::call("removeTaskPreReq", $args);
        return $result[0]["result"];
    }

    public static function getLatestAvailableTasks($limit = 15, $offset = 0)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($limit).', '.
                PDOWrapper::cleanseNull($offset);
        
        if ($r = PDOWrapper::call("getLatestAvailableTasks", $args)) {
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
    public static function getUserTopTasks(
        $userId,
        $strict,
        $limit,
        $offset,
        $taskType,
        $sourceLanguageCode,
        $targetLanguageCode
    ) {
        $ret = false;
        $args = PDOWrapper::cleanse($userId).", ";

        if ($strict) {
            $args .= "1, ";
        } else {
            $args .= "0, ";
        }
        
        $args .= PDOWrapper::cleanseNullOrWrapStr($limit).', '.
                PDOWrapper::cleanseNull($offset).', ';
        
        $args .=  PDOWrapper::cleanseNullOrWrapStr($taskType).', ';
        $args .=  PDOWrapper::cleanseNullOrWrapStr($sourceLanguageCode).', ';
        $args .=  PDOWrapper::cleanseNullOrWrapStr($targetLanguageCode);

        if ($result = PDOWrapper::call("getUserTopTasks", $args)) {

            $ret = array();
            foreach ($result as $row) {
                 $ret[] = ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }

        
    public static function getTasksWithTag($tagId, $limit = 15)
    {
        $ret = null;
        $args = PDOWrapper::cleanse($tagId)
                .",".PDOWrapper::cleanse($limit);
                
        if ($result= PDOWrapper::call("getTaggedTasks", $args)) {
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


    private static function archiveTaskNode($node, $graph, $userId)
    {
        $ret = true;
        $task = self::getTask($node->getTaskId());
        $dependantNodes = $node->getNextList();
        if (count($dependantNodes) > 0) {
            $builder = new APIWorkflowBuilder();
            foreach ($dependantNodes as $dependantId) {
                $dTask = self::getTask($dependantId);
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
            $subscribedUsers = self::getSubscribedUsers($node->getTaskId());
            $ret = self::archiveTask($node->getTaskId(), $userId);
            Notify::sendTaskArchivedNotifications($node->getTaskId(), $subscribedUsers);
        }
        

        return $ret;
    }

    private static function archiveTask($taskId, $userId)
    {
        $args = PDOWrapper::cleanseNull($taskId)
                .",".PDOWrapper::cleanseNull($userId);
        
        $result = PDOWrapper::call("archiveTask", $args);
        if ($result[0]['result']) {
            self::delete($taskId);
        }
        return $result[0]['result'];
    }
        
    public static function claimTask($taskId, $userId)
    {
        $args = PDOWrapper::cleanse($taskId)
                .",".PDOWrapper::cleanse($userId);
        
        $ret = PDOWrapper::call("claimTask", $args);
        return $ret[0]['result'];
    }
    
    public static function unClaimTask($taskId, $userId)
    {
        $args = PDOWrapper::cleanse($taskId)
                .",".PDOWrapper::cleanse($userId);
        
        $ret = PDOWrapper::call("unClaimTask", $args);
        return $ret[0]['result'];
    }

    public static function hasUserClaimedTask($userId, $taskId)
    {
        $args = PDOWrapper::cleanse($taskId)
                .",".PDOWrapper::cleanse($userId);
        $result = PDOWrapper::call("hasUserClaimedTask", $args);
        return $result[0]['result'];
    }

    public static function hasUserClaimedSegmentationTask($userId, $projectId)
    {
        $args = PDOWrapper::cleanse($userId)
            .",".PDOWrapper::cleanse($projectId);
        $result = PDOWrapper::call("hasUserClaimedSegmentationTask", $args);
        return $result[0]['result'];
    }
    
    public static function taskIsClaimed($taskId)
    {
        $args = PDOWrapper::cleanse($taskId);
        $result =  PDOWrapper::call("taskIsClaimed", $args);
        return $result[0]['result'];
    }
    
    public static function getUserTasks($userId, $limit = null, $offset = 0)
    {
        $args = PDOWrapper::cleanse($userId).
                ', '.PDOWrapper::cleanseNull($limit).
                ', '.PDOWrapper::cleanseNull($offset);
        $result = PDOWrapper::call("getUserTasks", $args);
        if ($result) {
            $tasks = array();
            foreach ($result as $taskData) {
                $tasks[] = ModelFactory::buildModel("Task", $taskData);
            }
            return $tasks;
        } else {
            return null;
        }
    }

    public static function getUserTasksCount($userId)
    {
        $args = PDOWrapper::cleanse($userId);
        $result = PDOWrapper::call("getUserTasksCount", $args);
        return $result[0]['result'];
    }
    
    public static function getUserArchivedTasks($userId, $limit = 10)
    {
        $args = PDOWrapper::cleanse($userId)
                .",".PDOWrapper::cleanse($limit);
        $result = PDOWrapper::call("getUserArchivedTasks", $args);
        if ($result) {
            $tasks = array();
            foreach ($result as $taskData) {
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
    public static function getSubscribedUsers($taskId)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($taskId);
        if ($result = PDOWrapper::call('getSubscribedUsers', $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("User", $row);
            }
        }
        return $ret;
    }

    /*
    * Check to see if a translation for this task has been uploaded before
    */
    public static function hasBeenUploaded($taskId, $userId)
    {
        return self::checkTaskFileVersion($taskId, $userId);
    }

    public static function downloadTask($taskId, $version = 0)
    {
        $task = self::getTask($taskId);
        $task=$task[0];
        if (!is_object($task)) {
            header('HTTP/1.0 500 Not Found');
            die;
        }
        $task_file_info = self::getTaskFileInfo($taskId, $version);
        if (empty($task_file_info)) {
            throw new Exception("Task file info not set for.");
        }

        $absolute_file_path = Upload::absoluteFilePathForUpload($task, $version, $task_file_info['filename']);
        $file_content_type = $task_file_info['content-type'];
        IO::downloadFile($absolute_file_path, $file_content_type);
    }
    
    public static function downloadConvertedTask($taskId, $version = 0)
    {
        $task = self::getTask($taskId);

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
        IO::downloadConvertedFile($absolute_file_path, $file_content_type, $taskID);
    }
    
    public static function getUserClaimedTask($taskId)
    {
        $ret = null;
        $args = PDOWrapper::cleanse($taskId);
        if ($result = PDOWrapper::call('getUserClaimedTask', $args)) {
            $ret = ModelFactory::buildModel("User", $result[0]);
        }
        return $ret;
    }
    
    public static function checkTaskFileVersion($taskId, $userId = null)
    {
        $args = PDOWrapper::cleanse($taskId)
                .",".PDOWrapper::cleanseNull($userId);
        $result = PDOWrapper::call("getLatestFileVersion", $args);
        return $result[0]['latest_version'] > 0;
    }
    
    public static function recordFileUpload($taskId, $filename, $content_type, $userId, $version = null)
    {
        $args = PDOWrapper::cleanseNull($taskId)
                .",".PDOWrapper::cleanseWrapStr($filename)
                .",".PDOWrapper::cleanseWrapStr($content_type)
                .",".PDOWrapper::cleanseNull($userId)
                .','.PDOWrapper::cleanseNull($version);
        if ($result = PDOWrapper::call("recordFileUpload", $args)) {
            return $result[0]['version'];
        } else {
            return null;
        }
    }

    public static function getTaskFileInfo($taskId, $version = 0)
    {
        $ret = false;
        $args = PDOWrapper::cleanse($taskId)
                .",".PDOWrapper::cleanse($version)
                .",null, null, null, null";
        if ($r = PDOWrapper::call("getTaskFileMetaData", $args)) {
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
        $args = PDOWrapper::cleanse($taskId)
                .",".PDOWrapper::cleanse($version)
                .",null, null, null, null";
        if ($r = PDOWrapper::call("getTaskFileMetaData", $args)) {
            return $r[0]['filename'];
        } else {
            return null;
        }
    }

    public static function getLatestFileVersion($taskId, $userId = null)
    {
        $args = PDOWrapper::cleanse($taskId)
                .",".PDOWrapper::cleanseNull($userId);
        $ret = null;
        if ($result = PDOWrapper::call("getLatestFileVersion", $args)) {
            if (is_numeric($result[0]['latest_version'])) {
                $ret = intval($result[0]['latest_version']);
            }
        }
        return $ret;
    }
    
    public static function uploadFile($task, $convert, &$file, $version, $userId, $filename)
    {
        $success = null;
        if ($convert) {
            $success = Upload::apiSaveFile(
                $task,
                $userId,
                FormatConverter::convertFromXliff($file),
                $filename,
                $version
            );
        } else {
            //touch this and you will die painfully sinisterly sean :)
            $success = Upload::apiSaveFile($task, $userId, $file, $filename, $version);
        }
        if (!$success) {
            throw new SolasMatchException("Failed to write file data.", HttpStatusEnum::INTERNAL_SERVER_ERROR);
        }
    }
    
    public static function uploadOutputFile($task, $convert, &$file, $userId, $filename)
    {
        self::uploadFile($task, $convert, $file, null, $userId, $filename);
        $graphBuilder = new APIWorkflowBuilder();
        $graph = $graphBuilder->buildProjectGraph($task->getProjectId());
        if ($graph) {
            $index = $graphBuilder->find($task->getId(), $graph);
            $taskNode = $graph->getAllNodes($index);
            foreach ($taskNode->getNextList() as $nextTaskId) {
                $result = TaskDao::getTask($nextTaskId);
                $nextTask = $result[0];
                self::uploadFile($nextTask, $convert, $file, 0, $userId, $filename);
            }
        }
    }
    
    public static function getClaimedTime($taskId)
    {
        $ret = null;
        $args = PDOWrapper::cleanse($taskId);
        if ($result = PDOWrapper::call('getTaskClaimedTime', $args)) {
            $ret = $result[0]['result'];
        }
        return $ret;
    }
}
