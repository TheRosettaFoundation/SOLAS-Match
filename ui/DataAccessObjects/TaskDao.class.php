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

    public function getTask($id)
    {
        $request = "{$this->siteApi}v0/tasks/$id";
        $response =$this->client->castCall(array("Task"), $request);
        return $response;
    }
    
    public function getTasks()
    {
        $request = "{$this->siteApi}v0/tasks";
        $response =$this->client->castCall(array("Task"), $request);
        return $response;
    }
    

    public function getTaskPreReqs($taskId)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/prerequisites";
        $response =$this->client->castCall(array("Task"), $request);
        return $response;
    }

    public function getTopTasks($limit = null)
    {
        $request = "{$this->siteApi}v0/tasks/top_tasks";
        $args=$limit ? array("limit" => $limit) : null;
        $response =$this->client->castCall(array("Task"), $request,HTTP_Request2::METHOD_GET, null, $args);
        return $response;
    }

    public function getTaskTags($taskId)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/tags";
        $args=$limit ? array("limit" => $limit) : null;
        $response =$this->client->castCall(array("Tag"), $request,HTTP_Request2::METHOD_GET, null, $args);
        return $response;
    }

    // this is wrong fix
    public function getTaskFile($taskId, $version = 0, $convertToXliff = false)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/file";
        $args=array("version" => $version,"convertToXliff"=>$convertToXliff);
        $response =$this->client->castCall(null, $request,HTTP_Request2::METHOD_GET, null, $args);
        return $response;
    }

    public function getTaskVersion($taskId)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/version";
        $response =$this->client->castCall(null, $request);
        return $response;
    }

    public function getTaskInfo($taskId, $version = 0)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/info";
        $args = array("version" => $version);
        $response =$this->client->castCall("TaskMetaData", $request,HTTP_Request2::METHOD_GET, null, $args);
        return $response;
    }

    public function isTaskClaimed($taskId, $userId = null)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/claimed";
        $args=$userId ? array("userID" => $userId) : null;
        $response =$this->client->castCall(null, $request,HTTP_Request2::METHOD_GET, null, $args);
        return $response;
    }

    public function getUserClaimedTask($taskId)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/user";
        $response =$this->client->castCall("User", $request);
        return $response;
    }

    public function createTask($task)
    {
        $request = "{$this->siteApi}v0/tasks";
        $response =$this->client->castCall("Task", $request,HTTP_Request2::METHOD_POST, $task);
        return $response;
    }

    public function updateTask($task)
    {
        $request = "{$this->siteApi}v0/tasks/{$task->getId()}";
        $response =$this->client->castCall("Task", $request,HTTP_Request2::METHOD_PUT, $task);
        return $response;
    }

    public function deleteTask($taskId)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId";
        $response =$this->client->castCall(null, $request, HTTP_Request2::METHOD_DELETE);
    }

    public function addTaskPreReq($taskId, $preReqId)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/prerequisites/$preReqId";
        $response =$this->client->castCall(null, $request, HTTP_Request2::METHOD_PUT);
    }

    public function removeTaskPreReq($taskId, $preReqId)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/prerequisites/$preReqId";
        $this->client->call($request, HTTP_Request2::METHOD_DELETE);
    }

    public function archiveTask($taskId, $userId)
    {
        $request = "{$this->siteApi}v0/tasks/archiveTask/$taskId/user/$userId";
        $response = $this->client->call($request, HTTP_Request2::METHOD_PUT);
        return $response;
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

        $response = $this->client->call($request, HTTP_Request2::METHOD_PUT, null, $args,$fileData);
    }

    public function uploadOutputFile($taskId, $userId, $fileData, $convert = false)
    {
        $request = "{$this->siteApi}v0/tasks/uploadOutputFile/$taskId/$userId";

        $args = null;
        if ($convert) {
            $args= array('convertFromXliff' => $convert);
        }

        $this->client->call($request, HTTP_Request2::METHOD_PUT, null, $args,$fileData);
    }
}
