<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;

/**
 * Description of Users
 *
 * @author sean
 */

require_once __DIR__.'/../../Common/protobufs/models/OAuthResponse.php';
require_once __DIR__."/../../Common/protobufs/models/PasswordResetRequest.php";
require_once __DIR__."/../../Common/protobufs/models/PasswordReset.php";
require_once __DIR__."/../DataAccessObjects/UserDao.class.php";
require_once __DIR__."/../DataAccessObjects/TaskDao.class.php";
require_once __DIR__."/../lib/Notify.class.php";
require_once __DIR__."/../lib/Middleware.php";


class Users
{
    public static function init()
    {
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users(:format)/',
            function ($format = ".json") {
                API\Dispatcher::sendResponse(null, "display all users", null, $format);
            },
            'getUsers',
            null
        );

        /**
        * Gets a single user by their id
        * @param The id of a badge
        * @return Badge
        **/
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/:userId/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = DAO\UserDao::getUser($userId);
                if (!is_null($data)) {
                    $data->setPassword(null);
                    $data->setNonce(null);
                }
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUser'
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/getByEmail/:email/',
            function ($email, $format = ".json") {
                if (!is_numeric($email) && strstr($email, '.')) {
                    $temp = array();
                    $temp = explode('.', $email);
                    $lastIndex = sizeof($temp)-1;
                    if ($lastIndex > 0) {
                        $email = $temp[0];
                        for ($i = 1; $i < $lastIndex; $i++) {
                            $email = "{$email}.{$temp[$i]}";
                        }
                        if ($temp[$lastIndex] != "json") {
                            $email = "{$email}.{$temp[$lastIndex]}";
                        }
                    }
                }
                $data = DAO\UserDao::getUser(null, $email);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUserByEmail',
            '\SolasMatch\API\Lib\Middleware::registerValidation'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/users/:userId/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                DAO\UserDao::deleteUser($userId);
                API\Dispatcher::sendResponse(null, null, null, $format);
            },
            'deleteUser',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        //returns the currently logged in user based on oauth token
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/getCurrentUser(:format)/',
            function ($format = ".json") {
                $user = DAO\UserDao::getLoggedInUser();
                API\Dispatcher::sendResponse(null, $user, null, $format);
            },
            'getCurrentUser',
            null
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/login(:format)/',
            function ($format = ".json") {
                $data = new Common\Protobufs\Models\Login();
                $data->setEmail("sample@example.com");
                $data->setPassword("sample_password");
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getLoginTemplate',
            null
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/users/login(:format)/',
            function ($format = ".json") {
                $body = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $loginData = $client->deserialize($body, "\SolasMatch\Common\Protobufs\Models\Login");
                $params = array();
                $params['client_id'] = API\Dispatcher::clenseArgs('client_id', Common\Enums\HttpMethodEnum::GET, null);
                $params['client_secret'] = API\Dispatcher::clenseArgs('client_secret', Common\Enums\HttpMethodEnum::GET, null);
                $params['username'] = $loginData->getEmail();
                $params['password'] = $loginData->getPassword();
                try {
                    $server = API\Dispatcher::getOauthServer();
                    $response = $server->getGrantType('password')->completeFlow($params);
                    $oAuthResponse = new Common\Protobufs\Models\OAuthResponse();
                    $oAuthResponse->setToken($response['access_token']);
                    $oAuthResponse->setTokenType($response['token_type']);
                    $oAuthResponse->setExpires($response['expires']);
                    $oAuthResponse->setExpiresIn($response['expires_in']);

                    $user = DAO\UserDao::getLoggedInUser($response['access_token']);
                    $user->setPassword(null);
                    $user->setNonce(null);
                    API\Dispatcher::sendResponse(null, $user, null, $format, $oAuthResponse);
                } catch (Common\Exceptions\SolasMatchException $e) {
                    API\Dispatcher::sendResponse(null, $e->getMessage(), $e->getCode(), $format);
                } catch (\Exception $e) {
                    API\Dispatcher::sendResponse(null, $e->getMessage(), Common\Enums\HttpStatusEnum::UNAUTHORIZED, $format);
                }
            },
            'login',
            null
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/:email/auth/code(:format)/',
            function ($email, $format = '.json') {
                $user = DAO\UserDao::getUser(null, $email);
                if (!$user) {
                    DAO\UserDao::apiRegister($email, md5($email), false);
                    $user = DAO\UserDao::getUser(null, $email);
                    //$user = $user[0];
                    DAO\UserDao::finishRegistration($user->getId());
                }
                $params = array();
                try {
                    if (DAO\AdminDao::isUserBanned($user->getId())) {
                        throw new \Exception("User is banned");
                    }
                    $server = API\Dispatcher::getOauthServer();
                    $authCodeGrant = $server->getGrantType('authorization_code');
                    $params = $authCodeGrant->checkAuthoriseParams();
                    $authCode = $authCodeGrant->newAuthoriseRequest('user', $user->getId(), $params);
                } catch (\Exception $e) {
                    DAO\UserDao::logLoginAttempt($user->getId(), $email, 0);
                    if (!isset($params['redirect_uri'])) {
                        API\Dispatcher::getDispatcher()->redirect(
                            API\Dispatcher::getDispatcher()->request()->getReferrer().
                            "?error=auth_failed&error_message={$e->getMessage()}"
                        );
                    } else {
                        API\Dispatcher::getDispatcher()->redirect(
                            $params['redirect_uri']."?error=auth_failed&error_message={$e->getMessage()}"
                        );
                    }
                }
                API\Dispatcher::getDispatcher()->redirect($params['redirect_uri']."?code=$authCode");
            },
            'getAuthCode',
            null
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/users/authCode/login(:format)/',
            function ($format = '.json') {
                try {
                    $server = API\Dispatcher::getOauthserver();
                    $authCodeGrant = $server->getGrantType('authorization_code');
                    $accessToken = $authCodeGrant->completeFlow();

                    $oAuthToken = new Common\Protobufs\Models\OAuthResponse();
                    $oAuthToken->setToken($accessToken['access_token']);
                    $oAuthToken->setTokenType($accessToken['token_type']);
                    $oAuthToken->setExpires($accessToken['expires']);
                    $oAuthToken->setExpiresIn($accessToken['expires_in']);

                    $user = DAO\UserDao::getLoggedInUser($accessToken['access_token']);
                    $user->setPassword(null);
                    $user->setNonce(null);

                    DAO\UserDao::logLoginAttempt($user->getId(), $user->getEmail(), 1);

                    API\Dispatcher::sendResponse(null, $user, null, $format, $oAuthToken);
                } catch (\Exception $e) {
                    API\Dispatcher::sendResponse(null, $e->getMessage(), Common\Enums\HttpStatusEnum::BAD_REQUEST, $format);
                }
            },
            'getAccessToken',
            null
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/passwordReset(:format)/',
            function ($format = ".json") {
                $data = Common\Lib\ModelFactory::buildModel("PasswordReset", array());
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getResetTemplate'
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/passwordReset/:key/',
            function ($key, $format = ".json") {
                if (!is_numeric($key) && strstr($key, '.')) {
                    $key = explode('.', $key);
                    $format = '.'.$key[1];
                    $key = $key[0];
                }
                $data = DAO\UserDao::getPasswordResetRequests(null, $key);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getResetRequest',
            null
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/users/passwordReset(:format)/',
            function ($format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\PasswordReset');
                $result = DAO\UserDao::passwordReset($data->getPassword(), $data->getKey());
                API\Dispatcher::sendResponse(null, $result, null, $format);
            },
            'resetPassword',
            null
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/register(:format)/',
            function ($format = ".json") {
                $data = new Common\Protobufs\Models\Register();
                $data->setPassword("test");
                $data->setEmail("test@test.rog");
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getRegisterTemplate',
            null
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/users/register(:format)/',
            function ($format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Register");
                $registered = DAO\UserDao::apiRegister($data->getEmail(), $data->getPassword());
                API\Dispatcher::sendResponse(null, $registered, null, $format);
            },
            'register',
            null
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/:uuid/registered(:format)/',
            function ($uuid, $format = '.json') {
                $user = DAO\UserDao::getRegisteredUser($uuid);
                API\Dispatcher::sendResponse(null, $user, null, $format);
            },
            'getRegisteredUser',
            null
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/users/:uuid/finishRegistration(:format)/',
            function ($uuid, $format = '.json') {
                $user = DAO\UserDao::getRegisteredUser($uuid);
                if ($user != null) {
                    $ret = DAO\UserDao::finishRegistration($user->getId());
                    API\Dispatcher::sendResponse(null, $ret, null, $format);
                } else {
                    API\Dispatcher::sendResponse(null, "Invalid UUID", Common\Enums\HttpStatusEnum::UNAUTHORIZED, $format);
                }
            },
            'finishRegistration',
            null
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/:userId/verified(:format)/',
            function ($userId, $format = '.json') {
                $ret = DAO\UserDao::isUserVerified($userId);
                API\Dispatcher::sendResponse(null, $ret, null, $format);
            },
            'isUserVerified',
            null
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/users/leaveOrg/:userId/:orgId/',
            function ($userId, $orgId, $format = ".json") {
                if (!is_numeric($orgId) && strstr($orgId, '.')) {
                    $orgId = explode('.', $orgId);
                    $format = '.'.$orgId[1];
                    $orgId = $orgId[0];
                }
                $data = DAO\OrganisationDao::revokeMembership($orgId, $userId);
                if (is_array($data)) {
                    $data = $data[0];
                }
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'userLeaveOrg',
            '\SolasMatch\API\Lib\Middleware::authUserOrAdminForOrg'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/users/:userId/requestReference(:format)/',
            function ($userId, $format = ".json") {
                DAO\UserDao::requestReference($userId);
                API\Dispatcher::sendResponse(null, null, null, $format);
            },
            "userRequestReference",
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/getClaimedTasksCount/:userId/',
            function ($userId, $format = '.json') {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = DAO\TaskDao::getUserTasksCount($userId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUserClaimedTasksCount',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/subscribedToTask/:userId/:taskId/',
            function ($userId, $taskId, $format = ".json") {
                if (!is_numeric($taskId) && strstr($taskId, '.')) {
                    $taskId = explode('.', $taskId);
                    $format = '.'.$taskId[1];
                    $taskId = $taskId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\UserDao::isSubscribedToTask($userId, $taskId), null, $format);
            },
            'userSubscribedToTask',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/subscribedToProject/:userId/:projectId/',
            function ($userId, $projectId, $format = ".json") {
                if (!is_numeric($projectId) && strstr($projectId, '.')) {
                    $projectId = explode('.', $projectId);
                    $format = '.'.$projectId[1];
                    $projectId = $projectId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\UserDao::isSubscribedToProject($userId, $projectId), null, $format);
            },
            'userSubscribedToProject',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/isBlacklistedForTask/:userId/:taskId/',
            function ($userId, $taskId, $format = ".json") {
                if (!is_numeric($taskId) && strstr($taskId, '.')) {
                    $taskId = explode('.', $taskId);
                    $format = '.'.$taskId[1];
                    $taskId = $taskId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\UserDao::isBlacklistedForTask($userId, $taskId), null, $format);
            },
            'isBlacklistedForTask'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/:userId/orgs(:format)/',
            function ($userId, $format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\UserDao::findOrganisationsUserBelongsTo($userId), null, $format);
            },
            'getUserOrgs'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/:userId/badges(:format)/',
            function ($userId, $format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\UserDao::getUserBadges($userId), null, $format);
            },
            'getUserbadges'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/users/:userId/badges(:format)/',
            function ($userId, $format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\Badge');
                API\Dispatcher::sendResponse(null, DAO\BadgeDao::assignBadge($userId, $data->getId()), null, $format);
            },
            'addUserbadges'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/users/assignBadge/:email/:badgeId/',
            function ($email, $badgeId, $format = ".json") {
                if (!is_numeric($badgeId) && strstr($badgeId, '.')) {
                    $badgeId = explode('.', $badgeId);
                    $format = '.'.$badgeId[1];
                    $badgeId = $badgeId[0];
                }
                $ret = false;
                $user = DAO\UserDao::getUser(null, $email);
                $ret = DAO\BadgeDao::assignBadge($user->getId(), $badgeId);
                API\Dispatcher::sendResponse(null, $ret, null, $format);
            },
            "assignBadge",
            null
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/users/:userId/badges/:badgeId/',
            function ($userId, $badgeId, $format = ".json") {
                if (!is_numeric($badgeId) && strstr($badgeId, '.')) {
                     $badgeId = explode('.', $badgeId);
                     $format = '.'.$badgeId[1];
                     $badgeId = $badgeId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\BadgeDao::assignBadge($userId, $badgeId), null, $format);
            },
            'addUserbadgesByID',
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgBadge'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/users/:userId/badges/:badgeId/',
            function ($userId, $badgeId, $format = ".json") {
                if (!is_numeric($badgeId) && strstr($badgeId, '.')) {
                    $badgeId = explode('.', $badgeId);
                    $format = '.'.$badgeId[1];
                    $badgeId = $badgeId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\BadgeDao::removeUserBadge($userId, $badgeId), null, $format);
            },
            'deleteUserbadgesByID',
            '\SolasMatch\API\Lib\Middleware::authenticateUserOrOrgForOrgBadge'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/:userId/tags(:format)/',
            function ($userId, $format = ".json") {
                $limit = API\Dispatcher::clenseArgs('limit', Common\Enums\HttpMethodEnum::GET, null);
                API\Dispatcher::sendResponse(null, DAO\UserDao::getUserTags($userId, $limit), null, $format);
            },
            'getUsertags'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/:userId/taskStreamNotification(:format)/',
            function ($userId, $format = ".json") {
                $data = DAO\UserDao::getUserTaskStreamNotification($userId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUserTaskStreamNotification',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/users/:userId/taskStreamNotification(:format)/',
            function ($userId, $format = ".json") {
                $ret = DAO\UserDao::removeTaskStreamNotification($userId);
                API\Dispatcher::sendResponse(null, $ret, null, $format);
            },
            'removeUserTaskStreamNotification',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/users/:userId/taskStreamNotification(:format)/',
            function ($userId, $format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\UserTaskStreamNotification');
                $ret = DAO\UserDao::requestTaskStreamNotification($data);
                API\Dispatcher::sendResponse(null, $ret, null, $format);
            },
            'updateTaskStreamNotification',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/:userId/tasks(:format)/',
            function ($userId, $format = ".json") {
                $limit = API\Dispatcher::clenseArgs('limit', Common\Enums\HttpMethodEnum::GET, 10);
                $offset = API\Dispatcher::clenseArgs('offset', Common\Enums\HttpMethodEnum::GET, 0);
                API\Dispatcher::sendResponse(null, DAO\TaskDao::getUserTasks($userId, $limit, $offset), null, $format);
            },
            'getUsertasks',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/users/:userId/tasks(:format)/',
            function ($userId, $format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\Task');
                API\Dispatcher::sendResponse(null, DAO\TaskDao::claimTask($data->getId(), $userId), null, $format);
                Lib\Notify::notifyUserClaimedTask($userId, $data->getId());
                Lib\Notify::notifyOrgClaimedTask($userId, $data->getId());
            },
            'userClaimTask',
            '\SolasMatch\API\Lib\Middleware::authenticateTaskNotClaimed'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/users/:userId/tasks/:taskId/',
            function ($userId, $taskId, $format = ".json") {
                if (!is_numeric($taskId) && strstr($taskId, '.')) {
                    $taskId = explode('.', $taskId);
                    $format = '.'.$taskId[1];
                    $taskId = $taskId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\TaskDao::unClaimTask($taskId, $userId), null, $format);
                Lib\Notify::sendTaskRevokedNotifications($taskId, $userId);
            },
            'userUnClaimTask',
            '\SolasMatch\API\Lib\Middleware::authUserOrOrgForTask'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/:userId/tasks/:taskId/review(:format)/',
            function ($userId, $taskId, $format = '.json') {
                $reviews = DAO\TaskDao::getTaskReviews(null, $taskId, $userId);
                API\Dispatcher::sendResponse(null, $reviews[0], null, $format);
            },
            'getUserTaskReview',
            '\SolasMatch\API\Lib\Middleware::authUserOrOrgForTask'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/:userId/topTasks(:format)/',
            function ($userId, $format = ".json") {
                $limit = API\Dispatcher::clenseArgs('limit', Common\Enums\HttpMethodEnum::GET, 5);
                $offset = API\Dispatcher::clenseArgs('offset', Common\Enums\HttpMethodEnum::GET, 0);
                $filter = API\Dispatcher::clenseArgs('filter', Common\Enums\HttpMethodEnum::GET, '');
                $strict = API\Dispatcher::clenseArgs('strict', Common\Enums\HttpMethodEnum::GET, false);
                $filters = Common\Lib\APIHelper::parseFilterString($filter);
                $filter = "";
                $taskType = '';
                $sourceLanguageCode = '';
                $targetLanguageCode = '';
                if (isset($filters['taskType']) && $filters['taskType'] != '') {
                    $taskType = $filters['taskType'];
                }
                if (isset($filters['sourceLanguage']) && $filters['sourceLanguage'] != '') {
                    $sourceLanguageCode = $filters['sourceLanguage'];
                }
                if (isset($filters['targetLanguage']) && $filters['targetLanguage'] != '') {
                    $targetLanguageCode = $filters['targetLanguage'];
                }
                $dao = new DAO\TaskDao();
                $data = $dao->getUserTopTasks(
                    $userId,
                    $strict,
                    $limit,
                    $offset,
                    $taskType,
                    $sourceLanguageCode,
                    $targetLanguageCode
                );
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUserTopTasks',
            "\SolasMatch\API\Lib\Middleware::isloggedIn"
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/:userId/archivedTasks(:format)/',
            function ($userId, $format = ".json") {
                $limit = API\Dispatcher::clenseArgs('limit', Common\Enums\HttpMethodEnum::GET, 5);
                $data = DAO\TaskDao::getUserArchivedTasks($userId, $limit);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUserArchivedTasks'
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/:userId/archivedTasks/:taskId/archiveMetaData(:format)/',
            function ($userId, $taskId, $format = ".json") {
                if (!is_numeric($taskId) && strstr($taskId, '.')) {
                    $taskId = explode('.', $taskId);
                    $format = '.'.$taskId[1];
                    $taskId = $taskId[0];
                }
                $data = DAO\TaskDao::getArchivedTaskMetaData($taskId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUserArchivedTaskMetaData',
            '\SolasMatch\API\Lib\Middleware::authUserOrOrgForTask'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/users/:userId/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\User');
                $data->setId($userId);
                $data = DAO\UserDao::save($data);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'updateUser',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/users/:userId/tags(:format)/',
            function ($userId, $format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\Tag');
                $data = DAO\UserDao::likeTag($userId, $data->getId());
                if (is_array($data)) {
                    $data = $data[0];
                }
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'addUsertag',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/users/:userId/tags/:tagId/',
            function ($userId, $tagId, $format = ".json") {
                if (!is_numeric($tagId) && strstr($tagId, '.')) {
                    $tagId = explode('.', $tagId);
                    $format = '.'.$tagId[1];
                    $tagId = $tagId[0];
                }
                $data = DAO\UserDao::likeTag($userId, $tagId);
                if (is_array($data)) {
                    $data = $data[0];
                }
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'addUserTagById',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/users/:userId/tags/:tagId/',
            function ($userId, $tagId, $format = ".json") {
                if (!is_numeric($tagId) && strstr($tagId, '.')) {
                    $tagId = explode('.', $tagId);
                    $format = '.'.$tagId[1];
                    $tagId = $tagId[0];
                }
                $data = DAO\UserDao::removeTag($userId, $tagId);
                if (is_array($data)) {
                    $data = $data[0];
                }
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'deleteUserTagById',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/:userId/trackedTasks(:format)/',
            function ($userId, $format = ".json") {
                $data = DAO\UserDao::getTrackedTasks($userId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUserTrackedTasks',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/users/:userId/trackedTasks(:format)/',
            function ($userId, $format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\Task');
                $data = DAO\UserDao::trackTask($userId, $data->getId());
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'addUserTrackedTasks',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/users/:userId/trackedTasks/:taskId/',
            function ($userId, $taskId, $format = ".json") {
                if (!is_numeric($taskId) && strstr($taskId, '.')) {
                    $taskId = explode('.', $taskId);
                    $format = '.'.$taskId[1];
                    $taskId = $taskId[0];
                }
                $data = DAO\UserDao::trackTask($userId, $taskId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'addUserTrackedTasksById',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/users/:userId/trackedTasks/:taskId/',
            function ($userId, $taskId, $format = ".json") {
                if (!is_numeric($taskId) && strstr($taskId, '.')) {
                    $taskId = explode('.', $taskId);
                    $format = '.'.$taskId[1];
                    $taskId = $taskId[0];
                }
                $data = DAO\UserDao::ignoreTask($userId, $taskId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'deleteUserTrackedTasksById',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/email/:email/passwordResetRequest(:format)/',
            function ($email, $format = ".json") {
                $data = DAO\UserDao::hasRequestedPasswordReset($email) ? 1 : 0;
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'hasUserRequestedPasswordReset',
            null
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/email/:email/passwordResetRequest/time(:format)/',
            function ($email, $format = ".json") {
                $resetRequest = DAO\UserDao::getPasswordResetRequests($email);
                API\Dispatcher::sendResponse(null, $resetRequest->getRequestTime(), null, $format);
            },
            "PasswordResetRequestTime",
            null
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/users/email/:email/passwordResetRequest(:format)/',
            function ($email, $format = ".json") {
                $user = DAO\UserDao::getUser(null, $email);
                //$user = $user[0];
                if ($user) {
                    API\Dispatcher::sendResponse(null, DAO\UserDao::createPasswordReset($user), null, $format);
                    Lib\Notify::sendPasswordResetEmail($user->getId());
                } else {
                    API\Dispatcher::sendResponse(null, null, null, $format);
                }
            },
            'createPasswordResetRequest',
            null
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/:userId/projects(:format)/',
            function ($userId, $format = ".json") {
                $data = DAO\UserDao::getTrackedProjects($userId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUserTrackedProjects',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/users/:userId/projects/:projectId/',
            function ($userId, $projectId, $format = ".json") {
                if (!is_numeric($projectId) && strstr($projectId, '.')) {
                    $projectId = explode('.', $projectId);
                    $format = '.'.$projectId[1];
                    $projectId = $projectId[0];
                }
                $data = DAO\UserDao::trackProject($projectId, $userId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'userTrackProject',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/users/:userId/projects/:projectId/',
            function ($userId, $projectId, $format = ".json") {
                if (!is_numeric($projectId) && strstr($projectId, '.')) {
                    $projectId = explode('.', $projectId);
                    $format = '.'.$projectId[1];
                    $projectId = $projectId[0];
                }
                $data = DAO\UserDao::unTrackProject($projectId, $userId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'userUnTrackProject',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/:userId/personalInfo(:format)/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = DAO\UserDao::getPersonalInfo(null, $userId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUserPersonalInfo',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/users/:userId/personalInfo(:format)/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\UserPersonalInformation");
                API\Dispatcher::sendResponse(null, DAO\UserDao::createPersonalInfo($data), null, $format);
            },
            'createUserPersonalInfo',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/users/:userId/personalInfo(:format)/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\UserPersonalInformation');
                $data = DAO\UserDao::updatePersonalInfo($data);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'updateUserPersonalInfo',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/:userId/secondaryLanguages(:format)/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = DAO\UserDao::getSecondaryLanguages($userId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getSecondaryLanguages'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/users/:userId/secondaryLanguages(:format)/',
            function ($userId, $format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Locale");
                API\Dispatcher::sendResponse(null, DAO\UserDao::createSecondaryLanguage($userId, $data), null, $format);
            },
            'createSecondaryLanguage',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/users/removeSecondaryLanguage/:userId/:languageCode/:countryCode/',
            function ($userId, $languageCode, $countryCode, $format = ".json") {
                if (strstr($countryCode, '.')) {
                    $countryCode = explode('.', $countryCode);
                    $format = '.'.$countryCode[1];
                    $countryCode = $countryCode[0];
                }
                $data = DAO\UserDao::deleteSecondaryLanguage($userId, $languageCode, $countryCode);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'deleteSecondaryLanguage',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/:userId/organisations(:format)/',
            function ($userId, $format = ".json") {
                $data = DAO\UserDao::getTrackedOrganisations($userId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUserTrackedOrganisations',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/users/:userId/organisations/:organisationId/',
            function ($userId, $organisationId, $format = ".json") {
                if (!is_numeric($organisationId) && strstr($organisationId, '.')) {
                    $organisationId = explode('.', $organisationId);
                    $format = '.'.$organisationId[1];
                    $organisationId = $organisationId[0];
                }
                $data = DAO\UserDao::trackOrganisation($userId, $organisationId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'userTrackOrganisation',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/users/:userId/organisations/:organisationId/',
            function ($userId, $organisationId, $format = ".json") {
                if (!is_numeric($organisationId) && strstr($organisationId, '.')) {
                    $organisationId = explode('.', $organisationId);
                    $format = '.'.$organisationId[1];
                    $organisationId = $organisationId[0];
                }
                $data = DAO\UserDao::unTrackOrganisation($userId, $organisationId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'userUnTrackOrganisation',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/users/subscribedToOrganisation/:userId/:organisationId/',
            function ($userId, $organisationId, $format = ".json") {
                if (!is_numeric($organisationId) && strstr($organisationId, '.')) {
                    $organisationId = explode('.', $organisationId);
                    $format = '.'.$organisationId[1];
                    $organisationId = $organisationId[0];
                }
                API\Dispatcher::sendResponse(
                    null,
                    DAO\UserDao::isSubscribedToOrganisation($userId, $organisationId), null, $format
                );
            },
            'userSubscribedToOrganisation',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
    }
}
Users::init();
