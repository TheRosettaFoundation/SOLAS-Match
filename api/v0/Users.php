<?php

/**
 * Description of Users
 *
 * @author sean
 */

require_once __DIR__."/../DataAccessObjects/UserDao.class.php";
require_once __DIR__."/../DataAccessObjects/TaskDao.class.php";
require_once __DIR__."/../lib/Notify.class.php";
require_once __DIR__."/../lib/NotificationTypes.class.php";
require_once __DIR__."/../lib/Middleware.php";


class Users
{
    public static function init()
    {
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users(:format)/',
            function ($format = ".json") {
                Dispatcher::sendResponce(null, "display all users", null, $format);
            },
            'getUsers',
            null
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/:userId/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = UserDao::getUser($userId);
                if (is_array($data)) {
                    $data = $data[0];
                }
                if (!is_null($data)) {
                    $data->setPassword(null);
                    $data->setNonce(null);
                }
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'getUser'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::DELETE,
            '/v0/users/:userId/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                UserDao::deleteUser($userId);
                Dispatcher::sendResponce(null, null, null, $format);
            },
            'deleteUser',
            'Middleware::authUserOwnsResource'
        );
        
        //returns the currently logged in user based on oauth token
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/getCurrentUser(:format)/',
            function ($format = ".json") {
                $user = UserDao::getLoggedInUser();
                Dispatcher::sendResponce(null, $user, null, $format);
            },
            'getCurrentUser',
            null
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/:uuid/registered(:format)/',
            function ($uuid, $format = '.json') {
                $user = UserDao::getRegisteredUser($uuid);
                Dispatcher::sendResponce(null, $user, null, $format);
            },
            'getRegisteredUser',
            null
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::POST,
            '/v0/users/:uuid/finishRegistration(:format)/',
            function ($uuid, $format = '.json') {
                $user = UserDao::getRegisteredUser($uuid);
                if ($user != null) {
                    $ret = UserDao::finishRegistration($user->getId());
                    $server = Dispatcher::getOauthServer();
                    $response = $server->getGrantType('password')->completeFlow(
                        array(
                            "client_id"=>$user->getId(),
                            "client_secret"=>$user->getPassword()
                        )
                    );
                    $oAuthResponse = new OAuthResponce();
                    $oAuthResponse->setToken($response['access_token']);
                    $oAuthResponse->setTokenType($response['token_type']);
                    $oAuthResponse->setExpires($response['expires']);
                    $oAuthResponse->setExpiresIn($response['expires_in']);
                    Dispatcher::sendResponce(null, $ret, null, $format, $oAuthResponse);
                } else {
                    Dispatcher::sendResponce(null, "Invalid UUID", HttpStatusEnum::UNAUTHORIZED, $format);
                }
            },
            'finishRegistration',
            null
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/:userId/verified(:format)/',
            function ($userId, $format = '.json') {
                $ret = UserDao::isUserVerified($userId);
                Dispatcher::sendResponce(null, $ret, null, $format);
            },
            'isUserVerified',
            null
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::DELETE,
            '/v0/users/leaveOrg/:userId/:orgId/',
            function ($userId, $orgId, $format = ".json") {
                if (!is_numeric($orgId) && strstr($orgId, '.')) {
                    $orgId = explode('.', $orgId);
                    $format = '.'.$orgId[1];
                    $orgId = $orgId[0];
                }
                $data = OrganisationDao::revokeMembership($orgId, $userId);
                if (is_array($data)) {
                    $data = $data[0];
                }
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'userLeaveOrg',
            'Middleware::authUserOrAdminForOrg'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::PUT,
            '/v0/users/:userId/requestReference(:format)/',
            function ($userId, $format = ".json") {
                UserDao::requestReference($userId);
                Dispatcher::sendResponce(null, null, null, $format);
            },
            "userRequestReference",
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
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
                $data = UserDao::getUser(null, $email);
                if (is_array($data)) {
                    $data = $data[0];
                }
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'getUserByEmail',
            "Middleware::registerValidation"
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/getClaimedTasksCount/:userId/',
            function ($userId, $format = '.json') {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = TaskDao::getUserTasksCount($userId);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'getUserClaimedTasksCount',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/subscribedToTask/:userId/:taskId/',
            function ($userId, $taskId, $format = ".json") {
                if (!is_numeric($taskId) && strstr($taskId, '.')) {
                    $taskId = explode('.', $taskId);
                    $format = '.'.$taskId[1];
                    $taskId = $taskId[0];
                }
                Dispatcher::sendResponce(null, UserDao::isSubscribedToTask($userId, $taskId), null, $format);
            },
            'userSubscribedToTask',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/subscribedToProject/:userId/:projectId/',
            function ($userId, $projectId, $format = ".json") {
                if (!is_numeric($projectId) && strstr($projectId, '.')) {
                    $projectId = explode('.', $projectId);
                    $format = '.'.$projectId[1];
                    $projectId = $projectId[0];
                }
                Dispatcher::sendResponce(null, UserDao::isSubscribedToProject($userId, $projectId), null, $format);
            },
            'userSubscribedToProject',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/isBlacklistedForTask/:userId/:taskId/',
            function ($userId, $taskId, $format = ".json") {
                if (!is_numeric($taskId) && strstr($taskId, '.')) {
                    $taskId = explode('.', $taskId);
                    $format = '.'.$taskId[1];
                    $taskId = $taskId[0];
                }
                Dispatcher::sendResponce(null, UserDao::isBlacklistedForTask($userId, $taskId), null, $format);
            },
            'isBlacklistedForTask'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/:userId/orgs(:format)/',
            function ($userId, $format = ".json") {
                Dispatcher::sendResponce(null, UserDao::findOrganisationsUserBelongsTo($userId), null, $format);
            },
            'getUserOrgs'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/:userId/badges(:format)/',
            function ($userId, $format = ".json") {
                Dispatcher::sendResponce(null, UserDao::getUserBadges($userId), null, $format);
            },
            'getUserbadges'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::POST,
            '/v0/users/:userId/badges(:format)/',
            function ($userId, $format = ".json") {
                $data = Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data = $client->deserialize($data, 'Badge');
                Dispatcher::sendResponce(null, BadgeDao::assignBadge($userId, $data->getId()), null, $format);
            },
            'addUserbadges'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::PUT,
            '/v0/users/assignBadge/:email/:badgeId/',
            function ($email, $badgeId, $format = ".json") {
                if (!is_numeric($badgeId) && strstr($badgeId, '.')) {
                    $badgeId = explode('.', $badgeId);
                    $format = '.'.$badgeId[1];
                    $badgeId = $badgeId[0];
                }
                $ret = false;
                $user = UserDao::getUser(null, $email);
                if (count($user) > 0) {
                    $user = $user[0];
                    $ret = BadgeDao::assignBadge($user->getId(), $badgeId);
                }
                Dispatcher::sendResponce(null, $ret, null, $format);
            },
            "assignBadge",
            null
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::PUT,
            '/v0/users/:userId/badges/:badgeId/',
            function ($userId, $badgeId, $format = ".json") {
                if (!is_numeric($badgeId) && strstr($badgeId, '.')) {
                     $badgeId = explode('.', $badgeId);
                     $format = '.'.$badgeId[1];
                     $badgeId = $badgeId[0];
                }
                Dispatcher::sendResponce(null, BadgeDao::assignBadge($userId, $badgeId), null, $format);
            },
            'addUserbadgesByID',
            'Middleware::authenticateUserForOrgBadge'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::DELETE,
            '/v0/users/:userId/badges/:badgeId/',
            function ($userId, $badgeId, $format = ".json") {
                if (!is_numeric($badgeId) && strstr($badgeId, '.')) {
                    $badgeId = explode('.', $badgeId);
                    $format = '.'.$badgeId[1];
                    $badgeId = $badgeId[0];
                }
                Dispatcher::sendResponce(null, BadgeDao::removeUserBadge($userId, $badgeId), null, $format);
            },
            'deleteUserbadgesByID',
            'Middleware::authenticateUserOrOrgForOrgBadge'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/:userId/tags(:format)/',
            function ($userId, $format = ".json") {
                $limit = Dispatcher::clenseArgs('limit', HttpMethodEnum::GET, null);
                Dispatcher::sendResponce(null, UserDao::getUserTags($userId, $limit), null, $format);
            },
            'getUsertags'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/:userId/taskStreamNotification(:format)/',
            function ($userId, $format = ".json") {
                $data = UserDao::getUserTaskStreamNotification($userId);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'getUserTaskStreamNotification',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::DELETE,
            '/v0/users/:userId/taskStreamNotification(:format)/',
            function ($userId, $format = ".json") {
                $ret = UserDao::removeTaskStreamNotification($userId);
                Dispatcher::sendResponce(null, $ret, null, $format);
            },
            'removeUserTaskStreamNotification',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::PUT,
            '/v0/users/:userId/taskStreamNotification(:format)/',
            function ($userId, $format = ".json") {
                $data = Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data = $client->deserialize($data, 'UserTaskStreamNotification');
                $ret = UserDao::requestTaskStreamNotification($data);
                Dispatcher::sendResponce(null, $ret, null, $format);
            },
            'updateTaskStreamNotification',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/:userId/tasks(:format)/',
            function ($userId, $format = ".json") {
                $limit = Dispatcher::clenseArgs('limit', HttpMethodEnum::GET, 10);
                $offset = Dispatcher::clenseArgs('offset', HttpMethodEnum::GET, 0);
                Dispatcher::sendResponce(null, TaskDao::getUserTasks($userId, $limit, $offset), null, $format);
            },
            'getUsertasks',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::POST,
            '/v0/users/:userId/tasks(:format)/',
            function ($userId, $format = ".json") {
                $data = Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data = $client->deserialize($data, 'Task');
                Dispatcher::sendResponce(null, TaskDao::claimTask($data->getId(), $userId), null, $format);
                Notify::notifyUserClaimedTask($userId, $data->getId());
                Notify::notifyOrgClaimedTask($userId, $data->getId());
            },
            'userClaimTask',
            'Middleware::authenticateTaskNotClaimed'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::DELETE,
            '/v0/users/:userId/tasks/:taskId/',
            function ($userId, $taskId, $format = ".json") {
                if (!is_numeric($taskId) && strstr($taskId, '.')) {
                    $taskId = explode('.', $taskId);
                    $format = '.'.$taskId[1];
                    $taskId = $taskId[0];
                }
                Dispatcher::sendResponce(null, TaskDao::unClaimTask($taskId, $userId), null, $format);
                Notify::sendTaskRevokedNotifications($taskId, $userId);
            },
            'userUnClaimTask',
            'Middleware::authUserOrOrgForTask'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/:userId/tasks/:taskId/review(:format)/',
            function ($userId, $taskId, $format = '.json') {
                $reviews = TaskDao::getTaskReviews(null, $taskId, $userId);
                Dispatcher::sendResponce(null, $reviews[0], null, $format);
            },
            'getUserTaskReview',
            'Middleware::authUserOrOrgForTask'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/:userId/topTasks(:format)/',
            function ($userId, $format = ".json") {
                $limit = Dispatcher::clenseArgs('limit', HttpMethodEnum::GET, 5);
                $offset = Dispatcher::clenseArgs('offset', HttpMethodEnum::GET, 0);
                $filter = Dispatcher::clenseArgs('filter', HttpMethodEnum::GET, '');
                $strict = Dispatcher::clenseArgs('strict', HttpMethodEnum::GET, false);
                $filters = APIHelper::parseFilterString($filter);
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
                $dao = new TaskDao();
                $data = $dao->getUserTopTasks(
                    $userId,
                    $strict,
                    $limit,
                    $offset,
                    $taskType,
                    $sourceLanguageCode,
                    $targetLanguageCode
                );
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'getUserTopTasks',
            "Middleware::isloggedIn"
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/:userId/archivedTasks(:format)/',
            function ($userId, $format = ".json") {
                $limit = Dispatcher::clenseArgs('limit', HttpMethodEnum::GET, 5);
                $data = TaskDao::getUserArchivedTasks($userId, $limit);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'getUserArchivedTasks'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/:userId/archivedTasks/:taskId/archiveMetaData(:format)/',
            function ($userId, $taskId, $format = ".json") {
                if (!is_numeric($taskId) && strstr($taskId, '.')) {
                    $taskId = explode('.', $taskId);
                    $format = '.'.$taskId[1];
                    $taskId = $taskId[0];
                }
                $data = TaskDao::getArchivedTaskMetaData($taskId);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'getUserArchivedTaskMetaData',
            'Middleware::authUserOrOrgForTask'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::PUT,
            '/v0/users/:userId/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data = $client->deserialize($data, 'User');
                $data->setId($userId);
                $data = UserDao::save($data);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'updateUser',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::POST,
            '/v0/users/:userId/tags(:format)/',
            function ($userId, $format = ".json") {
                $data = Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data = $client->deserialize($data, 'Tag');
                $data = UserDao::likeTag($userId, $data->getId());
                if (is_array($data)) {
                    $data = $data[0];
                }
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'addUsertag',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::PUT,
            '/v0/users/:userId/tags/:tagId/',
            function ($userId, $tagId, $format = ".json") {
                if (!is_numeric($tagId) && strstr($tagId, '.')) {
                    $tagId = explode('.', $tagId);
                    $format = '.'.$tagId[1];
                    $tagId = $tagId[0];
                }
                $data = UserDao::likeTag($userId, $tagId);
                if (is_array($data)) {
                    $data = $data[0];
                }
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'addUserTagById',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::DELETE,
            '/v0/users/:userId/tags/:tagId/',
            function ($userId, $tagId, $format = ".json") {
                if (!is_numeric($tagId) && strstr($tagId, '.')) {
                    $tagId = explode('.', $tagId);
                    $format = '.'.$tagId[1];
                    $tagId = $tagId[0];
                }
                $data = UserDao::removeTag($userId, $tagId);
                if (is_array($data)) {
                    $data = $data[0];
                }
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'deleteUserTagById',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/:userId/trackedTasks(:format)/',
            function ($userId, $format = ".json") {
                $data=UserDao::getTrackedTasks($userId);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'getUserTrackedTasks',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::POST,
            '/v0/users/:userId/trackedTasks(:format)/',
            function ($userId, $format = ".json") {
                $data = Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data = $client->deserialize($data, 'Task');
                $data = UserDao::trackTask($userId, $data->getId());
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'addUserTrackedTasks',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::PUT,
            '/v0/users/:userId/trackedTasks/:taskId/',
            function ($userId, $taskId, $format = ".json") {
                if (!is_numeric($taskId) && strstr($taskId, '.')) {
                    $taskId = explode('.', $taskId);
                    $format = '.'.$taskId[1];
                    $taskId = $taskId[0];
                }
                $data = UserDao::trackTask($userId, $taskId);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'addUserTrackedTasksById',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::DELETE,
            '/v0/users/:userId/trackedTasks/:taskId/',
            function ($userId, $taskId, $format = ".json") {
                if (!is_numeric($taskId) && strstr($taskId, '.')) {
                    $taskId = explode('.', $taskId);
                    $format = '.'.$taskId[1];
                    $taskId = $taskId[0];
                }
                $data=UserDao::ignoreTask($userId, $taskId);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'deleteUserTrackedTasksById',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/email/:email/passwordResetRequest(:format)/',
            function ($email, $format = ".json") {
                $data = UserDao::hasRequestedPasswordReset($email) ? 1 : 0;
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'hasUserRequestedPasswordReset',
            null
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/email/:email/passwordResetRequest/time(:format)/',
            function ($email, $format = ".json") {
                $resetRequest = UserDao::getPasswordResetRequests($email);
                Dispatcher::sendResponce(null, $resetRequest->getRequestTime(), null, $format);
            },
            "PasswordResetRequestTime",
            null
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::POST,
            '/v0/users/email/:email/passwordResetRequest(:format)/',
            function ($email, $format = ".json") {
                $user = UserDao::getUser(null, $email);
                $user = $user[0];
                if ($user) {
                    Dispatcher::sendResponce(null, UserDao::createPasswordReset($user), null, $format);
                    Notify::sendPasswordResetEmail($user->getId());
                } else {
                    Dispatcher::sendResponce(null, null, null, $format);
                }
            },
            'createPasswordResetRequest',
            null
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/:userId/projects(:format)/',
            function ($userId, $format = ".json") {
                $data = UserDao::getTrackedProjects($userId);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'getUserTrackedProjects',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::PUT,
            '/v0/users/:userId/projects/:projectId/',
            function ($userId, $projectId, $format = ".json") {
                if (!is_numeric($projectId) && strstr($projectId, '.')) {
                    $projectId = explode('.', $projectId);
                    $format = '.'.$projectId[1];
                    $projectId = $projectId[0];
                }
                $data = UserDao::trackProject($projectId, $userId);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'userTrackProject',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::DELETE,
            '/v0/users/:userId/projects/:projectId/',
            function ($userId, $projectId, $format = ".json") {
                if (!is_numeric($projectId) && strstr($projectId, '.')) {
                    $projectId = explode('.', $projectId);
                    $format = '.'.$projectId[1];
                    $projectId = $projectId[0];
                }
                $data = UserDao::unTrackProject($projectId, $userId);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'userUnTrackProject',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/:userId/personalInfo(:format)/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = UserDao::getPersonalInfo(null, $userId);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'getUserPersonalInfo',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::POST,
            '/v0/users/:userId/personalInfo(:format)/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data = $client->deserialize($data, "UserPersonalInformation");
                Dispatcher::sendResponce(null, UserDao::createPersonalInfo($data), null, $format);
            },
            'createUserPersonalInfo',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::PUT,
            '/v0/users/:userId/personalInfo(:format)/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data = $client->deserialize($data, 'UserPersonalInformation');
                $data = UserDao::updatePersonalInfo($data);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'updateUserPersonalInfo',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/:userId/secondaryLanguages(:format)/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = UserDao::getSecondaryLanguages($userId);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'getSecondaryLanguages'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::POST,
            '/v0/users/:userId/secondaryLanguages(:format)/',
            function ($userId, $format = ".json") {
                $data = Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data = $client->deserialize($data, "Locale");
                Dispatcher::sendResponce(null, UserDao::createSecondaryLanguage($userId, $data), null, $format);
            },
            'createSecondaryLanguage',
            'Middleware::authUserOwnsResource'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::DELETE,
            '/v0/users/removeSecondaryLanguage/:userId/:languageCode/:countryCode/',
            function ($userId, $languageCode, $countryCode, $format = ".json") {
                if (strstr($countryCode, '.')) {
                    $countryCode = explode('.', $countryCode);
                    $format = '.'.$countryCode[1];
                    $countryCode = $countryCode[0];
                }
                $data = UserDao::deleteSecondaryLanguage($userId, $languageCode, $countryCode);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'deleteSecondaryLanguage',
            'Middleware::authUserOwnsResource'
        );

        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/:userId/organisations(:format)/',
            function ($userId, $format = ".json") {
                $data = UserDao::getTrackedOrganisations($userId);
                Dispatcher::sendResponce(null, $data, null, $format);
                },
                'getUserTrackedOrganisations',
                'Middleware::authUserOwnsResource'
        );

        Dispatcher::registerNamed(
            HttpMethodEnum::PUT,
            '/v0/users/:userId/organisations/:organisationId/',
            function ($userId, $organisationId, $format = ".json") {
                if (!is_numeric($organisationId) && strstr($organisationId, '.')) {
                    $organisationId = explode('.', $organisationId);
                    $format = '.'.$organisationId[1];
                    $organisationId = $organisationId[0];
                }
                $data = UserDao::trackOrganisation($userId, $organisationId);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'userTrackOrganisation',
            'Middleware::authUserOwnsResource'
        );

        Dispatcher::registerNamed(
            HttpMethodEnum::DELETE,
            '/v0/users/:userId/organisations/:organisationId/',
            function ($userId, $organisationId, $format = ".json") {
                if (!is_numeric($organisationId) && strstr($organisationId, '.')) {
                    $organisationId = explode('.', $organisationId);
                    $format = '.'.$organisationId[1];
                    $organisationId = $organisationId[0];
                }
                $data = UserDao::unTrackOrganisation($userId, $organisationId);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'userUnTrackOrganisation',
            'Middleware::authUserOwnsResource'
        );

        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/users/subscribedToOrganisation/:userId/:organisationId/',
            function ($userId, $organisationId, $format = ".json") {
                if (!is_numeric($organisationId) && strstr($organisationId, '.')) {
                    $organisationId = explode('.', $organisationId);
                    $format = '.'.$organisationId[1];
                    $organisationId = $organisationId[0];
                }
                Dispatcher::sendResponce(null,
                    UserDao::isSubscribedToOrganisation($userId, $organisationId), null, $format);
            },
            'userSubscribedToOrganisation',
            'Middleware::authUserOwnsResource'
        );
    }
}
Users::init();
