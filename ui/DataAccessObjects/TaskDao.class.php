<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\Common as Common;
use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\API\Lib as LibAPI;

require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/BaseDao.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";

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
    
    public function getAlsoViewedTasks($taskId,$limit, $offset)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/tasks/$taskId/alsoViewedTasks/$limit/$offset";

        $ret = $this->client->call(
            array("\SolasMatch\Common\Protobufs\Models\Task"),
            $request,
            Common\Enums\HttpMethodEnum::GET,
            null
        );
        return $ret;
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

    public function delete_review($task_id, $user_id)
    {
        LibAPI\PDOWrapper::call('delete_review', LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanse($user_id));
    }

    public function createTask($task)
    {
        $u = Common\Lib\UserSession::getCurrentUserID();
        $title = $task->getTitle();
        error_log("createTask($u): $title");

        $request = "{$this->siteApi}v0/tasks";
        $response = $this->client->call(
            "\SolasMatch\Common\Protobufs\Models\Task",
            $request,
            Common\Enums\HttpMethodEnum::POST,
            $task
        );
        if (!empty($response)) {
            if (get_class($response) === 'SolasMatch\Common\Protobufs\Models\Task') {
                $this->inheritRequiredTaskQualificationLevel($response->getId());

                error_log("TaskDAO::createTask id: " . $response->getId());
                if ($response->getPublished()) {
                    error_log("TaskDAO::createTask published: True");
                } else {
                    error_log("TaskDAO::createTask published: False");
                }
            } else {
                error_log("get_class(): " . get_class($response));
            }
        } else {
            error_log("TaskDAO::createTask Failed");
        }
        return $response;
    }

    public function createTaskDirectly($task)
    {
        $sourceLocale = $task->getSourceLocale();
        $targetLocale = $task->getTargetLocale();
        $args = 'null,' .
            LibAPI\PDOWrapper::cleanseNull($task->getProjectId()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($task->getTitle()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->getWordCount()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getLanguageCode()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($targetLocale->getLanguageCode()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($task->getComment()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getCountryCode()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($targetLocale->getCountryCode()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($task->getDeadline()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->getTaskType()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->getTaskStatus()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->getPublished());
        $result = LibAPI\PDOWrapper::call('taskInsertAndUpdate', $args);
error_log("createTaskDirectly: $args");
        if (empty($result[0]['id'])) return 0;
        $task_id = $result[0]['id'];
        $this->inheritRequiredTaskQualificationLevel($task_id);
        return $task_id;
    }

    public function trackTaskDirectly($user_id, $task_id)
    {
        LibAPI\PDOWrapper::call('UserTrackTask', LibAPI\PDOWrapper::cleanseNull($user_id) . ',' . LibAPI\PDOWrapper::cleanseNull($task_id));
    }

    public function updateTask($task)
    {
        $u = Common\Lib\UserSession::getCurrentUserID();
        $id = $task->getId();
        $title = $task->getTitle();
        error_log("updateTask($u) $id: $title");

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

    public function sendOrgFeedbackDeclined($task_id, $claimant_id, $memsource_project)
    {
        $projectDao = new ProjectDao();
        $user_id = $projectDao->get_user_id_from_memsource_user($memsource_project['owner_uid']);
        if (!$user_id) return;
        $result = $projectDao->get_user($user_id);
        if (empty($result)) return;
        $email = $result[0]['email'];

        $feedback = $this->encrypt_to_ensure_integrity("$task_id,$claimant_id,$user_id") . "::Unfortunately the task has been revoked from you.\nIf you have questions please email: $email";

        $feedbackData = new Common\Protobufs\Emails\OrgFeedback();
        $feedbackData->setTaskId($task_id);
        $feedbackData->setUserId($user_id);
        $feedbackData->setClaimantId($claimant_id);
        $feedbackData->setFeedback($feedback);
        $request = "{$this->siteApi}v0/tasks/$task_id/sendOrgFeedbackDeclined";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT, $feedbackData);
    }

    // Since no session will be sent, encrypt and verify on other side
    public function encrypt_to_ensure_integrity($data) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        return  bin2hex(openssl_encrypt($data, 'aes-256-cbc', base64_decode(Common\Lib\Settings::get('badge.key')), 0, $iv) . "::$iv");
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

    public function saveTaskFile($taskId, $userId, $fileData, $version = null)
    {
        $request = "{$this->siteApi}v0/io/upload/task/$taskId/$userId";
        $args = array();
        if (!is_null($version)) {
            $args["version"] = $version;
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

    public function saveTaskFileFromProject($taskId, $userId, $fileData, $version = null)
    {
        $request = "{$this->siteApi}v0/io/upload/taskfromproject/$taskId/$userId";
        $args = array();
        if (!is_null($version)) {
            $args["version"] = $version;
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

    public function uploadOutputFile($taskId, $userId, $fileData)
    {
        $request = "{$this->siteApi}v0/io/upload/taskOutput/$taskId/$userId";

        $args = null;
        
        $response = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT, null, $args, $fileData);
    }

    public function sendTaskUploadNotifications($taskId, $type)
    {
        $request = "{$this->siteApi}v0/io/upload/sendTaskUploadNotifications/$taskId/$type";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
    }

    public function getClaimedDate($taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/tasks/$taskId/timeClaimed";
        $ret = $this->client->call(null, $request);
        return $ret;
    }
    
    public function downloadTaskVersion($taskId, $version)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/io/download/task/$taskId";
        $args = array();
        $args['version'] = $version;
        
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::GET, null, $args);

        switch ($this->client->getResponseCode()) {
            default:
                return $ret;
            case Common\Enums\HttpStatusEnum::NOT_FOUND:
                throw new Common\Exceptions\SolasMatchException("File not found!");
                break;
        }
    }

    public function organisationHasQualifiedBadge($org_id)
    {
        $ret = 0;
        $result = LibAPI\PDOWrapper::call('organisationHasQualifiedBadge', LibAPI\PDOWrapper::cleanse($org_id));
        if (!empty($result)) {
            $ret = 1;
        }
        return $ret;
    }

    public function setRestrictedTask($task_id)
    {
        LibAPI\PDOWrapper::call('setRestrictedTask', LibAPI\PDOWrapper::cleanse($task_id));
    }

    public function removeRestrictedTask($task_id)
    {
        LibAPI\PDOWrapper::call('removeRestrictedTask', LibAPI\PDOWrapper::cleanse($task_id));
    }

    public function getRestrictedTask($task_id)
    {
        $ret = 0;
        $result = LibAPI\PDOWrapper::call('getRestrictedTask', LibAPI\PDOWrapper::cleanse($task_id));
        if (!empty($result)) {
            $ret = 1;
        }
        return $ret;
    }

    public function isUserRestrictedFromTask($task_id, $user_id)
    {
        $result = LibAPI\PDOWrapper::call('isUserRestrictedFromTask', LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanse($user_id));
        if (!empty($result)) {
            return $result[0]['result'];
        }
        return false;
    }

    public function isUserRestrictedFromTaskButAllowTranslatorToDownload($task_id, $user_id)
    {
        $result = LibAPI\PDOWrapper::call('isUserRestrictedFromTaskButAllowTranslatorToDownload', LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanse($user_id));
        if (!empty($result)) {
            return $result[0]['result'];
        }
        return false;
    }

    public function isUserRestrictedFromProject($project_id, $user_id)
    {
        $result = LibAPI\PDOWrapper::call('isUserRestrictedFromProject', LibAPI\PDOWrapper::cleanse($project_id) . ',' . LibAPI\PDOWrapper::cleanse($user_id));
        if (!empty($result)) {
            return $result[0]['result'];
        }
        return false;
    }

    public function insert_project_restrictions($project_id, $restrict_translate_tasks, $restrict_revise_tasks)
    {
        LibAPI\PDOWrapper::call('insert_project_restrictions',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanse($restrict_translate_tasks) . ',' .
            LibAPI\PDOWrapper::cleanse($restrict_revise_tasks));
    }

    public function get_project_restrictions($project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_project_restrictions', LibAPI\PDOWrapper::cleanse($project_id));
        if ($result) {
            return $result[0];
        }
        return false;
    }

    public function insertWordCountRequestForProjects($project_id, $source_language, $target_languages, $user_word_count)
    {
        LibAPI\PDOWrapper::call('insertWordCountRequestForProjects',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($source_language) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($target_languages) . ',' .
            LibAPI\PDOWrapper::cleanse($user_word_count));
    }

    public function updateWordCountRequestForProjects($project_id, $matecat_id_project, $matecat_id_project_pass, $matecat_word_count, $state)
    {
        LibAPI\PDOWrapper::call('updateWordCountRequestForProjects',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanse($matecat_id_project) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($matecat_id_project_pass) . ',' .
            LibAPI\PDOWrapper::cleanse($matecat_word_count) . ',' .
            LibAPI\PDOWrapper::cleanse($state));
    }

    public function insertWordCountRequestForProjectsErrors($project_id, $status, $message)
    {
error_log("insertWordCountRequestForProjectsErrors($project_id, $status, $message)");
        LibAPI\PDOWrapper::call('insertWordCountRequestForProjectsErrors',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($status) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($message));
    }

    public function getWordCountRequestForProjects($state)
    {
        $result = LibAPI\PDOWrapper::call('getWordCountRequestForProjects', LibAPI\PDOWrapper::cleanse($state));
        return $result;
    }

    public function getWordCountRequestForProject($project_id)
    {
        $result = LibAPI\PDOWrapper::call('getWordCountRequestForProject', LibAPI\PDOWrapper::cleanse($project_id));
        if ($result) {
            return $result[0];
        } else {
            return false;
        }
    }

    public function getTaskChunks($project_id)
    {
        $result = LibAPI\PDOWrapper::call('getTaskChunks', LibAPI\PDOWrapper::cleanse($project_id));
        return $result;
    }

    public function getTaskChunk($task_id)
    {
        $result = LibAPI\PDOWrapper::call('getTaskChunk', LibAPI\PDOWrapper::cleanse($task_id));
        return $result;
    }

    public function getTaskSubChunks($matecat_id_job)
    {
        $result = LibAPI\PDOWrapper::call('getTaskSubChunks', LibAPI\PDOWrapper::cleanse($matecat_id_job));
        return $result;
    }

    public function is_chunk_or_parent_of_chunk($project_id, $task_id)
    {
        $result = LibAPI\PDOWrapper::call('is_chunk_or_parent_of_chunk', LibAPI\PDOWrapper::cleanse($project_id) . ',' . LibAPI\PDOWrapper::cleanse($task_id));
        return $result;
    }

    public function is_parent_of_chunk($project_id, $task_id)
    {
        $result = LibAPI\PDOWrapper::call('is_parent_of_chunk', LibAPI\PDOWrapper::cleanse($project_id) . ',' . LibAPI\PDOWrapper::cleanse($task_id));
        return $result;
    }

    public function getMatchingTask($id_job, $id_chunk_password, $matching_type_id)
    {
        $result = LibAPI\PDOWrapper::call('getMatchingTask', LibAPI\PDOWrapper::cleanse($id_job) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($id_chunk_password) . ',' . LibAPI\PDOWrapper::cleanse($matching_type_id));
        return $result;
    }

    public function get_parent_transation_task($task)
    {
        $task_id = 0;
        $result = LibAPI\PDOWrapper::call('get_parent_transation_task', LibAPI\PDOWrapper::cleanse($task->getId()));
        if (!empty($result)) {
            $task_id = $result[0]['task_id'];
        }
        return $task_id;
    }

    public function getOtherPendingChunks($task_id)
    {
      $projectDao = new ProjectDao();
      $memsource_task = $projectDao->get_memsource_task($task_id);
      if ($memsource_task) {
          if (!strpos($memsource_task['internalId'], '.')) return []; // Not split
          $taskDao = new TaskDao();
          $task = $taskDao->getTask($task_id);
          $result = LibAPI\PDOWrapper::call('getOtherPendingMemsourceJobs',
             LibAPI\PDOWrapper::cleanse($task_id) . ',' .
             LibAPI\PDOWrapper::cleanse($task->getTaskType()) . ',' .
             LibAPI\PDOWrapper::cleanse($task->getProjectId()) . ',' .
             LibAPI\PDOWrapper::cleanseWrapStr($memsource_task['internalId']));
      } else {
        $matecat_tasks = $this->getTaskChunk($task_id);
        if (empty($matecat_tasks)) return array();

        $matecat_id_job = $matecat_tasks[0]['matecat_id_job'];
        $type_id        = $matecat_tasks[0]['type_id'];

        $result = LibAPI\PDOWrapper::call('getOtherPendingChunks',
            LibAPI\PDOWrapper::cleanse($task_id) . ',' .
            LibAPI\PDOWrapper::cleanse($type_id) . ',' .
            LibAPI\PDOWrapper::cleanse($matecat_id_job));
      }
        if (empty($result)) return array();

        $other_task_ids = array();
        foreach ($result as $row) {
            $other_task_ids[] = $row['task_id'];
        }
        return $other_task_ids;
    }

    public function addUserToTaskBlacklist($user_id, $task_id)
    {
        LibAPI\PDOWrapper::call('addUserToTaskBlacklist', LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanse($task_id));
    }

    public static function removeUserFromTaskBlacklist($user_id, $task_id)
    {
        LibAPI\PDOWrapper::call('removeUserFromTaskBlacklist', LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanse($task_id));
    }

    public function insertTaskChunks($task_id, $project_id, $type_id, $matecat_langpair, $matecat_id_job, $chunk_number, $chunk_password, $job_first_segment)
    {
        LibAPI\PDOWrapper::call('insertTaskChunks',
            LibAPI\PDOWrapper::cleanse($task_id) . ',' .
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanse($type_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($matecat_langpair) . ',' .
            LibAPI\PDOWrapper::cleanse($matecat_id_job) . ',' .
            LibAPI\PDOWrapper::cleanse($chunk_number) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($chunk_password) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($job_first_segment));
    }

    public function get_matecat_analyze_url($project_id, $memsource_project)
    {
        if ($memsource_project) return "https://kato.translatorswb.org/simplesaml/saml2/idp/SSOService.php?spentityid=https://cloud.memsource.com/web/saml2Login/metadata/127330&RelayState=https://cloud.memsource.com/web/project2/show/{$memsource_project['memsource_project_uid']}";

        $matecat_analyze_url = '';
        $result = LibAPI\PDOWrapper::call('getWordCountRequestForProject', LibAPI\PDOWrapper::cleanse($project_id));
        if (!empty($result)) {
            $matecat_id_project      = $result[0]['matecat_id_project'];
            $matecat_id_project_pass = $result[0]['matecat_id_project_pass'];
            if (!empty($matecat_id_project) && !empty($matecat_id_project_pass)) {
                $matecat_api = Common\Lib\Settings::get('matecat.url');
                $matecat_analyze_url = "{$matecat_api}analyze/proj-$project_id/$matecat_id_project-$matecat_id_project_pass";
            }
        }

        return $matecat_analyze_url;
    }

    public function updateWordCountForProject($project_id, $matecat_word_count)
    {
        LibAPI\PDOWrapper::call('updateWordCountForProject', LibAPI\PDOWrapper::cleanse($project_id) . ',' . LibAPI\PDOWrapper::cleanse($matecat_word_count));
    }

    public function get_creator($project_id, $memsource_project = 0) {
        if ($memsource_project) {
            $projectDao = new ProjectDao();
            $user_id = $projectDao->get_user_id_from_memsource_user($memsource_project['owner_uid']);
            if (!$user_id) $user_id = 62927; // translators@translatorswithoutborders.org
//(**)dev server            if (!$user_id) $user_id = 3297;
            $result = $projectDao->get_user($user_id);
            return $result[0];
        }

        $result = LibAPI\PDOWrapper::call('get_creator', LibAPI\PDOWrapper::cleanse($project_id));
        return $result[0];
    }

    public function get_self_creator_from_project_file($project_id) {
        $result = LibAPI\PDOWrapper::call('get_creator', LibAPI\PDOWrapper::cleanse($project_id));
        return $result[0];
    }

    public function getProjectFileLocation($project_id) {
        $result = LibAPI\PDOWrapper::call('getProjectFile', LibAPI\PDOWrapper::cleanse($project_id) . ',NULL,NULL,NULL,NULL');
        if ($result) {
            return $result[0];
        } else {
            return false;
        }
    }

    public function getPhysicalProjectFilePath($project_id, $filename) {
        $path = file_get_contents(Common\Lib\Settings::get('files.upload_path') . "proj-$project_id/$filename");
        if ($path === false) return false;
        return Common\Lib\Settings::get('files.upload_path') . $path;
    }

    public function insertMatecatLanguagePairs($task_id, $project_id, $type_id, $matecat_langpair)
    {
        LibAPI\PDOWrapper::call('insertMatecatLanguagePairs',
            LibAPI\PDOWrapper::cleanse($task_id) . ',' .
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanse($type_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($matecat_langpair));
    }

    public function updateMatecatLanguagePairs($project_id, $type_id, $matecat_langpair, $matecat_id_job, $matecat_id_job_password, $matecat_id_file)
    {
        LibAPI\PDOWrapper::call('updateMatecatLanguagePairs',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanse($type_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($matecat_langpair) . ',' .
            LibAPI\PDOWrapper::cleanse($matecat_id_job) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($matecat_id_job_password) . ',' .
            LibAPI\PDOWrapper::cleanse($matecat_id_file));
    }

    public function getMatecatLanguagePairs($task_id)
    {
        $result = LibAPI\PDOWrapper::call('getMatecatLanguagePairs', LibAPI\PDOWrapper::cleanse($task_id));
        return $result;
    }

    public function getMatecatLanguagePairsForProject($project_id)
    {
        $result = LibAPI\PDOWrapper::call('getMatecatLanguagePairsForProject', LibAPI\PDOWrapper::cleanse($project_id));
        return $result;
    }

    public function get_matecat_url($task, $memsource_task)
    {
//dev server(**)        if ($memsource_task) return "https://dev.translatorswb.org/simplesaml/saml2/idp/SSOService.php?spentityid=https://cloud.memsource.com/web/saml2Login/metadata/127330&RelayState=https://cloud.memsource.com/web/job/{$memsource_task['memsource_task_uid']}/translate";
        if ($memsource_task) return "https://kato.translatorswb.org/simplesaml/saml2/idp/SSOService.php?spentityid=https://cloud.memsource.com/web/saml2Login/metadata/127330&RelayState=https://cloud.memsource.com/web/job/{$memsource_task['memsource_task_uid']}/translate";

        $matecat_url = '';
        if ($task->getTaskType() == Common\Enums\TaskTypeEnum::TRANSLATION || $task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING) {
            $job_first_segment = '';
            $translate = 'translate';
            if ($task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING) $translate = 'revise';

            $matecat_tasks = $this->getMatecatLanguagePairs($task->getId());
            if (empty($matecat_tasks)) {
                $matecat_tasks = $this->getTaskChunk($task->getId());
                if (!empty($matecat_tasks)) {
                    $matecat_tasks[0]['matecat_id_job_password'] = $matecat_tasks[0]['matecat_id_chunk_password'];
                    $job_first_segment                           = $matecat_tasks[0]['job_first_segment'];
                }
            }
            if (!empty($matecat_tasks)) {
                $matecat_langpair = $matecat_tasks[0]['matecat_langpair'];
                $matecat_id_job = $matecat_tasks[0]['matecat_id_job'];
                $matecat_id_job_password = $matecat_tasks[0]['matecat_id_job_password'];
                //$matecat_id_file = $matecat_tasks[0]['matecat_id_file'];
                if (!empty($matecat_langpair) && !empty($matecat_id_job) && !empty($matecat_id_job_password)) {
                    $matecat_api = Common\Lib\Settings::get('matecat.url');
                    $matecat_url = "{$matecat_api}$translate/proj-" . $task->getProjectId() . '/' . str_replace('|', '-', $matecat_langpair) . "/$matecat_id_job-$matecat_id_job_password$job_first_segment";

                    if ($translate === 'revise') { // Make sure it has been translated in MateCat
                        $download_status = $this->getMatecatTaskStatus($task->getId(), $matecat_id_job, $matecat_id_job_password);

                        if ($download_status !== 'translated' && $download_status !== 'approved') {
                            $matecat_url = ''; // Disable Kató access for Proofreading if job file is not translated
                        }
                    }
                }
            }
        }
        return $matecat_url;
    }

    public function get_matecat_url_regardless($task, $memsource_task)
    {
        if ($memsource_task) return "https://kato.translatorswb.org/simplesaml/saml2/idp/SSOService.php?spentityid=https://cloud.memsource.com/web/saml2Login/metadata/127330&RelayState=https://cloud.memsource.com/web/job/{$memsource_task['memsource_task_uid']}/translate";

        $matecat_url = '';
        if ($task->getTaskType() == Common\Enums\TaskTypeEnum::TRANSLATION || $task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING) {
            $job_first_segment = '';
            $translate = 'translate';
            if ($task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING) $translate = 'revise';

            $matecat_tasks = $this->getMatecatLanguagePairs($task->getId());
            if (empty($matecat_tasks)) {
                $matecat_tasks = $this->getTaskChunk($task->getId());
                if (!empty($matecat_tasks)) {
                    $matecat_tasks[0]['matecat_id_job_password'] = $matecat_tasks[0]['matecat_id_chunk_password'];
                    $job_first_segment                           = $matecat_tasks[0]['job_first_segment'];
                }
            }
            if (!empty($matecat_tasks)) {
                $matecat_langpair = $matecat_tasks[0]['matecat_langpair'];
                $matecat_id_job = $matecat_tasks[0]['matecat_id_job'];
                $matecat_id_job_password = $matecat_tasks[0]['matecat_id_job_password'];
                //$matecat_id_file = $matecat_tasks[0]['matecat_id_file'];
                if (!empty($matecat_langpair) && !empty($matecat_id_job) && !empty($matecat_id_job_password)) {
                    $matecat_api = Common\Lib\Settings::get('matecat.url');
                    $matecat_url = "{$matecat_api}$translate/proj-" . $task->getProjectId() . '/' . str_replace('|', '-', $matecat_langpair) . "/$matecat_id_job-$matecat_id_job_password$job_first_segment";
                }
            }
        }
        return $matecat_url;
    }

    public function getMatecatTaskStatus($task_id, $matecat_id_job, $matecat_id_job_password)
    {
        $taskDao = new TaskDao();
        $recorded_status = $taskDao->getMatecatRecordedJobStatus($matecat_id_job, $matecat_id_job_password);
        if ($recorded_status === 'approved') { // We do not need to query MateCat...
            return 'approved';
        }
        $download_status = '';

        // https://www.matecat.com/api/docs#!/Project/get_v1_jobs_id_job_password_stats
        $matecat_api = Common\Lib\Settings::get('matecat.url');
        $re = curl_init("{$matecat_api}api/v1/jobs/$matecat_id_job/$matecat_id_job_password/stats");

        curl_setopt($re, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($re, CURLOPT_COOKIESESSION, true);
        curl_setopt($re, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($re, CURLOPT_AUTOREFERER, true);

        $httpHeaders = array(
            'Expect:'
        );
        curl_setopt($re, CURLOPT_HTTPHEADER, $httpHeaders);

        curl_setopt($re, CURLOPT_HEADER, true);
        curl_setopt($re, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($re, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($re, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($re);

        $header_size = curl_getinfo($re, CURLINFO_HEADER_SIZE);
        $header = substr($res, 0, $header_size);
        $res = substr($res, $header_size);
        $responseCode = curl_getinfo($re, CURLINFO_HTTP_CODE);

        curl_close($re);

        if ($responseCode == 200) {
            $response_data = json_decode($res, true);

            if (!empty($response_data['stats']['DOWNLOAD_STATUS'])) {
                $download_status = $response_data['stats']['DOWNLOAD_STATUS'];
                if ($download_status === 'draft') {
                    $download_status = $recorded_status; // getMatecatRecordedJobStatus() MIGHT have a "better" status
                }
            } else {
                error_log("{$matecat_api}api/v1/jobs/$matecat_id_job/$matecat_id_job_password/stats getMatecatTaskStatus($task_id) DOWNLOAD_STATUS empty!");
            }
        } else {
            error_log("{$matecat_api}api/v1/jobs/$matecat_id_job/$matecat_id_job_password/stats getMatecatTaskStatus($task_id) responseCode: $responseCode");
        }

        return $download_status;
    }

    public function getStatusOfSubChunks($project_id, $matecat_langpair = '', $matecat_id_job = 0, $matecat_id_job_password = '', $matecat_id_file = 0)
    {
        $chunks = array();

        $result = LibAPI\PDOWrapper::call('getWordCountRequestForProject', LibAPI\PDOWrapper::cleanse($project_id));
        if (!empty($result)) {
            $matecat_id_project      = $result[0]['matecat_id_project'];
            $matecat_id_project_pass = $result[0]['matecat_id_project_pass'];

            // https://www.matecat.com/api/docs#/Project/get_api_v2_projects__id_project___password_
            $matecat_api = Common\Lib\Settings::get('matecat.url');
            $re = curl_init("{$matecat_api}api/v2/projects/$matecat_id_project/$matecat_id_project_pass");

            curl_setopt($re, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($re, CURLOPT_COOKIESESSION, true);
            curl_setopt($re, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($re, CURLOPT_AUTOREFERER, true);

            $httpHeaders = array(
                'Expect:'
            );
            curl_setopt($re, CURLOPT_HTTPHEADER, $httpHeaders);

            curl_setopt($re, CURLOPT_HEADER, true);
            curl_setopt($re, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($re, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($re, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($re, CURLOPT_TIMEOUT, 300); // Just so it does not hang forever and block because we may be called inside file lock
            $res = curl_exec($re);

            $header_size = curl_getinfo($re, CURLINFO_HEADER_SIZE);
            $header = substr($res, 0, $header_size);
            $res = substr($res, $header_size);
            $responseCode = curl_getinfo($re, CURLINFO_HTTP_CODE);

            curl_close($re);

            if ($responseCode == 200) {
                $response_data = json_decode($res, true);

                if (!empty($response_data['project']['jobs'])) {
                    $jobs = $response_data['project']['jobs'];
                    foreach ($jobs as $job) {
                        if ($matecat_id_job == 0 || $job['id'] == $matecat_id_job) {
                            $job_first_segment = '';
                            if (!empty($job['job_first_segment'])) $job_first_segment = '#' . $job['job_first_segment'];

                            $stats = $job['stats'];

                            $matecat_id_chunk_password = $job['password'];
                            $translate_url = "{$matecat_api}translate/proj-$project_id/" . str_replace('|', '-', $matecat_langpair) . "/$matecat_id_job-$matecat_id_chunk_password$job_first_segment";
                            $revise_url    = "{$matecat_api}revise/proj-$project_id/"    . str_replace('|', '-', $matecat_langpair) . "/$matecat_id_job-$matecat_id_chunk_password$job_first_segment";
                            $matecat_id_file = ''; // Need all files in job to be downloaded
                            $matecat_download_url = "{$matecat_api}?action=downloadFile&id_job=$matecat_id_job&id_file=$matecat_id_file&password=$matecat_id_job_password&download_type=all";

                            $taskDao = new TaskDao();
                            $recorded_status = $taskDao->getMatecatRecordedJobStatus($job['id'], $job['password']);
                            if ($recorded_status === 'approved') {
                                $stats['DOWNLOAD_STATUS'] = 'approved';
                            }
                            if ($stats['DOWNLOAD_STATUS'] === 'draft') {
                                $stats['DOWNLOAD_STATUS'] = $recorded_status; // getMatecatRecordedJobStatus() MIGHT have a "better" status
                            }
                            $chunks[] = array(
                                'matecat_id_job'            => $job['id'],
                                'matecat_id_chunk_password' => $matecat_id_chunk_password,
                                'job_first_segment'         => $job_first_segment,
                                'translate_url'        => $translate_url,
                                'revise_url'           => $revise_url,
                                'matecat_download_url' => $matecat_download_url,
                                'DOWNLOAD_STATUS'      => $stats['DOWNLOAD_STATUS']);
                        }
                    }
                } else {
                    error_log("{$matecat_api}api/v2/projects/$matecat_id_project/$matecat_id_project_pass ($project_id) No Jobs!");
                }
            } else {
                error_log("{$matecat_api}api/v2/projects/$matecat_id_project/$matecat_id_project_pass ($project_id) responseCode: $responseCode");
            }
        }

        return $chunks;
    }

    public function insertMatecatRecordedJobStatus($matecat_id_job, $matecat_id_job_password, $job_status)
    {
        LibAPI\PDOWrapper::call('insertMatecatRecordedJobStatus',
            LibAPI\PDOWrapper::cleanse($matecat_id_job) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($matecat_id_job_password) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($job_status));
    }

    public function getMatecatRecordedJobStatus($matecat_id_job, $matecat_id_job_password)
    {
        $result = LibAPI\PDOWrapper::call('getMatecatRecordedJobStatus', LibAPI\PDOWrapper::cleanse($matecat_id_job) . ',' . LibAPI\PDOWrapper::cleanseNullOrWrapStr($matecat_id_job_password));
        if ($result) {
            return $result[0]['job_status'];
        } else {
            return 'draft';
        }
    }

    public function get_matecat_job_id_recorded_status($task)
    {
        if (($task->getTaskType() == Common\Enums\TaskTypeEnum::TRANSLATION || $task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING) &&
            ($task->getTaskStatus() == Common\Enums\TaskStatusEnum::IN_PROGRESS || $task->getTaskStatus() == Common\Enums\TaskStatusEnum::COMPLETE)) {
            $matecat_tasks = $this->getMatecatLanguagePairs($task->getId());
            if (empty($matecat_tasks)) {
                $matecat_tasks = $this->getTaskChunk($task->getId());
                if (!empty($matecat_tasks)) {
                    $matecat_tasks[0]['matecat_id_job_password'] = $matecat_tasks[0]['matecat_id_chunk_password'];
                }
            }
            if (!empty($matecat_tasks)) {
                $matecat_id_job = $matecat_tasks[0]['matecat_id_job'];
                $matecat_id_job_password = $matecat_tasks[0]['matecat_id_job_password'];
                if (!empty($matecat_id_job) && !empty($matecat_id_job_password)) {
                    $recorded_status = $this->getMatecatRecordedJobStatus($matecat_id_job, $matecat_id_job_password);
                    return array($matecat_id_job, $matecat_id_job_password, $recorded_status);
                }
            }
        }
        return array (0, '', 'draft');
    }

    public function set_task_complete_date($task_id)
    {
        LibAPI\PDOWrapper::call('set_task_complete_date', LibAPI\PDOWrapper::cleanse($task_id));
    }

    public function get_task_complete_date($task_id)
    {
        $result = LibAPI\PDOWrapper::call('get_task_complete_date', LibAPI\PDOWrapper::cleanse($task_id));
        if (empty($result)) return 0;
        return $result[0]['complete_date'];
    }

    public function all_chunked_active_projects()
    {
        $result = LibAPI\PDOWrapper::call('all_chunked_active_projects', '');
        return $result;
    }

    public function setTaskStatus($task_id, $status)
    {
        LibAPI\PDOWrapper::call('setTaskStatus',
            LibAPI\PDOWrapper::cleanse($task_id) . ',' .
            LibAPI\PDOWrapper::cleanse($status));
    }

    public function getTaskStatus($task_id)
    {
        $result = LibAPI\PDOWrapper::call('getTaskStatus', LibAPI\PDOWrapper::cleanse($task_id));
        if (empty($result)) return 0;
        return $result[0]['task-status_id'];
    }

    public function taskIsClaimed($task_id)
    {
        $args = LibAPI\PDOWrapper::cleanse($task_id);
        $result = LibAPI\PDOWrapper::call('taskIsClaimed', $args);
        return $result[0]['result'];
    }

    public function claimTask($task_id, $user_id)
    {
        $args = LibAPI\PDOWrapper::cleanse($task_id) . "," . LibAPI\PDOWrapper::cleanse($user_id);
        $result = LibAPI\PDOWrapper::call('claimTask', $args);
        return $result[0]['result'];
    }

    public function claimTaskAndDeny($task_id, $user_id, $memsource_task)
    {
        $result = LibAPI\PDOWrapper::call('claimTask', LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanse($user_id));
        if ($success = $result[0]['result']) {
            // Add corresponding task(s) to deny list for translator
            $projectDao = new ProjectDao();
            $top_level = $projectDao->get_top_level($memsource_task['internalId']);
            $task = $this->getTask($task_id);
            $project_tasks = $projectDao->get_tasks_for_project($task->getProjectId());
            foreach ($project_tasks as $project_task) {
                if ($top_level == $projectDao->get_top_level($project_task['internalId'])) {
                    if ($memsource_task['workflowLevel'] != $project_task['workflowLevel']) { // Not same workflowLevel
                        if ( $memsource_task['task-type_id'] == Common\Enums\TaskTypeEnum::TRANSLATION ||
                            ($memsource_task['task-type_id'] == Common\Enums\TaskTypeEnum::PROOFREADING && $project_task['task-type_id'] == Common\Enums\TaskTypeEnum::TRANSLATION)) {
//(**)Need to add additional code to deny if user translated ANY file (not just current)
//(**)Will there be index on QA/Proofread?
                            if (($memsource_task['beginIndex'] <= $project_task['endIndex']) && ($project_task['beginIndex'] <= $memsource_task['endIndex'])) { // Overlap
                                error_log("Adding $user_id to Deny List for {$project_task['id']} {$project_task['internalId']}");
                                $this->addUserToTaskBlacklist($user_id, $project_task['id']);
                            }
                        }
                    }
                }
            }
            $projectDao->make_tasks_claimable($task->getProjectId());
        }
        return $success;
    }

    public function unClaimTask($task_id, $user_id, $userFeedback = null)
    {
        $args = LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanseNullOrWrapStr($userFeedback) . ',0';
        $result = LibAPI\PDOWrapper::call('unClaimTaskMemsource', $args);
        return $result[0]['result'];
    }

    public function set_memsource_status($task_id, $memsource_task_uid, $status)
    {
        $args = LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($memsource_task_uid) . ',' . LibAPI\PDOWrapper::cleanseNullOrWrapStr($status);
        LibAPI\PDOWrapper::call('set_memsource_status', $args);
    }

    public function record_task_if_translated_in_matecat($task)
    {
        if ($task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING) {

            $matecat_tasks = $this->getMatecatLanguagePairs($task->getId());
            if (!empty($matecat_tasks)) {
                $matecat_langpair = $matecat_tasks[0]['matecat_langpair'];
                $matecat_id_job = $matecat_tasks[0]['matecat_id_job'];
                $matecat_id_job_password = $matecat_tasks[0]['matecat_id_job_password'];
                if (!empty($matecat_langpair) && !empty($matecat_id_job) && !empty($matecat_id_job_password)) {
                    $download_status = $this->getMatecatTaskStatus($task->getId(), $matecat_id_job, $matecat_id_job_password);

                    if ($download_status === 'translated' || $download_status === 'approved') {
                        // Allow Kató access for Proofreading if job file is translated
                        LibAPI\PDOWrapper::call('record_task_translated_in_matecat', LibAPI\PDOWrapper::cleanse($task->getId()));
                    }
                }
            }
        }
    }

    public function get_allow_download($task, $memsource_task)
    {
        if ($memsource_task) return 0;

        $allow = 1;
        $matecat_tasks = $this->getTaskChunk($task->getId());
        if (!empty($matecat_tasks)) {
            $allow = 0;
        }
        return $allow;
    }

    public function inheritRequiredTaskQualificationLevel($task_id)
    {
        LibAPI\PDOWrapper::call('inheritRequiredTaskQualificationLevel', LibAPI\PDOWrapper::cleanse($task_id));
    }

    public function updateRequiredTaskQualificationLevel($task_id, $required_qualification_level)
    {
        LibAPI\PDOWrapper::call('updateRequiredTaskQualificationLevel',
            LibAPI\PDOWrapper::cleanse($task_id) . ',' .
            LibAPI\PDOWrapper::cleanse($required_qualification_level));
    }

    public function getRequiredTaskQualificationLevel($task_id)
    {
        $result = LibAPI\PDOWrapper::call('getRequiredTaskQualificationLevel', LibAPI\PDOWrapper::cleanse($task_id));
        if (empty($result)) return 1;
        return $result[0]['required_qualification_level'];
    }

    public function list_qualified_translators($task_id)
    {
        $result = LibAPI\PDOWrapper::call('list_qualified_translators', LibAPI\PDOWrapper::cleanse($task_id));
        return $result;
    }

    public function list_task_invites_not_sent_strict($task_id)
    {
        $result = LibAPI\PDOWrapper::call('list_task_invites_not_sent_strict', LibAPI\PDOWrapper::cleanse($task_id));
        if (empty($result)) $result = array();
        return $result;
    }

    public function list_task_invites_not_sent($task_id)
    {
        $result = LibAPI\PDOWrapper::call('list_task_invites_not_sent', LibAPI\PDOWrapper::cleanse($task_id));
        if (empty($result)) $result = array();
        return $result;
    }

    public function list_task_invites_not_sent_words($task_id)
    {
        $result = LibAPI\PDOWrapper::call('list_task_invites_not_sent_words', LibAPI\PDOWrapper::cleanse($task_id));
        if (empty($result)) $result = array();
        return $result;
    }

    public function list_task_invites_not_sent_tags($task_id)
    {
        $result = LibAPI\PDOWrapper::call('list_task_invites_not_sent_tags', LibAPI\PDOWrapper::cleanse($task_id));
        if (empty($result)) $result = array();
        return $result;
    }

    public function list_task_invites_sent($task_id)
    {
        $result = LibAPI\PDOWrapper::call('list_task_invites_sent', LibAPI\PDOWrapper::cleanse($task_id));
        if (empty($result)) $result = array();
        return $result;
    }

    public function insert_task_invite_sent_to_users($insert)
    {
        LibAPI\PDOWrapper::call('insert_task_invite_sent_to_users', LibAPI\PDOWrapper::cleanseWrapStr($insert));
    }

    public function set_project_tm_key($project_id, $mt_engine, $pretranslate_100, $lexiqa, $private_tm_key)
    {
        LibAPI\PDOWrapper::call('set_project_tm_key',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanse($mt_engine) . ',' .
            LibAPI\PDOWrapper::cleanse($pretranslate_100) . ',' .
            LibAPI\PDOWrapper::cleanse($lexiqa) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($private_tm_key));
    }

    public function get_project_tm_key($project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_project_tm_key', LibAPI\PDOWrapper::cleanse($project_id));
        if (empty($result)) $result = array();
        return $result;
    }

    public function getVolunteerProjectTasks($project_id, $user_id)
    {
        $result = LibAPI\PDOWrapper::call('getVolunteerProjectTasks', LibAPI\PDOWrapper::cleanse($project_id) . ',' . LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) $result = array();
        return $result;
    }

    public function get_paid_status($task_id)
    {
        $result = LibAPI\PDOWrapper::call('get_paid_status', LibAPI\PDOWrapper::cleanse($task_id));
        if (empty($result)) return 0;
        return 1;
    }

    public function set_paid_status($task_id)
    {
        LibAPI\PDOWrapper::call('set_paid_status', LibAPI\PDOWrapper::cleanse($task_id));
    }

    public function clear_paid_status($task_id)
    {
        LibAPI\PDOWrapper::call('clear_paid_status', LibAPI\PDOWrapper::cleanse($task_id));
    }

    public function get_all_as_paid($project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_all_as_paid', LibAPI\PDOWrapper::cleanse($project_id));
        return $result[0]['result'];
    }

    public function set_all_as_paid($project_id)
    {
        LibAPI\PDOWrapper::call('set_all_as_paid', LibAPI\PDOWrapper::cleanse($project_id));
    }

    public function set_revision_as_paid($project_id)
    {
        LibAPI\PDOWrapper::call('set_revision_as_paid', LibAPI\PDOWrapper::cleanse($project_id));
    }

    public function clear_all_as_paid($project_id)
    {
        LibAPI\PDOWrapper::call('clear_all_as_paid', LibAPI\PDOWrapper::cleanse($project_id));
    }
}
