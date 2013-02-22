<?php

require_once 'Common/lib/APIHelper.class.php';

class TaskDao
{
    private $client;
    private $siteApi;

    public function __construct()
    {
        $this->client = new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi = Settings::get("site.api");
    }

    public function getTask($params)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/tasks";
        
        $id = null;
        if (isset($params['id'])) {
            $id = $params['id'];
            $request = "$request/$id";
        }
        
        $response = $this->client->call($request);
        if (!is_null($id)) {
            $ret = $this->client->cast("Task", $response);
        } else {
            $ret = $this->client->cast(array("Task"), $response);
        }
        
        return $ret;
    }

    public function getTaskPreReqs($taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/tasks/$taskId/prerequisites";
        $ret = $this->client->call($request);
        return $ret;
    }

    public function getTopTasks($limit = null)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/tasks/top_tasks";

        $args = null;
        if ($limit) {
            $args = array("limit" => $limit);
        }

        $response = $this->client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        $ret = $this->client->cast(array("Task"), $response);
        return $ret;
    }

    public function getTaskTags($taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/tasks/$taskId/tags";
        $response = $this->client->call($request);
        $ret = $this->client->cast(array("Tag"), $response);
        return $ret;
    }

    public function getTaskFile($taskId, $version = 0, $convertToXliff = false)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/tasks/$taskId/tags";

        $args = array("version" => $version);
        if ($convertToXliff) {
            $args['convertToXliff'] = $convertToXliff;
        }
        
        $response = $this->client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        $ret = $this->client->cast(array("Tag"), $response);
        return $ret;
    }

    public function getTaskVersion($taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/tasks/$taskId/version";
        $ret = $this->client->call($request);
        return $ret;
    }

    public function getTaskInfo($taskId, $version = 0)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/tasks/$taskId/info";
        $args = array("version" => $version);
        $response = $this->client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        $ret = $this->client->cast("TaskMetaData", $response);
        return $ret;
    }

    public function isTaskClaimed($taskId, $userId = null)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/tasks/$taskId/claimed";

        $args = null;
        if (!is_null($userId)) {
            $args = array("userID" => $userId);
        }

        $ret = $this->client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        return $ret;
    }

    public function getUserClaimedTask($taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/tasks/$taskId/user";
        $response = $this->client->call($request);
        $ret = $this->client->cast("User", $response);
        return $ret;
    }

    public function createTask($task)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/tasks";
        $response = $this->client->call($request, HTTP_Request2::METHOD_POST, $task);
        $ret = $this->client->cast("Task", $response);
        return $ret;
    }

    public function updateTask($task)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/tasks/{$task->getId()}";
        $response = $this->client->call($request, HTTP_Request2::METHOD_PUT, $task);
        $ret = $this->client->cast("Task", $response);
        return $ret;
    }

    public function deleteTask($taskId)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId";
        $this->client->call($request, HTTP_Request2::METHOD_DELETE);
    }

    public function addTaskPreReq($taskId, $preReqId)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/prerequisites/$preReqId";
        $this->client->call($request, HTTP_Request2::METHOD_PUT);
    }

    public function removeTaskPreReq($taskId, $preReqId)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/prerequisites/$preReqId";
        $this->client->call($request, HTTP_Request2::METHOD_DELETE);
    }

    public function archiveTask($taskId, $userId)
    {
        $request = "{$this->siteApi}v0/tasks/archiveTask/$taskId/user/$userId";
        $this->client->call($request, HTTP_Request2::METHOD_PUT);
    }

    public function setTaskTags($task)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/tags";
        $this->client->call($request, HTTP_Request2::METHOD_PUT, $task);
    }

    public function sendFeedback($taskId, $userIds, $feedback)
    {
        $feedbackData = new FeedbackEmail();
        $feedbackData->setTaskId($taskId);
        if (is_array($userIds)) {
            foreach ($userIds as $userId) {
                $feedbackData->addUserId($userId);
            }
        } else {
            $feedbackData->addUserId($userIds);
        }
        $feedbackData->setFeedback($feedback);
        $request = "{$this->siteApi}v0/tasks/{$feedbackData->getTaskId()}/feedback";
        $this->client->call($request, HTTP_Request2::METHOD_PUT, $feedbackData);
    }

    public function saveTaskFile($taskId, $filename, $userId, $fileData, $version = null, $convert = false)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/file/$filename/$userId";

        $args = array();
        if ($version) {
            $args["version"] = $version;
        }
        if ($convert) {
            $args['convertFromXliff'] = $convert;
        }

        $response = $this->client->call($request, HTTP_Request2::METHOD_PUT, $fileData, $args);
    }

    public function uploadOutputFile($taskId, $filename, $userId, $fileData, $convert = false)
    {
        $request = "{$this->siteApi}v0/tasks/uploadOutputFile$taskId/$filename/$userId";

        $args = null;
        if ($convert) {
            $args= array('convertFromXliff' => $convert);
        }

        $this->client->call($request, HTTP_Request2::METHOD_PUT, $fileData, $args);
    }
}
