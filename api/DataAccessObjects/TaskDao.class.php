<?php

namespace SolasMatch\API\DAO;

use \SolasMatch\Common as Common;
use \SolasMatch\API\Lib as Lib;

//! The Task Data access Object for the API
/*!
  A class for setting and retrieving Task related data from the database. Used by the API Route Handlers to supply
  info requested through the API and perform actions. All data is retrieved and input with direct access to the
  database using stored procedures.
*/

require_once __DIR__."/../../Common/protobufs/Requests/UserTaskScoreRequest.php";
require_once __DIR__."/../../Common/protobufs/models/Task.php";
require_once __DIR__."/../../Common/lib/SolasMatchException.php";
require_once __DIR__."/../lib/PDOWrapper.class.php";
require_once __DIR__."/../lib/Notify.class.php";
require_once __DIR__."/../lib/APIWorkflowBuilder.class.php";
require_once __DIR__."/../lib/Upload.class.php";

class TaskDao
{
    //! Retrieve a single Task from the database
    /*!
      Gets a single task by its id. If taskId is null then null is returned
      @param int $taskId is the id of a task
      @return Returns a single Task object
    */
    public static function getTask($taskId)
    {
        $task = null;
        if (!is_null($taskId)) {
            $args = Lib\PDOWrapper::cleanseNull($taskId).
                ", null, null, null, null, null, null, null, null, null, null, null, null, null";
            $result = Lib\PDOWrapper::call("getTask", $args);
            if ($result) {
                $task = Common\Lib\ModelFactory::buildModel("Task", $result[0]);
            }
        }
        return $task;
    }

    //! Retrieve Task objects from the database
    /*!
      Get a list of Task objects from the database. The list that is returned can be filtered by the input parameters.
      If null is passed for any of the input parameters it will be ignored. If null is passed for all parameters then
      every Task in the system will be returned.
      @param int $taskId is the id of the requested Task
      @param int $projectId is the id of the Project the requested Tasks belong to
      @param string $title is the title of the requested Task
      @param int $wordCount is the word count of the requested Task
      @param string $sourceLanguageCode is the language code for the source language of the requested Task.
      <b>Note:</b>This will be converted to a language id on the database
      @param string $targetLanguageCode is the language code for the target language of the requested Task
      <b>Note:</b>This will be converted to a language id on the database
      @param string $createTime is the date and time that the requested Task was created in the format
      "YYYY-MM-DD HH:MM:SS"
      @param string $sourceCountryCode is the country code for the source language of the requested Task
      <b>Note:</b>This will be converted to a country id on the database
      @param string $targetCountryCode is the country code for the target language of the requested Task
      <b>Note:</b>This will be converted to a country id on the database
      @param string $comment is the comment made by the Organisation when creating the requested Task
      @param TaskTypeEnum $taskTypeId is the type of the requested Task (e.g. translation, proofreading, etc.).
      @param TaskStatusEnum $taskStatusId is the status of the requested Task (e.g. in progress).
      @param int $published selects only published/unpublished tasks
      @param string $deadline is the deadline of the requested Task in the format "YYYY-MM-DD HH:MM:SS"
    */
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
    
    //! Save a Task to the database
    /*!
     Save a Task to the database. If the input Task does not have an id then a new Task will be created. If the input
     Task does have an id then it will update that Task in the database with its new values. This trigger a User Task
     score request for this Task.
     @param Task $task is the Task being saved to the database.
     @return Returns the updated/created Task object
    */
    public static function save($task)
    {
        if (is_null($task->getId())) {
            self::insert($task);
            // self::calculateTaskScore($task->getId()); // Not required see https://github.com/TheRosettaFoundation/SOLAS-Match/commit/ce6724ca50cd68eb1898156fd942237bdb5dddcf and https://github.com/TheRosettaFoundation/SOLAS-Match-Backend/commit/0130685a93246d8f6f82ac44041cd039c1879cd0
        } else {
            self::update($task);
            //commented out the following line which triggers task-score-calculation & email per every task update 
            //operation. This is to reduce the Disk Read/Write usage of the server by the PluginHandler. 
            
            //self::calculateTaskScore($task->getId());
        }
        return $task;
    }

    //! Submit a Review of a Task
    /*!
      Provide a Review for a Task. Reviews can be used to help understand the quality of a volunteers work as well as
      the quality of the source files uploaded by the Organisation.
      @param TaskReview $review is a model that holds the details of the Task Review
      @return Returns 1 if the review was submitted successfully, 0 otherwise
    */
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

    //! Retrieve TaskReview objects from the database
    /*!
      Retrieve a list of TaskReview objects. The resultant list can be filtered using the input arguments. If any of
      the input args are null then they will be ignored.
      @param int $projectId is the id of a Project
      @param int $taskId is the id of a Task
      @param int $userId is the id of a User
      @param int $corrections is a value between 1 and 5 where 1 is poor and 5 is good
      @param int $grammar is a value between 1 and 5 where 1 is poor and 5 is good
      @param int $spelling is a value between 1 and 5 where 1 is poor and 5 is good
      @param int $consistency is a value between 1 and 5 where 1 is poor and 5 is good
      @param String $comment is the comment related to the requested TaskReview
    */
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
    
    //! Delete a Task from the database
    /*!
      Permanently delete a Task from the database.
      @param int $taskId is the id of a Task
      @return Returns 1 if the Task with id $taskId was deleted successfully, 0 otherwise
    */
    public static function delete($taskId)
    {
        $args = Lib\PDOWrapper::cleanseNull($taskId);
        $result = Lib\PDOWrapper::call("deleteTask", $args);
        return $result[0]["result"];
    }

    // Send a message to the backend to calculate the score all users and one task
    private static function calculateTaskScore($taskId)
    {
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
    }
    
    //! Get the list of Tags associated with the specified Task
    /*!
      Get the list of Tags associated with the specified Task
      @param int $taskId is the id of a Task
      @return Returns a list of Tag objects
    */
    public static function getTags($taskId)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($taskId);
        $result = Lib\PDOWrapper::call("getTaskTags", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Tag", $row);
            }
        }
        return $ret;
    }

    // Insert a Task into the database (pass by reference so no return)
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
        error_log("TaskDAO::insert args: " . $args);
        $result = Lib\PDOWrapper::call("taskInsertAndUpdate", $args);
        if ($result) {
            $task = Common\Lib\ModelFactory::buildModel("Task", $result[0]);
            if (!empty($task)) {
                error_log("TaskDAO::insert id: " . $task->getId());
                if ($task->getPublished()) {
                    error_log("TaskDAO::insert published: True");
                } else {
                    error_log("TaskDAO::insert published: False");
                }
            }
        } else {
            error_log("TaskDAO::insert Failed");
            $task = null;
        }
    }
    
    public static function getAlsoViewedTasks(
            $taskId,
            $limit = null,
            $offset = 0
    ) {
        $ret = null;
        
        $args = Lib\PDOWrapper::cleanse($taskId).', ';
        $args .= Lib\PDOWrapper::cleanseNull($limit).', ';
        $args .= Lib\PDOWrapper::cleanse($offset);
        
        $result = Lib\PDOWrapper::call("alsoViewedTasks", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }

    public static function getTaskPreReqs($taskId)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($taskId);
        $result = Lib\PDOWrapper::call("getTaskPreReqs", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }
    
    //! Get a list of Tasks that have the specified Task/Project as a prereq
    /*!
      If a valid taskId is passed then it will get all Tasks that have the specified Task as a prerequisite. If a valid
      projectId is passed it will get all the root Tasks (all Tasks with no prereqs) for the specified Project. If they
      are both valid it will select based on the $taskId.
      @param int $taskId is the id of a Task
      @param int $projectId is the id of a Project
      @return Returns an array of Task objects
    */
    public static function getTasksFromPreReq($taskId, $projectId)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($taskId).",".Lib\PDOWrapper::cleanseNull($projectId);
        $result = Lib\PDOWrapper::call("getTasksFromPreReq", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }

    //! Add a prerequisite to a Task
    /*!
      Adds a prerequisite to a Task. A prerequisite is a Task that must be completed before any Tasks dependant on it
      can be claimed. This function will make the Task with id $taskId dependant on the Task with id $preReqId.
      @param int $taskId is the id of a Task
      @param int $preReqId is the id of a Task
      @return Returns 1 if the prereq was added successfully, 0 otherwise
    */
    public static function addTaskPreReq($taskId, $preReqId)
    {
        $args = Lib\PDOWrapper::cleanseNull($taskId).",".
            Lib\PDOWrapper::cleanseNull($preReqId);
        $result = Lib\PDOWrapper::call("addTaskPreReq", $args);
        return $result[0]["result"];
    }

    //! Remove a prerequisite from a Task
    /*!
      Remove a prerequisite from a Task. This function will remove the dependency the Task with id $taskId has on the
      Task with id $preReqId
      @param int $taskId is the id of a Task
      @param int $preReqId is the id of a Task
      @return Returns 1 if the prereq was removed successfully, 0 otherwise
    */
    public static function removeTaskPreReq($taskId, $preReqId)
    {
        $args = Lib\PDOWrapper::cleanseNull($taskId).",".
            Lib\PDOWrapper::cleanseNull($preReqId);
        $result = Lib\PDOWrapper::call("removeTaskPreReq", $args);
        return $result[0]["result"];
    }

    //! Get a list of the most recently created Tasks
    /*!
      Get a list of the most recently created Task objects. This will only return Task objects that are in the
      unclaimed state. A limit and an offset can be used to return all available Tasks in batches. The limit
      defaults to 15 and the offset defaults to 0.
      @param int $limit is the number of Task objects that will be returned
      @param int $offset is the index of the Task to start returning
      @return Returns an array of Task objects
    */
    public static function getLatestAvailableTasks($limit = 15, $offset = 0)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($limit).', '.
                Lib\PDOWrapper::cleanseNull($offset);
        $result = Lib\PDOWrapper::call("getLatestAvailableTasks", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }

    //! Get the count of most recently created Tasks
    /*!
      Get the count  of the most recently created Task objects. 
      @return Returns an integer
    */
    public static function getLatestAvailableTasksCount()
    {
        $result = Lib\PDOWrapper::call("getLatestAvailableTasksCount", "");
        if ($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }
    
    //! Get a list of Task objects ordered by their score for a given User
    /*!
      Get a list of the most relevant Tasks for a given User. The Task objects that are returned will be ordered by the
      score they received from the UserTaskScore algorithm. If strict mode is enabled then only Tasks that match the
      Users native and/or secondary languages for both their source and target will be returned. A limit and offset can
      be used to return the list in batches (started at offset return limit Tasks). The list of returned tasks can be
      further filtered by specifying a Task type, source Language or a target Language. These filter options will be
      ignored if they are null.
      @param int $userId is the id of a User
      @param bool $strict is true to enable strict mode, false to disable it
      @param int $limit is the max number of Tasks to be returned
      @param int $offset is the offset from which to start returning Tasks
      @param TaskTypeEnum $taskType is the Task type filter or null
      @param String $sourceLanguageCode is a Language code or null
      @param String $targetLanguageCode is a Language code or null
      @return Returns a list of Task objects
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
        $result = Lib\PDOWrapper::call("getUserTopTasks", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $row) {
                 $ret[] = Common\Lib\ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }
    
    //! Get the count of Task objects for a given User
    /*!
      Get the count of Task objects for a given User. If strict mode is enabled then only Tasks that match the
      Users native and/or secondary languages for both their source and target will be returned. 
      The list of returned tasks can be further filtered by specifying a Task type, source Language or a 
      target Language. These filter options will be ignored if they are null.
      @param int $userId is the id of a User
      @param bool $strict is true to enable strict mode, false to disable it
      @param TaskTypeEnum $taskType is the Task type filter or null
      @param String $sourceLanguageCode is a Language code or null
      @param String $targetLanguageCode is a Language code or null
      @return Returns the count of Task objects
    */
    public static function getUserTopTasksCount(
        $userId,
        $strict,
        $taskType,
        $sourceLanguageCode,
        $targetLanguageCode
    ) {
        $args = Lib\PDOWrapper::cleanse($userId).", ";

        if ($strict) {
            $args .= "1, ";
        } else {
            $args .= "0, ";
        }
        
        $args .=  Lib\PDOWrapper::cleanseNullOrWrapStr($taskType).', ';
        $args .=  Lib\PDOWrapper::cleanseNullOrWrapStr($sourceLanguageCode).', ';
        $args .=  Lib\PDOWrapper::cleanseNullOrWrapStr($targetLanguageCode);
        $result = Lib\PDOWrapper::call("getUserTopTasksCount", $args);
   
        if ($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
   
    }

    //! Get Tasks with the specified Tag
    /*!
      Return a list of Task objects where each Task returned has the specified Tag.
      @param int $tagId is the id of a Tag
      @param int $limit is the max number of Tags to return
      @return Returns an array of Task objects
    */
    public static function getTasksWithTag($tagId, $limit = 15)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanse($tagId).",".
            Lib\PDOWrapper::cleanse($limit);
        $result = Lib\PDOWrapper::call("getTaggedTasks", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $row) {
                    $ret[] = Common\Lib\ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }

    //! Archive a Task
    /*!
      Move a Task from the Tasks table to the ArchivedTasks table. This should only be done if the Task is complete
      and the Organisation has downloaded all the files they need. The userId is used to track which User archived
      the Task.
      @param int $taskId is the id of a Task
      @param int $userId is the id of a User
      @return Returns 1 if the Task was archived successfully, 0 otherwise
    */
    public static function moveToArchiveByID($taskId, $userId)
    {
        $ret = false;
        $task = self::getTasks($taskId);
        $task = $task[0];
        
        if (is_null($task)) {
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
        $dependantNodes = $node->getNext();
        if (count($dependantNodes) > 0) {
            $builder = new Lib\APIWorkflowBuilder();
            foreach ($dependantNodes as $dependantId) {
                $dTask = self::getTasks($dependantId);
                $index = $builder->find($dependantId, $graph);
                $dependant = $graph->getAllNodes($index);
                $preReqs = $dependant->getPrevious();
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

     //! Tracking Task Views
    /*!
      This API function is intended to record Task Views by Users. It records task views by users with a time-stamp.
      The information contained within the TaskViews table is primariliy intended for data-mining purposes 
      and improving the task score algorithm.
      @param int $taskId is the id of a Task
      @param int $userId is the id of a User
      @return Returns 1 on success, 0 otherwise
    */
    public static function recordTaskView($taskId, $userId)
    {
        $args = Lib\PDOWrapper::cleanseNull($taskId).",".
            Lib\PDOWrapper::cleanseNull($userId);
        
        $result = Lib\PDOWrapper::call("recordTaskView", $args);
        return $result[0]['result'];
    }
    
    //! Claim a Task for processing
    /*!
      A User claims a Task so they are the only ones working on it. It also allows them to upload new versions of the
      Task. Adds a row to the TaskClaims table. This alters the Task status so that it is in progress.
      @param int $taskId is the id of a Task
      @param int $userId is the id of a User
      @return Returns 1 on success, 0 otherwise
    */
    public static function claimTask($taskId, $userId)
    {
        $args = Lib\PDOWrapper::cleanse($taskId).",".
            Lib\PDOWrapper::cleanse($userId);
        $ret = Lib\PDOWrapper::call("claimTask", $args);
        return $ret[0]['result'];
    }
    
    //! Unclaim a Task
    /*!
      This unclaims a Task for a User and adds that User to the TaskTranslatorBlacklist so they can not claim it again.
      This could be called by an Organisation if they don't want a User to have claimed a Task or the User themselves
      if they find they can not complete the Task. This changes the Task status back to unclaimed.
      @param int $taskId is the id of a Task
      @param int $userId is the id of the User
      @return Returns 1 on success, 0 on failure
    */
    public static function unClaimTask($taskId, $userId, $userFeedback = null, $revokeByAdmin = false)
    {
        $args = Lib\PDOWrapper::cleanse($taskId).", ".
            Lib\PDOWrapper::cleanse($userId).", ".
            Lib\PDOWrapper::cleanseNullOrWrapStr($userFeedback);
        if ($revokeByAdmin) {
            $args .= ", 1";
        } else {
            $args .= ", 0";
        }

        error_log("call unClaimTask($args)");
        $ret = Lib\PDOWrapper::call("unClaimTask", $args);
        $result = $ret[0]['result'];
        error_log("result: $result");
        return $ret[0]['result'];
    }

    //! Determine if a User has claimed a Task
    /*!
      Determine if a User has claimed a Task
      @param int $userId is the id of a User
      @param int $taskId is the id of a Task
      @return Returns 1 if the specified User has claimed the Task, 0 otherwise
    */
    public static function hasUserClaimedTask($userId, $taskId)
    {
        $args = Lib\PDOWrapper::cleanse($taskId).",".
            Lib\PDOWrapper::cleanse($userId);
        $result = Lib\PDOWrapper::call("hasUserClaimedTask", $args);
        return $result[0]['result'];
    }

    //! Determine if a specified User has claimed a Segmentation Task for the specified Project
    /*!
      Determine if the User has claimed a Segmentation Task as part of the specified Project. This can be used to
      determine if the User should be able to create new Tasks for that project (even though they are not an Org
      member).
      @param int $userId is the id of a User
      @param int $projectId is the id of a Project
      @return Returns 1 if the User has claimed a segmentation Task in the specified Project, 0 otherwise
    */
    public static function hasUserClaimedSegmentationTask($userId, $projectId)
    {
        $args = Lib\PDOWrapper::cleanse($userId).",".
            Lib\PDOWrapper::cleanse($projectId);
        $result = Lib\PDOWrapper::call("hasUserClaimedSegmentationTask", $args);
        return $result[0]['result'];
    }
    
    //! Determine if a Task has already been claimed
    /*!
      Determine if a Task has already been claimed.
      @param int $taskId is the id of a Task
      @return Returns 1 if the Task is claimed, 0 otherwise
    */
    public static function taskIsClaimed($taskId)
    {
        $args = Lib\PDOWrapper::cleanse($taskId);
        $result = Lib\PDOWrapper::call("taskIsClaimed", $args);
        return $result[0]['result'];
    }
    
    //! Get a list of Tasks the User has claimed
    /*!
      Get a list of tasks claimed by the specified User. It is possible to return the Tasks in batches using the limit
      and offset parameters.
      @param int $limit is the max number of Tasks to be returned
      @param int offset is the offset to start at
      @return Returns an array of Task objects.
    */
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

    public static function getFilteredUserClaimedTasks(
            $userId,
            $orderBy,
            $limit = null,
            $offset = 0,
            $taskType = null,
            $taskStatus = null
    ) {
        $ret = null;
        
        $args = Lib\PDOWrapper::cleanse($userId).', ';
        $args .= Lib\PDOWrapper::cleanseNull($limit).', ';
        $args .= Lib\PDOWrapper::cleanse($offset).', ';
        $args .= Lib\PDOWrapper::cleanseNull($taskType).', ';
        $args .= Lib\PDOWrapper::cleanseNull($taskStatus).', ';
        $args .= Lib\PDOWrapper::cleanse($orderBy);
        
        $result = Lib\PDOWrapper::call("getFilteredUserClaimedTasks", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }

    public static function getFilteredUserClaimedTasksCount(
            $userId,
            $taskType = null,
            $taskStatus = null
    ) {
        $args  = Lib\PDOWrapper::cleanse($userId).', ';
        $args .= Lib\PDOWrapper::cleanseNull($taskType).', ';
        $args .= Lib\PDOWrapper::cleanseNull($taskStatus);
        $result = Lib\PDOWrapper::call("getFilteredUserClaimedTasksCount", $args);
        return $result[0]['result'];
    }
    
    public static function getUserRecentTasks(
            $userId,
            $limit = null,
            $offset = 0
    ) {
        $ret = null;
        
        $args = Lib\PDOWrapper::cleanse($userId).', ';
        $args .= Lib\PDOWrapper::cleanseNull($limit).', ';
        $args .= Lib\PDOWrapper::cleanse($offset);
        
        $result = Lib\PDOWrapper::call("getUserRecentTasks", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Task", $row);
            }
        }
        return $ret;
    }

    public static function getUserRecentTasksCount(
            $userId
    ) {
        $args  = Lib\PDOWrapper::cleanse($userId);
        $result = Lib\PDOWrapper::call("getUserRecentTasksCount", $args);
        return $result[0]['result'];
    }

    //! Get a count of the User's claimed Tasks
    /*!
      Get the number of Tasks a User has claimed
      @param int $userId is the id of a User
      @return Returns an int
    */
    public static function getUserTasksCount($userId)
    {
        $args = Lib\PDOWrapper::cleanse($userId);
        $result = Lib\PDOWrapper::call("getUserTasksCount", $args);
        return $result[0]['result'];
    }
    
    //! Get an array of ArchivedTasks that the User had claimed
    /*!
      Get the details of the ArchivedTasks the specified User had claimed. The total number of ArchivedTask objects
      that will be returned can be manipulated using the limit param.
      @param int $userId is the id of a User
      @param int $limit is the max number of ArchivedTasks to be returned
      @return Returns an array of ArchivedTask objects
    */
    public static function getUserArchivedTasks($userId, $limit = 10, $offset = 0)
    {
        $args = Lib\PDOWrapper::cleanse($userId).",".
                Lib\PDOWrapper::cleanse($limit).",".
                Lib\PDOWrapper::cleanse($offset);
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
    
    public static function getUserArchivedTasksCount($userId)
    {
        $args = Lib\PDOWrapper::cleanse($userId);
        $result = Lib\PDOWrapper::call("getUserArchivedTasksCount", $args);
        if ($result) {
            return $result[0]['count'];
        } else {
            return null;
        }
    }
    
    //! Get the list of Users that are subscribed to the specified Task
    /*
       Get the list of Organisation members that are tracking the specified Task. This is mostly used for
       notifications.
       @param int $taskId is the id of a Task
       @return Returns an array of User objects.
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

    //! Check if the specified User has uploaded a new version of a Task
    /*!
      Check if the specified User has uploaded an output file for a Task before.
      @param int $taskId is the id of a Task
      @param int $userId is the id of a User
      @return Returns 1 if the User has uploaded an output file, 0 otherwise
    */
    public static function hasBeenUploaded($taskId, $userId)
    {
        return self::checkTaskFileVersion($taskId, $userId);
    }
    
    //! Get the User that claimed the specified Task
    /*!
      Get the User that claimed the specified Task.
      @param int $taskId is the id of a Task
      @return Returns a User object or null
    */
    public static function getUserClaimedTask($taskId)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanse($taskId);
        if ($result = Lib\PDOWrapper::call('getUserClaimedTask', $args)) {
            $ret = Common\Lib\ModelFactory::buildModel("User", $result[0]);
        }
        return $ret;
    }
    
    //! Get the latest version number for a specified Task
    /*!
      Used to get the version number for the last uploaded file for this Task. If a valid task id is passed then it
      will find the latest version of that Task. If a valid user id is passed it will find the oldest version number
      for all the files they uploaded. If both are valid it will return the biggest version number for the specified
      Task that was uploaded by the specified User.
      @param int $taskId is the id of a Task
      @param int $userId is the id of a User
      @return Returns an int
    */
    public static function checkTaskFileVersion($taskId, $userId = null)
    {
        $args = Lib\PDOWrapper::cleanse($taskId).", ".
            Lib\PDOWrapper::cleanseNull($userId);
        $result = Lib\PDOWrapper::call("getLatestFileVersion", $args);
        return $result[0]['latest_version'] > 0;
    }
    
    //! Record a file upload
    /*!
      Used to save the details of a file to the DB. Previous versions can be overwritten by specifying the version. The
      version defaults to the latest version + 1.
      @param int $taskId is the id of a Task
      @param String $filename is the name of the uploaded file.
      @param String $content_type is the mime type for the file
      @param int $userId is the id of the User that is uploading the file
      @param int $version is the version of the file you are uploading.
      @return Returns the version of the file uploaded on success, null on failure
    */
    public static function recordFileUpload($taskId, $filename, $content_type, $userId, $version = null)
    {
        $args = Lib\PDOWrapper::cleanseNull($taskId).", ".
            Lib\PDOWrapper::cleanseWrapStr($filename).", ".
            Lib\PDOWrapper::cleanseWrapStr($content_type).", ".
            Lib\PDOWrapper::cleanseNull($userId).', '.
            Lib\PDOWrapper::cleanseNull($version);
        $result = Lib\PDOWrapper::call("recordFileUpload", $args);
        if ($result) {
            return $result[0]['version'];
        } else {
            return null;
        }
    }

    //! Retrieve Task file information
    /*!
      Get the details of a Task file. The version can be specified but defaults to 0.
      @param int $taskId is the if of a Task
      @param int $version is the version of the file being requested
      @return Returns an associative array in the form:
      {
        "task_id": <ID of the Task>,
        "version_id": <The version number for the file>,
        "filename": <The name of the file>,
        "content-type": <The mime type for the file>,
        "user_id": <The id of the User that uploaded the file>,
        "upload-time": <The date and time that the file was uploaded>
      }
    */
    public static function getTaskFileInfo($taskId, $version = 0)
    {
        $ret = false;
        $args = Lib\PDOWrapper::cleanse($taskId).", ".
            Lib\PDOWrapper::cleanse($version).", null, null, null, null";
        $result = Lib\PDOWrapper::call("getTaskFileMetaData", $args);
        if ($result) {
            $file_info = array();
            foreach ($result[0] as $key => $value) {
                if (!is_numeric($key)) {
                    $file_info[$key] = $value;
                }
            }
            $ret = $file_info;
        }
        return $ret;
    }
    
    //! Get the name of a Task file
    /*!
      Get the name of a specified Task at a specified version.
      @param int $taskId is the id of a Task
      @param int $version is the version of the Task file
      @return Returns a String
    */
    public static function getFilename($taskId, $version)
    {
        $args = Lib\PDOWrapper::cleanse($taskId).", ".
            Lib\PDOWrapper::cleanse($version).", null, null, null, null";
        $result = Lib\PDOWrapper::call("getTaskFileMetaData", $args);
        if ($result) {
            return $result[0]['filename'];
        } else {
            return null;
        }
    }

    //! Get the latest version number for a Task file
    /*!
      Get the latest file version for a specified Task. If a valid userId is passed then it will only get the latest
      version based on that User's uploads.
      @param int $taskId is the id of a Task
      @param int $userId is the id of a User or null
      @retrun Retuns an int or null
    */
    public static function getLatestFileVersion($taskId, $userId = null)
    {
        $args = Lib\PDOWrapper::cleanse($taskId).",".
            Lib\PDOWrapper::cleanseNull($userId);
        $ret = null;
        $result = Lib\PDOWrapper::call("getLatestFileVersion", $args);
        if ($result) {
            if (is_numeric($result[0]['latest_version'])) {
                $ret = intval($result[0]['latest_version']);
            }
        }
        return $ret;
    }
    
    
    
    
    
    //! Get the date and time at which the specified Task was claimed
    /*!
      Get the date and time at which the specified Task was claimed.
      @param int $taskId is the id of a Task
      @return Returns a String containing the datetime
    */
    public static function getClaimedTime($taskId)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanse($taskId);
        $result = Lib\PDOWrapper::call('getTaskClaimedTime', $args);
        if ($result) {
            $ret = $result[0]['result'];
        }
        return $ret;
    }
    
    //! Given a completed translation task, get the Proofread task
    /*!
       This function returns the proofread task corresponding to a 
       translation task. It will return the proofread task only if the status of the 
       proofread task is `Complete`. This function is used to display the download link of the proofread 
       file of Translation Tasks in the Claimed task page.
       @param int $taskId is the id of a (translation) Task
       @return Returns a Task object.
    */
    public static function getProofreadTask($taskId)
    {
        $task = null;
        if (!is_null($taskId)) {
            $args = Lib\PDOWrapper::cleanseNull($taskId);
            $result = Lib\PDOWrapper::call("getProofreadTask", $args);
            if ($result) {
                $task = Common\Lib\ModelFactory::buildModel("Task", $result[0]);
            }
        }
        return $task;    
    }
}
