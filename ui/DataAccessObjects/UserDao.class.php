<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/Enums/HttpStatusEnum.class.php";
require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/../../Common/protobufs/models/OAuthResponse.php";
require_once __DIR__."/BaseDao.php";

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
    
    public function createSecondaryLanguage($userId, $locale)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/secondaryLanguages";
        $ret = $this->client->call(
            "\SolasMatch\Common\Protobufs\Models\Locale",
            $request,
            Common\Enums\HttpMethodEnum::POST,
            $locale
        );
        return $ret;
    }
    
    public function getSecondaryLanguages($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/secondaryLanguages";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\Locale"), $request);
        return $ret;
    }
    
    public function deleteSecondaryLanguage($userId, $locale)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/removeSecondaryLanguage/$userId/{$locale->getLanguageCode()}".
            "/{$locale->getCountryCode()}";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
        return $ret;
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
