<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\API\Lib as LibAPI;
use \SolasMatch\Common as Common;

define("PROJECTQUEUE", "3");
define("UserTaskCancelled", "36");

require_once __DIR__."/../../Common/Enums/HttpStatusEnum.class.php";
require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/../../Common/protobufs/models/OAuthResponse.php";
require_once __DIR__."/BaseDao.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";
require_once __DIR__."/../../Common/Enums/MemsourceRoleEnum.class.php";
require_once __DIR__."/../../Common/lib/MemsourceTimezone.class.php";
require_once __DIR__ . '/../../Common/lib/Authentication.class.php';
require_once __DIR__ . '/../../Common/lib/MoodleRest.php';


class UserDao extends BaseDao
{
    public function __construct()
    {
        $this->client = new Common\Lib\APIHelper(Common\Lib\Settings::get("ui.api_format"));
        $this->siteApi = Common\Lib\Settings::get("site.api");
        $this->usernamePrefix = strpos($this->siteApi, 'twbplatform') ? 'TWB_' : 'DEV_';
        $this->memsourceAuthUrlApi = Common\Lib\Settings::get("memsource.api_auth_url");
        $this->memsourceApiV1 = Common\Lib\Settings::get("memsource.api_url_v1");
        $this->memsourceApiV2 = Common\Lib\Settings::get("memsource.api_url_v2");
        $this->memsourceApiToken = Common\Lib\Settings::get("memsource.memsource_api_token");
    }
    
    public function getUser($userId)
    {
        $ret = null;
        
        $ret = Common\Lib\CacheHelper::getCached(
            Common\Lib\CacheHelper::GET_USER.$userId,
            Common\Enums\TimeToLiveEnum::MINUTE,
            function ($args) {
                $user = null;
                $result = LibAPI\PDOWrapper::call('getUser', LibAPI\PDOWrapper::cleanseNull($args[0]) . ',null,null,null,null,null,null,null,null');
                if (!empty($result)) {
                    $user = Common\Lib\ModelFactory::buildModel('User', $result[0]);
                    if (!is_null($user)) {
                        $user->setPassword('');
                        $user->setNonce('');
                    }
                }
                return $user;
            },
            array($userId)
        );
        return $ret;
    }
    
    public function getUserByEmail($email, $headerHash = null)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/getByEmail/$email/email";
        $ret = $this->client->call(
            "\SolasMatch\Common\Protobufs\Models\User",
            $request,
            Common\Enums\HttpMethodEnum::GET,
            null,
            null,
            null,
            array("X-Custom-Authorization:$headerHash")
        );
        return $ret;
    }

    public function isUserVerified($userId)
    {
        $ret = false;
        $request = "{$this->siteApi}v0/users/$userId/verified";
        $ret = $this->client->call(null, $request);
        return $ret;
    }

    public function isSubscribedToTask($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/subscribedToTask/$userId/$taskId";
        $ret = $this->client->call(null, $request);
        return $ret;
    }

    public function isSubscribedToProject($userId, $projectId)
    {
        $request = "{$this->siteApi}v0/users/subscribedToProject/$userId/$projectId";
        $ret = $this->client->call(null, $request);
        if (empty($ret)) return 0;
        return $ret;
    }

    public function getUserOrgs($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/orgs";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\Organisation"), $request);
        return $ret;
    }

    public function getUserBadges($user_id)
    {
        $ret = null;
        $args = LibAPI\PDOWrapper::cleanse($user_id);
        $result = LibAPI\PDOWrapper::call('getUserBadges', $args);
        if ($result) {
            $ret = array();
            foreach ($result as $badge) {
                $ret[] = Common\Lib\ModelFactory::buildModel('Badge', $badge);
            }
        }
        return $ret;
    }

    public function getUserTags($userId, $limit = null)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tags";

        $args = null;
        if ($limit) {
            $args = array("limit" => $limit);
        }
        $ret = $this->client->call(
            array("\SolasMatch\Common\Protobufs\Models\Tag"),
            $request,
            Common\Enums\HttpMethodEnum::GET,
            null,
            $args
        );
        return $ret;
    }

    public function getUserTasks($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tasks";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\Task"), $request);
        return $ret;
    }

    public function getUserTaskReviews($userId, $taskId)
    {
        $result = LibAPI\PDOWrapper::call('getTaskReviews', 'NULL,' . LibAPI\PDOWrapper::cleanseNull($taskId) . ',' . LibAPI\PDOWrapper::cleanseNull($userId) . ',NULL,NULL,NULL,NULL,NULL');
        if (empty($result)) return null;
        return Common\Lib\ModelFactory::buildModel('TaskReview', $result[0]);
    }

    public function getUserTaskStreamNotification($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/taskStreamNotification";
        $ret = $this->client->call("\SolasMatch\Common\Protobufs\Models\UserTaskStreamNotification", $request);
        return $ret;
    }

    public function getUserTopTasks($userId, $strict = false, $limit = null, $filter = array(), $offset = null)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/topTasks";

        $args = array();
        if ($limit) {
            $args["limit"] = $limit;
        }

        if ($offset) {
            $args["offset"] = $offset;
        }

        $filterString = "";
        if ($filter) {
            if (isset($filter['taskType']) && $filter['taskType'] != '') {
                $filterString .= "taskType:".$filter['taskType'].';';
            }
            if (isset($filter['sourceLanguage']) && $filter['sourceLanguage'] != '') {
                $filterString .= "sourceLanguage:".$filter['sourceLanguage'].';';
            }
            if (isset($filter['targetLanguage']) && $filter['targetLanguage'] != '') {
                $filterString .= "targetLanguage:".$filter['targetLanguage'].';';
            }
        }

        if ($filterString != '') {
            $args['filter'] = $filterString;
        }

        $args['strict'] = $strict;

        $ret = $this->client->call(
            array("\SolasMatch\Common\Protobufs\Models\Task"),
            $request,
            Common\Enums\HttpMethodEnum::GET,
            null,
            $args
        );
        return $ret;
    }

    public function getUserTopTasksCount($userId, $strict = false, $filter = array())
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/topTasksCount";

        $args = array();

        $filterString = '';
        if ($filter) {
            if (isset($filter['taskType']) && $filter['taskType'] != '') {
                $filterString .= "taskType:".$filter['taskType'].';';
            }
            if (isset($filter['sourceLanguage']) && $filter['sourceLanguage'] != '') {
                $filterString .= "sourceLanguage:".$filter['sourceLanguage'].';';
            }
            if (isset($filter['targetLanguage']) && $filter['targetLanguage'] != '') {
                $filterString .= "targetLanguage:".$filter['targetLanguage'].';';
            }
        }

        if ($filterString != '') {
            $args['filter'] = $filterString;
        }

        $args['strict'] = $strict;
        $ret = $this->client->call(
            null,
            $request,
            Common\Enums\HttpMethodEnum::GET,
            null,
            $args
        );
        return $ret;
    }

    public function getFilteredUserClaimedTasks($userId, $selectedOrdering, $limit, $offset, $selectedTaskType, $selectedTaskStatus)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/filteredClaimedTasks/$selectedOrdering/$limit/$offset/$selectedTaskType/$selectedTaskStatus";

        $ret = $this->client->call(
            array("\SolasMatch\Common\Protobufs\Models\Task"),
            $request,
            Common\Enums\HttpMethodEnum::GET,
            null
        );
        return $ret;
    }
    
    public function getFilteredUserClaimedTasksCount($userId, $selectedTaskType, $selectedTaskStatus)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/filteredClaimedTasksCount/$selectedTaskType/$selectedTaskStatus";

        $ret = $this->client->call(
            null,
            $request,
            Common\Enums\HttpMethodEnum::GET,
            null
        );
        return $ret;
    }
    
    public function getUserRecentTasks($userId, $limit, $offset)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/recentTasks/$limit/$offset";

        $ret = $this->client->call(
            array("\SolasMatch\Common\Protobufs\Models\Task"),
            $request,
            Common\Enums\HttpMethodEnum::GET,
            null
        );
        return $ret;
    }
    
    public function getUserRecentTasksCount($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/recentTasksCount";

        $ret = $this->client->call(
            null,
            $request,
            Common\Enums\HttpMethodEnum::GET,
            null
        );
        return $ret;
    }
    

    public function getUserArchivedTasks($userId, $offset = 0, $limit = 10)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/archivedTasks/$limit/$offset";

        $ret = $this->client->call(
            array("\SolasMatch\Common\Protobufs\Models\ArchivedTask"),
            $request,
            Common\Enums\HttpMethodEnum::GET,
            null
        );
        return $ret;
    }
    
    public function getUserArchivedTasksCount($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/archivedTasksCount";
    
        $ret = $this->client->call(
            null,
            $request,
            Common\Enums\HttpMethodEnum::GET,
            null
        );
        return $ret;
    }

    public function getUserTrackedTasks($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/trackedTasks";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\Task"), $request);
        return $ret;
    }

    public function getUserTrackedProjects($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/projects";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\Project"), $request);
        return $ret;
    }

    public function leaveOrganisation($userId, $orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/leaveOrg/$userId/$orgId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
        return $ret;
    }

    public function addUserBadge($userId, $badge)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/badges";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::POST, $badge);
        return $ret;
    }

    public function NotifyRegistered($userId)
    {
        $request = "{$this->siteApi}v0/users/NotifyRegistered/$userId";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
    }

    public function assignBadge($email, $badgeId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/assignBadge/".urlencode($email)."/$badgeId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
        return $ret;
    }

    public function removeTaskStreamNotification($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/taskStreamNotification";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
        return $ret;
    }

    public function set_special_translator($user_id, $type)
    {
        LibAPI\PDOWrapper::call('set_special_translator', LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanse($type));
    }

    public function get_special_translator($user_id)
    {
        $type = 0;
        $result = LibAPI\PDOWrapper::call('get_special_translator', LibAPI\PDOWrapper::cleanse($user_id));
        if (!empty($result)) {
            $type = $result[0]['type'];
        }
        return $type;
    }

    public function requestTaskStreamNotification($notifData)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/{$notifData->getUserId()}/taskStreamNotification";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT, $notifData);
        return $ret;
    }

    public function removeUserBadge($userId, $badgeId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/badges/$badgeId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
        return $ret;
    }

    public function claimTask($userId, $taskId, $memsource_task, $project_id, $task)
    {
error_log("claimTask($userId, $taskId, ..., $project_id, ...)");
        $taskDao = new TaskDao();
        $taskDao->claimTask($taskId, $userId, false);

        if ($memsource_task) {
            $memsource_user_uid = $this->get_memsource_user($userId);
            if (!$memsource_user_uid) {
                $ch = curl_init('https://cloud.memsource.com/web/api2/v3/users');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $user_personal_info = $this->getUserPersonalInformation($userId);
                $user_info = $this->getUser($userId);
                $user_country = $this->getUserPersonalInformation($userId)->country;
                $timezones = Common\Lib\MemsourceTimezone::timezones();
                $timezone = !empty($timezones[$user_country]) ? $timezones[$user_country] : 'Europe/Rome';
                $data = array(
                    'email' => $user_info->email,
                    'password' => 'Ab#0' . uniqid(),
                    'firstName' => str_replace(['<', '>', '&', '%', '{', '}', '[', ']', '^', '#', '*', '$'], '_', $user_personal_info->firstName),
                    'lastName'  => str_replace(['<', '>', '&', '%', '{', '}', '[', ']', '^', '#', '*', '$'], '_', $user_personal_info->lastName),
                    'role' => Common\Enums\MemsourceRoleEnum::LINGUIST,
                    'timezone' => $timezone,
                    'userName' => $this->usernamePrefix . str_replace(['<', '>', '&', '%', '{', '}', '[', ']', '^', '#', '*', '$'], '', $user_info->display_name) . "_$userId",
                    'receiveNewsletter' => false,
                    'active' => true,
                    'editAllTermsInTB' => false,
                    'editTranslationsInTM' => false,
                    'enableMT' => false,
                    'mayRejectJobs' => false,
                );
                $payload = json_encode($data);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                $authorization = 'Authorization: Bearer ' . $this->memsourceApiToken;
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));                
                $result_exec = curl_exec($ch);
                $result = json_decode($result_exec, true);                
                curl_close($ch);
                if (!empty($result['uid'])) {
                    $memsource_user_uid = $result['uid'];
                    $this->set_memsource_user($userId, 0, $memsource_user_uid);
                    error_log("LINGUIST memsource user $memsource_user_uid created for $userId");
                } else {
                    error_log("No memsource user created for $userId");
                    error_log(print_r($result, true));
                    return -1;
                }
            }
            if ($memsource_user_uid) {
                $projectDao = new ProjectDao();
                $memsource_project = $projectDao->get_memsource_project($project_id);
                $projectUid = $memsource_project['memsource_project_uid'];
                $taskUid = $memsource_task['memsource_task_uid'];
                $authorization = 'Authorization: Bearer ' . $this->memsourceApiToken;

                $url = $this->memsourceApiV1.'projects/' . $projectUid . '/jobs/' . $taskUid;
                $ch = curl_init($url);
                $deadline = $task->getDeadline();
                $data = array(
                    'status' => 'ACCEPTED',
                    'dateDue' => substr($deadline, 0, 10) . 'T' . substr($deadline, 11, 8) . 'Z',
                    'providers' => array(
                        array(
                            'type' => 'USER',
                            'id' => $memsource_user_uid
                        )
                    )
                );
                $payload = json_encode($data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                $result = curl_exec($ch);
                if ($error_number = curl_errno($ch)) {
                    error_log("Failed: claimTask($userId, $taskId...) $url Curl error ($error_number): " . curl_error($ch));
                } elseif (($responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE)) > 204) {
                    $error_number = -1;
                    error_log("Failed: claimTask($userId, $taskId...) $url responseCode: $responseCode");
                }
                curl_close($ch);
                if ($error_number) {
                    $taskDao->unClaimTask($taskId, $userId);
                    return 0;
                }

                LibAPI\PDOWrapper::call('update_tasks_status_claimant', LibAPI\PDOWrapper::cleanse($taskId) . ',10,' . LibAPI\PDOWrapper::cleanse($userId) . ',NULL');

                // This now only does the notifications
error_log("claimTask($userId, $taskId, ..., $project_id, ...) Before Notify");
                $this->client->call(null, "{$this->siteApi}v0/users/$userId/tasks/$taskId", Common\Enums\HttpMethodEnum::POST);
error_log("claimTask($userId, $taskId, ..., $project_id, ...) After Notify");

                // Add corresponding task(s) to deny list for translator
                $projectDao = new ProjectDao();
                $top_level = $projectDao->get_top_level($memsource_task['internalId']);
                $project_tasks = $projectDao->get_tasks_for_project($project_id);
                foreach ($project_tasks as $project_task) {
                    if ($top_level == $projectDao->get_top_level($project_task['internalId'])) {
                        if ($memsource_task['workflowLevel'] != $project_task['workflowLevel']) { // Not same workflowLevel
                            if ( $task->getTaskType() == Common\Enums\TaskTypeEnum::TRANSLATION ||
                                ($task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING && $project_task['task-type_id'] == Common\Enums\TaskTypeEnum::TRANSLATION)) {
//(**)Need to add additional code to deny if user translated ANY file (not just current)
//(**)Will there be index on QA/Proofread?
                                if (($memsource_task['beginIndex'] <= $project_task['endIndex']) && ($project_task['beginIndex'] <= $memsource_task['endIndex'])) { // Overlap
                                    error_log("Adding $userId to Deny List for {$project_task['id']} {$project_task['internalId']}");
                                    $taskDao->addUserToTaskBlacklist($userId, $project_task['id']);
                                }
                            }
                        }
                    }
                }
                $projectDao->make_tasks_claimable($project_id);
            }
        } else {
            $this->client->call(null, "{$this->siteApi}v0/users/$userId/tasks/$taskId", Common\Enums\HttpMethodEnum::POST);
        }
        return 1;
    }

    public function claimTask_shell($userId, $taskId)
    {
error_log("claimTask_shell($userId, $taskId)");
        $taskDao = new TaskDao();
        $taskDao->claimTask($taskId, $userId, false);
        LibAPI\PDOWrapper::call('update_tasks_status_claimant', LibAPI\PDOWrapper::cleanse($taskId) . ',3,' . LibAPI\PDOWrapper::cleanse($userId) . ',NULL');
        $this->client->call(null, "{$this->siteApi}v0/users/$userId/tasks/$taskId", Common\Enums\HttpMethodEnum::POST);
    }

    public function propagate_cancelled($cancelled, $memsource_project, $task_id, $comment)
    {
      if (!$memsource_project) return 0;
      error_log("function propagate_cancelled($cancelled... $task_id)");
      $projectDao = new ProjectDao();
      $taskDao = new TaskDao();
      $memsource_task = $projectDao->get_memsource_task($task_id);
      $task_ids = [$task_id];
      $shell_task = $memsource_task && preg_match('/^\d*$/', $memsource_task['memsource_task_uid']); // A Phrase uid will not be an int, for a Shell Task this contains task_id (an int)
      if ($cancelled && $memsource_project && $memsource_task && !$shell_task) {
          $top_level = $projectDao->get_top_level($memsource_task['internalId']);
          $project_tasks = $projectDao->get_tasks_for_project($memsource_project['project_id']);
          $task_ids = [];
          foreach ($project_tasks as $project_task) {
              if ($top_level == $projectDao->get_top_level($project_task['internalId'])) $task_ids[] = $project_task['id'];
          }
      }
      foreach ($task_ids as $index => $task_id) {
        error_log("function propagate_cancelled(... $task_id)");
        $task = $taskDao->getTask($task_id);
        if ($cancelled && $task->get_cancelled() || !$cancelled && !$task->get_cancelled()) {
            error_log('Task already in correct cancelled state');
            unset($task_ids[$index]);
            continue;
        }
        $memsource_task = $projectDao->get_memsource_task($task_id);

        $user_id = 0;
        $details_claimant = $taskDao->getUserClaimedTask($task_id);
        if ($details_claimant) $user_id = $details_claimant->getId();

        if ($memsource_project && $memsource_task) {
            $memsource_project_uid = $memsource_project['memsource_project_uid'];
            $memsource_task_uid = $memsource_task['memsource_task_uid'];
            $authorization = 'Authorization: Bearer ' . $this->memsourceApiToken;

            $status_id = $task->getTaskStatus();
            if ($status_id == Common\Enums\TaskStatusEnum::IN_PROGRESS && $projectDao->are_translations_not_all_complete($task, $memsource_task)) $status_id = Common\Enums\TaskStatusEnum::CLAIMED;

            $memsource_user_uid = 0;
            if ($details_claimant) $memsource_user_uid = $this->get_memsource_user($user_id);

            $deadline = $task->getDeadline();
            $status = 'CANCELLED';
            if (!$cancelled) {
                $task->set_cancelled(0);
                $status = [1 => 'NEW', 2 => 'NEW', 3 => 'ACCEPTED', 4 => 'COMPLETED', 10 => 'ACCEPTED'][$task->getTaskStatus()];
                $word_count = $task->get_word_count_original();
                if ($word_count < 1) $word_count = 1;
                $task->setWordCount($word_count);
                $taskDao->updateTask($task);
                $projectDao->update_tasks_status_cancelled($task_id, $status_id, 0, $comment);
            }
            $data = [
                'status' => $status,
                'dateDue' => substr($deadline, 0, 10) . 'T' . substr($deadline, 11, 8) . 'Z'
            ];
            if ($memsource_user_uid) $data['providers'] = [['type' => 'USER', 'id' => $memsource_user_uid]];
            error_log(print_r($data, true));

           if (!$shell_task) {
            $ch = curl_init($this->memsourceApiV1 . "projects/$memsource_project_uid/jobs/$memsource_task_uid");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $result = curl_exec($ch);
            curl_close($ch);
           } else error_log('Skipping Phrase for Shell Task');

            if ($cancelled) {
                $task->set_cancelled(1);
                if ($status_id == Common\Enums\TaskStatusEnum::IN_PROGRESS) {
                  if (!$shell_task) {
                    $ch = curl_init("https://cloud.memsource.com/web/api2/v1/projects/$memsource_project_uid/jobs/segmentsCount");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['jobs' => [(object)['uid' => $memsource_task['memsource_task_uid']]], 'getParts' => (object)[]]));
                    $result = curl_exec($ch);
                    curl_close($ch);
                    $result = json_decode($result, true);
                    error_log(print_r($result, true));
                    if (!empty($result['segmentsCountsResults'])) {
                        foreach ($result['segmentsCountsResults'] as $job_counts) {
                            if (!empty($job_counts['jobPartUid']) && $job_counts['jobPartUid'] == $memsource_task['memsource_task_uid']) {
                                if (isset($job_counts['counts']['confirmedWordsCount'])) {
                                    $word_count = $job_counts['counts']['confirmedWordsCount'];
                                    if ($word_count < 1) $word_count = 1;
                                    $task->setWordCount($word_count);
                                    $task->set_cancelled(2);
                                }
                            }
                        }
                    }
                  } else error_log('Skipping Phrase segmentsCount for Shell Task');
                }
                $task->setPublished(0);
                $taskDao->updateTask($task);
                $projectDao->update_tasks_status_cancelled($task_id, $status_id, 1, $comment);
            }
        }
        if ($cancelled && $user_id && $task->getTaskStatus() == Common\Enums\TaskStatusEnum::IN_PROGRESS) { // email Linguist
            $this->client->call(null, "{$this->siteApi}v0/users/$user_id/UserTaskCancelled/$task_id", Common\Enums\HttpMethodEnum::DELETE);

            $creator = $taskDao->get_creator($memsource_project['project_id'], $memsource_project); // email owner (or projects@translatorswithoutborders.org for self service)
            $args =
                LibAPI\PDOWrapper::cleanse(PROJECTQUEUE) . ',' .
                LibAPI\PDOWrapper::cleanse(UserTaskCancelled) . ',' .
                LibAPI\PDOWrapper::cleanse($creator['id']) . ',' .
                '0,0,0,' .
                LibAPI\PDOWrapper::cleanse($task_id) . ',' .
                '0,' .
                LibAPI\PDOWrapper::cleanseWrapStr('');
            LibAPI\PDOWrapper::call('insert_queue_request', $args);
            error_log("notifyUserTaskCancelled[creator]({$creator['id']}, $task_id)");
        }
      }
      return count($task_ids);
    }

    public function set_dateDue_in_memsource($task, $memsource_task, $deadline)
    {
        if ($memsource_task && !Common\Enums\TaskTypeEnum::$enum_to_UI[$task->getTaskType()]['shell_task']) {
            $memsource_user_uid = 0;
            $taskDao = new TaskDao();
            $claimant = $taskDao->getUserClaimedTask($task->getId());
            if (!empty($claimant)) $memsource_user_uid = $this->get_memsource_user($claimant->getId());
            $projectDao = new ProjectDao();
            $memsource_project = $projectDao->get_memsource_project($task->getProjectId());
            $authorization = 'Authorization: Bearer ' . $this->memsourceApiToken;
            $url = $this->memsourceApiV1 . 'projects/' . $memsource_project['memsource_project_uid'] . '/jobs/' . $memsource_task['memsource_task_uid'];
            $ch = curl_init($url);
            $data = ['dateDue' => substr($deadline, 0, 10) . 'T' . substr($deadline, 11, 8) . 'Z'];
            if ($memsource_user_uid) {
                $data['status'] = 'ACCEPTED';
                $data['providers'] = [['type' => 'USER', 'id' => $memsource_user_uid]];
            } else {
                $data['status'] = 'NEW';
            }
            $payload = json_encode($data);
            error_log("set_dateDue_in_memsource(): $payload");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $result = curl_exec($ch);
            if ($error_number = curl_errno($ch)) {
                error_log("Failed: set_dateDue_in_memsource() $url Curl error ($error_number): " . curl_error($ch));
            } elseif (($responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE)) > 204) {
                error_log("Failed: set_dateDue_in_memsource() $url responseCode: $responseCode");
            }
            curl_close($ch);
        }
    }

    public function create_memsource_user($user_id)
    {
        $ch = curl_init('https://cloud.memsource.com/web/api2/v3/users');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $user_personal_info = $this->getUserPersonalInformation($user_id);
        $user_info = $this->getUser($user_id);
        $user_country = $this->getUserPersonalInformation($user_id)->country;
        $timezones = Common\Lib\MemsourceTimezone::timezones();
        $timezone = !empty($timezones[$user_country]) ? $timezones[$user_country] : 'Europe/Rome';
        $data = array(
            'email' => $user_info->email,
            'password' => 'Ab#0' . uniqid(),
            'firstName' => str_replace(['<', '>', '&', '%', '{', '}', '[', ']', '^', '#', '*', '$'], '_', $user_personal_info->firstName),
            'lastName'  => str_replace(['<', '>', '&', '%', '{', '}', '[', ']', '^', '#', '*', '$'], '_', $user_personal_info->lastName),
            'role' => Common\Enums\MemsourceRoleEnum::PROJECT_MANAGER,
            'timezone' => $timezone,
            'userName' => $this->usernamePrefix . str_replace(['<', '>', '&', '%', '{', '}', '[', ']', '^', '#', '*', '$'], '', $user_info->display_name) . "_$user_id",
            'receiveNewsletter' => false,
            'active' => true,
            'projectCreate' => true,
            'projectViewOther' => true,
            'projectEditOther' => true,
            'projectDeleteOther' => true,
            'projectTemplateCreate' => true,
            'projectTemplateViewOther' => true,
            'projectTemplateEditOther' => true,
            'projectTemplateDeleteOther' => true,
            'transMemoryCreate' => true,
            'transMemoryViewOther' => true,
            'transMemoryEditOther' => true,
            'transMemoryDeleteOther' => true,
            'transMemoryExportOther' => true,
            'transMemoryImportOther' => true,
            'termBaseCreate' => true,
            'termBaseViewOther' => true,
            'termBaseEditOther' => true,
            'termBaseDeleteOther' => true,
            'termBaseExportOther' => true,
            'termBaseImportOther' => true,
            'termBaseApproveOther' => true,
            'userCreate' => false,
            'userViewOther' => true,
            'userEditOther' => false,
            'userDeleteOther' => false,
            'clientDomainSubDomainCreate' => false,
            'clientDomainSubDomainViewOther' => true,
            'clientDomainSubDomainEditOther' => false,
            'clientDomainSubDomainDeleteOther' => false,
            'vendorCreate' => false,
            'vendorViewOther' => true,
            'vendorEditOther' => false,
            'vendorDeleteOther' => false,
            'dashboardSetting' => 'OWN_DATA',
            'setupServer' => false,
        );
        $payload = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $authorization = 'Authorization: Bearer ' . $this->memsourceApiToken;
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        $result_exec = curl_exec($ch);
        $result = json_decode($result_exec, true);
        curl_close($ch);
        if (!empty($result['uid'])) {
            $memsource_user_uid = $result['uid'];
            $this->set_memsource_user($user_id, 0, $memsource_user_uid);
            error_log("PROJECT_MANAGER memsource user $memsource_user_uid created for $user_id");
            return $memsource_user_uid;
        } else {
            error_log("No PROJECT_MANAGER memsource user created for $user_id");
            error_log(print_r($result, true));
            return 0;
        }
    }

    public function queue_claim_task($user_id, $task_id)
    {
        LibAPI\PDOWrapper::call('queue_claim_task', LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanse($task_id));
    }

    public function unclaimTask($userId, $taskId, $feedback)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tasks/$taskId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE, $feedback);

        LibAPI\PDOWrapper::call('update_tasks_status', LibAPI\PDOWrapper::cleanse($taskId) . ',2,NULL');
        return $ret;
    }

    public function updateUser($user)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/{$user->getId()}";
        $ret = $this->client->call(
            "\SolasMatch\Common\Protobufs\Models\User",
            $request,
            Common\Enums\HttpMethodEnum::PUT,
            $user
        );
        Common\Lib\CacheHelper::unCache(Common\Lib\CacheHelper::GET_USER.$user->getId());
        return $ret;
    }

    public function addUserTag($userId, $tag)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tags";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::POST, $tag);
        return $ret;
    }

    public function addUserTagById($userId, $tagId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tags/$tagId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
        return $ret;
    }

    public function requestReferenceEmail($userId)
    {
        $request = "{$this->siteApi}v0/users/$userId/requestReference";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
    }

    public function removeUserTag($userId, $tagId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tags/$tagId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
        return $ret;
    }

    public function trackTask($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/trackedTasks/$taskId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
        return $ret;
    }

    public function untrackTask($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/trackedTasks/$taskId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
        return $ret;
    }

    public function trackProject($userId, $projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/projects/$projectId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
        return $ret;
    }

    public function untrackProject($userId, $projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/projects/$projectId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
        return $ret;
    }
    
    public function login($email, $password)
    {
        $ret = null;
        $login = new Common\Protobufs\Models\Login();
        $login->setEmail($email);
        $login->setPassword($password);
        $request = "{$this->siteApi}v0/users/login";
        $queryArgs = array(
            'client_id' => Common\Lib\Settings::get('oauth.client_id'),
            'client_secret' => Common\Lib\Settings::get('oauth.client_secret')
        );
        try {
            $ret = $this->client->call(
                "\SolasMatch\Common\Protobufs\Models\User",
                $request,
                Common\Enums\HttpMethodEnum::POST,
                $login,
                $queryArgs
            );
        } catch (Common\Exceptions\SolasMatchException $e) {
            switch($e->getCode()) {
                case Common\Enums\HttpStatusEnum::NOT_FOUND:
                    throw new Common\Exceptions\SolasMatchException(
                        Lib\Localisation::getTranslation('common_error_login_incorrect')
                    );
                    break;
                case Common\Enums\HttpStatusEnum::UNAUTHORIZED:
                    // TODO: Resend verification email
                    throw new Common\Exceptions\SolasMatchException(
                        Lib\Localisation::getTranslation('common_error_login_unverified')
                    );
                    break;
                case Common\Enums\HttpStatusEnum::FORBIDDEN:
                    $userDao = new UserDao();
                    $banComment = $userDao->getBannedComment($email);
                    throw new Common\Exceptions\SolasMatchException(
                        sprintf(
                            Lib\Localisation::getTranslation("common_this_user_account_has_been_banned"),
                            $banComment
                        )
                    );
                    break;
                default:
                    throw $e;
            }
        }
        
        $headers = $this->client->getHeaders();
        if (isset($headers["X-Custom-Token"])) {
            Common\Lib\UserSession::setAccessToken(
                $this->client->deserialize(
                    base64_decode($headers["X-Custom-Token"]),
                    '\SolasMatch\Common\Protobufs\Models\OAuthResponse'
                )
            );
        }
        return $ret;
    }

    public function requestAuthCode($email)
    {
        global $app;

        $redirectUri = '';
        if (isset($_SERVER['HTTPS']) && !is_null($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $redirectUri = 'https://';
        } else {
            $redirectUri = 'http://';
        }
        $redirectUri .= $_SERVER['SERVER_NAME'] . $app->getRouteCollector()->getRouteParser()->urlFor('login');

        $request = "{$this->siteApi}v0/users/$email/auth/code/?".
            'client_id='.Common\Lib\Settings::get('oauth.client_id').'&'.
            "redirect_uri=$redirectUri&".
            'response_type=code';

        return $request;
    }
    
    public function loginWithAuthCode($authCode)
    {
        global $app;

        $request = "{$this->siteApi}v0/users/authCode/login";

        $redirectUri = '';
        if (isset($_SERVER['HTTPS']) && !is_null($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $redirectUri = 'https://';
        } else {
            $redirectUri = 'http://';
        }
        $redirectUri .= $_SERVER['SERVER_NAME'] . $app->getRouteCollector()->getRouteParser()->urlFor('login');

        $postArgs = 'client_id='.Common\Lib\Settings::get('oauth.client_id').'&'.
            'client_secret='.Common\Lib\Settings::get('oauth.client_secret').'&'.
            "redirect_uri=$redirectUri&".
            "code=$authCode";

        $user = $this->client->call(
            '\SolasMatch\Common\Protobufs\Models\User',
            $request,
            Common\Enums\HttpMethodEnum::POST,
            $postArgs
        );
        $headers = $this->client->getHeaders();
        if (isset($headers["X-Custom-Token"])) {
            Common\Lib\UserSession::setAccessToken(
                $this->client->deserialize(
                    base64_decode($headers["X-Custom-Token"]),
                    '\SolasMatch\Common\Protobufs\Models\OAuthResponse'
                )
            );
        }

        return $user;
    }

    public function get_password_reset_request_by_uuid($uuid)
    {
        return LibAPI\PDOWrapper::call('get_password_reset_request_by_uuid', LibAPI\PDOWrapper::cleanseNullOrWrapStr($uuid));
    }

    public function request_password_reset($email)
    {
        $results = LibAPI\PDOWrapper::call('getUser', 'null,null,' . LibAPI\PDOWrapper::cleanseWrapStr($email) . ',null,null,null,null,null,null');
        if (empty($results)) return 0;
        $user_id = $results[0]['id'];

        $results = LibAPI\PDOWrapper::call('get_password_reset_request', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($results)) {
            LibAPI\PDOWrapper::call('add_password_reset_request', LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanseWrapStr(md5(uniqid(rand()))));
        }

        $results = LibAPI\PDOWrapper::call('update_password_reset_request_count', LibAPI\PDOWrapper::cleanse($user_id));
        if (!$results[0]['result']) return -1; // Too many requests, DOS?

        $request = "{$this->siteApi}v0/users/email/$user_id/send_password_reset_verification";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::POST);
        return 1;
    }

    public function resetPassword($password, $uuid)
    {
        $results = LibAPI\PDOWrapper::call('get_password_reset_request_by_uuid', LibAPI\PDOWrapper::cleanseNullOrWrapStr($uuid));
        if (empty($results)) return 0;
        $user_id = $results[0]['user_id'];
        $results = LibAPI\PDOWrapper::call('getUser', LibAPI\PDOWrapper::cleanse($user_id) . ',null,null,null,null,null,null,null,null');
        if (empty($results)) return 0;

        $user = Common\Lib\ModelFactory::buildModel('User', $results[0]);
        $nonce = Common\Lib\Authentication::generateNonce();
        $user->setNonce($nonce);
        $user->setPassword(Common\Lib\Authentication::hashPassword($password, $nonce));
        $this->saveUser($user);

        LibAPI\PDOWrapper::call('finishRegistration', LibAPI\PDOWrapper::cleanse($user_id)); // Just in case user is trying to do registration and also password reset... they have proved ownership of email
        return 1;
    }

    public function get_password_reset_request_uuid($user_id)
    {
        $results = LibAPI\PDOWrapper::call('get_password_reset_request', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($results)) return 0;
        return $results[0]['uuid'];
    }

    public function register($email, $password, $first_name = '', $last_name = '', $communications_consent = 0)
    {
        $ret = null;
        $registerData = new Common\Protobufs\Models\Register();
        $registerData->setEmail($email);
        $registerData->setPassword($password);
        $registerData->setFirstName($first_name);
        $registerData->setLastName($last_name);
        $registerData->setCommunicationsConsent($communications_consent);
        $request = "{$this->siteApi}v0/users/register";
        $registered = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::POST, $registerData);
        if ($registered) {
            return true;
        } else {
            return false;
        }
    }

    public function verifyUserByEmail($email)
    {
        $user = null;
        $result = LibAPI\PDOWrapper::call('getUser', 'null,null,' . LibAPI\PDOWrapper::cleanseNullOrWrapStr($email) . ',null,null,null,null,null,null');
        if (!empty($result)) {
            $user = Common\Lib\ModelFactory::buildModel('User', $result[0]);
        }
        return $user;
    }

    public function terms_accepted($user_id)
    {
        $terms_accepted = 0;
        $result = LibAPI\PDOWrapper::call('terms_accepted', LibAPI\PDOWrapper::cleanse($user_id));
        if (!empty($result)) {
            $terms_accepted = $result[0]['accepted_level'];
        }
        return $terms_accepted;
    }

    public function setRequiredProfileCompletedinSESSION($user_id)
    {
        if ($accepted_level = $this->terms_accepted($user_id)) {
            $_SESSION['profile_completed'] = $accepted_level;
        }
    }

    public function update_terms_accepted($user_id, $accepted_level)
    {
        $_SESSION['profile_completed'] = $accepted_level;
        LibAPI\PDOWrapper::call('update_terms_accepted', LibAPI\PDOWrapper::cleanse($user_id) . ",$accepted_level");
    }

    public function get_post_login_message($user_id)
    {
        $result = LibAPI\PDOWrapper::call('get_post_login_message', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result) || $result[0]['show'] == 0) return 0;
        return  $result[0]['message'];
    }

    public function update_post_login_message($user_id)
    {
        LibAPI\PDOWrapper::call('update_post_login_message', LibAPI\PDOWrapper::cleanse($user_id) . ',0');
    }

    public function saveUser($user)
    {
        $userId = $user->getId();
        $nativeLanguageCode = null;
        $nativeCountryCode = null;
        if (!is_null($userId) && !is_null($user->getNativeLocale())) {
            $nativeLocale = $user->getNativeLocale();
            $nativeLanguageCode = $nativeLocale->getLanguageCode();
            $nativeCountryCode = $nativeLocale->getCountryCode();
        }

        $args = LibAPI\PDOWrapper::cleanseNullOrWrapStr($user->getEmail()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($user->getNonce()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($user->getPassword()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($user->getBiography()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($user->getDisplayName()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($nativeLanguageCode) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($nativeCountryCode) . ',' .
            LibAPI\PDOWrapper::cleanseNull($userId);
        LibAPI\PDOWrapper::call('userInsertAndUpdate', $args);
    }

    public function getUserPersonalInformation($user_id)
    {
        $userPersonalInfo = null;
        $result = LibAPI\PDOWrapper::call('getUserPersonalInfo', 'null,' . LibAPI\PDOWrapper::cleanseNull($user_id) . ',null,null,null,null,null,null,null,null,null,null');
        if (!empty($result)) {
            $userPersonalInfo = Common\Lib\ModelFactory::buildModel('UserPersonalInformation', $result[0]);
        }
        return $userPersonalInfo;
    }

    public function saveUserPersonalInformation($userInfo)
    {
        $args = LibAPI\PDOWrapper::cleanseNull($userInfo->getId()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($userInfo->getUserId()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($userInfo->getFirstName()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($userInfo->getLastName()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($userInfo->getMobileNumber()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($userInfo->getBusinessNumber()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($userInfo->getLanguagePreference()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($userInfo->getJobTitle()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($userInfo->getAddress()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($userInfo->getCity()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($userInfo->getCountry()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($userInfo->getReceiveCredit() ? 1 : 0);
        LibAPI\PDOWrapper::call('userPersonalInfoInsertAndUpdate', $args);
    }

    public function addOrgAdmin($user_id, $org_id)
    {
        $args = LibAPI\PDOWrapper::cleanseNull($user_id) . ',' . LibAPI\PDOWrapper::cleanseNull($org_id);
        LibAPI\PDOWrapper::call('acceptMemRequest', $args);
        LibAPI\PDOWrapper::call('addAdmin', $args);
    }

    public function is_admin_or_org_member($user_id)
    {
        $result = LibAPI\PDOWrapper::call('is_admin_or_org_member', LibAPI\PDOWrapper::cleanse($user_id));
        return $result[0]['result'];
    }

    public function is_admin_or_member_for_org($user_id, $org_id)
    {
        $result = LibAPI\PDOWrapper::call('is_admin_or_member_for_org', LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanse($org_id));
        return $result[0]['result'];
    }

    public function getOrgIDUsingName($org_name)
    {
        $org_id = 0;
        $result = LibAPI\PDOWrapper::call('getOrg', 'null,' . LibAPI\PDOWrapper::cleanseNullOrWrapStr($org_name) . ',null,null,null,null,null,null,null');
        if (!empty($result)) {
            $org_id = $result[0]['id'];
        }
        return $org_id;
    }

    public static function insertOrg($org_name, $email)
    {
        $org_id = 0;
        $result = LibAPI\PDOWrapper::call('organisationInsertAndUpdate', 'null,null,' . LibAPI\PDOWrapper::cleanseNullOrWrapStr($org_name) . ',null,' . LibAPI\PDOWrapper::cleanseNullOrWrapStr($email) . ',null,null,null,null');
        if (!empty($result)) {
            $org_id = $result[0]['id'];
        }
        return $org_id;
    }

    public function finishRegistration($uuid)
    {
        $request = "{$this->siteApi}v0/users/$uuid/finishRegistration";
        $resp = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::POST);
        return $resp;
    }

    public function finishRegistrationManually($email)
    {
        $request = "{$this->siteApi}v0/users/$email/manuallyFinishRegistration";
        $resp = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::POST);
        return $resp;
    }

    public function getRegisteredUser($registrationId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$registrationId/registered";
        $ret = $this->client->call("\SolasMatch\Common\Protobufs\Models\User", $request);
        return $ret;
    }

    public function changeEmail($user_id, $email, $old_email)
    {
        error_log("changeEmail($user_id, $email, $old_email)");
        LibAPI\PDOWrapper::call('changeEmail', LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanseNullOrWrapStr($email));

        $error = '';
        $record = $this->get_memsource_user_record($old_email);
        if ($record) $this->change_memsource_user_email($user_id, $record, $email);
        else {
            error_log("changeEmail($user_id, $email, $old_email), can't find email in Phrase");
            $error =  "<br />Can't find $old_email in Phrase.";
        }

        $ip = Common\Lib\Settings::get('moodle.ip');
        $token = Common\Lib\Settings::get('moodle.token');
        $MoodleRest = new Common\Lib\MoodleRest();
        $MoodleRest->setServerAddress("http://$ip/webservice/rest/server.php");
        $MoodleRest->setToken($token);
        $MoodleRest->setReturnFormat(Common\Lib\MoodleRest::RETURN_ARRAY);
        //$MoodleRest->setDebug();
        $results = $MoodleRest->request('core_user_get_users_by_field', ['field' => 'email', 'values' => [$old_email]]);
        error_log('core_user_get_users_by_field: ' . print_r($results, 1));
        if (empty($results) || !empty($results['warnings'])) $error .= "<br />Can't find $old_email in Moodle.";
        else {
            if (count($results) > 1) $error .= "<br />Duplicate $old_email in Moodle.";
            else {
                $results = $MoodleRest->request('core_user_update_users', ['users' => [['id' => $results[0]['id'], 'email' => $email]]]);
                error_log('core_user_update_users: ' . print_r($results, 1));
                if (empty($results) || !empty($results['warnings'])) $error .= "<br />Did not change email in Moodle.";
            }
        }
        if ($error) return "Changed email in TWB Platform but...$error";
        return '';
    }

    public function get_memsource_user_record($old_email)
    {
        $ch = curl_init("https://cloud.memsource.com/web/api2/v1/users?email=$old_email");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $authorization = 'Authorization: Bearer ' . $this->memsourceApiToken;
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        $result = curl_exec($ch);
        curl_close($ch);
        if (empty($result)) {
            error_log("No data returned from Memsource in get_memsource_user_record($old_email)");
            return 0;
        }
        $response_data = json_decode($result, true);
        if (empty($response_data['content'])) {
            error_log("No ['content'] returned from Memsource in get_memsource_user_record($old_email)");
            error_log(print_r($response_data, true));
            return 0;
        }
        foreach ($response_data['content'] as $user) {
            if ($user['email'] === $old_email) return $user;
        }
        error_log("No matching email returned from Memsource in get_memsource_user_record($old_email)");
        error_log(print_r($response_data, true));
        return 0;
    }

    public function change_memsource_user_email($user_id, $record, $email)
    {
        if ($record['role'] === Common\Enums\MemsourceRoleEnum::LINGUIST) {
            $ch = curl_init("https://cloud.memsource.com/web/api2/v3/users/{$record['uid']}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $user_personal_info = $this->getUserPersonalInformation($user_id);
            $data = array(
                'email' => $email,
                'firstName' => str_replace(['<', '>', '&', '%', '{', '}', '[', ']', '^', '#', '*', '$'], '_', $user_personal_info->firstName),
                'lastName'  => str_replace(['<', '>', '&', '%', '{', '}', '[', ']', '^', '#', '*', '$'], '_', $user_personal_info->lastName),
                'role' => Common\Enums\MemsourceRoleEnum::LINGUIST,
                'timezone' => $record['timezone'],
                'userName' => $record['userName'],
                'receiveNewsletter' => false,
                'active' => true,
                'editAllTermsInTB' => false,
                'editTranslationsInTM' => false,
                'enableMT' => false,
                'mayRejectJobs' => false,
            );
            if (!empty($record['note'])) $data['note'] = $record['note'];
            // Linguists should not have any of these
            $data['sourceLocales'] = [];
            $data['targetLocales'] = [];
            $data['workflowSteps'] = [];
            $data['clients'] = [];
            $data['domains'] = [];
            $data['subDomains'] = [];
            $payload = json_encode($data);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $authorization = 'Authorization: Bearer ' . $this->memsourceApiToken;
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
            $result_exec = curl_exec($ch);
            $result = json_decode($result_exec, true);
            curl_close($ch);
            if (empty($result['email'])) {
                error_log("No email returned from Memsource in change_memsource_user_email($user_id, ..., $email) {$record['uid']}");
                error_log(print_r($result, true));
            }
        }
    }

    public function createPersonalInfo($userId, $personalInfo)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/personalInfo";
        $ret = $this->client->call(
            "\SolasMatch\Common\Protobufs\Models\UserPersonalInformation",
            $request,
            Common\Enums\HttpMethodEnum::POST,
            $personalInfo
        );
        return $ret;
    }
    
    public function updatePersonalInfo($userId, $personalInfo)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/personalInfo";
        $ret = $this->client->call(
            "\SolasMatch\Common\Protobufs\Models\UserPersonalInformation",
            $request,
            Common\Enums\HttpMethodEnum::PUT,
            $personalInfo
        );
        return $ret;
    }
    
    public function getBannedComment($email)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/email/$email/getBannedComment";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::GET);
        return $ret;
    }
    
    public function createUserQualifiedPair($user_id, $language_code_source, $country_code_source, $language_code_target, $country_code_target, $qualification_level)
    {
        LibAPI\PDOWrapper::call('createUserQualifiedPair',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($language_code_source) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($country_code_source) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($language_code_target) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($country_code_target) . ',' .
            LibAPI\PDOWrapper::cleanse($qualification_level));
            error_log("createUserQualifiedPair($user_id, $language_code_source, $country_code_source, $language_code_target, $country_code_target, $qualification_level)");
    }

    public function updateUserQualifiedPair($user_id, $language_code_source, $country_code_source, $language_code_target, $country_code_target, $qualification_level)
    {
        LibAPI\PDOWrapper::call('updateUserQualifiedPair',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($language_code_source) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($country_code_source) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($language_code_target) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($country_code_target) . ',' .
            LibAPI\PDOWrapper::cleanse($qualification_level));
            error_log("updateUserQualifiedPair($user_id, $language_code_source, $country_code_source, $language_code_target, $country_code_target, $qualification_level)");
    }

    public function getUserQualifiedPairs($user_id)
    {
        $result = LibAPI\PDOWrapper::call('getUserQualifiedPairs', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) return [];

        $projectDao = new ProjectDao();
        $selections = $projectDao->get_selections();

        foreach ($result as $i => $r) {
            foreach ($selections as $selection) {
                if ($r['language_code_source'] === $selection['language_code'] && $r['country_code_source'] === $selection['country_code']) {
                    $result[$i]['language_source'] = $selection['selection'];
                    $result[$i]['country_source']  = 'ANY';
                }
                if ($r['language_code_target'] === $selection['language_code'] && $r['country_code_target'] === $selection['country_code']) {
                    $result[$i]['language_target'] = $selection['selection'];
                    $result[$i]['country_target']  = 'ANY';
                }
            }
        }
        return $result;
    }

    public function removeUserQualifiedPair($user_id, $language_code_source, $country_code_source, $language_code_target, $country_code_target)
    {
        LibAPI\PDOWrapper::call('removeUserQualifiedPair',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($language_code_source) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($country_code_source) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($language_code_target) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($country_code_target));
            error_log("removeUserQualifiedPair($user_id, $language_code_source, $country_code_source, $language_code_target, $country_code_target)");
    }

    public function updateRequiredOrgQualificationLevel($org_id, $required_qualification_level)
    {
        LibAPI\PDOWrapper::call('updateRequiredOrgQualificationLevel',
            LibAPI\PDOWrapper::cleanse($org_id) . ',' .
            LibAPI\PDOWrapper::cleanse($required_qualification_level));
    }

    public function getRequiredOrgQualificationLevel($org_id)
    {
        $result = LibAPI\PDOWrapper::call('getRequiredOrgQualificationLevel', LibAPI\PDOWrapper::cleanse($org_id));
        if (empty($result)) return 1;
        return $result[0]['required_qualification_level'];
    }

    public function deleteUser($userId)
    {
        $request = "{$this->siteApi}v0/users/$userId";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
    }
    
    public function isBlacklistedForTask($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/isBlacklistedForTask/$userId/$taskId";
        $ret = $this->client->call(null, $request);
        return $ret;
    }

    public function is_denied_for_task($user_id, $task_id)
    {
        $result = LibAPI\PDOWrapper::call('isUserBlacklistedForTask', LibAPI\PDOWrapper::cleanseNull($user_id) . ',' . LibAPI\PDOWrapper::cleanseNull($task_id));
        return $result[0]['result'];
    }

    public function isBlacklistedForTaskByAdmin($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/isBlacklistedForTaskByAdmin/$userId/$taskId";
        $ret = $this->client->call(null, $request);
        return $ret;
    }

    public function trackOrganisation($userId, $organisationId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/organisations/$organisationId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
        return $ret;
    }

    public function untrackOrganisation($userId, $organisationId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/organisations/$organisationId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
        return $ret;
    }

    public function getUserTrackedOrganisations($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/organisations";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\Organisation"), $request);
        return $ret;
    }

    public function isSubscribedToOrganisation($userId, $organisationId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/subscribedToOrganisation/$userId/$organisationId";
        $ret = $this->client->call(null, $request);
        return $ret;
    }

    public function getUserURLs($user_id)
    {
        $result = LibAPI\PDOWrapper::call('getUserURLs', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) $result = [];
        return $result;
    }

    public function insertUserURL($user_id, $key, $value)
    {
        LibAPI\PDOWrapper::call('insertUserURL',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($key) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($value));
    }

    public function getUserExpertises($user_id)
    {
        $result = LibAPI\PDOWrapper::call('getUserExpertises', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) $result = [];
        return $result;
    }

    public function addUserExpertise($user_id, $key)
    {
        LibAPI\PDOWrapper::call('addUserExpertise',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($key));
    }

    public function removeUserExpertise($user_id, $key)
    {
        LibAPI\PDOWrapper::call('removeUserExpertise',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($key));
    }

    public function getUserHowheards($user_id)
    {
        $result = LibAPI\PDOWrapper::call('getUserHowheards', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) $result = [];
        return $result;
    }

    public function insertUserHowheard($user_id, $key)
    {
        LibAPI\PDOWrapper::call('insertUserHowheard',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($key));
    }

    public function updateUserHowheard($user_id, $reviewed)
    {
        LibAPI\PDOWrapper::call('updateUserHowheard',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanse($reviewed));
    }

    public function insert_communications_consent($user_id, $accepted)
    {
        LibAPI\PDOWrapper::call('insert_communications_consent',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanse($accepted));
    }

    public function get_communications_consent($user_id)
    {
        $result = LibAPI\PDOWrapper::call('get_communications_consent', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) return 0;
        return $result[0]['accepted'];
    }

    public function getUserCertifications($user_id)
    {
        $result = LibAPI\PDOWrapper::call('getUserCertifications', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) $result = [];
        return $result;
    }

    public function getUserCertificationByID($id)
    {
        $result = LibAPI\PDOWrapper::call('getUserCertificationByID', LibAPI\PDOWrapper::cleanse($id));
        return $result[0];
    }

    public function users_review()
    {
        $result = LibAPI\PDOWrapper::call('users_review', '');
        if (empty($result)) $result = [];
        return $result;
    }

    public function users_new()
    {
        $result = LibAPI\PDOWrapper::call('users_new', '');
        if (empty($result)) $result = [];
        return $result;
    }

    public function users_tracked()
    {
        $result = LibAPI\PDOWrapper::call('users_tracked', '');
        if (empty($result)) $result = [];
        return $result;
    }

    public function saveUserFile($user_id, $cert_id, $note, $filename, $file)
    {
       $destination = Common\Lib\Settings::get('files.upload_path') . "certs/$user_id/$cert_id";
       if (!file_exists($destination)) mkdir($destination, 0755, true);

        $vid = 0;
        while (true) { // Find next free vid
            $destination = Common\Lib\Settings::get('files.upload_path') . "certs/$user_id/$cert_id/$vid";
            if (!file_exists($destination)) break;
            $vid++;
        }

        $mime = $this->detectMimeType($file, $filename);
        $canonicalMime = $this->client->getCanonicalMime($filename);
        error_log("saveUserFile($user_id, $cert_id, $note, $filename, ...)");

        if (!is_null($canonicalMime) && $mime != $canonicalMime) {
            error_log("content type ($mime) of file does not match ($canonicalMime) expected from extension");
            return;
        }

        mkdir($destination, 0755);
        file_put_contents("$destination/$filename", $file);

        LibAPI\PDOWrapper::call('insertUserCertification',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanse($vid) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($cert_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($filename) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($mime) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($note));
    }

    public function updateCertification($id, $reviewed)
    {
        LibAPI\PDOWrapper::call('updateCertification',
            LibAPI\PDOWrapper::cleanse($id) . ',' .
            LibAPI\PDOWrapper::cleanse($reviewed));
    }

    public function deleteCertification($id)
    {
        LibAPI\PDOWrapper::call('deleteCertification', LibAPI\PDOWrapper::cleanse($id));
    }

    public function detectMimeType($file, $filename)
    {
        $result = null;

        $mimeMap = array(
                "xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                ,"xlsm" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                ,"xltx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.template"
                ,"potx" => "application/vnd.openxmlformats-officedocument.presentationml.template"
                ,"ppsx" => "application/vnd.openxmlformats-officedocument.presentationml.slideshow"
                ,"pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation"
                ,"sldx" => "application/vnd.openxmlformats-officedocument.presentationml.slide"
                ,"docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                ,"dotx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.template"
                ,"xlam" => "application/vnd.ms-excel.addin.macroEnabled.12"
                ,"xlsb" => "application/vnd.ms-excel.sheet.binary.macroEnabled.12"
                ,"xlf"  => "application/xliff+xml"
                ,"doc"  => "application/msword"
                ,"ppt"  => "application/vnd.ms-powerpoint"
                ,"xls"  => "application/vnd.ms-excel"
        );

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($file);

        $extension = explode(".", $filename);
        $extension = $extension[count($extension)-1];

        if (($mime == "application/octet-stream" || $mime == "application/zip" || $extension == "doc" || $extension == "xlf")
            && (array_key_exists($extension, $mimeMap))) {
            $result = $mimeMap[$extension];
        } elseif ($mime === 'text/plain' && $extension === 'json') {
            $result = 'application/json';
        } elseif ($mime === 'application/zip' && $extension === 'odt') {
            $result = 'application/vnd.oasis.opendocument.text';
        } elseif ($mime === 'text/xml' && $extension === 'xml') {
            $result = 'application/xml';
        } else {
            $result = $mime;
        }

        return $result;
    }

    public function getURLList($user_id)
    {
        $url_list = [];
        $url_list['proz']   = ['desc' => 'Your ProZ.com URL (optional)', 'state' => ''];
        $url_list['linked'] = ['desc' => 'Your LinkedIn URL (optional)', 'state' => ''];
        $url_list['face']   = ['desc' => 'Your Facebook URL (optional)', 'state' => ''];
        $url_list['other']  = ['desc' => 'Other URL', 'state' => ''];
        $urls = $this->getUserURLs($user_id);
        foreach ($urls as $url) {
            if (empty($url['url'])) continue;
            if (strpos($url['url'], 'https://') === false) $url['url'] = 'https://' . $url['url'];
            $url_list[$url['url_key']]['state'] = $url['url'];
        }
        return $url_list;
    }

    public function getCapabilityList($user_id)
    {
        $capability_list = [];
        $services = $this->get_user_services($user_id);
        foreach ($services as $service) {
            $capability_list['badge_id_' . $service['id']] = $service;
        }
        return $capability_list;
    }

    public function get_user_services($user_id)
    {
        return LibAPI\PDOWrapper::call('get_user_services', LibAPI\PDOWrapper::cleanse($user_id));
    }

    public function add_user_service($user_id, $service_id)
    {
        LibAPI\PDOWrapper::call('add_user_service', LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanse($service_id));
    }

    public function remove_user_service($user_id, $service_id)
    {
        LibAPI\PDOWrapper::call('remove_user_service', LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanse($service_id));
    }

    public function getExpertiseList($user_id)
    {
        $expertise_list = [];
        $expertise_list['Accounting']         = ['desc' => 'Accounting & Finance', 'state' => 0];
        $expertise_list['Legal']              = ['desc' => 'Legal Documents / Contracts / Law', 'state' => 0];
        $expertise_list['Technical']          = ['desc' => 'Technical / Engineering', 'state' => 0];
        $expertise_list['IT']                 = ['desc' => 'Information Technology (IT)', 'state' => 0];
        $expertise_list['Literary']           = ['desc' => 'Literary', 'state' => 0];
        $expertise_list['Medical']            = ['desc' => 'Medical / Pharmaceutical', 'state' => 0];
        $expertise_list['Science']            = ['desc' => 'Science / Scientific', 'state' => 0];
        $expertise_list['Health']             = ['desc' => 'Health', 'state' => 0];
        $expertise_list['Nutrition']          = ['desc' => 'Food Security & Nutrition', 'state' => 0];
        $expertise_list['Telecommunications'] = ['desc' => 'Telecommunications', 'state' => 0];
        $expertise_list['Education']          = ['desc' => 'Education', 'state' => 0];
        $expertise_list['Protection']         = ['desc' => 'Protection & Early Recovery', 'state' => 0];
        $expertise_list['Migration']          = ['desc' => 'Migration & Displacement', 'state' => 0];
        $expertise_list['CCCM']               = ['desc' => 'Camp Coordination & Camp Management', 'state' => 0];
        $expertise_list['Shelter']            = ['desc' => 'Shelter', 'state' => 0];
        $expertise_list['WASH']               = ['desc' => 'Water, Sanitation and Hygiene Promotion', 'state' => 0];
        $expertise_list['Logistics']          = ['desc' => 'Logistics', 'state' => 0];
        $expertise_list['Equality']           = ['desc' => 'Equality & Inclusion', 'state' => 0];
        $expertise_list['Gender']             = ['desc' => 'Gender Equality', 'state' => 0];
        $expertise_list['Peace']              = ['desc' => 'Peace & Justice', 'state' => 0];
        $expertise_list['Environment']        = ['desc' => 'Environment & Climate Action', 'state' => 0];
        $expertises = $this->getUserExpertises($user_id);
        foreach ($expertises as $expertise) {
            $expertise_list[$expertise['expertise_key']]['state'] = 1;
        }
        return $expertise_list;
    }

    public function getHowheardList($user_id)
    {
        $howheard_list = [];
        $howheard_list['Twitter']    = ['desc' => 'Twitter', 'state' => 0];
        $howheard_list['Facebook']   = ['desc' => 'Facebook', 'state' => 0];
        $howheard_list['LinkedIn']   = ['desc' => 'LinkedIn', 'state' => 0];
        $howheard_list['Event']      = ['desc' => 'Event/Conference', 'state' => 0];
        $howheard_list['Referral']   = ['desc' => 'Word of mouth/Referral', 'state' => 0];
        $howheard_list['Newsletter'] = ['desc' => 'TWB Newsletter', 'state' => 0];
        $howheard_list['Internet']   = ['desc' => 'Internet search', 'state' => 0];
        $howheard_list['staff']      = ['desc' => 'Contacted by TWB staff', 'state' => 0];
        $howheard_list['Other']      = ['desc' => 'Other', 'state' => 0];
        $howheards = $this->getUserHowheards($user_id);
        if (!empty($howheards)) {
            $howheard_list[$howheards[0]['howheard_key']]['state'] = 1;
        } elseif ($referer = $this->get_tracked_registration($user_id)) {
            if (in_array($referer, $this->get_referers())) $howheard_list['Referral']['state'] = 1;
        }
        return $howheard_list;
    }

    public function getCertificationList($user_id)
    {
        $certification_list = [];
        $certification_list['ATA']     = ['desc' => 'American Translators Association (ATA) - ATA Certified', 'state' => 0, 'reviewed' => 0];
        $certification_list['APTS']    = ['desc' => 'Arab Professional Translators Society (APTS) - Certified Translator, Certified Translator/Interpreter or Certified Associate', 'state' => 0, 'reviewed' => 0];
        $certification_list['ATIO']    = ['desc' => 'Association of Translators and Interpreters of Ontario (ATIO) - Certified Translators or Candidates', 'state' => 0, 'reviewed' => 0];
        $certification_list['ATIM']    = ['desc' => 'Association of Translators, Terminologists and Interpreters of Manitoba - Certified Translators', 'state' => 0, 'reviewed' => 0];
        $certification_list['ABRATES'] = ['desc' => 'Brazilian Association of Translators and Interpreters (ABRATES) - Accredited Translators (Credenciado)', 'state' => 0, 'reviewed' => 0];
        $certification_list['CIOL']    = ['desc' => 'Chartered Institute of Linguists (CIOL) - Member, Fellow, Chartered Linguist, or DipTrans IOL Certificate holder', 'state' => 0, 'reviewed' => 0];
        $certification_list['ITIA']    = ['desc' => 'Irish Translators’ and Interpreters’ Association (ITIA) - Professional Member', 'state' => 0, 'reviewed' => 0];
        $certification_list['ITI']     = ['desc' => 'Institute of Translation and Interpreting (ITI) - ITI Assessed', 'state' => 0, 'reviewed' => 0];
        $certification_list['NAATI']   = ['desc' => 'National Accreditation Authority for Translators and Interpreters (NAATI) - Certified Translator or Advanced Certified Translator', 'state' => 0, 'reviewed' => 0];
        $certification_list['NZSTI']   = ['desc' => 'New Zealand Society of Translators and Interpreters (NZSTI) - Full Members', 'state' => 0, 'reviewed' => 0];
        $certification_list['ProZ']    = ['desc' => 'ProZ Certified PRO members', 'state' => 0, 'reviewed' => 0];
        $certification_list['Austria'] = ['desc' => 'UNIVERSITAS Austria Interpreters’ and Translators’ Association - Certified Members', 'state' => 0, 'reviewed' => 0];
        $certification_list['ETLA']    = ['desc' => 'Egyptian Translators and Linguists Association (ETLA) - Members', 'state' => 0, 'reviewed' => 0];
        $certification_list['SATI']    = ['desc' => 'South African Translators’ Institute (SATI) - Accredited Translators or Sworn Translators', 'state' => 0, 'reviewed' => 0];
        $certification_list['CATTI']   = ['desc' => 'China Accreditation Test for Translators and Interpreters (CATTI) - Senior Translators or Level 1 Translators', 'state' => 0, 'reviewed' => 0];
        $certification_list['STIBC']   = ['desc' => 'Society of Translators and Interpreters of British Columbia (STIBC) - Certified Translators or Associate Members', 'state' => 0, 'reviewed' => 0];
        $certification_list['ITA']     = ['desc' => 'Israel Translators Association (ITA) - Recognized translators', 'state' => 0, 'reviewed' => 0];
        $certification_list['NITI']    = ['desc' => 'Nigerian Institute of Translators and Interpreters (NITI) - Full Members', 'state' => 0, 'reviewed' => 0];
        $certification_list['CTINB']   = ['desc' => 'Corporation of Translators, Terminologists and Interpreters of New-Brunswick (CTINB) - Certified Translators or Associate Members', 'state' => 0, 'reviewed' => 0];
        $certification_list['ATIA']    = ['desc' => 'Association of Translators and Interpreters of Alberta (ATIA) - Certified Members', 'state' => 0, 'reviewed' => 0];
        $certification_list['ATIS']    = ['desc' => 'Association of Translators and Interpreters of Saskatchewan (ATIS) - Certified Members', 'state' => 0, 'reviewed' => 0];
        $certification_list['ATINS']   = ['desc' => 'Association of Translators and Interpreters of Nova Scotia (ATINS) - Certified Members', 'state' => 0, 'reviewed' => 0];
        usort($certification_list, function($a, $b){ return strcmp($a["desc"], $b["desc"]); }); 
        $certifications = $this->getUserCertifications($user_id);
        foreach ($certifications as $certification) {
            if (!empty($certification_list[$certification['certification_key']])) {
                $certification_list[$certification['certification_key']]['state'] = 1;
                if ($certification['reviewed']) $certification_list[$certification['certification_key']]['reviewed'] = 1;
            }
        }
        return $certification_list;
    }

    public function supported_ngos($user_id)
    {
        $result = LibAPI\PDOWrapper::call('supported_ngos', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) $result = [];
        return $result;
    }

    public function supported_ngos_paid($user_id)
    {
        $result = LibAPI\PDOWrapper::call('supported_ngos_paid', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) $result = [];
        return $result;
    }

    public function quality_score($user_id)
    {
        $result = LibAPI\PDOWrapper::call('quality_score', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) return ['cor' => '', 'gram' => '', 'spell' => '', 'cons' => '', 'num_legacy' => 0, 'accuracy' => '', 'fluency' => '', 'terminology' => '', 'style' => '', 'design' => '', 'num_new' => 0, 'num' => 0];
         return $result[0];
    }

    public function admin_comments($user_id)
    {
        $result = LibAPI\PDOWrapper::call('admin_comments', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) $result = [];
        return $result;
    }

    public function admin_comments_average($user_id)
    {
        $result = LibAPI\PDOWrapper::call('admin_comments_average', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) return '';
        return $result[0]['average'];
    }

    public function insert_admin_comment($user_id, $admin_id, $work_again, $comment)
    {
        LibAPI\PDOWrapper::call('insert_admin_comment',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanse($admin_id) . ',' .
            LibAPI\PDOWrapper::cleanse($work_again) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($comment));
    }

    public function insert_print_request($user_id, $cert_type, $loggedInUserId)
    {
        $user_badges = $this->get_points_for_badges($user_id);
        LibAPI\PDOWrapper::call('insert_print_request',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanse($user_badges['words_donated_for_cert']) . ',' .
            LibAPI\PDOWrapper::cleanse($user_badges['hours_donated_for_cert']) . ',' .
            LibAPI\PDOWrapper::cleanse($cert_type) . ',' .
            LibAPI\PDOWrapper::cleanse($loggedInUserId) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr(uniqid()));
    }

    public function get_print_request_by_user($user_id, $request_type)
    {
        $result = LibAPI\PDOWrapper::call('get_print_request_by_user',
            LibAPI\PDOWrapper::cleanse($user_id). ',' .
            LibAPI\PDOWrapper::cleanse($request_type)
        );
        if (empty($result)) $result = [];
        return $result;
    }

    public function get_print_request_by_valid_key($valid_key)
    {
        $result = LibAPI\PDOWrapper::call('get_print_request_by_valid_key', LibAPI\PDOWrapper::cleanseWrapStr($valid_key));
        return $result;
    }

    public function get_print_request_valid_key_for_user($user_id, $request_type)
    {
        $result = LibAPI\PDOWrapper::call('get_print_request_valid_key_for_user', LibAPI\PDOWrapper::cleanse($user_id). ',' . LibAPI\PDOWrapper::cleanse($request_type));
        if (empty($result)) return [];
        return $result[0];
    }

    public function delete_admin_comment($id)
    {
        LibAPI\PDOWrapper::call('delete_admin_comment', LibAPI\PDOWrapper::cleanse($id));
    }

    public function adjust_points($user_id)
    {
        $result = LibAPI\PDOWrapper::call('adjust_points', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) $result = [];
        return $result;
    }

    public function insert_adjust_points($user_id, $admin_id, $points, $comment)
    {
        LibAPI\PDOWrapper::call('insert_adjust_points',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanse($admin_id) . ',' .
            LibAPI\PDOWrapper::cleanse($points) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($comment));
    }

    public function delete_adjust_points($id)
    {
        LibAPI\PDOWrapper::call('delete_adjust_points', LibAPI\PDOWrapper::cleanse($id));
    }

    public function adjust_points_strategic($user_id)
    {
        $result = LibAPI\PDOWrapper::call('adjust_points_strategic', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) $result = [];
        return $result;
    }

    public function insert_adjust_points_strategic($user_id, $admin_id, $points, $comment)
    {
        LibAPI\PDOWrapper::call('insert_adjust_points_strategic',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanse($admin_id) . ',' .
            LibAPI\PDOWrapper::cleanse($points) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($comment));
    }

    public function delete_adjust_points_strategic($id)
    {
        LibAPI\PDOWrapper::call('delete_adjust_points_strategic', LibAPI\PDOWrapper::cleanse($id));
    }

    public function record_track_code($track_code)
    {
        LibAPI\PDOWrapper::call('record_track_code', LibAPI\PDOWrapper::cleanseWrapStr($track_code));
    }

    public function insert_tracked_registration($user_id, $track_code)
    {
        if (in_array($track_code, ['AABBCC'])) return; // Allow old codes to be disabled

        LibAPI\PDOWrapper::call('insert_tracked_registration',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($track_code));
    }

    public function get_tracked_registration($user_id)
    {
        $result = LibAPI\PDOWrapper::call('get_tracked_registration', LibAPI\PDOWrapper::cleanse($user_id));
        if (!empty($result)) return $result[0]['referer'];
        return '';
    }

    public function get_tracked_registration_for_verified($user_id)
    {
        $result = LibAPI\PDOWrapper::call('get_tracked_registration', LibAPI\PDOWrapper::cleanse($user_id));
        if (!empty($result) && in_array($result[0]['referer'], ['RWS Moravia', 'Welocalize', 'Lionbridge', 'SDL'])) return true;
        return false;
    }

    public function get_memsource_user($user_id)
    {
        $result = LibAPI\PDOWrapper::call('get_memsource_user', LibAPI\PDOWrapper::cleanse($user_id));

        if (empty($result)) return 0;

        return $result[0]['memsource_user_uid'];
    }

    public function set_memsource_user($user_id, $memsource_user_id, $memsource_user_uid)
    {
        LibAPI\PDOWrapper::call('set_memsource_user', LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanse($memsource_user_id) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($memsource_user_uid));
    }

    public function memsource_list_jobs($memsource_project_uid, $project_id)
    {
        $projectDao = new ProjectDao();

        $url = $this->memsourceApiV1 . "projects/$memsource_project_uid/workflowSteps";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $authorization = 'Authorization: Bearer ' . $this->memsourceApiToken;
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
        $result = curl_exec($ch);
        $result = json_decode($result, true);
        curl_close($ch);
        if (!isset($result['projectWorkflowSteps'])) return [];
        $workflowlevels = count($result['projectWorkflowSteps']);

        if (!empty($result['projectWorkflowSteps'])) {
            $workflowLevels_array = ['', '', '', '', '', '', '', '', '', '', '', '']; // Will contain e.g. 'Translation' or 'Revision' for workflowLevel 1 possibly up to 12
            $found_something = 0;
            foreach ($result['projectWorkflowSteps'] as $step) {
                foreach ($workflowLevels_array as $i => $w) {
                    if ($step['workflowLevel'] == $i + 1) {
                        $workflowLevels_array[$i] = $step['name'];
                        if (!empty($step['name'])) $found_something = 1;
                    }
                }
            }
            if ($found_something) {
                $projectDao->update_memsource_project($project_id, $workflowLevels_array);
error_log("Sync memsource_list_jobs() project_id: $project_id, workflowLevels_array: {$workflowLevels_array[0]}, {$workflowLevels_array[1]}, {$workflowLevels_array[2]}");//(**)
            }
        }

        $jobs = [];
        $totalPages = 1;
        if ($workflowlevels) {
            for ($workflow_param = 1; $workflow_param <= $workflowlevels; $workflow_param++) {
                for ($p = 0; $p < $totalPages; $p++) {
                    $url = $this->memsourceApiV2 . "projects/$memsource_project_uid/jobs?pageNumber=$p&workflowLevel=$workflow_param";
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
                    $result = curl_exec($ch);
                    $result = json_decode($result, true);
                    if (!empty($result['content'])) {
                        foreach ($result['content'] as $job) {
                            if (!empty($job['uid'])) $jobs[$job['uid']] = $job;
                        }
                    }
                    curl_close($ch);

                    if (!empty($result['totalPages'])) $totalPages = $result['totalPages'];
                }
             }
        } else {
                for ($p = 0; $p < $totalPages; $p++) {
                    $url = $this->memsourceApiV2 . "projects/$memsource_project_uid/jobs?pageNumber=$p";
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
                    $result = curl_exec($ch);
                    $result = json_decode($result, true);
                    if (!empty($result['content'])) {
                        foreach ($result['content'] as $job) {
                            if (!empty($job['uid'])) $jobs[$job['uid']] = $job;
                        }
                    }
                    curl_close($ch);

                    if (!empty($result['totalPages'])) $totalPages = $result['totalPages'];
                }
        }
        return $jobs;
    }

    public function memsource_get_job($memsource_project_uid, $memsource_task_uid)
    {
        $url = $this->memsourceApiV1 . "projects/$memsource_project_uid/jobs/$memsource_task_uid";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $authorization = 'Authorization: Bearer ' . $this->memsourceApiToken;
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
        $result = curl_exec($ch);
        $job = json_decode($result, true);
        curl_close($ch);

        if (empty($job['innerId'])) return 0;
        return $job;
    }

    public function memsource_get_target_file($memsource_project_uid, $memsource_task_uid)
    {
        $url = "{$this->memsourceApiV1}projects/$memsource_project_uid/jobs/$memsource_task_uid/targetFile";
        $re = curl_init($url);
        $httpHeaders = ["Authorization: Bearer $this->memsourceApiToken"];
        curl_setopt($re, CURLOPT_HTTPHEADER, $httpHeaders);
        curl_setopt($re, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($re);
        if ($error_number = curl_errno($re)) error_log("memsource_get_target_file($memsource_project_uid, $memsource_task_uid) Curl error ($error_number): " . curl_error($re));
        $responseCode = curl_getinfo($re, CURLINFO_HTTP_CODE);
        curl_close($re);
        if ($responseCode != 200 || strlen($res) > 100000000) {
            error_log("ERROR memsource_get_target_file($memsource_project_uid, $memsource_task_uid) responseCode: $responseCode");
            return 0;
        }
        return $res;
    }

    public function create_memsource_project($post, $project, $file_name, $file)
    {
        $projectDao = new ProjectDao();

        [$kp_language, $kp_country] = $projectDao->convert_selection_to_language_country($post['sourceLanguageSelect']);
        $sourceLang = $projectDao->convert_language_country_to_memsource($kp_language, $kp_country);
        if (!$sourceLang) return 0;
        [$kp_language, $kp_country] = $projectDao->convert_memsource_to_language_country($sourceLang);
        $kp_source_language = "{$kp_language}-{$kp_country}";

        $targetCount = 0;
        $langs = [];
        $kp_target_languages = [];
        while (!empty($post["target_language_$targetCount"])) {
            [$kp_language, $kp_country] = $projectDao->convert_selection_to_language_country($post["target_language_$targetCount"]);
            $lang = $projectDao->convert_language_country_to_memsource($kp_language, $kp_country);
            if (!$lang) return 0;
            $langs[] = $lang;
            [$kp_language, $kp_country] = $projectDao->convert_memsource_to_language_country($lang);
            $kp_target_languages[] = "{$kp_language}-{$kp_country}";
            $targetCount++;
        }
        if (empty($langs)) return 0;
        $kp_target_languages = implode(',', $kp_target_languages);
        $projectDao->record_memsource_project_languages($project->getId(), $kp_source_language, $kp_target_languages);

        // Create Project
        $url = 'https://cloud.memsource.com/web/api2/v1/projects';
        $ch = curl_init($url);
        $deadline = $project->getDeadline();
        if (!empty($post['translation_0']) && empty($post['proofreading_0'])) {
            $workflowSteps = [
                ['id' => 'cFUVHSAAmsVrftA3GC0Ak6'],
            ];
            if ($this->usernamePrefix === 'DEV_') {
                $workflowSteps = [
                    ['id' => 'MyL6Z9IF6ZqQexoZ1OLAS3'],
                ];
            }
        } elseif (empty($post['translation_0']) && !empty($post['proofreading_0'])) {
            $workflowSteps = [
                ['id' => '1Y5F5rJDuvNTnyQBkCUhw0']
            ];
            if ($this->usernamePrefix === 'DEV_') {
                $workflowSteps = [
                    ['id' => '07djiVynQ1FIiQbaKWZzja']
                ];
            }
        } else {
            $workflowSteps = [
                ['id' => 'cFUVHSAAmsVrftA3GC0Ak6'],
                ['id' => '1Y5F5rJDuvNTnyQBkCUhw0']
            ];
            if ($this->usernamePrefix === 'DEV_') {
                $workflowSteps = [
                    ['id' => 'MyL6Z9IF6ZqQexoZ1OLAS3'],
                    ['id' => '07djiVynQ1FIiQbaKWZzja']
                ];
            }
        }
        $data = [
            'name' => $post['project_title'],
            'note' => $post['project_description'],
            'sourceLang' => $sourceLang,
            'targetLangs' => $langs,
            'useDefaultProjectSettings' => true,
            'workflowSteps' => $workflowSteps,
            'dateDue' => substr($deadline, 0, 10) . 'T' . substr($deadline, 11, 8) . 'Z',
            'purchaseOrder' => 'https://twbplatform.org/project/' . $project->getId() . '/view',
        ];
        if ($client = $projectDao->get_memsource_client($project->getOrganisationId())) $data['client'] = ['id' => $client['memsource_client_uid']];

        $payload = json_encode($data);
error_log("Project payload: $payload");//(**)
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $authorization = 'Authorization: Bearer ' . $this->memsourceApiToken;
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $project_result = curl_exec($ch);
        $project_result = json_decode($project_result, true);
error_log(print_r($project_result, true));//(**)
        curl_close($ch);
        if (empty($project_result['uid'])) {
            error_log('memsource Project Create Failed: ' . print_r($project_result, true));
            return 0;
        }

        $workflowLevels = ['', '', '', '', '', '', '', '', '', '', '', '']; // Will contain e.g. 'Translation' or 'Revision' for workflowLevel 1 possibly up to 12
        if (!empty($project_result['workflowSteps'])) {
            foreach ($project_result['workflowSteps'] as $step) {
                foreach ($workflowLevels as $i => $w) {
                    if ($step['workflowLevel'] == $i + 1) $workflowLevels[$i] = $step['name'];
                }
            }
        }
        $projectDao->set_memsource_project($project->getId(), $project_result['id'], $project_result['uid'],
            empty($project_result['createdBy']['uid']) ? '' : $project_result['createdBy']['uid'],
            empty($project_result['owner']['uid']) ? '' : $project_result['owner']['uid'],
            $workflowLevels);

        $split = 1;
        $projectDao->set_memsource_self_service_project($project_result['id'], $split);

        if ($client) {
            // List clients's TMs
            $memsource_client_uid = $client['memsource_client_uid'];
            $url = "https://cloud.memsource.com/web/api2/v1/transMemories?clientId=$memsource_client_uid";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [$authorization]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
error_log("List of TMs: $result");//(**)
            $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($responseCode <= 204) {
                $result = json_decode($result, true);
                $working_tm_uid = '';
                if (!empty($result['content'])) {
                    $content = $result['content'];
                    foreach ($content as $i => $row) {
                        if (strpos($row['name'], '_Working') && $row['sourceLang'] === $sourceLang) {
                            $working_tm_uid = $row['uid'];
                            $working_tm_targets = $row['targetLangs'];
                            break;
                        }
                    }
                }
                if (!$working_tm_uid) { // Must create a TM
                    $orgDao = new OrganisationDao();
                    $org = $orgDao->getOrganisation($project->getOrganisationId());
                    $org_name = $org->getName();

                    $url = 'https://cloud.memsource.com/web/api2/v1/transMemories';
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $data = [
                        'name' => "{$org_name}_Working",
                        'sourceLang' => $sourceLang,
                        'targetLangs' => $langs,
                        'client' => ['id' => $memsource_client_uid],
                        'note' => "Short description of the TM*: Created automatically from KP for self-service partner\r\nLast maintenance: " . date('Y-m-d') . "\r\nMaintenance lead: TWB API\r\nTasks performed: Created from KP",
                    ];
                    $payload = json_encode($data);
error_log("Create TM $payload");//(**)
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
                    $result = curl_exec($ch);
error_log("Created TM: $result");//(**)
                    $result = json_decode($result, true);
                    curl_close($ch);
                    if (!empty($result['uid'])) {
                        $working_tm_uid = $result['uid'];
                        $working_tm_targets = $langs;
                    } else {
                        error_log("Failed to create TM: {$org_name}_Working ($sourceLang)");
                    }
                }

                if ($working_tm_uid) {
                    foreach ($langs as $language) {
                        if (!in_array($language, $working_tm_targets)) {
                            // Add $language to TM
                            $url = "https://cloud.memsource.com/web/api2/v1/transMemories/$working_tm_uid/targetLanguages";
                            $ch = curl_init($url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $data = [
                                'language' => $language
                            ];
                            $payload = json_encode($data);
error_log("Add Language to TM $payload");//(**)
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
                            $result = curl_exec($ch);
error_log("Language added: $result");//(**)
                            $result = json_decode($result, true);
                            curl_close($ch);
                            if (empty($result['targetLangs']) || !in_array($language, $result['targetLangs'])) {
                                error_log("Failed to add $language to TM: {$org_name}_Working ($sourceLang)");
                            }
                        }
                    }

                    // Add TM to project
                    $url = "https://cloud.memsource.com/web/api2/v3/projects/{$project_result['uid']}/transMemories";
                    $ch = curl_init($url);
                    $data = [
                      'dataPerContext' => [
                        [
                          'transMemories' => [
                            [
                                'transMemory' => ['uid' => $working_tm_uid],
                                'readMode' => true,
                                'writeMode' => true,
                                'penalty' => 0,
                                'applyPenaltyTo101Only' => false,
                                'order' => 0
                            ]
                          ],
                          'orderEnabled' => true
                        ]
                      ]
                    ];
                    $payload = json_encode($data);
error_log("Add TM to project $payload");//(**)
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                    $result = curl_exec($ch);
error_log("TM added: $result");//(**)
                    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    if ($responseCode > 204) error_log("Add TM $url responseCode: $responseCode");
                    curl_close($ch);
                }
            } else {
                error_log("Failed to list TMs: $url responseCode: $responseCode");
            }
        } else {
            error_log('Failed to list TMs because memsource client not mapped for project_id: ' . $project->getId() . ', org_id: ' . $project->getOrganisationId());
        }

        // Pre-Translate Settings
        $url = "https://cloud.memsource.com/web/api2/v3/projects/{$project_result['uid']}/preTranslateSettings";
        $ch = curl_init($url);
        $data = [
            'translationMemorySettings' => [
                'useTranslationMemory' => true,
                'translationMemoryThreshold' => .7,
                'confirm100PercentMatches' => false,
                'confirm101PercentMatches' => true,
                'lock100PercentMatches' => false,
                'lock101PercentMatches' => false,
            ],
            'repetitionsSettings' => [
                'autoPropagateRepetitions' => true,
                'confirmRepetitions' => true,
            ],
            'machineTranslationSettings' => [
                'machineTranslation' => false,
            ],
            'nonTranslatableSettings' => [
                'preTranslateNonTranslatables' => true,
                'confirm100PercentMatches' => false,
                'lock100PercentMatches' => false,
                'nonTranslatablesInEditors' => true,
            ],
            'overwriteExistingTranslations' => false,
            'preTranslateOnJobCreation' => true,
            'setJobStatusCompleted' => false,
            'setJobStatusCompletedWhenConfirmed' => false,
            'setProjectStatusCompleted' => false,
        ];
        $payload = json_encode($data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $result = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($responseCode > 204) error_log("Pre-Translate $url responseCode: $responseCode");
        curl_close($ch);

        // Create Job
        $url = "https://cloud.memsource.com/web/api2/v1/projects/{$project_result['uid']}/jobs";
        $ch = curl_init($url);
        $metadata = json_encode(['targetLangs' => $langs]);
        $headers = [
            $authorization,
            "Memsource: $metadata",
            "Content-Disposition: attachment; filename*=UTF-8''" . rawurlencode($file_name),
            'Content-type: application/octet-stream',
            'Content-Length: ' . strlen($file),
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $result = json_decode($result, true);
error_log(print_r($result, true));//(**)
        curl_close($ch);
        if (empty($result['jobs'])) {
            error_log('memsource Job Create Failed: ' . print_r($result, true));
            return 0;
        }

        $memsource_project = $projectDao->get_memsource_project($project->getId());

        //$jobs_indexed = [];
        //foreach ($result['jobs'] as $job) {
        //    $jobs_indexed["{$job['targetLang']}-{$job['workflowLevel']}"] = $job;
        //}
        //$memsource_project['jobs'] = $jobs_indexed;
        return $memsource_project;
    }

    public function record_referer($referer)
    {
        $result = LibAPI\PDOWrapper::call('record_referer', LibAPI\PDOWrapper::cleanseWrapStr(mb_substr($referer, 0, 30)));
        return $result[0]['url'];
    }

    public function get_referers()
    {
        $referers = [];
        $results = LibAPI\PDOWrapper::call('get_referers', '');
        foreach ($results as $result) $referers[] = $result['referer'];
        return $referers;
    }

    public function get_referer_link($referer)
    {
        $results = LibAPI\PDOWrapper::call('get_referer_link', LibAPI\PDOWrapper::cleanseWrapStr($referer));
        return $results[0]['url'];
    }

    public function set_google_user_details($email, $first_name, $last_name)
    {
        LibAPI\PDOWrapper::call('set_google_user_details', LibAPI\PDOWrapper::cleanseNullOrWrapStr($email) . ',' . LibAPI\PDOWrapper::cleanseNullOrWrapStr($first_name) . ',' . LibAPI\PDOWrapper::cleanseNullOrWrapStr($last_name));
    }

    public function get_users_by_month()
    {
        $result = LibAPI\PDOWrapper::call('getUsersAddedLast30Days', '');
        if (empty($result)) return 0;
        return $result[0]['users_joined'];
    }

    public function user_has_strategic_languages($user_id)
    {
        return LibAPI\PDOWrapper::call('user_has_strategic_languages', LibAPI\PDOWrapper::cleanse($user_id));
    }

    public function get_points_for_badges($user_id)
    {
        $result = LibAPI\PDOWrapper::call('get_points_for_badges', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) return ['first_name' => '', 'last_name' => '', 'words_donated' => 0, 'recognition_points' => 0, 'strategic_points' => 0, 'words_donated_for_cert' => 0, 'hours_donated_for_cert' => 0];

        return $result[0];
    }

    public function get_user_tasks($user_id, $limit, $offset)
    {
        $result = LibAPI\PDOWrapper::call('getUserTasks', LibAPI\PDOWrapper::cleanse($user_id). ',' . LibAPI\PDOWrapper::cleanse($limit). ',' . LibAPI\PDOWrapper::cleanse($offset));
        if (empty($result)) return [];
        return $result;
    }

    public function generate_user_rate_pair_selections()
    {
        $selections = LibAPI\PDOWrapper::call('generate_user_rate_pair_selections', '');
        $source_options = [];
        $target_options = [];
        foreach ($selections as $selection) {
            $source_options[$selection['lid']] = $selection['selection_source'];
            $target_options[$selection['lid'] . '-' . $selection['cid']] = $selection['selection'];
        }
        asort($source_options);
        asort($target_options);
        return [$source_options, $target_options];
    }

    public function create_user_rate_pair($user_id, $task_type, $language_id_source, $language_id_target, $country_id_target, $unit_rate)
    {
        LibAPI\PDOWrapper::call('create_user_rate_pair',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanse($task_type) . ',' .
            LibAPI\PDOWrapper::cleanse($language_id_source) . ',' .
            LibAPI\PDOWrapper::cleanse($language_id_target) . ',' .
            LibAPI\PDOWrapper::cleanse($country_id_target) . ',' .
            LibAPI\PDOWrapper::cleanse($unit_rate));
            error_log("create_user_rate_pair($user_id, $task_type, $language_id_source, $language_id_target, $country_id_target, $unit_rate)");
    }

    public function update_user_rate_pair($user_id, $task_type, $language_id_source, $language_id_target, $country_id_target, $unit_rate)
    {
        LibAPI\PDOWrapper::call('update_user_rate_pair',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanse($task_type) . ',' .
            LibAPI\PDOWrapper::cleanse($language_id_source) . ',' .
            LibAPI\PDOWrapper::cleanse($language_id_target) . ',' .
            LibAPI\PDOWrapper::cleanse($country_id_target) . ',' .
            LibAPI\PDOWrapper::cleanse($unit_rate));
            error_log("update_user_rate_pair($user_id, $task_type, $language_id_source, $language_id_target, $country_id_target, $unit_rate)");
    }

    public function get_user_rate_pairs($user_id)
    {
        $result = LibAPI\PDOWrapper::call('get_user_rate_pairs', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) return [];
        return $result;
    }

    public function remove_user_rate_pair($user_id, $task_type, $language_id_source, $language_id_target, $country_id_target)
    {
        LibAPI\PDOWrapper::call('remove_user_rate_pair',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanse($task_type) . ',' .
            LibAPI\PDOWrapper::cleanse($language_id_source) . ',' .
            LibAPI\PDOWrapper::cleanse($language_id_target) . ',' .
            LibAPI\PDOWrapper::cleanse($country_id_target));
            error_log("remove_user_rate_pair($user_id, $task_type, $language_id_source, $language_id_target, $country_id_target)");
    }
}
