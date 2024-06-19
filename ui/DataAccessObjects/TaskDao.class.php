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
        $ret = null;
        $result = LibAPI\PDOWrapper::call('getTaskPreReqs', LibAPI\PDOWrapper::cleanseNull($taskId));
        if ($result) {
            $ret = [];
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel('Task', $row);
            }
        }
        return $ret;
    }

    public function getProofreadTask($id)
    {
        $request = "{$this->siteApi}v0/tasks/proofreadTask/$id";
        $response =$this->client->call("\SolasMatch\Common\Protobufs\Models\Task", $request);
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
        $reviews = [];
        $result = LibAPI\PDOWrapper::call('getTaskReviews', 'NULL,' . LibAPI\PDOWrapper::cleanseNull($taskId) . ',NULL,NULL,NULL,NULL,NULL,NULL');
        if ($result) {
            foreach ($result as $row) {
                $reviews[] = Common\Lib\ModelFactory::buildModel('TaskReview', $row);
            }
        }
        return $reviews;
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
            LibAPI\PDOWrapper::cleanseNull($task->get_word_count_partner_weighted()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->get_word_count_original()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->get_source_quantity()) . ',' .
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
            LibAPI\PDOWrapper::cleanseNull($task->get_word_count_partner_weighted()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->get_word_count_original()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task->get_source_quantity()) . ',' .
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
        LibAPI\PDOWrapper::call('deleteTask', LibAPI\PDOWrapper::cleanseNull($taskId));
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
        if (preg_match('/^\d*$/', $memsource_project['owner_uid'])) $user_id = (int)$memsource_project['owner_uid'];
        else                                                        $user_id = $projectDao->get_user_id_from_memsource_user($memsource_project['owner_uid']);
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

    // User Feedback, feedback sent from the user who claimed the task to the organisation
    public function sendUserFeedback($taskId, $userId, $feedback)
    {
        if (empty($feedback)) $feedback = '';
        LibAPI\PDOWrapper::call('insert_queue_request', '3,11,0,0,0,0,' . LibAPI\PDOWrapper::cleanse($taskId) . ',' . LibAPI\PDOWrapper::cleanse($userId) . ',' .LibAPI\PDOWrapper::cleanseWrapStr($feedback));
    }

    public function submitReview($review)
    {
        $comment = $review->getComment();
        if (!empty($comment)) $comment = substr($comment, 0, 8192);

        $ret = null;
        $args = LibAPI\PDOWrapper::cleanseNull($review->getProjectId()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($review->getTaskId()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($review->getUserId()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($review->getCorrections()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($review->getGrammar()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($review->getSpelling()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($review->getConsistency()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($review->getReviseTaskId()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($comment);
        $result = LibAPI\PDOWrapper::call('submitTaskReview', $args);
        if ($result) {
            $ret = $result[0]['result'];
        }
        return $ret;
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
        if (strpos($this->siteApi, 'twbplatform')) return "https://twbplatform.org/simplesaml/saml2/idp/SSOService.php?spentityid=https://eu.phrase.com/idm/saml/hoKMZESrFauJjF1hpLXwwo&RelayState=https://cloud.memsource.com/web/project2/show/{$memsource_project['memsource_project_uid']}";
        else                                 return "https://dev.translatorswb.org/simplesaml/saml2/idp/SSOService.php?spentityid=https://eu.phrase.com/idm/saml/5w166eozpDWHg3OKbfYvxm&RelayState=https://cloud.memsource.com/web/project2/show/{$memsource_project['memsource_project_uid']}";
    }

    public function get_creator($project_id, $memsource_project = 0) {
        if ($memsource_project) {
            $projectDao = new ProjectDao();
            if (preg_match('/^\d*$/', $memsource_project['owner_uid'])) $user_id = (int)$memsource_project['owner_uid'];
            else                                                        $user_id = $projectDao->get_user_id_from_memsource_user($memsource_project['owner_uid']);
            if (!$user_id) $user_id = 62927; // translators@translatorswithoutborders.org
//(**)dev server            if (!$user_id) $user_id = 3297;
            $result = $projectDao->get_user($user_id);
            return $result[0];
        }

        $result = LibAPI\PDOWrapper::call('get_creator', LibAPI\PDOWrapper::cleanse($project_id));
        if (empty($result)) return ['id' => 99269, 'email' => 'projects@translatorswithoutborders.org'];
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
        if (strpos($this->siteApi, 'twbplatform')) return "https://twbplatform.org/simplesaml/saml2/idp/SSOService.php?spentityid=https://eu.phrase.com/idm/saml/hoKMZESrFauJjF1hpLXwwo&RelayState=https://cloud.memsource.com/web/job/{$memsource_task['memsource_task_uid']}/translate";
        else                                 return "https://dev.translatorswb.org/simplesaml/saml2/idp/SSOService.php?spentityid=https://eu.phrase.com/idm/saml/5w166eozpDWHg3OKbfYvxm&RelayState=https://cloud.memsource.com/web/job/{$memsource_task['memsource_task_uid']}/translate";
    }

    public function get_matecat_url_regardless($task, $memsource_task)
    {
        if (empty($memsource_task)) return '';
        if (strpos($this->siteApi, 'twbplatform')) return "https://twbplatform.org/simplesaml/saml2/idp/SSOService.php?spentityid=https://eu.phrase.com/idm/saml/hoKMZESrFauJjF1hpLXwwo&RelayState=https://cloud.memsource.com/web/job/{$memsource_task['memsource_task_uid']}/translate";
        else                                 return "https://dev.translatorswb.org/simplesaml/saml2/idp/SSOService.php?spentityid=https://eu.phrase.com/idm/saml/5w166eozpDWHg3OKbfYvxm&RelayState=https://cloud.memsource.com/web/job/{$memsource_task['memsource_task_uid']}/translate";
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
      if ($success = $result[0]['result']) {
        $this->update_task_rate_from_user($task_id, $user_id);

        if ($make_tasks_claimable) {
            $projectDao = new ProjectDao();
            $task = $this->getTask($task_id);
            $projectDao->make_tasks_claimable($task->getProjectId());

            LibAPI\PDOWrapper::call('update_tasks_status_claimant', LibAPI\PDOWrapper::cleanse($task_id) . ',10,' . LibAPI\PDOWrapper::cleanse($user_id) . ',NULL');
        }
      }
        return $success;
    }

    public function claimTaskAndDeny($task_id, $user_id, $memsource_task)
    {
        $result = LibAPI\PDOWrapper::call('claimTask', LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanse($user_id));
        if ($success = $result[0]['result']) {
            $this->update_task_rate_from_user($task_id, $user_id);

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

    public function update_task_rate_from_user($task_id, $user_id)
    {
        $result = LibAPI\PDOWrapper::call('get_user_rate_for_task', LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) return;
        $paid_status = $this->get_paid_status($task_id);
        if (empty($paid_status)) return;
        $paid_status['unit_rate'] = $result[0]['unit_rate'];
        $this->update_paid_status($paid_status);
        error_log("update_task_rate_from_user($task_id, $user_id): " . $paid_status['unit_rate']);
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

    public function list_qualified_translators($task_id, $org_id, $include_site)
    {
        $result = LibAPI\PDOWrapper::call('list_qualified_translators', LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanse($org_id) . ',' . LibAPI\PDOWrapper::cleanse($include_site));
        return $result;
    }

    public function list_task_invites_not_sent_no_source($task_id, $site_admin)
    {
        $result = LibAPI\PDOWrapper::call('list_task_invites_not_sent_no_source', LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanse($site_admin));
        if (empty($result)) $result = array();
        return $result;
    }

    public function list_task_invites_not_sent_no_source_strict($task_id, $site_admin)
    {
        $result = LibAPI\PDOWrapper::call('list_task_invites_not_sent_no_source_strict', LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanse($site_admin));
        if (empty($result)) $result = array();
        return $result;
    }

    public function list_task_invites_not_sent_strict($task_id, $site_admin)
    {
        $result = LibAPI\PDOWrapper::call('list_task_invites_not_sent_strict', LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanse($site_admin));
        if (empty($result)) $result = array();
        return $result;
    }

    public function list_task_invites_not_sent($task_id, $site_admin)
    {
        $result = LibAPI\PDOWrapper::call('list_task_invites_not_sent', LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanse($site_admin));
        if (empty($result)) $result = array();
        return $result;
    }

    public function list_task_invites_not_sent_words($task_id)
    {
        $result = LibAPI\PDOWrapper::call('list_task_invites_not_sent_words', LibAPI\PDOWrapper::cleanse($task_id));
        if (empty($result)) $result = array();
        return $result;
    }

    public function list_task_invites_not_sent_words_no_source($task_id)
    {
        $result = LibAPI\PDOWrapper::call('list_task_invites_not_sent_words_no_source', LibAPI\PDOWrapper::cleanse($task_id));
        if (empty($result)) $result = array();
        return $result;
    }

    public function list_task_invites_not_sent_rates($task_id)
    {
        $result = LibAPI\PDOWrapper::call('list_task_invites_not_sent_rates', LibAPI\PDOWrapper::cleanse($task_id));
        if (empty($result)) $result = [];
        return $result;
    }

    public function users_to_discard_for_search($task_type, $org_id)
    {
        $result = LibAPI\PDOWrapper::call('users_to_discard_for_search', LibAPI\PDOWrapper::cleanse($task_type) . ',' . LibAPI\PDOWrapper::cleanse($org_id));
        if (empty($result)) return [];

        $users = [];
        foreach ($result as $row) {
            $users[] = $row['user_id'];
        }
        return $users;
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

    public function get_user_paid_eligible_pairs($task_id, $no_source, $not_strict)
    {
        $result = LibAPI\PDOWrapper::call('get_user_paid_eligible_pairs', LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanse($no_source) . ',' . LibAPI\PDOWrapper::cleanse($not_strict));
        if (empty($result)) $result = [];

        $eligible = [];
        foreach ($result as $row) {
            $eligible[$row['user_id']] = $row['eligible_level'];
        }
        return $eligible;
    }

    public function create_user_paid_eligible_pair($user_id, $language_id_source, $country_id_source, $language_id_target, $country_id_target, $eligible_level)
    {
        LibAPI\PDOWrapper::call('create_user_paid_eligible_pair',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanse($language_id_source) . ',' .
            LibAPI\PDOWrapper::cleanse($country_id_source) . ',' .
            LibAPI\PDOWrapper::cleanse($language_id_target) . ',' .
            LibAPI\PDOWrapper::cleanse($country_id_target) . ',' .
            LibAPI\PDOWrapper::cleanse($eligible_level));
            error_log("create_user_paid_eligible_pair($user_id, $language_id_source, $country_id_source, $language_id_target, $country_id_target, $eligible_level)");
    }

    public function remove_user_paid_eligible_pair($user_id, $language_id_source, $country_id_source, $language_id_target, $country_id_target)
    {
        LibAPI\PDOWrapper::call('remove_user_paid_eligible_pair',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanse($language_id_source) . ',' .
            LibAPI\PDOWrapper::cleanse($country_id_source) . ',' .
            LibAPI\PDOWrapper::cleanse($language_id_target) . ',' .
            LibAPI\PDOWrapper::cleanse($country_id_target));
            error_log("remove_user_paid_eligible_pair($user_id, $language_id_source, $country_id_source, $language_id_target, $country_id_target)");
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
            LibAPI\PDOWrapper::cleanse($paid_status['unit_rate_pricing']) . ',' .
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
    public function get_payment_status_for_project($project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_payment_status_for_project', LibAPI\PDOWrapper::cleanse($project_id));
        if (empty($result)) return [];
        $paids = [];
        foreach ($result as $row) {
            $paids[$row['id']] = $row;
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
        foreach ($zahara_purchase_orders as $po) $purchase_order_hashs[$po['purchase_order']] = $po['md5_hash'];

        $po_ss_completed = [];
        foreach ($res['values'] as $row) {
            if (!is_numeric($row[0])) continue;
            $row[0] = (int)$row[0];

            if ($row[10] == 'Completed') $po_ss_completed[$row[0]] = 1;

            if (empty($row[11])) $row[11] = '';
            if (empty($row[13])) $row[13] = '';
            if (empty($row[14])) $row[14] = '';

            $hash = '';
            foreach ($row as $v) $hash .= $v;

            $insert = -1;
            if (empty($purchase_order_hashs[$row[0]])) {
                $insert = 1;
                error_log('Inserting PO: ' . $row[0]);
            } elseif ($purchase_order_hashs[$row[0]] != md5($hash)) {
                $insert = 0;
                error_log('Updating PO: ' . $row[0]);
            }
            if ($insert != -1) {
                $args = LibAPI\PDOWrapper::cleanse($row[0]) . ',';
                if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $row[2])) $args .= LibAPI\PDOWrapper::cleanseWrapStr($row[2]) . ',';
                else $args .= 'NULL,';
                $args .= LibAPI\PDOWrapper::cleanseWrapStr($row[3]) . ',';
                $args .= LibAPI\PDOWrapper::cleanseWrapStr($row[4]) . ',';
                if (!is_numeric($row[7])) $row[7] = 0;
                $args .= LibAPI\PDOWrapper::cleanse($row[7]) . ',';
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
                $total_expected_cost = $task['word-count']*$task['unit_rate'];
                if ($task['divide_rate_by_60']) $total_expected_cost /= 60;
error_log("total_expected_cost: $total_expected_cost, divide_rate_by_60 " . $task['divide_rate_by_60']);
                if ($total_expected_cost < 600) $status = 'Ready for payment';
                else                            $status = 'Pending documentation';
                error_log('Task: ' . $task['id'] . ', PO: ' . $task['purchase_order'] . " Changed to $status");
                LibAPI\PDOWrapper::call('update_paid_status_status', LibAPI\PDOWrapper::cleanse($task['id']) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($status));
                $ids[] = $task['id'];
            }
        }
        LibAPI\PDOWrapper::call('insert_sync_po_event', LibAPI\PDOWrapper::cleanse(Common\Lib\UserSession::getCurrentUserID()) . ',' . LibAPI\PDOWrapper::cleanse(count($ids)) . ',' . LibAPI\PDOWrapper::cleanseWrapStr(implode(',', $ids)));
        return count($ids) + 1;
    }

    public function update_hubspot_deals($deal_id)
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
        $ch = curl_init('https://sheets.googleapis.com/v4/spreadsheets/1eXrwnydULQwSBIJRlpkjZBTJtXb8ph1ZPaJ2h8jBEZQ/values/Deals');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', "Authorization: Bearer $access_token"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($result, true);
        if (empty($res['values'])) {
            error_log("Failed to read data from Google: $result");
            return 0;
        }

        $hubspot_deals = LibAPI\PDOWrapper::call('get_hubspot_deals', '');
        if (empty($hubspot_deals)) $hubspot_deals = [];
        $deal_id_hashs = [];
        foreach ($hubspot_deals as $deal) $deal_id_hashs[$deal['deal_id']] = $deal['md5_hash'];

        $found = 0;
        foreach ($res['values'] as $row) {
            if (!is_numeric($row[2])) continue;
            $row[2] = (int)$row[2];
            if ($row[2] < 1) continue;
            if ($row[2] == $deal_id) $found = 1;

            $hash = '';
            foreach ($row as $v) $hash .= $v;

            $insert = -1;
            if (empty($deal_id_hashs[$row[2]])) {
                $insert = 1;
                error_log('Inserting HubSpot Deal: ' . $row[2]);
            } elseif ($deal_id_hashs[$row[2]] != md5($hash)) {
                $insert = 0;
                error_log('Updating HubSpot Deal: ' . $row[2]);
            }
            if ($insert != -1) {
                $args = LibAPI\PDOWrapper::cleanse($row[2]) . ',';
                $args .= LibAPI\PDOWrapper::cleanseWrapStr($row[0]) . ',';
                $args .= LibAPI\PDOWrapper::cleanseWrapStr('') . ',';
                $args .= LibAPI\PDOWrapper::cleanseWrapStr($row[1]) . ',';

                $matches = [];
                if (preg_match('#^(\d{4})-(\d{2})-(\d{2})$#', $row[4], $matches)) {
                    $args .= LibAPI\PDOWrapper::cleanseWrapStr("{$matches[1]}-{$matches[2]}-{$matches[3]} 00:00:00") . ',';
                } else $args .= 'NULL,';
                if (preg_match('#^(\d{4})-(\d{2})-(\d{2})$#', $row[5], $matches)) {
                    $args .= LibAPI\PDOWrapper::cleanseWrapStr("{$matches[1]}-{$matches[2]}-{$matches[3]} 23:59:59") . ',';
                } else $args .= 'NULL,';

                if (empty($row[6])) $row[6] = '0.0';
                $row[6] = str_replace(['$', ','], ['', ''], $row[6]);
                if (!is_numeric($row[6])) $row[6] = '0.0';
                $args .= LibAPI\PDOWrapper::cleanse($row[6]) . ',';

                if (empty($row[7])) $row[7] = '0.0';
                $row[7] = str_replace(['$', ','], ['', ''], $row[7]);
                if (!is_numeric($row[7])) $row[7] = '0.0';
                $args .= LibAPI\PDOWrapper::cleanse($row[7]) . ',';

                if (empty($row[8])) $row[8] = '0.0';
                $row[8] = str_replace(['$', ','], ['', ''], $row[8]);
                if (!is_numeric($row[8])) $row[8] = '0.0';
                $args .= LibAPI\PDOWrapper::cleanse($row[8]) . ',';

                if (empty($row[11])) $row[11] = '';
                $args .= LibAPI\PDOWrapper::cleanseWrapStr($row[11]) . ',';
                $args .= LibAPI\PDOWrapper::cleanseWrapStr(md5($hash));
                LibAPI\PDOWrapper::call('insert_update_hubspot_deal', "$insert,$args");
                error_log("insert_update_hubspot_deal($insert,$args");
            }
        }
        if (!empty($deal_id) && !$found) {
            error_log("Not found!: update_hubspot_deals($deal_id)");
            return -1;
        }
        return 1;
    }

    public function get_project_complete_date($project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_project_complete_date', LibAPI\PDOWrapper::cleanse($project_id));
        if ($result) return $result[0];
        return ['deal_id' => 0, 'allocated_budget' => 0];
    }

    public function update_project_deal_id($project_id, $deal_id)
    {
        LibAPI\PDOWrapper::call('update_project_deal_id',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanse($deal_id));
    }

    public function update_project_allocated_budget($project_id, $allocated_budget)
    {
        LibAPI\PDOWrapper::call('update_project_allocated_budget',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanse($allocated_budget));
    }

    public function insert_task_url($task_id, $url)
    {
        LibAPI\PDOWrapper::call('insert_task_url',
            LibAPI\PDOWrapper::cleanse($task_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($url));
    }

    public function update_task_url($task_id, $url)
    {
        LibAPI\PDOWrapper::call('update_task_url',
            LibAPI\PDOWrapper::cleanse($task_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($url));
    }

    public function get_task_url($task_id)
    {
        $result = LibAPI\PDOWrapper::call('get_task_url', LibAPI\PDOWrapper::cleanse($task_id));
        if (empty($result)) return '';
        return $result[0]['url'];
    }

    public function user_within_limitations($user_id, $task_id)
    {
        $result = LibAPI\PDOWrapper::call('user_within_limitations', LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanse($task_id));
        return $result[0]['result'];
    }

    public function insert_update_user_task_limitation($user_id, $admin_id, $max_not_complete_tasks, $allowed_types, $excluded_orgs, $limit_profile_changes)
    {
        LibAPI\PDOWrapper::call('insert_update_user_task_limitation', LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanse($admin_id) . ',' . LibAPI\PDOWrapper::cleanse($max_not_complete_tasks) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($allowed_types) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($excluded_orgs) . ',' . LibAPI\PDOWrapper::cleanse($limit_profile_changes));
    }

    public function get_user_task_limitation($user_id)
    {
        $result = LibAPI\PDOWrapper::call('get_user_task_limitation', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) return ['max_not_comlete_tasks' => 0, 'allowed_types' => '', 'excluded_orgs' => '', 'limit_profile_changes' => 0];
        return $result[0];
    }

    public function insert_update_linguist_payment_information($user_id, $admin_id, $country_id, $google_drive_link, $linguist_name)
    {
        LibAPI\PDOWrapper::call('insert_update_linguist_payment_information', LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanse($admin_id) . ',' . LibAPI\PDOWrapper::cleanse($country_id) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($google_drive_link) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($linguist_name));
    }

    public function get_linguist_payment_information($user_id)
    {
        $result = LibAPI\PDOWrapper::call('get_linguist_payment_information', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) return ['admin_id' => 0, 'admin_name' => '', 'country_id' => 0, 'google_drive_link' => ''];
        return $result[0];
    }

    public function get_active_languages($user_id)
    {
        $result = LibAPI\PDOWrapper::call('get_active_languages', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) return [];
        return $result;
    }

    public function generate_invoices()
    {
        $RH = new \SolasMatch\UI\RouteHandlers\UserRouteHandler();
        $statsDao = new StatisticsDao();

        $sow_reports = $statsDao->sow_report();

        $tasks = 0;
        $invoices = [];
        foreach ($sow_reports as $row) {
            if ($row['processed'] == 0 && !empty($row['google_drive_link']) && !empty($row['po_status']) && ($row['po_status'] == 'Completed' || $row['po_status'] == 'Approved')) {
                $i = $row['user_id'];
                if ($row['total_expected_cost'] >= 600) $i = "$i-P";
                if (empty($invoices[$i])) $invoices[$i]   = [$row];
                else                      $invoices[$i][] = $row;
                $tasks++;
            }
        }

        $access_token = $this->get_google_access_token();
        $invoice_date = date('Y-m-d H:i:s');
        foreach ($invoices as $invoice) {
            $amount = 0;
            $proforma = 0;
            foreach ($invoice as $row) {
                $amount += $row['total_expected_cost'];
                if ($row['total_expected_cost'] >= 600) $proforma = 1;
            }
            $result = LibAPI\PDOWrapper::call('insert_invoice', LibAPI\PDOWrapper::cleanse($proforma) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($invoice_date) . ',' . LibAPI\PDOWrapper::cleanse($row['user_id']) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($row['linguist']) . ',' . LibAPI\PDOWrapper::cleanse($amount));
            $invoice_number = $result[0]['id'];

            foreach ($invoice as $row) {
                LibAPI\PDOWrapper::call('update_invoice_processed', LibAPI\PDOWrapper::cleanse($row['task_id']) . ',' . LibAPI\PDOWrapper::cleanse($invoice_number));
            }
            $TWB = '-TWB-';
            if ($proforma) $TWB = '-DRAFT-';
            $filename = date('Ym') . $TWB . str_pad($invoice_number, 4, '0', STR_PAD_LEFT) . '.pdf';

            [$fn, $file] = $RH->get_invoice_pdf($invoice_number);
            $data = [
                'metadata' => new \CURLStringFile(json_encode(['name' => $filename, 'mimeType' => 'application/pdf', 'parents' => [substr($invoice[0]['google_drive_link'], strrpos($invoice[0]['google_drive_link'], '/') + 1)]]), $filename, 'application/json; charset=UTF-8'),
                'file'     => new \CURLStringFile($file, $filename, 'application/pdf')
            ];
            $ch = curl_init('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data', "Authorization: Bearer $access_token"]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
            $res = json_decode($result, true);
            if (empty($res['id'])) {
                error_log("Failed to read data from Google (upload): $result");
                $res['id'] = '';
            }
            LibAPI\PDOWrapper::call('update_invoice_filename', LibAPI\PDOWrapper::cleanse($invoice_number) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($filename) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($res['id']));
        }
        return [$tasks, count($invoices)];
    }

    public function set_invoice_paid($invoice_number)
    {
        $RH = new \SolasMatch\UI\RouteHandlers\UserRouteHandler();

        LibAPI\PDOWrapper::call('set_invoice_paid', LibAPI\PDOWrapper::cleanse($invoice_number));

        $access_token = $this->get_google_access_token();
        [$fn, $google_id] = $this->get_invoice_file_id($invoice_number);
        [$fn, $file] = $RH->get_invoice_pdf($invoice_number);

        $ch = curl_init("https://www.googleapis.com/upload/drive/v3/files/$google_id?&uploadType=media&supportsAllDrives=true");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/pdf', "Authorization: Bearer $access_token"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($result, true);
        if (empty($res['id'])) error_log("Failed to read data from Google (PATCH): $result");
    }

    public function set_invoice_revoked($invoice_number)
    {
        $RH = new \SolasMatch\UI\RouteHandlers\UserRouteHandler();

        $access_token = $this->get_google_access_token();
        [$filename, $google_id] = $this->get_invoice_file_id($invoice_number);

        $ch = curl_init("https://www.googleapis.com/drive/v3/files/$google_id?supportsAllDrives=true");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['name' => str_replace('.pdf', '-REVOKED.pdf', $filename)]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', "Authorization: Bearer $access_token"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($result, true);
        if (empty($res['id'])) error_log("Failed to read data from Google (rename): $result");

        LibAPI\PDOWrapper::call('set_invoice_revoked', LibAPI\PDOWrapper::cleanse($invoice_number));
    }

    public function get_google_access_token()
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
            error_log("Failed to get Google access_token (drive): $result");
            return '';
        }
        return $res['access_token'];
    }

    public function get_invoice_file_id($invoice_number)
    {
        $result = LibAPI\PDOWrapper::call('get_invoice', LibAPI\PDOWrapper::cleanse($invoice_number));
        if (empty($result)) return ['', ''];
        return [$result[0]['filename'], $result[0]['google_id']];
    }
}
