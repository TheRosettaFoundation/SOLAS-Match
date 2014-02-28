<?php

namespace SolasMatch\API\V0;

use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;

/**
 * Description of Users
 *
 * @author sean
 */

require_once __DIR__.'/../../Common/models/OAuthResponce.php';
require_once __DIR__."/../../Common/models/PasswordResetRequest.php";
require_once __DIR__."/../../Common/models/PasswordReset.php";
require_once __DIR__."/../DataAccessObjects/UserDao.class.php";
require_once __DIR__."/../DataAccessObjects/TaskDao.class.php";
require_once __DIR__."/../lib/Notify.class.php";
require_once __DIR__."/../lib/Middleware.php";


class Users
{
    public static function init()
    {
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/users(:format)/',
            function ($format = ".json") {
                API\Dispatcher::sendResponse(null, "display all users", null, $format);
            },
            'getUsers',
            null
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/users/:userId/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = DAO\UserDao::getUser($userId);
                if (is_array($data)) {
                    $data = $data[0];
                }
                if (!is_null($data)) {
                    $data->setPassword(null);
                    $data->setNonce(null);
                }
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUser'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::DELETE,
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
            \HttpMethodEnum::GET,
            '/v0/users/getCurrentUser(:format)/',
            function ($format = ".json") {
                $user = DAO\UserDao::getLoggedInUser();
                API\Dispatcher::sendResponse(null, $user, null, $format);
            },
            'getCurrentUser',
            null
        );

        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/users/login(:format)/',
            function ($format = ".json") {
                $data = new \Login();
                $data->setEmail("sample@example.com");
                $data->setPassword("sample_password");
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getLoginTemplate',
            null
        );

        API\Dispatcher::registerNamed(
            \HttpMethodEnum::POST,
            '/v0/users/login(:format)/',
            function ($format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new \APIHelper($format);
                $data = $client->deserialize($data, "Login");
                $params = array();
                $params['client_id'] = API\Dispatcher::clenseArgs('client_id', \HttpMethodEnum::GET, null);
                $params['client_secret'] = API\Dispatcher::clenseArgs('client_secret', \HttpMethodEnum::GET, null);
                $params['username'] = $data->getEmail();
                $params['password'] = $data->getPassword();
                try {
                    $server = API\Dispatcher::getOauthServer();
                    $response = $server->getGrantType('password')->completeFlow($params);
                    $oAuthResponse = new \OAuthResponce();
                    $oAuthResponse->setToken($response['access_token']);
                    $oAuthResponse->setTokenType($response['token_type']);
                    $oAuthResponse->setExpires($response['expires']);
                    $oAuthResponse->setExpiresIn($response['expires_in']);

                    $user = DAO\UserDao::getLoggedInUser($response['access_token']);
                    $user->setPassword(null);
                    $user->setNonce(null);
                    API\Dispatcher::sendResponse(null, $user, null, $format, $oAuthResponse);
                } catch (\Exception $e) {
                    API\Dispatcher::sendResponse(null, $e->getMessage(), \HttpStatusEnum::UNAUTHORIZED, $format);
                }
            },
            'login',
            null
        );

        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/users/login/openidLogin/:email/',
            function ($email, $format = ".json") {
                if (isset($_SERVER['HTTP_X_CUSTOM_AUTHORIZATION'])) {
                    $headerHash = $_SERVER['HTTP_X_CUSTOM_AUTHORIZATION'];
                    if (!is_numeric($email) && strstr($email, '.')) {
                        $temp = array();
                        $temp = explode('.', $email);
                        $lastIndex = sizeof($temp)-1;
                        if ($lastIndex > 1) {
                            $format='.'.$temp[$lastIndex];
                            $email = $temp[0];
                            for ($i = 1; $i < $lastIndex; $i++) {
                                $email = "{$email}.{$temp[$i]}";
                            }
                        }
                    }
                    $openidHash = md5($email.substr(\Settings::get("session.site_key"), 0, 20));
                    if ($headerHash != $openidHash) {
                        API\Dispatcher::getDispatcher()->halt(
                            \HttpStatusEnum::FORBIDDEN,
                            "The Autherization header does not match the current ".
                            "user or the user does not have permission to acess the current resource"
                        );
                    }
                }
                $data = DAO\UserDao::getUser(null, $email);
                if (is_array($data)) {
                    $data = $data[0];
                }
                $oAuthResponce = null;
                if (is_null($data)) {
                    $data = DAO\UserDao::apiRegister($email, md5($email));
                    if (is_array($data) && isset($data[0])) {
                        $data = $data[0];
                    }
                    DAO\UserDao::finishRegistration($data->getId());
                }
                $server = API\Dispatcher::getOauthServer();
                $responce = $server->getGrantType('password')->completeFlow(
                    array(
                        "client_id" => $data->getId(),
                        "client_secret" => $data->getPassword()
                    )
                );
                $oAuthResponce = new \OAuthResponce();
                $oAuthResponce->setToken($responce['access_token']);
                $oAuthResponce->setTokenType($responce['token_type']);
                $oAuthResponce->setExpires($responce['expires']);
                $oAuthResponce->setExpiresIn($responce['expires_in']);
                API\Dispatcher::sendResponse(null, $data, null, $format, $oAuthResponce);
            },
            'openidLogin',
            null
        );

        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/users/passwordReset(:format)/',
            function ($format = ".json") {
                $data = \ModelFactory::buildModel("PasswordReset", array());
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getResetTemplate'
        );

        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
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
            \HttpMethodEnum::POST,
            '/v0/users/passwordReset(:format)/',
            function ($format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new \APIHelper($format);
                $data = $client->deserialize($data, 'PasswordReset');
                $result = DAO\UserDao::passwordReset($data->getPassword(), $data->getKey());
                API\Dispatcher::sendResponse(null, $result, null, $format);
            },
            'resetPassword',
            null
        );

        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/users/register(:format)/',
            function ($format = ".json") {
                $data = new \Register();
                $data->setPassword("test");
                $data->setEmail("test@test.rog");
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getRegisterTemplate',
            null
        );

        API\Dispatcher::registerNamed(
            \HttpMethodEnum::POST,
            '/v0/users/register(:format)/',
            function ($format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new \APIHelper($format);
                $data = $client->deserialize($data, "Register");
                $data = DAO\UserDao::apiRegister($data->getEmail(), $data->getPassword());
                if (is_array($data) && isset($data[0])) {
                    $data = $data[0];
                }
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'register',
            null
        );

        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/users/:uuid/registered(:format)/',
            function ($uuid, $format = '.json') {
                $user = DAO\UserDao::getRegisteredUser($uuid);
                API\Dispatcher::sendResponse(null, $user, null, $format);
            },
            'getRegisteredUser',
            null
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::POST,
            '/v0/users/:uuid/finishRegistration(:format)/',
            function ($uuid, $format = '.json') {
                $user = DAO\UserDao::getRegisteredUser($uuid);
                if ($user != null) {
                    $ret = DAO\UserDao::finishRegistration($user->getId());
                    $server = API\Dispatcher::getOauthServer();
                    $response = $server->getGrantType('password')->completeFlow(
                        array(
                            "client_id"=>$user->getId(),
                            "client_secret"=>$user->getPassword()
                        )
                    );
                    $oAuthResponse = new \OAuthResponce();
                    $oAuthResponse->setToken($response['access_token']);
                    $oAuthResponse->setTokenType($response['token_type']);
                    $oAuthResponse->setExpires($response['expires']);
                    $oAuthResponse->setExpiresIn($response['expires_in']);
                    API\Dispatcher::sendResponse(null, $ret, null, $format, $oAuthResponse);
                } else {
                    API\Dispatcher::sendResponse(null, "Invalid UUID", \HttpStatusEnum::UNAUTHORIZED, $format);
                }
            },
            'finishRegistration',
            null
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/users/:userId/verified(:format)/',
            function ($userId, $format = '.json') {
                $ret = DAO\UserDao::isUserVerified($userId);
                API\Dispatcher::sendResponse(null, $ret, null, $format);
            },
            'isUserVerified',
            null
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::DELETE,
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
            \HttpMethodEnum::PUT,
            '/v0/users/:userId/requestReference(:format)/',
            function ($userId, $format = ".json") {
                DAO\UserDao::requestReference($userId);
                API\Dispatcher::sendResponse(null, null, null, $format);
            },
            "userRequestReference",
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/users/getByEmail/:email/',
            function ($email, $format = ".json") {
                if (!is_numeric($email) && strstr($email, '.')) {
                    $temp = array();
                    $temp = explode('.', $email);
                    $lastIndex = sizeof($temp)-1;
                    if ($lastIndex > 1) {
                        $format='.'.$temp[$lastIndex];
                        $email = $temp[0];
                        for ($i = 1; $i < $lastIndex; $i++) {
                            $email = "{$email}.{$temp[$i]}";
                        }
                    }
                }
                $data = DAO\UserDao::getUser(null, $email);
                if (is_array($data)) {
                    $data = $data[0];
                }
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUserByEmail',
            "\SolasMatch\API\Lib\Middleware::registerValidation"
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
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
            \HttpMethodEnum::GET,
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
            \HttpMethodEnum::GET,
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
            \HttpMethodEnum::GET,
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
            \HttpMethodEnum::GET,
            '/v0/users/:userId/orgs(:format)/',
            function ($userId, $format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\UserDao::findOrganisationsUserBelongsTo($userId), null, $format);
            },
            'getUserOrgs'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/users/:userId/badges(:format)/',
            function ($userId, $format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\UserDao::getUserBadges($userId), null, $format);
            },
            'getUserbadges'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::POST,
            '/v0/users/:userId/badges(:format)/',
            function ($userId, $format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new \APIHelper($format);
                $data = $client->deserialize($data, 'Badge');
                API\Dispatcher::sendResponse(null, DAO\BadgeDao::assignBadge($userId, $data->getId()), null, $format);
            },
            'addUserbadges'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::PUT,
            '/v0/users/assignBadge/:email/:badgeId/',
            function ($email, $badgeId, $format = ".json") {
                if (!is_numeric($badgeId) && strstr($badgeId, '.')) {
                    $badgeId = explode('.', $badgeId);
                    $format = '.'.$badgeId[1];
                    $badgeId = $badgeId[0];
                }
                $ret = false;
                $user = DAO\UserDao::getUser(null, $email);
                if (count($user) > 0) {
                    $user = $user[0];
                    $ret = DAO\BadgeDao::assignBadge($user->getId(), $badgeId);
                }
                API\Dispatcher::sendResponse(null, $ret, null, $format);
            },
            "assignBadge",
            null
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::PUT,
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
            \HttpMethodEnum::DELETE,
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
            \HttpMethodEnum::GET,
            '/v0/users/:userId/tags(:format)/',
            function ($userId, $format = ".json") {
                $limit = API\Dispatcher::clenseArgs('limit', \HttpMethodEnum::GET, null);
                API\Dispatcher::sendResponse(null, DAO\UserDao::getUserTags($userId, $limit), null, $format);
            },
            'getUsertags'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/users/:userId/taskStreamNotification(:format)/',
            function ($userId, $format = ".json") {
                $data = DAO\UserDao::getUserTaskStreamNotification($userId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUserTaskStreamNotification',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::DELETE,
            '/v0/users/:userId/taskStreamNotification(:format)/',
            function ($userId, $format = ".json") {
                $ret = DAO\UserDao::removeTaskStreamNotification($userId);
                API\Dispatcher::sendResponse(null, $ret, null, $format);
            },
            'removeUserTaskStreamNotification',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::PUT,
            '/v0/users/:userId/taskStreamNotification(:format)/',
            function ($userId, $format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new \APIHelper($format);
                $data = $client->deserialize($data, 'UserTaskStreamNotification');
                $ret = DAO\UserDao::requestTaskStreamNotification($data);
                API\Dispatcher::sendResponse(null, $ret, null, $format);
            },
            'updateTaskStreamNotification',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/users/:userId/tasks(:format)/',
            function ($userId, $format = ".json") {
                $limit = API\Dispatcher::clenseArgs('limit', \HttpMethodEnum::GET, 10);
                $offset = API\Dispatcher::clenseArgs('offset', \HttpMethodEnum::GET, 0);
                API\Dispatcher::sendResponse(null, DAO\TaskDao::getUserTasks($userId, $limit, $offset), null, $format);
            },
            'getUsertasks',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::POST,
            '/v0/users/:userId/tasks(:format)/',
            function ($userId, $format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new \APIHelper($format);
                $data = $client->deserialize($data, 'Task');
                API\Dispatcher::sendResponse(null, DAO\TaskDao::claimTask($data->getId(), $userId), null, $format);
                Lib\Notify::notifyUserClaimedTask($userId, $data->getId());
                Lib\Notify::notifyOrgClaimedTask($userId, $data->getId());
            },
            'userClaimTask',
            '\SolasMatch\API\Lib\Middleware::authenticateTaskNotClaimed'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::DELETE,
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
            \HttpMethodEnum::GET,
            '/v0/users/:userId/tasks/:taskId/review(:format)/',
            function ($userId, $taskId, $format = '.json') {
                $reviews = DAO\TaskDao::getTaskReviews(null, $taskId, $userId);
                API\Dispatcher::sendResponse(null, $reviews[0], null, $format);
            },
            'getUserTaskReview',
            '\SolasMatch\API\Lib\Middleware::authUserOrOrgForTask'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/users/:userId/topTasks(:format)/',
            function ($userId, $format = ".json") {
                $limit = API\Dispatcher::clenseArgs('limit', \HttpMethodEnum::GET, 5);
                $offset = API\Dispatcher::clenseArgs('offset', \HttpMethodEnum::GET, 0);
                $filter = API\Dispatcher::clenseArgs('filter', \HttpMethodEnum::GET, '');
                $strict = API\Dispatcher::clenseArgs('strict', \HttpMethodEnum::GET, false);
                $filters = \APIHelper::parseFilterString($filter);
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
            \HttpMethodEnum::GET,
            '/v0/users/:userId/archivedTasks(:format)/',
            function ($userId, $format = ".json") {
                $limit = API\Dispatcher::clenseArgs('limit', \HttpMethodEnum::GET, 5);
                $data = DAO\TaskDao::getUserArchivedTasks($userId, $limit);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUserArchivedTasks'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
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
            \HttpMethodEnum::PUT,
            '/v0/users/:userId/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new \APIHelper($format);
                $data = $client->deserialize($data, 'User');
                $data->setId($userId);
                $data = DAO\UserDao::save($data);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'updateUser',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::POST,
            '/v0/users/:userId/tags(:format)/',
            function ($userId, $format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new \APIHelper($format);
                $data = $client->deserialize($data, 'Tag');
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
            \HttpMethodEnum::PUT,
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
            \HttpMethodEnum::DELETE,
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
            \HttpMethodEnum::GET,
            '/v0/users/:userId/trackedTasks(:format)/',
            function ($userId, $format = ".json") {
                $data = DAO\UserDao::getTrackedTasks($userId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUserTrackedTasks',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::POST,
            '/v0/users/:userId/trackedTasks(:format)/',
            function ($userId, $format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new \APIHelper($format);
                $data = $client->deserialize($data, 'Task');
                $data = DAO\UserDao::trackTask($userId, $data->getId());
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'addUserTrackedTasks',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::PUT,
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
            \HttpMethodEnum::DELETE,
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
            \HttpMethodEnum::GET,
            '/v0/users/email/:email/passwordResetRequest(:format)/',
            function ($email, $format = ".json") {
                $data = DAO\UserDao::hasRequestedPasswordReset($email) ? 1 : 0;
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'hasUserRequestedPasswordReset',
            null
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/users/email/:email/passwordResetRequest/time(:format)/',
            function ($email, $format = ".json") {
                $resetRequest = DAO\UserDao::getPasswordResetRequests($email);
                API\Dispatcher::sendResponse(null, $resetRequest->getRequestTime(), null, $format);
            },
            "PasswordResetRequestTime",
            null
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::POST,
            '/v0/users/email/:email/passwordResetRequest(:format)/',
            function ($email, $format = ".json") {
                $user = DAO\UserDao::getUser(null, $email);
                $user = $user[0];
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
            \HttpMethodEnum::GET,
            '/v0/users/:userId/projects(:format)/',
            function ($userId, $format = ".json") {
                $data = DAO\UserDao::getTrackedProjects($userId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUserTrackedProjects',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::PUT,
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
            \HttpMethodEnum::DELETE,
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
            \HttpMethodEnum::GET,
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
            \HttpMethodEnum::POST,
            '/v0/users/:userId/personalInfo(:format)/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new \APIHelper($format);
                $data = $client->deserialize($data, "UserPersonalInformation");
                API\Dispatcher::sendResponse(null, DAO\UserDao::createPersonalInfo($data), null, $format);
            },
            'createUserPersonalInfo',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::PUT,
            '/v0/users/:userId/personalInfo(:format)/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new \APIHelper($format);
                $data = $client->deserialize($data, 'UserPersonalInformation');
                $data = DAO\UserDao::updatePersonalInfo($data);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'updateUserPersonalInfo',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
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
            \HttpMethodEnum::POST,
            '/v0/users/:userId/secondaryLanguages(:format)/',
            function ($userId, $format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new \APIHelper($format);
                $data = $client->deserialize($data, "Locale");
                API\Dispatcher::sendResponse(null, DAO\UserDao::createSecondaryLanguage($userId, $data), null, $format);
            },
            'createSecondaryLanguage',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::DELETE,
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
    }
}
Users::init();
