<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\API\Lib as LibAPI;
use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/Enums/HttpStatusEnum.class.php";
require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/../../Common/protobufs/models/OAuthResponse.php";
require_once __DIR__."/BaseDao.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";


class UserDao extends BaseDao
{
    public function __construct()
    {
        $this->client = new Common\Lib\APIHelper(Common\Lib\Settings::get("ui.api_format"));
        $this->siteApi = Common\Lib\Settings::get("site.api");
    }
    
    public function getUserDart($userId)
    {
        $ret = null;
        $helper = new Common\Lib\APIHelper(Common\Enums\FormatEnum::JSON);
        return $helper->serialize($this->getUser($userId));
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
        $request = "{$this->siteApi}v0/users/getByEmail/$email";
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

    public function getUserRealName($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/realName";
        $ret = $this->client->call(null, $request);
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
        $ret = null;
        $request = "{$this->siteApi}v0/users/subscribedToProject/$userId/$projectId";
        $ret = $this->client->call(null, $request);
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
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tasks/$taskId/review";
        $ret = $this->client->call("\SolasMatch\Common\Protobufs\Models\TaskReview", $request);
        return $ret;
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

    public function hasUserRequestedPasswordReset($email)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/email/$email/passwordResetRequest";
        $ret = $this->client->call(null, $request);
        return $ret;
    }

    public function getPasswordResetRequestTime($email)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/email/$email/passwordResetRequest/time";
        $ret = $this->client->call(null, $request);
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

    public function addUserBadgeById($userId, $badgeId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/badges/$badgeId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
        return $ret;
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

    public function claimTask($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tasks/$taskId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::POST);

        $taskDao = new TaskDao();
        $matecat_tasks = $taskDao->getTaskChunk($taskId);
        if (!empty($matecat_tasks)) {
            // We are a chunk
            $matecat_id_job          = $matecat_tasks[0]['matecat_id_job'];
            $matecat_id_job_password = $matecat_tasks[0]['matecat_id_chunk_password'];
            $type_id                 = $matecat_tasks[0]['type_id'];
            $matching_type_id = Common\Enums\TaskTypeEnum::PROOFREADING;
            if ($type_id == Common\Enums\TaskTypeEnum::PROOFREADING) $matching_type_id = Common\Enums\TaskTypeEnum::TRANSLATION;
            $matching_tasks = $taskDao->getMatchingTask($matecat_id_job, $matecat_id_job_password, $matching_type_id);
            if (!empty($matching_tasks)) {
                $taskDao->addUserToTaskBlacklist($userId, $matching_tasks[0]['id']);
            }
        }

        return $ret;
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

    public function requestPasswordReset($email)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/email/$email/passwordResetRequest";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::POST);
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
        $app = \Slim\Slim::getInstance();
        $redirectUri = '';
        if (isset($_SERVER['HTTPS']) && !is_null($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $redirectUri = 'https://';
        } else {
            $redirectUri = 'http://';
        }
        $redirectUri .= $_SERVER['SERVER_NAME'].$app->urlFor('login');

        $request = "{$this->siteApi}v0/users/$email/auth/code/?".
            'client_id='.Common\Lib\Settings::get('oauth.client_id').'&'.
            "redirect_uri=$redirectUri&".
            'response_type=code';
        $app->redirect($request);
    }
    
    public function loginWithGooglePlus($accessToken)
    {
        $app = \Slim\Slim::getInstance();
        $request = "{$this->siteApi}v0/users/gplus/login";
        $postArg = "token=$accessToken"; 
        $email = $this->client->call(
            null,
            $request,
            Common\Enums\HttpMethodEnum::POST,
            $postArg
        );
        error_log("loginWithGooglePlus, Login: $email");
        self::requestAuthCode($email);
    }

    public function loginWithAuthCode($authCode)
    {
        $app = \Slim\Slim::getInstance();
        $request = "{$this->siteApi}v0/users/authCode/login";

        $redirectUri = '';
        if (isset($_SERVER['HTTPS']) && !is_null($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $redirectUri = 'https://';
        } else {
            $redirectUri = 'http://';
        }
        $redirectUri .= $_SERVER['SERVER_NAME'].$app->urlFor('login');

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

    public function getPasswordResetRequest($key)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/passwordReset/$key";
        $ret = $this->client->call("\SolasMatch\Common\Protobufs\Models\PasswordResetRequest", $request);
        return $ret;
    }

    public function resetPassword($password, $key)
    {
        $ret = null;
        $passwordReset = new Common\Protobufs\Models\PasswordReset();
        $passwordReset->setPassword($password);
        $passwordReset->setKey($key);
        $request = "{$this->siteApi}v0/users/passwordReset";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::POST, $passwordReset);
        return $ret;
    }

    public function register($email, $password)
    {
        $ret = null;
        $registerData = new Common\Protobufs\Models\Register();
        $registerData->setEmail($email);
        $registerData->setPassword($password);
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
        $terms_accepted = false;
        $result = LibAPI\PDOWrapper::call('terms_accepted', LibAPI\PDOWrapper::cleanse($user_id));
        if (!empty($result)) {
            $terms_accepted = $result[0]['accepted_level'] >= 1;
        }
        return $terms_accepted;
    }

    public function setRequiredProfileCompletedinSESSION($user_id)
    {
        if ($this->terms_accepted($user_id)) {
            $_SESSION['profile_completed'] = 1;
        }
    }

    public function update_terms_accepted($user_id)
    {
        $_SESSION['profile_completed'] = 1;
        LibAPI\PDOWrapper::call('update_terms_accepted', LibAPI\PDOWrapper::cleanse($user_id) . ',1');
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

    public function changeEmail($user_id, $email)
    {
        $ret = null;
        $registerData = new Common\Protobufs\Models\Register();
        $registerData->setEmail($email);
        $registerData->setPassword("$user_id"); // Repurpose field to hold User for which email is to be changed
        $request = "{$this->siteApi}v0/users/changeEmail";
        $registered = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::POST, $registerData);
        if ($registered) {
            return true;
        } else {
            return false;
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
        if (empty($result)) $result = array();
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

    public function userDownload($certification)
    {
        $app = \Slim\Slim::getInstance();

        $destination = Common\Lib\Settings::get('files.upload_path') . "certs/{$certification['user_id']}/{$certification['certification_key']}/{$certification['vid']}/{$certification['filename']}";

        $app->response->headers->set('Content-type', $certification['mimetype']);
        $app->response->headers->set('Content-Disposition', "attachment; filename=\"" . trim($certification['filename'], '"') . "\"");
        $app->response->headers->set('Content-length', filesize($destination));
        $app->response->headers->set('X-Frame-Options', 'ALLOWALL');
        $app->response->headers->set('Pragma', 'no-cache');
        $app->response->headers->set('Cache-control', 'no-cache, must-revalidate, no-transform');
        $app->response->headers->set('X-Sendfile', realpath($destination));
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
        $url_list['proz']   = ['desc' => 'Your ProZ.com URL (if you have one)', 'state' => ''];
        $url_list['linked'] = ['desc' => 'Your LinkedIn URL (if you have one)', 'state' => ''];
        $url_list['other']  = ['desc' => 'Other URL', 'state' => ''];
        $urls = $this->getUserURLs($user_id);
        foreach ($urls as $url) {
            $url_list[$url['url_key']]['state'] = $url['url'];
        }
        return $url_list;
    }

    public function getCapabilityList($user_id)
    {
        $capability_list = [];
        $capability_list['badge_id_6']  = ['desc' => 'Translation',         'state' => 0, 'id' =>  6];
        $capability_list['badge_id_7']  = ['desc' => 'Revision',            'state' => 0, 'id' =>  7];
        $capability_list['badge_id_10'] = ['desc' => 'Subtitling',          'state' => 0, 'id' => 10];
        $capability_list['badge_id_11'] = ['desc' => 'Monolingual editing', 'state' => 0, 'id' => 11];
        $capability_list['badge_id_12'] = ['desc' => 'DTP',                 'state' => 0, 'id' => 12];
        $capability_list['badge_id_13'] = ['desc' => 'Voiceover',           'state' => 0, 'id' => 13];
        $capability_list['badge_id_8']  = ['desc' => 'Interpretation',      'state' => 0, 'id' =>  8];
        // If we add >13, then the code below will need to be changed as will authenticateUserForOrgBadge() and SQL for removeUserBadge
        $badges = $this->getUserBadges($user_id);
        if (!empty($badges)) {
            foreach ($badges as $badge) {
                if ($badge->getId() >= 6 && $badge->getId() != 9 && $badge->getId() <= 13) {
                    $capability_list['badge_id_' . $badge->getId()]['state'] = 1;
                }
            }
        }
        return $capability_list;
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

    public function insert_admin_comment($user_id, $admin_id, $work_again, $comment)
    {
        LibAPI\PDOWrapper::call('insert_admin_comment',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanse($admin_id) . ',' .
            LibAPI\PDOWrapper::cleanse($work_again) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($comment));
    }

    public function delete_admin_comment($id)
    {
        LibAPI\PDOWrapper::call('delete_admin_comment', LibAPI\PDOWrapper::cleanse($id));
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
}
