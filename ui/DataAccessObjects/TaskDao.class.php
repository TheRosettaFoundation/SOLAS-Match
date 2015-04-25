<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\Common as Common;
use \SolasMatch\UI\Lib as Lib;

require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/BaseDao.php";

class TaskDao extends BaseDao
{
    public function __construct()
    {
        $this->client = new Common\Lib\APIHelper(Common\Lib\Settings::get("ui.api_format"));
        $this->siteApi = Common\Lib\Settings::get("site.api");
    }

    public function getTask($id)
    {
        $request = "{$this->siteApi}v0/tasks/$id";
        $response =$this->client->call("\SolasMatch\Common\Protobufs\Models\Task", $request);
        return $response;
    }
    
    public function getTasks()
    {
        $request = "{$this->siteApi}v0/tasks";
        $response =$this->client->call(array("\SolasMatch\Common\Protobufs\Models\Task"), $request);
        return $response;
    }
    

    public function getTaskPreReqs($taskId)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/prerequisites";
        $response =$this->client->call(array("\SolasMatch\Common\Protobufs\Models\Task"), $request);
        return $response;
    }

    public function getProofreadTask($id)
    {
        $request = "{$this->siteApi}v0/tasks/proofreadTask/$id";
        $response =$this->client->call("\SolasMatch\Common\Protobufs\Models\Task", $request);
        return $response;
    }

    public function getTopTasks($limit = null, $offset = null)
    {
        $request = "{$this->siteApi}v0/tasks/topTasks";
        $args = array();
        if ($limit != null) {
            $args['limit'] = $limit;
        }
        if ($offset != null) {
            $args['offset'] = $offset;
        }
        $response = $this->client->call(
            array("\SolasMatch\Common\Protobufs\Models\Task"),
            $request,
            Common\Enums\HttpMethodEnum::GET,
            null,
            $args
        );
        return $response;
    }

    public function getTopTasksCount()
    {
        $request = "{$this->siteApi}v0/tasks/topTasksCount";
        $args = array();

        $response = $this->client->call(
            null,
            $request,
            Common\Enums\HttpMethodEnum::GET,
            null,
            $args
        );
        return $response;
    }

    public function getTaskTags($taskId, $limit = null)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/tags";
        $response = $this->client->call(
            array("\SolasMatch\Common\Protobufs\Models\Tag"),
            $request,
            Common\Enums\HttpMethodEnum::GET,
            null
        );
        return $response;
    }

    public function getTaskVersion($taskId)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/version";
        $response = intval($this->client->call(null, $request));
        return $response;
    }

    public function getTaskInfo($taskId, $version = 0)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/info";
        $args = array("version" => $version);
        $response = $this->client->call(
            "\SolasMatch\Common\Protobufs\Models\TaskMetadata",
            $request,
            Common\Enums\HttpMethodEnum::GET,
            null,
            $args
        );
        return $response;
    }

    public function isTaskClaimed($taskId, $userId = null)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/claimed";
        $args = $userId ? array("userID" => $userId) : null;
        $response = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::GET, null, $args);
        return $response;
    }

    public function getUserClaimedTask($taskId)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/user";
        $response = $this->client->call("\SolasMatch\Common\Protobufs\Models\User", $request);
        return $response;
    }

    public function getTaskReviews($taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/tasks/$taskId/reviews";
        $ret = $this->client->call(
            array("\SolasMatch\Common\Protobufs\Models\TaskReview"),
            $request
        );
        return $ret;
    }

    public function createTask($task)
    {
        $request = "{$this->siteApi}v0/tasks";
        $response = $this->client->call(
            "\SolasMatch\Common\Protobufs\Models\Task",
            $request,
            Common\Enums\HttpMethodEnum::POST,
            $task
        );
        return $response;
    }

    public function updateTask($task)
    {
        $request = "{$this->siteApi}v0/tasks/{$task->getId()}";
        $response = $this->client->call(
            "\SolasMatch\Common\Protobufs\Models\Task",
            $request,
            Common\Enums\HttpMethodEnum::PUT,
            $task
        );
        return $response;
    }

    public function deleteTask($taskId)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId";
        $response =$this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
    }

    public function addTaskPreReq($taskId, $preReqId)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/prerequisites/$preReqId";
        $response = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
        return $response;
    }

    public function removeTaskPreReq($taskId, $preReqId)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/prerequisites/$preReqId";
        $response = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
        return $response;
    }

    public function archiveTask($taskId, $userId)
    {
        $request = "{$this->siteApi}v0/tasks/archiveTask/$taskId/user/$userId";
        $response = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
        return $response;
    }

    public function recordTaskView($taskId, $userId)
    {
        $request = "{$this->siteApi}v0/tasks/recordView/$taskId/user/$userId";
        $response = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
        return $response;
    }
    
    public function setTaskTags($task)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId/tags";
        $response =$this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT, $task);
    }

    public function sendOrgFeedback($taskId, $userId, $claimantId, $feedback)
    {
        $feedbackData = new Common\Protobufs\Emails\OrgFeedback();
        $feedbackData->setTaskId($taskId);
        $feedbackData->setUserId($userId);
        $feedbackData->setClaimantId($claimantId);
        $feedbackData->setFeedback($feedback);
        $request = "{$this->siteApi}v0/tasks/{$feedbackData->getTaskId()}/orgFeedback";
        $response = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT, $feedbackData);
    }

    public function sendUserFeedback($taskId, $userId, $feedback)
    {
        $feedbackData = new Common\Protobufs\Emails\UserFeedback();
        $feedbackData->setTaskId($taskId);
        $feedbackData->setClaimantId($userId);
        $feedbackData->setFeedback($feedback);
        $request = "{$this->siteApi}v0/tasks/{$feedbackData->getTaskId()}/userFeedback";
        $response = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT, $feedbackData);
    }

    public function submitReview($review)
    {
        $request = "{$this->siteApi}v0/tasks/reviews";
        $response = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::POST, $review);
        return $response;
    }

    public function saveTaskFile($taskId, $userId, $fileData, $version = null, $convert = null)
    {
        $request = "{$this->siteApi}v0/io/upload/task/$taskId/$userId";
        $args = array();
        if (!is_null($version)) {
            $args["version"] = $version;
        }
        if (!is_null($convert)) {
            $args['convertFromXliff'] = $convert ? 1 : 0;
        }

        $response = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT, null, $args, $fileData);
        
        switch($this->client->getResponseCode()) {
            case Common\Enums\HttpStatusEnum::CREATED:
                return;
            case Common\Enums\HttpStatusEnum::BAD_REQUEST:
                $projectDao = new ProjectDao();
                $taskDao = new TaskDao();
                $task = $taskDao->getTask($taskId);
                $projectFile = $projectDao->getProjectFileInfo($task->getProjectId());
                $projectFileName = $projectFile->getFilename();
                $projectFileExtension = explode(".", $projectFileName);
                $projectFileExtension = $projectFileExtension[count($projectFileExtension)-1];
                $projectMime = $projectFile->getMime();
                throw new Common\Exceptions\SolasMatchException(
                    sprintf(
                        Lib\Localisation::getTranslation('common_error_upload_invalid_content'),
                        $projectFileExtension,
                        $projectMime
                    ),
                    $this->client->getResponseCode()
                );
                break;
            case Common\Enums\HttpStatusEnum::INTERNAL_SERVER_ERROR:
                throw new Common\Exceptions\SolasMatchException(
                    Lib\Localisation::getTranslation('common_error_upload_internal_server_error'),
                    $this->client->getResponseCode()
                );
                break;
        }
    }

    public function uploadOutputFile($taskId, $userId, $fileData, $convert = false)
    {
        $request = "{$this->siteApi}v0/io/upload/taskOutput/$taskId/$userId";

        $args = null;
        if ($convert) {
            $args= array('convertFromXliff' => $convert);
        }
        
        $response = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT, null, $args, $fileData);
    }
    
    public function getClaimedDate($taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/tasks/$taskId/timeClaimed";
        $ret = $this->client->call(null, $request);
        return $ret;
    }
    
    public function downloadTaskVersion($taskId, $version, $convert)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/io/download/task/$taskId";
        $args = array();
        $args['version'] = $version;
        if ($convert) {
            $args['convertToXliff'] = $convert;
        }
        
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::GET, null, $args);

        switch ($this->client->getResponseCode()) {
            default:
                return $ret;
            case Common\Enums\HttpStatusEnum::NOT_FOUND:
                throw new Common\Exceptions\SolasMatchException("File not found!");
                break;
        }
    }
}
