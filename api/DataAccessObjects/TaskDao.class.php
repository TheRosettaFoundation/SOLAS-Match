<?php

namespace SolasMatch\API\DAO;

use \SolasMatch\Common as Common;
use \SolasMatch\API\Lib as Lib;

require_once __DIR__."/../../Common/protobufs/Requests/UserTaskScoreRequest.php";
require_once __DIR__."/../../Common/protobufs/models/Task.php";
include_once __DIR__."/../../Common/lib/SolasMatchException.php";
require_once __DIR__."/../lib/PDOWrapper.class.php";
require_once __DIR__."/../lib/Notify.class.php";
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
    /**
     * Gets a single task by its id
     * @param The id of a task
     * @return Task
     * @author Tadhg O'Flaherty
     **/
    public static function getTask($taskId)
    {
        $tasks = array();
        $args = PDOWrapper::cleanseNull($taskId)
                .","."null"
                .","."null"
                .","."null"
                .","."null"
                .","."null"
                .","."null"
                .","."null"
                .","."null"
                .","."null"
                .","."null"
                .","."null"
                .","."null"
                .","."null";
        $result = PDOWrapper::call("getTask", $args);
        if ($result) {
            foreach ($result as $row) {
                $tasks[] = ModelFactory::buildModel("Task", $row);
            }
        }
        if (sizeof($tasks) == 0) {
            $tasks=null;
        }
        return $tasks[0];
    }

    public static function getTasks(
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
        $args = Lib\PDOWrapper::cleanseNull($taskId).",".
            Lib\PDOWrapper::cleanseNull($projectId).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($title).",".
            Lib\PDOWrapper::cleanseNull($wordCount).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($sourceLanguageCode).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($targetLanguageCode).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($createdTime).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($sourceCountryCode).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($targetCountryCode).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($comment).",".
            Lib\PDOWrapper::cleanseNull($taskTypeId).",".
            Lib\PDOWrapper::cleanseNull($taskStatusId).",".
            Lib\PDOWrapper::cleanseNull($published).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($deadline);
        $result = Lib\PDOWrapper::call("getTask", $args);
        if ($result) {
            foreach ($result as $row) {
                $tasks[] = Common\Lib\ModelFactory::buildModel("Task", $row);
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
        $args = Lib\PDOWrapper::cleanseNull($review->getProjectId()).",".
            Lib\PDOWrapper::cleanseNull($review->getTaskId()).",".
            Lib\PDOWrapper::cleanseNull($review->getUserId()).",".
            Lib\PDOWrapper::cleanseNull($review->getCorrections()).",".
            Lib\PDOWrapper::cleanseNull($review->getGrammar()).",".
            Lib\PDOWrapper::cleanseNull($review->getSpelling()).",".
            Lib\PDOWrapper::cleanseNull($review->getConsistency()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($review->getComment());
        $result = Lib\PDOWrapper::call('submitTaskReview', $args);
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
        $args = Lib\PDOWrapper::cleanseNull($projectId).",".
            Lib\PDOWrapper::cleanseNull($taskId).",".
            Lib\PDOWrapper::cleanseNull($userId).",".
            Lib\PDOWrapper::cleanseNull($corrections).",".
            Lib\PDOWrapper::cleanseNull($grammar).",".
            Lib\PDOWrapper::cleanseNull($spelling).",".
            Lib\PDOWrapper::cleanseNull($consistency).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($comment);
        $reviews = null;
        $result = Lib\PDOWrapper::call("getTaskReviews", $args);
        if ($result) {
            $reviews = array();
            foreach ($result as $row) {
                $reviews[] = Common\Lib\ModelFactory::buildModel('TaskReview', $row);
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
        $old_file_path = Lib\Upload::absoluteFilePathForUpload($task, 0, $task_file_info['filename']);
        
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
        $new_file_path = Lib\Upload::absoluteFilePathForUpload($task, 0, $file_info['filename']);
        Lib\Upload::createFolderPath($task);
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
        $args = Lib\PDOWrapper::cleanseNull($task->getId()).",".
            Lib\PDOWrapper::cleanseNull($task->getProjectId()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($task->getTitle()).",".
            Lib\PDOWrapper::cleanseNull($task->getWordCount()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getLanguageCode()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($targetLocale->getLanguageCode()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($task->getComment()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getCountryCode()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($targetLocale->getCountryCode()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($task->getDeadline()).",".
            Lib\PDOWrapper::cleanseNull($task->getTaskType()).",".
            Lib\PDOWrapper::cleanseNull($task->getTaskStatus()).",".
            Lib\PDOWrapper::cleanse($task->getPublished());
        $result = Lib\PDOWrapper::call("taskInsertAndUpdate", $args);
        if ($result) {
            $task = Common\Lib\ModelFactory::buildModel('Task', $result);
        } else {
            return null;
        }
    }
    
    public static function delete($TaskId)
    {
        $args = Lib\PDOWrapper::cleanseNull($TaskId);
        $result = Lib\PDOWrapper::call("deleteTask", $args);
        return $result[0]["result"];
    }

    private static function calculateTaskScore($taskId)
    {
        $use_backend = Common\Lib\Settings::get('site.backend');
        if (strcasecmp($use_backend, "y") == 0) {
            $mMessagingClient = new Lib\MessagingClient();
            if ($mMessagingClient->init()) {
                $request = new Common\Protobufs\Requests\UserTaskScoreRequest();
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
        $args = Lib\PDOWrapper::cleanseNull($taskId);
        
        if ($result = Lib\PDOWrapper::call("getTaskTags", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Tag", $row);
            }
        }
        return $ret;
    }

    private static function insert(&$task)
    {
        $sourceLocale = $task->getSourceLocale();
        $targetLocale = $task->getTargetLocale();
        $args = "null ,".
            Lib\PDOWrapper::cleanseNull($task->getProjectId()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($task->getTitle()).",".
            Lib\PDOWrapper::cleanseNull($task->getWordCount()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getLanguageCode()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($targetLocale->getLanguageCode()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($task->getComment()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getCountryCode()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($targetLocale->getCountryCode()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($task->getDeadline()).",".
            Lib\PDOWrapper::cleanseNull($task->getTaskType()).",".
            Lib\PDOWrapper::cleanseNull($task->getTaskStatus()).",".
            Lib\PDOWrapper::cleanseNull($task->getPublished());
        $result = Lib\PDOWrapper::call("taskInsertAndUpdate", $args);
        if ($result) {
            $task = Common\Lib\ModelFactory::buildModel("Task", $result[0]);
        } else {
            $task = null;
        }
    }

    public static function getTaskPreReqs($taskId)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($taskId);
        if ($result = Lib\PDOWrapper::call("getTaskPreReqs", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }
    
    public static function getTasksFromPreReq($taskId, $projectId)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($taskId).",".Lib\PDOWrapper::cleanseNull($projectId);
        if ($result = Lib\PDOWrapper::call("getTasksFromPreReq", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }

    public static function addTaskPreReq($taskId, $preReqId)
    {
        $args = Lib\PDOWrapper::cleanseNull($taskId).",".
            Lib\PDOWrapper::cleanseNull($preReqId);
        $result = Lib\PDOWrapper::call("addTaskPreReq", $args);
        return $result[0]["result"];
    }

    public static function removeTaskPreReq($taskId, $preReqId)
    {
        $args = Lib\PDOWrapper::cleanseNull($taskId).",".
            Lib\PDOWrapper::cleanseNull($preReqId);
        $result = Lib\PDOWrapper::call("removeTaskPreReq", $args);
        return $result[0]["result"];
    }

    public static function getLatestAvailableTasks($limit = 15, $offset = 0)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($limit).', '.
                Lib\PDOWrapper::cleanseNull($offset);
        
        if ($r = Lib\PDOWrapper::call("getLatestAvailableTasks", $args)) {
            $ret = array();
            foreach ($r as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Task", $row);
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
        $args = Lib\PDOWrapper::cleanse($userId).", ";

        if ($strict) {
            $args .= "1, ";
        } else {
            $args .= "0, ";
        }
        
        $args .= Lib\PDOWrapper::cleanseNullOrWrapStr($limit).', '.
                Lib\PDOWrapper::cleanseNull($offset).', ';
        
        $args .=  Lib\PDOWrapper::cleanseNullOrWrapStr($taskType).', ';
        $args .=  Lib\PDOWrapper::cleanseNullOrWrapStr($sourceLanguageCode).', ';
        $args .=  Lib\PDOWrapper::cleanseNullOrWrapStr($targetLanguageCode);

        if ($result = Lib\PDOWrapper::call("getUserTopTasks", $args)) {

            $ret = array();
            foreach ($result as $row) {
                 $ret[] = Common\Lib\ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }

        
    public static function getTasksWithTag($tagId, $limit = 15)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanse($tagId).",".
            Lib\PDOWrapper::cleanse($limit);
                
        if ($result = Lib\PDOWrapper::call("getTaggedTasks", $args)) {
            $ret = array();
            foreach ($result as $row) {
                    $ret[] = Common\Lib\ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }

    public static function moveToArchiveByID($taskId, $userId)
    {
        $ret = false;
        $task = self::getTasks($taskId);
        $task = $task[0];
        
        if(is_null($task)) {
            return 0 ;
        }

        $graphBuilder = new Lib\APIWorkflowBuilder();
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
        $task = self::getTasks($node->getTaskId());
        $dependantNodes = $node->getNextList();
        if (count($dependantNodes) > 0) {
            $builder = new Lib\APIWorkflowBuilder();
            foreach ($dependantNodes as $dependantId) {
                $dTask = self::getTasks($dependantId);
                $index = $builder->find($dependantId, $graph);
                $dependant = $graph->getAllNodes($index);
                $preReqs = $dependant->getPreviousList();
                if ((count($preReqs) == 2 && $dTask->getTaskType() == Common\Enums\TaskTypeEnum::DESEGMENTATION) ||
                        count($preReqs) == 1) {
                    $ret = $ret && (self::archiveTaskNode($dependant, $graph, $userId));
                }
            }
        }

        if ($ret) {
            $subscribedUsers = self::getSubscribedUsers($node->getTaskId());
            $ret = self::archiveTask($node->getTaskId(), $userId);
            Lib\Notify::sendTaskArchivedNotifications($node->getTaskId(), $subscribedUsers);
        }

        return $ret;
    }

    private static function archiveTask($taskId, $userId)
    {
        $args = Lib\PDOWrapper::cleanseNull($taskId).",".
            Lib\PDOWrapper::cleanseNull($userId);
        
        $result = Lib\PDOWrapper::call("archiveTask", $args);
        if ($result[0]['result']) {
            self::delete($taskId);
        }
        return $result[0]['result'];
    }
        
    public static function claimTask($taskId, $userId)
    {
        $args = Lib\PDOWrapper::cleanse($taskId).",".
            Lib\PDOWrapper::cleanse($userId);
        $ret = Lib\PDOWrapper::call("claimTask", $args);
        return $ret[0]['result'];
    }
    
    public static function unClaimTask($taskId, $userId)
    {
        $args = Lib\PDOWrapper::cleanse($taskId).",".
            Lib\PDOWrapper::cleanse($userId);
        $ret = Lib\PDOWrapper::call("unClaimTask", $args);
        return $ret[0]['result'];
    }

    public static function hasUserClaimedTask($userId, $taskId)
    {
        $args = Lib\PDOWrapper::cleanse($taskId).",".
            Lib\PDOWrapper::cleanse($userId);
        $result = Lib\PDOWrapper::call("hasUserClaimedTask", $args);
        return $result[0]['result'];
    }

    public static function hasUserClaimedSegmentationTask($userId, $projectId)
    {
        $args = Lib\PDOWrapper::cleanse($userId).",".
            Lib\PDOWrapper::cleanse($projectId);
        $result = Lib\PDOWrapper::call("hasUserClaimedSegmentationTask", $args);
        return $result[0]['result'];
    }
    
    public static function taskIsClaimed($taskId)
    {
        $args = Lib\PDOWrapper::cleanse($taskId);
        $result = Lib\PDOWrapper::call("taskIsClaimed", $args);
        return $result[0]['result'];
    }
    
    public static function getUserTasks($userId, $limit = null, $offset = 0)
    {
        $args = Lib\PDOWrapper::cleanse($userId).', '.
            Lib\PDOWrapper::cleanseNull($limit).', '.
            Lib\PDOWrapper::cleanseNull($offset);
        $result = Lib\PDOWrapper::call("getUserTasks", $args);
        if ($result) {
            $tasks = array();
            foreach ($result as $taskData) {
                $tasks[] = Common\Lib\ModelFactory::buildModel("Task", $taskData);
            }
            return $tasks;
        } else {
            return null;
        }
    }

    public static function getUserTasksCount($userId)
    {
        $args = Lib\PDOWrapper::cleanse($userId);
        $result = Lib\PDOWrapper::call("getUserTasksCount", $args);
        return $result[0]['result'];
    }
    
    public static function getUserArchivedTasks($userId, $limit = 10)
    {
        $args = Lib\PDOWrapper::cleanse($userId).",".
            Lib\PDOWrapper::cleanse($limit);
        $result = Lib\PDOWrapper::call("getUserArchivedTasks", $args);
        if ($result) {
            $tasks = array();
            foreach ($result as $taskData) {
                $tasks[] = Common\Lib\ModelFactory::buildModel("ArchivedTask", $taskData);
            }
            return $tasks;
        } else {
            return null;
        }
    }
    
    public static function getArchivedTaskMetaData($taskId)
    {
        $return = null;
        $args = Lib\PDOWrapper::cleanse($taskId).", null, null, null, null, null, null, null, null, null";
        if ($r = Lib\PDOWrapper::call("getArchivedTaskMetaData", $args)) {
            $file_info = array();
            foreach ($r[0] as $key => $value) {
                if (!is_numeric($key)) {
                    $file_info[$key] = $value;
                }
            }
            $return = $file_info;
        }
        return $return;
    }

    /*
       Get User Notification List for this task
    */
    public static function getSubscribedUsers($taskId)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($taskId);
        if ($result = Lib\PDOWrapper::call('getSubscribedUsers', $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("User", $row);
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
        $task = self::getTasks($taskId);
        $task=$task[0];
        if (!is_object($task)) {
            header('HTTP/1.0 500 Not Found');
            die;
        }
        $task_file_info = self::getTaskFileInfo($taskId, $version);
        if (empty($task_file_info)) {
            throw new \Exception("Task file info not set for.");
        }

        $absolute_file_path = Lib\Upload::absoluteFilePathForUpload($task, $version, $task_file_info['filename']);
        $file_content_type = $task_file_info['content-type'];
        Lib\IO::downloadFile($absolute_file_path, $file_content_type);
    }
    
    public static function downloadConvertedTask($taskId, $version = 0)
    {
        $task = self::getTasks($taskId);

        if (!is_object($task)) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        
        $task_file_info = self::getTaskFileInfo($taskID, $version);

        if (empty($task_file_info)) {
            throw new \Exception("Task file info not set for.");
        }

        $absolute_file_path = Lib\Upload::absoluteFilePathForUpload($task, $version, $task_file_info['filename']);
        $file_content_type = $task_file_info['content-type'];
        Lib\IO::downloadConvertedFile($absolute_file_path, $file_content_type, $taskID);
    }
    
    public static function getUserClaimedTask($taskId)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanse($taskId);
        if ($result = Lib\PDOWrapper::call('getUserClaimedTask', $args)) {
            $ret = Common\Lib\ModelFactory::buildModel("User", $result[0]);
        }
        return $ret;
    }
    
    public static function checkTaskFileVersion($taskId, $userId = null)
    {
        $args = Lib\PDOWrapper::cleanse($taskId).", ".
            Lib\PDOWrapper::cleanseNull($userId);
        $result = Lib\PDOWrapper::call("getLatestFileVersion", $args);
        return $result[0]['latest_version'] > 0;
    }
    
    public static function recordFileUpload($taskId, $filename, $content_type, $userId, $version = null)
    {
        $args = Lib\PDOWrapper::cleanseNull($taskId).", ".
            Lib\PDOWrapper::cleanseWrapStr($filename).", ".
            Lib\PDOWrapper::cleanseWrapStr($content_type).", ".
            Lib\PDOWrapper::cleanseNull($userId).', '.
            Lib\PDOWrapper::cleanseNull($version);
        if ($result = Lib\PDOWrapper::call("recordFileUpload", $args)) {
            return $result[0]['version'];
        } else {
            return null;
        }
    }

    public static function getTaskFileInfo($taskId, $version = 0)
    {
        $ret = false;
        $args = Lib\PDOWrapper::cleanse($taskId).", ".
            Lib\PDOWrapper::cleanse($version).", null, null, null, null";
        if ($r = Lib\PDOWrapper::call("getTaskFileMetaData", $args)) {
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
        $args = Lib\PDOWrapper::cleanse($taskId).", ".
            Lib\PDOWrapper::cleanse($version).", null, null, null, null";
        if ($r = Lib\PDOWrapper::call("getTaskFileMetaData", $args)) {
            return $r[0]['filename'];
        } else {
            return null;
        }
    }

    public static function getLatestFileVersion($taskId, $userId = null)
    {
        $args = Lib\PDOWrapper::cleanse($taskId).",".
            Lib\PDOWrapper::cleanseNull($userId);
        $ret = null;
        if ($result = Lib\PDOWrapper::call("getLatestFileVersion", $args)) {
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
            $success = Lib\Upload::apiSaveFile(
                $task,
                $userId,
                Lib\FormatConverter::convertFromXliff($file),
                $filename,
                $version
            );
        } else {
            //touch this and you will die painfully sinisterly sean :)
            $success = Lib\Upload::apiSaveFile($task, $userId, $file, $filename, $version);
        }
        if (!$success) {
            throw new Common\Exceptions\SolasMatchException(
                "Failed to write file data.",
                Common\Enums\HttpStatusEnum::INTERNAL_SERVER_ERROR
            );
        }
    }
    
    public static function uploadOutputFile($task, $convert, &$file, $userId, $filename)
    {
        self::uploadFile($task, $convert, $file, null, $userId, $filename);
        $graphBuilder = new Lib\APIWorkflowBuilder();
        $graph = $graphBuilder->buildProjectGraph($task->getProjectId());
        if ($graph) {
            $index = $graphBuilder->find($task->getId(), $graph);
            $taskNode = $graph->getAllNodes($index);
            foreach ($taskNode->getNextList() as $nextTaskId) {
                $result = TaskDao::getTasks($nextTaskId);
                $nextTask = $result[0];
                self::uploadFile($nextTask, $convert, $file, 0, $userId, $filename);
            }
        }
    }
    
    public static function getClaimedTime($taskId)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanse($taskId);
        if ($result = Lib\PDOWrapper::call('getTaskClaimedTime', $args)) {
            $ret = $result[0]['result'];
        }
        return $ret;
    }
}
