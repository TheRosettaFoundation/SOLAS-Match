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
require_once '/repo/neon-php/neon.php';


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
                $request = "{$args[2]}v0/users/$args[1]";
                return $args[0]->call("\SolasMatch\Common\Protobufs\Models\User", $request);
            },
            array($this->client, $userId, $this->siteApi)
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

    public function getUserBadges($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/badges";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\Badge"), $request);
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
        $this->verify_email_allowed_register($email);

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
        $this->verify_email_allowed_register($email);

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
        $this->verify_email_allowed_register($email);

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

    public function verify_email_allowed_register($email)
    {
        $app = \Slim\Slim::getInstance();
        error_log("verify_email_allowed_register($email)");

        $user = $this->verifyUserByEmail($email);
        $terms_accepted = false;
        if ($user) {
            $terms_accepted = $this->terms_accepted($user->getId());
        }
        if ($user && $terms_accepted) return; // User has previously accepted terms and conditions

        $neon = new \Neon();

        $credentials = array(
            'orgId'  => Common\Lib\Settings::get('neon.org_id'),
            'apiKey' => Common\Lib\Settings::get('neon.api_key')
        );

        $loginResult = $neon->login($credentials);
        if (!isset($loginResult['operationResult']) || $loginResult['operationResult'] !== 'SUCCESS') {
            error_log("verify_email_allowed_register($email), could not connect to NeonCRM");
            $app->redirect($app->urlFor('no_application_error'));
        }

        $search = array(
            'method' => 'account/listAccounts',
            'columns' => array('standardFields' => array('Email 1'),
                               'customFields'   => array(209))
        );
        $search['criteria'] = array(array('Email', 'EQUAL', $email));

        $result = $neon->search($search);

        $neon->go(array('method' => 'common/logout'));

        if (!empty($result) && !empty($result['searchResults'])) {
            $terms_accepted = false;
            foreach ($result['searchResults'] as $r) {
                if (!empty($r['Do you agree to abide by the Code of Conduct?']) && ($r['Do you agree to abide by the Code of Conduct?'] === 'Yes')) {
                    $terms_accepted = true;
                }
            }

            if ($terms_accepted) {
                if ($user) $this->update_terms_accepted($user->getId());
                error_log("verify_email_allowed_register($email) Accepted T&Cs in Neon");
                return; // User is known in Neon and has accepted terms and conditions
            } else {
                // User is known in Neon, but has not accepted terms and conditions
                error_log("verify_email_allowed_register($email) Ask to accept T&Cs");
                $app->redirect('https://kato.translatorswb.org/accept-code-of-conduct.html?email=' . urlencode($email));
            }
        }

        if ($user) {
            // They are a legacy Trommons user with no Neon account
            error_log("verify_email_allowed_register($email) Legacy Trommons user needs to fill in Neon application form (with explanation)");
            $app->redirect('https://kato.translatorswb.org/rosetta-info-update.html?email=' . urlencode($email));
        }

        // User is not known in Neon, they will be asked to fill in the Neon application form
        error_log("verify_email_allowed_register($email) Not allowed!");
        $app->redirect($app->urlFor('no_application'));
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

    public function update_terms_accepted($user_id)
    {
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

    public function process_neonwebhook()
    {
$NEON_NATIVELANGFIELD = 64;
$NEON_SOURCE1FIELD    = 167;
$NEON_TARGET1FIELD    = 168;
$NEON_SOURCE2FIELD    = 169;
$NEON_TARGET2FIELD    = 170;
$NEON_LEVELFIELD      = 173;

$from_neon_to_trommons_pair = array(
'Afrikaans' => array('af', 'ZA'),
'Albanian' => array('sq', 'AL'),
'Amharic' => array('am', 'AM'),
'Arabic' => array('ar', 'SA'),
'Aragonese' => array('an', 'ES'),
'Armenian' => array('hy', 'AM'),
'Asturian' => array('ast', 'ES'),
'Azerbaijani' => array('az', 'AZ'),
'Basque' => array('eu', 'ES'),
'Bengali' => array('bn', 'IN'),
'Belarus' => array('be', 'BY'),
'Belgian French' => array('fr', 'BE'),
'Bosnian' => array('bs', 'BA'),
'Breton' => array('br', 'FR'),
'Bulgarian' => array('bg', 'BG'),
'Burmese' => array('my', 'MM'),
'Catalan' => array('ca', 'ES'),
'Catalan Valencian' => array('ca', '--'),
'Cebuano' => array('ceb', 'PH'),
'Chinese Simplified' => array('zh', 'CN'),
'Chinese Traditional' => array('zh', 'TW'),
'Croatian' => array('hr', 'HR'),
'Czech' => array('cs', 'CZ'),
'Danish' => array('da', 'DK'),
'Dutch' => array('nl', 'NL'),
'English' => array('en', 'GB'),
'English US' => array('en', 'US'),
'Esperanto' => array('eo', '--'),
'Estonian' => array('et', 'EE'),
'Faroese' => array('fo', 'FO'),
'Fula' => array('ff', '--'),
'Finnish' => array('fi', 'FI'),
'Flemish' => array('nl', 'BE'),
'French' => array('fr', 'FR'),
'French Canada' => array('fr', 'CA'),
'Galician' => array('gl', 'ES'),
'Georgian' => array('ka', 'GE'),
'German' => array('de', 'DE'),
'Greek' => array('el', 'GR'),
'Gujarati' => array('gu', 'IN'),
'Haitian Creole French' => array('ht', 'HT'),
'Hausa' => array('ha', '--'),
'Hawaiian' => array('haw', 'US'),
'Hebrew' => array('he', 'IL'),
'Hindi' => array('hi', 'IN'),
'Hungarian' => array('hu', 'HU'),
'Icelandic' => array('is', 'IS'),
'Indonesian' => array('id', 'ID'),
'Irish Gaelic' => array('ga', 'IE'),
'Italian' => array('it', 'IT'),
'Japanese' => array('ja', 'JP'),
'Kanuri' => array('kr', '--'),
'Kazakh' => array('kk', 'KZ'),
'Khmer' => array('km', 'KH'),
'Korean' => array('ko', 'KR'),
'Kurdish Kurmanji' => array('ku', '--'),
'Kurdish Sorani' => array('ku', '--'),
'Kyrgyz' => array('ky', 'KG'),
'Latvian' => array('lv', 'LV'),
'Lingala' => array('ln', '--'),
'Lithuanian' => array('lt', 'LT'),
'Macedonian' => array('mk', 'MK'),
'Malagasy' => array('mg', 'MG'),
'Malay' => array('ms', 'MY'),
'Malayalam' => array('ml', 'IN'),
'Maltese' => array('mt', 'MT'),
'Maori' => array('mi', 'NZ'),
'Mongolian' => array('mn', 'MN'),
'Montenegrin' => array('sr', 'ME'),
'Ndebele' => array('nr', 'ZA'),
'Nepali' => array('ne', 'NP'),
'Nyanja' => array('ny', '--'),
'Occitan' => array('oc', 'FR'),
'Occitan Aran' => array('oc', 'ES'),
'Oriya' => array('or', 'IN'),
'Panjabi' => array('pa', 'IN'),
'Pashto' => array('ps', 'PK'),
'Dari' => array('prs', '--'),
'Persian' => array('fa', 'IR'),
'Polish' => array('pl', 'PL'),
'Portuguese' => array('pt', 'PT'),
'Portuguese Brazil' => array('pt', 'BR'),
'Quechua' => array('qu', '--'),
'Rohingya' => array('rhg', 'MM'),
'Rohingyalish' => array('rhl', 'MM'),
'Romanian' => array('ro', 'RO'),
'Russian' => array('ru', 'RU'),
'Serbian Latin' => array('sr', '--'),
'Serbian Cyrillic' => array('sr', 'RS'),
'Sesotho' => array('nso', 'ZA'),
'Setswana (South Africa)' => array('tn', 'ZA'),
'Slovak' => array('sk', 'SK'),
'Slovenian' => array('sl', 'SI'),
'Somali' => array('so', 'SO'),
'Spanish' => array('es', 'ES'),
'Spanish Latin America' => array('es', 'MX'),
'Spanish Colombia' => array('es', 'CO'),
'Swahili' => array('swh', 'KE'),
'Swedish' => array('sv', 'SE'),
'Swiss German' => array('de', 'CH'),
'Tagalog' => array('tl', 'PH'),
'Tamil' => array('ta', 'IN'),
'Telugu' => array('te', 'IN'),
'Tatar' => array('tt', 'RU'),
'Thai' => array('th', 'TH'),
'Tigrinya' => array('ti', '--'),
'Tsonga' => array('ts', 'ZA'),
'Turkish' => array('tr', 'TR'),
'Turkmen' => array('tk', 'TM'),
'Ukrainian' => array('uk', 'UA'),
'Urdu' => array('ur', 'PK'),
'Uzbek' => array('uz', 'UZ'),
'Vietnamese' => array('vi', 'VN'),
'Welsh' => array('cy', 'GB'),
'Xhosa' => array('xh', 'ZA'),
'Yoruba' => array('yo', 'NG'),
'Zulu' => array('zu', 'ZA'),
'Hmong' => array('hmn', 'CN'),
'Karen' => array('kar', 'MM'),
'Rundi' => array('run', 'BI'),
'Assamese' => array('asm', 'IN'),
'Garo' => array('grt', 'IN'),
'Khasi' => array('kha', 'IN'),
'Konkani' => array('kok', 'IN'),
'Manipuri' => array('mni', 'IN'),
'Mizo' => array('lus', 'IN'),
'Chadian Arabic' => array('shu', 'TD'),
'Kamba' => array('kam', 'KE'),
'Margi' => array('mrt', 'NG'),
'Borana' => array('gax', 'KE'),
'Meru' => array('mer', 'KE'),
'Kalenjin' => array('kln', 'KE'),
'Luo' => array('luo', 'KE'),
'Kikuyu' => array('ki', 'KE'),
'Maa' => array('cma', 'KE'),
'Mijikenda' => array('nyf', 'KE'),
'Luhya' => array('luy', 'KE'),
'Kisii' => array('guz', 'KE'),

'Fijian' => array('fj', 'FJ'),
'Bislama' => array('bi', 'VU'),
'Tok Pisin' => array('tpi', 'PG'),
'Tongan' => array('to', 'TO'),

'Saint Lucian Creole French' => array('acf', 'LC'),
'Antigua and Barbuda Creole English' => array('aig', 'AG'),
'Bahamas Creole English' => array('bah', 'BS'),
'Bemba' => array('bem', 'ZM'),
'Bajan' => array('bjs', 'BB'),
'Tibetan' => array('bo', 'CN'),
'Chamorro' => array('ch', 'GU'),
'Coptic' => array('cop', 'EG'),
'Seselwa Creole French' => array('crs', 'SC'),
'Maldivian' => array('dv', 'MV'),
'Dzongkha' => array('dz', 'BT'),
'Jamaican Creole English' => array('jam', 'JM'),
'Filipino' => array('fil', 'PH'),
'Fanagalo' => array('fng', 'ZA'),
'Grenadian Creole English' => array('gcl', 'GD'),
'Manx Gaelic' => array('gv', 'IM'),
'Guyanese Creole English' => array('gyn', 'GY'),
'Kabylian' => array('kab', 'DZ'),
'Kabuverdianu' => array('kea', 'CV'),
'Inuktitut Greenlandic' => array('kl', 'GL'),
'Mende' => array('men', 'SL'),
'Morisyen' => array('mfe', 'MU'),
'Marshallese' => array('mh', 'MH'),
'Niuean' => array('niu', 'NU'),
'Norwegian' => array('no', 'NO'),
'Norwegian Bokmål' => array('no', 'NO'),
"Norwegian Bokm\xE5l" => array('no', 'NO'),
'Norwegian Nynorsk' => array('nn', 'NO'),
'Odia' => array('ory', 'IN'),
'Palauan' => array('pau', 'PW'),
'Pijin' => array('pis', 'SB'),
'Potawatomi' => array('pot', 'US'),
'Crioulo Upper Guinea' => array('pov', 'GW'),
'Uma' => array('ppk', 'ID'),
'Balkan Gipsy' => array('rm', 'RO'),
'Kirundi' => array('run', 'RW'),
'Kinyarwanda' => array('rw', 'RW'),
'Sango' => array('sg', 'SG'),
'Samoan' => array('sm', 'WS'),
'Shona' => array('sn', 'ZW'),
'Sranan Tongo' => array('srn', 'SR'),
'Sotho Southern' => array('st', 'ST'),
'Vincentian Creole English' => array('svc', 'VC'),
'Syriac (Aramaic)' => array('syc', 'TR'),
'Tetum' => array('tet', 'TL'),
'Tokelauan' => array('tkl', 'TK'),
'Tamashek (Tuareg)' => array('tmh', 'DZ'),
'Tswana' => array('tn', 'BW'),
'Tuvaluan' => array('tvl', 'TV'),
'Virgin Islands Creole English' => array('vic', 'US'),
'Wallisian' => array('wls', 'WF'),
'Wolof' => array('wo', 'SN'),
'Classical Greek' => array('grc', 'GR'),
'Comorian Ngazidja' => array('zdj', 'KM'),
'Chinese Trad. (Hong Kong)' => array('zh', 'HK'),
'Chittagonian' => array('ctg', 'BD'),
'Austrian German' => array('de', 'AT'),
'English Australia' => array('en', 'AU'),
'English Canada' => array('en', 'CA'),
'English India' => array('en', 'IN'),
'English Ireland' => array('en', 'IE'),
'English New Zealand' => array('en', 'NZ'),
'English Singapore' => array('en', 'SG'),
'French Swiss' => array('fr', 'CH'),
'Italian Swiss' => array('it', 'CH'),
);

        $account_id   = '';
        $email        = '';
        $user_id      = '';
        $first_name   = '';
        $last_name    = '';
        $display_name = '';
        $nativelang   = '';
        $sourcelang1  = '';
        $targetlang1  = '';
        $sourcelang2  = '';
        $targetlang2  = '';
        $org_id_neon  = '';
        $org_name     = '';
        $quality_level= 1;
error_log('function process_neonwebhook()');
        if (!empty($_POST['payload'])) {
            $result = json_decode($_POST['payload'], true);
error_log(print_r($result, true));
            if (!empty($result['eventTrigger']) && $result['eventTrigger'] == 'editAccount') {

                if (!empty($result['data'])) {
                    $data = $result['data'];

                    if (!empty($data['individualAccount'])) {

                        if (!empty($result['customParameters'])) {
                            $customParameters = $result['customParameters'];
                            if (!empty($customParameters['apikey'])) {
                                $apikey_in = $customParameters['apikey'];
                                if (hash('sha512', $customParameters['apikey']) !== '42b1851be2de0ab64d18f9ad4ac7bef599343654345c20424794695af7db758ea9ad44de2e04375f6b5d6b6facb6e4797941db18383eae92bce032c896a20acb') {
                                    error_log('apikey from Neon does not match');
                                    return;
                                }
                            } else {
                                error_log('apikey not in customParameters from Neon');
                                return;
                            }
                        } else {
                            error_log('No customParameters from Neon');
                            return;
                        }

                        $account = $data['individualAccount'];

                        if (!empty($account['accountId'])) $account_id = $account['accountId'];
                        if (!empty($account['existingOrganizationId'])) $org_id_neon = $account['existingOrganizationId'];

                        if (!empty($account['primaryContact'])) {
                            $contact = $account['primaryContact'];

                            // These are in 8859-1 NOT UTF-8...
                            //if (!empty($contact['firstName']))     $first_name =   $contact['firstName'];
                            //if (!empty($contact['lastName']))      $last_name =    $contact['lastName'];
                            //if (!empty($contact['preferredName'])) $display_name = $contact['preferredName'];
                            if (!empty($contact['email1'])) $email = $contact['email1'];
                        }

                        if (!empty($account['customFieldDataList'])) {
                            $customFieldDataList = $account['customFieldDataList'];

                            if (!empty($customFieldDataList['customFieldData'])) {
                                $customFieldData = $customFieldDataList['customFieldData'];

                                foreach ($customFieldData as $field) {
                                    if ($field['fieldId'] == $NEON_NATIVELANGFIELD && !empty($field['fieldValue'])) $nativelang  = $field['fieldValue'];
                                    if ($field['fieldId'] == $NEON_SOURCE1FIELD    && !empty($field['fieldValue'])) $sourcelang1 = $field['fieldValue'];
                                    if ($field['fieldId'] == $NEON_TARGET1FIELD    && !empty($field['fieldValue'])) $targetlang1 = $field['fieldValue'];
                                    if ($field['fieldId'] == $NEON_SOURCE2FIELD    && !empty($field['fieldValue'])) $sourcelang2 = $field['fieldValue'];
                                    if ($field['fieldId'] == $NEON_TARGET2FIELD    && !empty($field['fieldValue'])) $targetlang2 = $field['fieldValue'];
                                    if ($field['fieldId'] == $NEON_LEVELFIELD      && !empty($field['fieldOptionId'])) {
                                        $levels = array('289' => 1, '290' => 2, '291' => 3);
                                        if (!empty($levels[$field['fieldOptionId']])) $quality_level = $levels[$field['fieldOptionId']];
                                    }
                                }
                            }
                        }

                        if (!empty($email) && $user = $this->verifyUserByEmail($email)) {
                            $user_id = $user->getId();

                            if (!empty($from_neon_to_trommons_pair[$sourcelang1]) && !empty($from_neon_to_trommons_pair[$targetlang1]) && ($sourcelang1 != $targetlang1)) {
                                $this->createUserQualifiedPair($user_id, $from_neon_to_trommons_pair[$sourcelang1][0], $from_neon_to_trommons_pair[$sourcelang1][1], $from_neon_to_trommons_pair[$targetlang1][0], $from_neon_to_trommons_pair[$targetlang1][1], $quality_level);
                            }
                            if (!empty($from_neon_to_trommons_pair[$sourcelang1]) && !empty($from_neon_to_trommons_pair[$targetlang2]) && ($sourcelang1 != $targetlang2)) {
                                $this->createUserQualifiedPair($user_id, $from_neon_to_trommons_pair[$sourcelang1][0], $from_neon_to_trommons_pair[$sourcelang1][1], $from_neon_to_trommons_pair[$targetlang2][0], $from_neon_to_trommons_pair[$targetlang2][1], $quality_level);
                            }
                            if (!empty($from_neon_to_trommons_pair[$sourcelang2]) && !empty($from_neon_to_trommons_pair[$targetlang1]) && ($sourcelang2 != $targetlang1)) {
                                $this->createUserQualifiedPair($user_id, $from_neon_to_trommons_pair[$sourcelang2][0], $from_neon_to_trommons_pair[$sourcelang2][1], $from_neon_to_trommons_pair[$targetlang1][0], $from_neon_to_trommons_pair[$targetlang1][1], $quality_level);
                            }
                            if (!empty($from_neon_to_trommons_pair[$sourcelang2]) && !empty($from_neon_to_trommons_pair[$targetlang2]) && ($sourcelang2 != $targetlang2)) {
                                $this->createUserQualifiedPair($user_id, $from_neon_to_trommons_pair[$sourcelang2][0], $from_neon_to_trommons_pair[$sourcelang2][1], $from_neon_to_trommons_pair[$targetlang2][0], $from_neon_to_trommons_pair[$targetlang2][1], $quality_level);
                            }
                        } else {
                            error_log("No Trommons User found for Neon ID: $account_id ($email)");
                        }
                    }
                }
            }
        }

        error_log("Neon Account update... email: $email, account_id: $account_id, user_id: $user_id, nativelang: $nativelang, org_id_neon: $org_id_neon");
        error_log("sourcelang1: $sourcelang1, sourcelang2: $sourcelang2, targetlang1: $targetlang1, targetlang2: $targetlang2, quality_level: $quality_level");

        if (!empty($account_id) && !empty($user_id)) {
            $account_id_wanted = $account_id;

            $neon = new \Neon();

            $credentials = array(
                'orgId'  => Common\Lib\Settings::get('neon.org_id'),
                'apiKey' => Common\Lib\Settings::get('neon.api_key')
            );

            $loginResult = $neon->login($credentials);
            if (isset($loginResult['operationResult']) && $loginResult['operationResult'] === 'SUCCESS') {
                $search = array(
                    'method' => 'account/listAccounts',
                    'columns' => array(
                        'standardFields' => array(
                            'Account ID',
                            'First Name',
                            'Last Name',
                            'Preferred Name',
                            'Company Name',
                            'Company ID'),
                    )
                );

                $search['criteria'] = array(array('Account ID', 'EQUAL', $account_id_wanted));

                $result = $neon->search($search);

                $neon->go(array('method' => 'common/logout'));

                if (empty($result) || empty($result['searchResults'])) {
                    error_log("No result found from NeonCRM (webhook), account_id: $account_id_wanted");
                } else {
                    $r = current($result['searchResults']);
                    $first_name   = (empty($r['First Name']))     ? '' : $r['First Name'];
                    $last_name    = (empty($r['Last Name']))      ? '' : $r['Last Name'];
                    $display_name = (empty($r['Preferred Name'])) ? '' : $r['Preferred Name'];

                    $userInfo = $this->getUserPersonalInformation($user_id);

                    if (!empty($first_name)) $userInfo->setFirstName($first_name);
                    if (!empty($last_name))  $userInfo->setLastName($last_name);

                    $this->saveUserPersonalInformation($userInfo);

                    if (!empty($display_name)) $user->setDisplayName($display_name);

                    if (!empty($from_neon_to_trommons_pair[$nativelang])) {
                        $locale = new Common\Protobufs\Models\Locale();
                        $locale->setLanguageCode($from_neon_to_trommons_pair[$nativelang][0]);
                        $locale->setCountryCode($from_neon_to_trommons_pair[$nativelang][1]);
                        $user->setNativeLocale($locale);
                    }

                    $this->saveUser($user);

                    $org_name = (empty($r['Company Name'])) ? '' : $r['Company Name'];
                    $org_name = trim(str_replace(array('"', '<', '>'), '', $org_name)); // Only Trommons value with limitations (not filtered on output)

                    error_log("Neon Account update... first_name: $first_name, last_name: $last_name, display_name: $display_name, org_name: $org_name");

                    if (!empty($org_name) && !empty($org_id_neon) && $org_id_neon != 3783) { // Translators without Borders (TWb)
                        if ($org_id_matching_neon = $this->getOrgIDMatchingNeon($org_id_neon)) {
                            $this->addOrgAdmin($user_id, $org_id_matching_neon);
                            error_log("process_neonwebhook($email), addOrgAdmin($user_id, $org_id_matching_neon)");

                        } elseif ($org_id_matching_neon = $this->getOrgIDUsingName($org_name)) { // unlikely?
                            $this->insertOrgIDMatchingNeon($org_id_matching_neon, $org_id_neon);

                            $this->addOrgAdmin($user_id, $org_id_matching_neon);
                            error_log("process_neonwebhook($email), addOrgAdmin($user_id, $org_id_matching_neon), $org_name existing");

                        } elseif (!empty($org_name)) {
                            $org_id_matching_neon = $this->insertOrg($org_name, $email);
                            error_log("process_neonwebhook($email), created Org: $org_name");
                            if (!empty($org_id_matching_neon)) {
                                $this->insertOrgIDMatchingNeon($org_id_matching_neon, $org_id_neon);

                                $this->addOrgAdmin($user_id, $org_id_matching_neon);
                                error_log("process_neonwebhook($email), addOrgAdmin($user_id, $org_id_matching_neon)");
                            }
                        }
                    }
                }
            } else {
                error_log('There was a problem connecting to NeonCRM (webhook)');
            }
        }
    }

    public function getOrgIDMatchingNeon($org_id_neon)
    {
        $org_id = 0;
        $result = LibAPI\PDOWrapper::call('getOrgIDMatchingNeon', LibAPI\PDOWrapper::cleanse($org_id_neon));
        if (!empty($result)) {
            $org_id = $result[0]['org_id'];
        }
        return $org_id;
    }

    public function insertOrgIDMatchingNeon($org_id, $org_id_neon)
    {
        LibAPI\PDOWrapper::call('insertOrgIDMatchingNeon', LibAPI\PDOWrapper::cleanse($org_id) . ',' . LibAPI\PDOWrapper::cleanse($org_id_neon));
    }

    public function addOrgAdmin($user_id, $org_id)
    {
        $args = LibAPI\PDOWrapper::cleanseNull($user_id) . ',' . LibAPI\PDOWrapper::cleanseNull($org_id);
        LibAPI\PDOWrapper::call('acceptMemRequest', $args);
        LibAPI\PDOWrapper::call('addAdmin', $args);
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
    
    public function getPersonalInfo($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/personalInfo";
        $ret = $this->client->call("\SolasMatch\Common\Protobufs\Models\UserPersonalInformation", $request);
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
}
