<?php

class TaskDao
{
    public function getTask($params)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tasks";
        
        $id = null;
        if (isset($params['id'])) {
            $id = $params['id'];
            $request = "$request/$id";
        }
        
        $response = $client->call($request);
        $ret = $client->cast(array("Task"), $response);
        
        if (!is_null($id) && is_array($ret)) {
            $ret = $ret[0];
        }
        
        return $ret;
    }

    public function getTaskPreReqs($taskId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tasks/$taskId/prerequisites";
        $ret = $client->call($request);
        return $ret;
    }

    public function getTopTasks($limit = null)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tasks/top_tasks";

        $args = null;
        if ($limit) {
            $args = array("limit" => $limit);
        }

        $response = $client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        $ret = $client->cast(array("Task"), $response);
        return $ret;
    }

    public function getTaskTags($taskId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tasks/$taskId/tags";
        $response = $client->call($request);
        $ret = $client->cast(array("Tag"), $response);
        return $ret;
    }

    public function getTaskFile($taskId, $version = 0, $convertToXliff = false)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tasks/$taskId/tags";

        $args = array("version" => $version);
        if ($convertToXliff) {
            $args['convertToXliff'] = $convertToXliff;
        }
        
        $response = $client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        $ret = $client->cast(array("Tag"), $response);
        return $ret;
    }

    public function getTaskVersion($taskId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tasks/$taskId/version";
        $ret = $client->call($request);
        return $ret;
    }

    public function getTaskInfo($taskId, $version = 0)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tasks/$taskId/info";
        $args = array("version" => $version);
        $response = $client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        $ret = $client->cast(array("TaskMetaData"), $response);
        return $ret;
    }

    public function getClaimedTasks($taskId, $userId = null)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tasks/$taskId/claimed";

        $args = null;
        if (!is_null($userId)) {
            $args = array("userID" => $userId);
        }

        $response = $client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        $ret = $client->cast(array("Task"), $response);
        return $ret;
    }

    public function getUserClaimedTask($taskId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tasks/$taskId/user";
        $response = $client->call($request);
        $ret = $client->cast(array("User"), $response);
        return $ret;
    }

    public function createTask($task)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tasks";
        $response = $client->call($request, HTTP_Request2::METHOD_POST, $task);
        $ret = $client->cast("Task", $response);
        return $ret;
    }

    public function updateTask($task)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tasks/{$task->getId()}";
        $response = $client->call($request, HTTP_Request2::METHOD_PUT, $task);
        $ret = $client->cast("Task", $response);
        return $ret;
    }

    public function deleteTask($taskId)
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tasks/$taskId";
        $client->call($request, HTTP_Request2::METHOD_DELETE);
    }

    public function addTaskPreReq($taskId, $preReqId)
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tasks/$taskId/prerequisites/$preReqId";
        $client->call($request, HTTP_Request2::METHOD_PUT);
    }

    public function removeTaskPreReq($taskId, $preReqId)
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tasks/$taskId/prerequisites/$preReqId";
        $client->call($request, HTTP_Request2::METHOD_DELETE);
    }

    public function archiveTask($taskId, $userId)
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tasks/archiveTask/$taskId/user/$userId";
        $client->call($request, HTTP_Request2::METHOD_PUT);
    }

    public function setTaskTags($task)
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tasks/$taskId/tags";
        $client->call($request, HTTP_Request2::METHOD_PUT, $task);
    }

    public function sendFeedback($feedbackData)
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tasks/{$feedbackData->getTaskId()}/feedback";
        $client->call($request, HTTP_Request2::METHOD_PUT, $feedbackData);
    }

    public function saveTaskFile($taskId, $filename, $userId, $fileData, $version = 0, $convert = false)
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tasks/$taskId/file/$filename/$userId";

        $args = array("version" => $version);
        if ($convert) {
            $args['convertFromXliff'] = $convert;
        }

        $client->call($request, HTTP_Request2::METHOD_PUT, $fileData, $args);
    }

    public function uploadOutputFile($taskId, $filename, $userId, $fileData, $convert = false)
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tasks/uploadOutputFile$taskId/$filename/$userId";

        $args = null;
        if ($convert) {
            $args= array('convertFromXliff' => $convert);
        }

        $client->call($request, HTTP_Request2::METHOD_PUT, $fileData, $args);
    }
}
