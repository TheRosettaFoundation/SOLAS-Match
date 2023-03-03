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

    public function createTaskDirectly($task)
    {
        $sourceLocale = $task->getSourceLocale();
        $targetLocale = $task->getTargetLocale();
        $args = 'null,' .
            LibAPI\PDOWrapper::cleanseNull($task->getProjectId()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($task->getTitle()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->getWordCount()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->get_word_count_original()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getLanguageCode()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($targetLocale->getLanguageCode()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($task->getComment()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getCountryCode()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($targetLocale->getCountryCode()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($task->getDeadline()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->getTaskType()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->getTaskStatus()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->getPublished()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->get_cancelled());
        $result = LibAPI\PDOWrapper::call('taskInsertAndUpdate', $args);
error_log("createTaskDirectly: $args");
        if (empty($result[0]['id'])) return 0;
        $task_id = $result[0]['id'];
        $this->inheritRequiredTaskQualificationLevel($task_id);

        LibAPI\PDOWrapper::call('insert_tasks_status', LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanse($task->getTaskStatus()));
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
        $sourceLocale = $task->getSourceLocale();
        $targetLocale = $task->getTargetLocale();
        $args =
            LibAPI\PDOWrapper::cleanseNull($task->getId()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->getProjectId()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($task->getTitle()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->getWordCount()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->get_word_count_original()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getLanguageCode()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($targetLocale->getLanguageCode()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($task->getComment()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getCountryCode()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($targetLocale->getCountryCode()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($task->getDeadline()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->getTaskType()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->getTaskStatus()) . ',' .
            LibAPI\PDOWrapper::cleanse($task->getPublished()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->get_cancelled());
        error_log("call taskInsertAndUpdate($args)");
        return LibAPI\PDOWrapper::call('taskInsertAndUpdate', $args);
    }

    public function deleteTask($taskId)
    {
        $request = "{$this->siteApi}v0/tasks/$taskId";
        $response =$this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
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

    public function getMatchingTask($id_job, $id_chunk_password, $matching_type_id)
    {
        $result = LibAPI\PDOWrapper::call('getMatchingTask', LibAPI\PDOWrapper::cleanse($id_job) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($id_chunk_password) . ',' . LibAPI\PDOWrapper::cleanse($matching_type_id));
        return $result;
    }

    public function getOtherPendingChunks($task_id)
    {
      $projectDao = new ProjectDao();
      $memsource_task = $projectDao->get_memsource_task($task_id);
          if (!strpos($memsource_task['internalId'], '.')) return []; // Not split
          $taskDao = new TaskDao();
          $task = $taskDao->getTask($task_id);
          $result = LibAPI\PDOWrapper::call('getOtherPendingMemsourceJobs',
             LibAPI\PDOWrapper::cleanse($task_id) . ',' .
             LibAPI\PDOWrapper::cleanse($task->getTaskType()) . ',' .
             LibAPI\PDOWrapper::cleanse($task->getProjectId()) . ',' .
             LibAPI\PDOWrapper::cleanseWrapStr($memsource_task['internalId']));
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

    public function get_matecat_analyze_url($project_id, $memsource_project)
    {
        if (strpos($this->siteApi, 'twbplatform')) return "https://twbplatform.org/simplesaml/saml2/idp/SSOService.php?spentityid=https://cloud.memsource.com/web/saml2Login/metadata/127330&RelayState=https://cloud.memsource.com/web/project2/show/{$memsource_project['memsource_project_uid']}";
        else                                 return "https://dev.translatorswb.org/simplesaml/saml2/idp/SSOService.php?spentityid=https://cloud.memsource.com/web/saml2Login/metadata/135305&RelayState=https://cloud.memsource.com/web/project2/show/{$memsource_project['memsource_project_uid']}";
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

    public function get_matecat_url($task, $memsource_task)
    {
        if (strpos($this->siteApi, 'twbplatform')) return "https://twbplatform.org/simplesaml/saml2/idp/SSOService.php?spentityid=https://cloud.memsource.com/web/saml2Login/metadata/127330&RelayState=https://cloud.memsource.com/web/job/{$memsource_task['memsource_task_uid']}/translate";
        else                                 return "https://dev.translatorswb.org/simplesaml/saml2/idp/SSOService.php?spentityid=https://cloud.memsource.com/web/saml2Login/metadata/135305&RelayState=https://cloud.memsource.com/web/job/{$memsource_task['memsource_task_uid']}/translate";
    }

    public function get_matecat_url_regardless($task, $memsource_task)
    {
        if (strpos($this->siteApi, 'twbplatform')) return "https://twbplatform.org/simplesaml/saml2/idp/SSOService.php?spentityid=https://cloud.memsource.com/web/saml2Login/metadata/127330&RelayState=https://cloud.memsource.com/web/job/{$memsource_task['memsource_task_uid']}/translate";
        else                                 return "https://dev.translatorswb.org/simplesaml/saml2/idp/SSOService.php?spentityid=https://cloud.memsource.com/web/saml2Login/metadata/135305&RelayState=https://cloud.memsource.com/web/job/{$memsource_task['memsource_task_uid']}/translate";
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

    public function setTaskStatus($task_id, $status)
    {
        LibAPI\PDOWrapper::call('setTaskStatus',
            LibAPI\PDOWrapper::cleanse($task_id) . ',' .
            LibAPI\PDOWrapper::cleanse($status));

        LibAPI\PDOWrapper::call('update_tasks_status', LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanse($status) . ',NULL');
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

    public function claimTask($task_id, $user_id, $make_tasks_claimable = true)
    {
        $args = LibAPI\PDOWrapper::cleanse($task_id) . "," . LibAPI\PDOWrapper::cleanse($user_id);
        $result = LibAPI\PDOWrapper::call('claimTask', $args);

        if ($make_tasks_claimable) {
            $projectDao = new ProjectDao();
            $task = $this->getTask($task_id);
            $projectDao->make_tasks_claimable($task->getProjectId());

            LibAPI\PDOWrapper::call('update_tasks_status_claimant', LibAPI\PDOWrapper::cleanse($task_id) . ',10,' . LibAPI\PDOWrapper::cleanse($user_id) . ',NULL');
        }

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
                        if ( $task->getTaskType() == Common\Enums\TaskTypeEnum::TRANSLATION ||
                            ($task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING && $project_task['task-type_id'] == Common\Enums\TaskTypeEnum::TRANSLATION)) {
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

            LibAPI\PDOWrapper::call('update_tasks_status_claimant', LibAPI\PDOWrapper::cleanse($task_id) . ',10,' . LibAPI\PDOWrapper::cleanse($user_id) . ',NULL');
        }
        return $success;
    }

    public function unClaimTask($task_id, $user_id, $userFeedback = null)
    {
        $args = LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanseNullOrWrapStr($userFeedback) . ',0';
        $result = LibAPI\PDOWrapper::call('unClaimTaskMemsource', $args);

        LibAPI\PDOWrapper::call('update_tasks_status', LibAPI\PDOWrapper::cleanse($task_id) . ',2,NULL');
        return $result[0]['result'];
    }

    public function set_memsource_status($task_id, $memsource_task_uid, $status)
    {
        $args = LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($memsource_task_uid) . ',' . LibAPI\PDOWrapper::cleanseNullOrWrapStr($status);
        LibAPI\PDOWrapper::call('set_memsource_status', $args);
    }

    public function get_allow_download($task, $memsource_task)
    {
        return 0;
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
        return $result[0];
    }

    public function set_paid_status($task_id)
    {
        LibAPI\PDOWrapper::call('set_paid_status', LibAPI\PDOWrapper::cleanse($task_id));
    }

    public function update_paid_status($paid_status)
    {
        LibAPI\PDOWrapper::call('update_paid_status',
            LibAPI\PDOWrapper::cleanse($paid_status['task_id']) . ',' .
            LibAPI\PDOWrapper::cleanse($paid_status['purchase_order']) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($paid_status['payment_status']) . ',' .
            LibAPI\PDOWrapper::cleanse($paid_status['unit_rate']) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($paid_status['status_changed']));
    }

    public function clear_paid_status($task_id)
    {
        LibAPI\PDOWrapper::call('clear_paid_status', LibAPI\PDOWrapper::cleanse($task_id));
    }

    public function get_paid_for_project($project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_paid_for_project', LibAPI\PDOWrapper::cleanse($project_id));
        if (empty($result)) return [];
        $paids = [];
        foreach ($result as $row) {
            $paids[$row['id']] = $row['level'];
        }
        return $paids;
    }

    public function sync_po()
    {
        $ch = curl_init('https://www.googleapis.com/oauth2/v4/token');
        $data = [
            'client_id' => Common\Lib\Settings::get('google_ss.client_id'),
            'client_secret' => Common\Lib\Settings::get('google_ss.client_secret'),
            'refresh_token' => Common\Lib\Settings::get('google_ss.refresh_token'),
            'grant_type' => 'refresh_token',
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($result, true);
        if (empty($res['access_token'])) {
            error_log("Failed to get Google access_token: $result");
            return 0;
        }
        $access_token = $res['access_token'];
        $ch = curl_init('https://sheets.googleapis.com/v4/spreadsheets/1Q2jqB3bol0_n-Gs75mBS0ik8S7-12GC2qtvddKdio5s/values/Zahara');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', "Authorization: Bearer $access_token"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($result, true);
        if (empty($res['values'])) {
            error_log("Failed to read data from Google: $result");
            return 0;
        }

        $completed_paid_tasks = LibAPI\PDOWrapper::call('get_completed_paid_tasks', '');

        $zahara_purchase_orders = LibAPI\PDOWrapper::call('get_zahara_purchase_orders', '');
        if (empty($zahara_purchase_orders)) $zahara_purchase_orders = [];
        $purchase_order_hashs = [];
        foreach ($zahara_purchase_orders as $po) $purchase_orders_hashs[$po['purchase_order']] = $po['md5_hash'];

        $po_ss_completed = [];
        foreach ($res['values'] as $row) {
            if (!is_numeric($row[0])) continue;
            if ($row[10] == 'Completed') $po_ss_completed[$row[0]] = 1;

            $hash = '';
            foreach ($row as $i) $hash .= $i;

            $insert = -1;
            if (empty($purchase_order_hashs[$row[0]])) {
                $insert = 1;
            } elseif ($purchase_order_hashs[$row[0]] != md5($hash)) {
                $insert = 0;
            }
            if ($insert != -1) {
                $args = LibAPI\PDOWrapper::cleanse($row[0]) . ',';
                if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $row[2])) $args .= LibAPI\PDOWrapper::cleanseWrapStr($row[2]) . ',';
                else $args .= 'NULL,';
                $args .= LibAPI\PDOWrapper::cleanseWrapStr($row[3]) . ',';
                $args .= LibAPI\PDOWrapper::cleanseWrapStr($row[4]) . ',';
                if (!is_numeric($row[7])) $row[7] = 0;
                $args .= LibAPI\PDOWrapper::cleanse($row[7]) . ',' .
                $args .= LibAPI\PDOWrapper::cleanseWrapStr($row[8]) . ',';
                $args .= LibAPI\PDOWrapper::cleanseWrapStr($row[9]) . ',';
                $args .= LibAPI\PDOWrapper::cleanseWrapStr($row[13]) . ',';
                $args .= LibAPI\PDOWrapper::cleanseWrapStr($row[10]) . ',';
                $args .= LibAPI\PDOWrapper::cleanseWrapStr($row[14]) . ',';
                if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $row[11])) $args .= LibAPI\PDOWrapper::cleanseWrapStr($row[11]) . ',';
                else $args .= 'NULL,';
                $args .= LibAPI\PDOWrapper::cleanseWrapStr(md5($hash));
                LibAPI\PDOWrapper::call('insert_update_zahara_purchase_orders', "$insert,$args");
            }
        }

        $ids = [];
        foreach ($completed_paid_tasks as $task) {
            if ($task['payment_status'] == 'Unsettled' && !empty($po_ss_completed[$task['purchase_order']])) {
                $linguist_total_for_project = 0;
                foreach ($completed_paid_tasks as $t) {
                    if ($task['project_id'] == $t['project_id'] && $task['user_id'] == $t['user_id'])
                        $linguist_total_for_project += $t['word-count']*$t['unit_rate'];
                }
                if ($linguist_total_for_project < 600) $status = 'Ready for payment';
                else                                   $status = 'Pending documentation';
                LibAPI\PDOWrapper::call('update_paid_status_status', LibAPI\PDOWrapper::cleanse($task['id']) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($status));
                $ids[] = $task['id'];
            }
        }
        LibAPI\PDOWrapper::call('insert_sync_po_event', LibAPI\PDOWrapper::cleanse(Common\Lib\UserSession::getCurrentUserID()) . ',' . LibAPI\PDOWrapper::cleanse(count($ids)) . ',' . LibAPI\PDOWrapper::cleanseWrapStr(implode(',', $ids)));
        return count($ids) + 1;
    }
}
