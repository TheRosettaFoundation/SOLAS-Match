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

    public function saveTaskFileFromProject($taskId, $userId, $fileData, $version = null, $convert = null)
    {
        $request = "{$this->siteApi}v0/io/upload/taskfromproject/$taskId/$userId";
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

    public function getMatchingTask($id_job, $id_chunk_password, $matching_type_id)
    {
        $result = LibAPI\PDOWrapper::call('getMatchingTask', LibAPI\PDOWrapper::cleanse($id_job) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($id_chunk_password) . ',' . LibAPI\PDOWrapper::cleanse($matching_type_id));
        return $result;
    }

    public function addUserToTaskBlacklist($user_id, $task_id)
    {
        LibAPI\PDOWrapper::call('addUserToTaskBlacklist', LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanse($task_id));
    }

    public function insertTaskChunks($task_id, $project_id, $type_id, $matecat_langpair, $matecat_id_job, $chunk_number, $chunk_password)
    {
        LibAPI\PDOWrapper::call('insertTaskChunks',
            LibAPI\PDOWrapper::cleanse($task_id) . ',' .
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanse($type_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($matecat_langpair) . ',' .
            LibAPI\PDOWrapper::cleanse($matecat_id_job) . ',' .
            LibAPI\PDOWrapper::cleanse($chunk_number) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($chunk_password));
    }

    public function get_matecat_analyze_url($project_id)
    {
        $matecat_analyze_url = '';
        $result = LibAPI\PDOWrapper::call('getWordCountRequestForProject', LibAPI\PDOWrapper::cleanse($project_id));
        if (!empty($result)) {
            $matecat_id_project      = $result[0]['matecat_id_project'];
            $matecat_id_project_pass = $result[0]['matecat_id_project_pass'];
            if (!empty($matecat_id_project) && !empty($matecat_id_project_pass)) {
                $matecat_analyze_url = "https://tm.translatorswb.org/analyze/proj-$project_id/$matecat_id_project-$matecat_id_project_pass";
            }
        }

        return $matecat_analyze_url;
    }

    public function updateWordCountForProject($project_id, $matecat_word_count)
    {
        LibAPI\PDOWrapper::call('updateWordCountForProject', LibAPI\PDOWrapper::cleanse($project_id) . ',' . LibAPI\PDOWrapper::cleanse($matecat_word_count));
    }

    public function get_creator($project_id) {
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

    public function get_matecat_url($task)
    {
        $matecat_url = '';
        if ($task->getTaskType() == Common\Enums\TaskTypeEnum::TRANSLATION || $task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING) {
            $translate = 'translate';
            if ($task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING) $translate = 'revise';

            $matecat_tasks = $this->getMatecatLanguagePairs($task->getId());
            if (empty($matecat_tasks)) {
                $matecat_tasks = $this->getTaskChunk($task->getId());
                if (!empty($matecat_tasks)) {
                    $matecat_tasks[0]['matecat_id_job_password'] = $matecat_tasks[0]['matecat_id_chunk_password'];
                }
            }
            if (!empty($matecat_tasks)) {
                $matecat_langpair = $matecat_tasks[0]['matecat_langpair'];
                $matecat_id_job = $matecat_tasks[0]['matecat_id_job'];
                $matecat_id_job_password = $matecat_tasks[0]['matecat_id_job_password'];
                //$matecat_id_file = $matecat_tasks[0]['matecat_id_file'];
                if (!empty($matecat_langpair) && !empty($matecat_id_job) && !empty($matecat_id_job_password)) {
                    $matecat_url = "https://tm.translatorswb.org/$translate/proj-" . $task->getProjectId() . '/' . str_replace('|', '-', $matecat_langpair) . "/$matecat_id_job-$matecat_id_job_password";

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

    public function get_matecat_url_regardless($task)
    {
        $matecat_url = '';
        if ($task->getTaskType() == Common\Enums\TaskTypeEnum::TRANSLATION || $task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING) {
            $translate = 'translate';
            if ($task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING) $translate = 'revise';

            $matecat_tasks = $this->getMatecatLanguagePairs($task->getId());
            if (empty($matecat_tasks)) {
                $matecat_tasks = $this->getTaskChunk($task->getId());
                if (!empty($matecat_tasks)) {
                    $matecat_tasks[0]['matecat_id_job_password'] = $matecat_tasks[0]['matecat_id_chunk_password'];
                }
            }
            if (!empty($matecat_tasks)) {
                $matecat_langpair = $matecat_tasks[0]['matecat_langpair'];
                $matecat_id_job = $matecat_tasks[0]['matecat_id_job'];
                $matecat_id_job_password = $matecat_tasks[0]['matecat_id_job_password'];
                //$matecat_id_file = $matecat_tasks[0]['matecat_id_file'];
                if (!empty($matecat_langpair) && !empty($matecat_id_job) && !empty($matecat_id_job_password)) {
                    $matecat_url = "https://tm.translatorswb.org/$translate/proj-" . $task->getProjectId() . '/' . str_replace('|', '-', $matecat_langpair) . "/$matecat_id_job-$matecat_id_job_password";
                }
            }
        }
        return $matecat_url;
    }

    public function getMatecatTaskStatus($task_id, $matecat_id_job, $matecat_id_job_password)
    {
        $download_status = '';

        // https://www.matecat.com/api/docs#!/Project/get_v1_jobs_id_job_password_stats
        $re = curl_init("https://tm.translatorswb.org/api/v1/jobs/$matecat_id_job/$matecat_id_job_password/stats");

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
            } else {
                error_log("https://tm.translatorswb.org/api/v1/jobs/$matecat_id_job/$matecat_id_job_password/stats getMatecatTaskStatus($task_id) DOWNLOAD_STATUS empty!");
            }
        } else {
            error_log("https://tm.translatorswb.org/api/v1/jobs/$matecat_id_job/$matecat_id_job_password/stats getMatecatTaskStatus($task_id) responseCode: $responseCode");
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
            $re = curl_init("https://tm.translatorswb.org/api/v2/projects/$matecat_id_project/$matecat_id_project_pass");

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
                            $stats = $job['stats'];

                            $matecat_id_chunk_password = $job['password'];
                            $translate_url = "https://tm.translatorswb.org/translate/proj-$project_id/" . str_replace('|', '-', $matecat_langpair) . "/$matecat_id_job-$matecat_id_chunk_password";
                            $revise_url    = "https://tm.translatorswb.org/revise/proj-$project_id/"    . str_replace('|', '-', $matecat_langpair) . "/$matecat_id_job-$matecat_id_chunk_password";
                            $matecat_download_url = "https://tm.translatorswb.org/?action=downloadFile&id_job=$matecat_id_job&id_file=$matecat_id_file&password=$matecat_id_job_password&download_type=all";

                            $chunks[] = array(
                                'matecat_id_job'            => $job['id'],
                                'matecat_id_chunk_password' => $matecat_id_chunk_password,
                                'translate_url'        => $translate_url,
                                'revise_url'           => $revise_url,
                                'matecat_download_url' => $matecat_download_url,
                                'DOWNLOAD_STATUS'      => $stats['DOWNLOAD_STATUS']);
                        }
                    }
                } else {
                    error_log("https://tm.translatorswb.org/api/v2/projects/$matecat_id_project/$matecat_id_project_pass ($project_id) No Jobs!");
                }
            } else {
                error_log("https://tm.translatorswb.org/api/v2/projects/$matecat_id_project/$matecat_id_project_pass ($project_id) responseCode: $responseCode");
            }
        }

        return $chunks;
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

    public function get_allow_download($task)
    {
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

    public function set_project_tm_key($project_id, $private_tm_key)
    {
        LibAPI\PDOWrapper::call('set_project_tm_key',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($private_tm_key));
    }

    public function get_project_tm_key($project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_project_tm_key', LibAPI\PDOWrapper::cleanse($project_id));
        if (!empty($result)) $result = $result[0]['private_tm_key'];
        return $result;
    }
}
