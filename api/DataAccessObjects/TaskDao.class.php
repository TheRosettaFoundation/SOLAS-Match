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

require_once __DIR__."/../../Common/protobufs/models/Task.php";
require_once __DIR__."/../../Common/lib/SolasMatchException.php";
require_once __DIR__."/../lib/PDOWrapper.class.php";
require_once __DIR__."/../lib/Notify.class.php";
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
        $task = self::getTasks($taskId);
        $task = $task[0];
        if (is_null($task)) return 0;

        $subscribedUsers = self::getSubscribedUsers($taskId);

        $result = Lib\PDOWrapper::call('archiveTask', Lib\PDOWrapper::cleanseNull($taskId) . ',' . Lib\PDOWrapper::cleanseNull($userId));
        if ($result[0]['result']) self::delete($taskId);

        Lib\Notify::sendTaskArchivedNotifications($taskId, $subscribedUsers);

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
        if (!empty($userFeedback)) $userFeedback = substr($userFeedback, 0, 4096);

        $args = Lib\PDOWrapper::cleanse($taskId).", ".
            Lib\PDOWrapper::cleanse($userId).", ".
            Lib\PDOWrapper::cleanseNullOrWrapStr($userFeedback);
        if ($revokeByAdmin) {
            $args .= ", 1";
        } else {
            $args .= ", 0";
        }

        error_log("call unClaimTask($args)");

        $memsource_task = self::get_memsource_task($taskId);
        if ($memsource_task) {
            $ret = Lib\PDOWrapper::call('unClaimTaskMemsource', $args);

            $task = self::getTask($taskId);
            $task_type_details = Lib\PDOWrapper::call('get_task_type_details', '');
            $enum_to_UI = [];
            foreach ($task_type_details as $task_type_detail) {
                $enum_to_UI[$task_type_detail['type_enum']] = $task_type_detail;
            }
            if ($enum_to_UI[$task->getTaskType()]['shell_task']) {
                $result = $ret[0]['result'];
                error_log("result (Shell): $result");
                return $result;
            }

          if ($task->getTaskType() != Common\Enums\TaskTypeEnum::SPOT_QUALITY_INSPECTION && $task->getTaskType() != Common\Enums\TaskTypeEnum::QUALITY_EVALUATION) {
            $project_tasks = self::get_tasks_for_project($task->getProjectId());

            $top_level = self::get_top_level($memsource_task['internalId']);
            // Remove any Deny List for this $userId for this top level 'internalId' for other 'workflowLevel's
            foreach ($project_tasks as $dependent_task) {
                if ($top_level == self::get_top_level($dependent_task['internalId']) && $dependent_task['task-type_id'] != Common\Enums\TaskTypeEnum::SPOT_QUALITY_INSPECTION && $dependent_task['task-type_id'] != Common\Enums\TaskTypeEnum::QUALITY_EVALUATION) {
                    if ($memsource_task['workflowLevel'] != $dependent_task['workflowLevel']) {
                        error_log("Removing $userId from Deny List for {$dependent_task['id']} {$dependent_task['internalId']}");
                        self::removeUserFromTaskBlacklist($userId, $dependent_task['id']);
                    }
                }
            }

            // Reapply Deny List for this $userId claimed tasks for this top level 'internalId' for other 'workflowLevel's
            foreach ($project_tasks as $claimed_task) { // Potential tasks that might have been claimed by $userId
                if ($top_level == self::get_top_level($claimed_task['internalId'])) {
                    if ($claimed_task['workflowLevel'] == $memsource_task['workflowLevel']) { // Only add back Deny if claimed 'workflowLevel' is same as unclaimed task
                        if (self::hasUserClaimedTask($userId, $claimed_task['id'])) {
                            foreach ($project_tasks as $dependent_task) {
                                if ($top_level == self::get_top_level($dependent_task['internalId'])) {
                                    if ($claimed_task['workflowLevel'] != $dependent_task['workflowLevel']) { // Not same workflowLevel
                                        if ( $claimed_task['task-type_id'] == Common\Enums\TaskTypeEnum::TRANSLATION ||
                                            ($claimed_task['task-type_id'] == Common\Enums\TaskTypeEnum::PROOFREADING && $dependent_task['task-type_id'] == Common\Enums\TaskTypeEnum::TRANSLATION)) {
                                            if (($claimed_task['beginIndex'] <= $dependent_task['endIndex']) && ($dependent_task['beginIndex'] <= $claimed_task['endIndex'])) { // Overlap
                                                error_log("Reapplying $userId to Deny List for {$dependent_task['id']} {$dependent_task['internalId']}");
                                                self::addUserToTaskBlacklist($userId, $dependent_task['id']);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
          }

            $memsource_project = self::get_memsource_project($task->getProjectId());
            $url = 'https://cloud.memsource.com/web/api2/v1/projects/' . $memsource_project['memsource_project_uid'] . '/jobs/' . $memsource_task['memsource_task_uid'];
            $ch = curl_init($url);
            $deadline = $task->getDeadline();
            $data = ['status' => 'NEW', 'dateDue' => substr($deadline, 0, 10) . 'T' . substr($deadline, 11, 8) . 'Z'];
            $payload = json_encode($data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $authorization = 'Authorization: Bearer ' . Common\Lib\Settings::get('memsource.memsource_api_token');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $result = curl_exec($ch);
            curl_close($ch);
        } else {
            $ret = Lib\PDOWrapper::call('unClaimTask', $args);
        }
        $result = $ret[0]['result'];
        error_log("result: $result");
        return $ret[0]['result'];
    }

    public static function decrypt_to_verify_integrity($data)
    {
        $data = hex2bin($data);
        $iv = substr($data, -16);
        return openssl_decrypt(substr($data, 0, -18), 'aes-256-cbc', base64_decode(Common\Lib\Settings::get('badge.key')), 0, $iv);
    }

    public static function get_memsource_task($task_id)
    {
        $result = Lib\PDOWrapper::call('get_memsource_task', Lib\PDOWrapper::cleanse($task_id));

        if (empty($result)) return 0;

        return $result[0];
    }

    public static function get_tasks_for_project($project_id)
    {
        $result = Lib\PDOWrapper::call('get_tasks_for_project', Lib\PDOWrapper::cleanse($project_id));
        if (empty($result)) return [];
        $tasks = [];
        foreach ($result as $row) {
            $tasks[$row['memsource_task_uid']] = $row;
        }
        return $tasks;
    }

    public static function get_top_level($id)
    {
        $pos = strpos($id, '.');
        if ($pos === false) return $id;
        return substr($id, 0, $pos);
    }

    public static function removeUserFromTaskBlacklist($user_id, $task_id)
    {
        Lib\PDOWrapper::call('removeUserFromTaskBlacklist', Lib\PDOWrapper::cleanse($user_id) . ',' . Lib\PDOWrapper::cleanse($task_id));
    }

    public static function addUserToTaskBlacklist($user_id, $task_id)
    {
        Lib\PDOWrapper::call('addUserToTaskBlacklist', Lib\PDOWrapper::cleanse($user_id) . ',' . Lib\PDOWrapper::cleanse($task_id));
    }

    public static function get_memsource_project($project_id)
    {
        $result = Lib\PDOWrapper::call('get_memsource_project', Lib\PDOWrapper::cleanse($project_id));

        if (empty($result)) return 0;

        return $result[0];
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
error_log("call recordFileUpload($args)");
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

    public static function get_queue_claim_tasks()
    {
        $result = Lib\PDOWrapper::call('get_queue_claim_tasks', '');
        if (!empty($result)) {
            return $result;
        } else {
            return [];
        }
    }

    public static function dequeue_claim_task($task_id)
    {
        Lib\PDOWrapper::call('dequeue_claim_task', Lib\PDOWrapper::cleanse($task_id));
    }
}
