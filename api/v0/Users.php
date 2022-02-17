<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;
use SolasMatch\API\DAO\AdminDao;
use \SolasMatch\Common\Exceptions as Exceptions;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__.'/../../Common/protobufs/models/OAuthResponse.php';
require_once __DIR__."/../../Common/protobufs/models/PasswordResetRequest.php";
require_once __DIR__."/../../Common/protobufs/models/PasswordReset.php";
require_once __DIR__."/../../Common/lib/Settings.class.php";
require_once __DIR__."/../DataAccessObjects/UserDao.class.php";
require_once __DIR__."/../DataAccessObjects/TaskDao.class.php";
require_once __DIR__."/../lib/Notify.class.php";
require_once __DIR__."/../lib/Middleware.php";


class Users
{
    public static function init()
    {
        global $app;

        $app->put(
            '/v0/users/:userId/trackedTasks/:taskId/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::addUserTrackedTasksById'
        );

        $app->delete(
            '/v0/users/:userId/trackedTasks/:taskId/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::deleteUserTrackedTasksById'
        );

        $app->put(
            '/v0/users/:userId/badges/:badgeId/',
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgBadge',
            '\SolasMatch\API\V0\Users::addUserbadgesByID'
        );

        $app->delete(
            '/v0/users/:userId/badges/:badgeId/',
            '\SolasMatch\API\Lib\Middleware::authenticateUserOrOrgForOrgBadge',
            '\SolasMatch\API\V0\Users::deleteUserbadgesByID'
        );

        $app->get(
            '/v0/users/:userId/tasks/:taskId/review/',
            '\SolasMatch\API\Lib\Middleware::authUserOrOrgForTask',
            '\SolasMatch\API\V0\Users::getUserTaskReview'
        );

        $app->post(
            '/v0/users/:userId/tasks/:taskId/',
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
            '\SolasMatch\API\V0\Users::userClaimTask'
        );

        $app->delete(
            '/v0/users/:userId/tasks/:taskId/',
            '\SolasMatch\API\Lib\Middleware::authUserOrOrgForTask',
            '\SolasMatch\API\V0\Users::userUnClaimTask'
        );

        $app->put(
            '/v0/users/:userId/tags/:tagId/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::addUserTagById'
        );

        $app->delete(
            '/v0/users/:userId/tags/:tagId/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::deleteUserTagById'
        );

        $app->get(
            '/v0/users/:userId/projects/:projectId/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::userTrackProject'
        );

        $app->delete(
            '/v0/users/:userId/projects/:projectId/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::userUnTrackProject'
        );

        $app->put(
            '/v0/users/:userId/projects/:projectId/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::userTrackProject'
        );

        $app->put(
            '/v0/users/:userId/organisations/:organisationId/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::userTrackOrganisation'
        );

        $app->delete(
            '/v0/users/:userId/organisations/:organisationId/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::userUnTrackOrganisation'
        );

        $app->get(
            '/v0/users/:userId/filteredClaimedTasks/:orderBy/:limit/:offset/:taskType/:taskStatus/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::getFilteredUserClaimedTasks'
        );

        $app->get(
            '/v0/users/:userId/filteredClaimedTasksCount/:taskType/:taskStatus/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::getFilteredUserClaimedTasksCount'
        );

        $app->get(
            '/v0/users/:userId/recentTasks/:limit/:offset/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::getUserRecentTasks'
        );

        $app->get(
            '/v0/users/:userId/recentTasksCount/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::getUserRecentTasksCount'
        );

        $app->put(
            '/v0/users/:userId/requestReference/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::userRequestReference'
        );

        $app->get(
            '/v0/users/:userId/realName/',
            '\SolasMatch\API\Lib\Middleware::authenticateUserMembership',
            '\SolasMatch\API\V0\Users::getUserRealName'
        );

        $app->get(
            '/v0/users/:userId/verified/',
            '\SolasMatch\API\V0\Users::isUserVerified'
        );

        $app->get(
            '/v0/users/:userId/orgs/',
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
            '\SolasMatch\API\V0\Users::getUserOrgs'
        );

        $app->post(
            '/v0/users/:userId/badges/',
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
            '\SolasMatch\API\V0\Users::addUserbadges'
        );

        $app->get(
            '/v0/users/:userId/tags/',
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
            '\SolasMatch\API\V0\Users::getUserTags'
        );

        $app->post(
            '/v0/users/:userId/tags/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::addUserTag'
        );

        $app->get(
            '/v0/users/:userId/taskStreamNotification/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::getUserTaskStreamNotification'
        );

        $app->delete(
            '/v0/users/:userId/taskStreamNotification/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::removeUserTaskStreamNotification'
        );

        $app->put(
            '/v0/users/:userId/taskStreamNotification/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::updateTaskStreamNotification'
        );

        $app->get(
            '/v0/users/:userId/tasks/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::getUserTasks'
        );

        $app->get(
            '/v0/users/:userId/topTasksCount/',
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
            '\SolasMatch\API\V0\Users::getUserTopTasksCount'
        );

        $app->get(
            '/v0/users/:userId/topTasks/',
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
            '\SolasMatch\API\V0\Users::getUserTopTasks'
        );

        $app->get(
            '/v0/users/:userId/archivedTasks/:limit/:offset/',
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
            '\SolasMatch\API\V0\Users::getUserArchivedTasks'
        );

        $app->get(
            '/v0/users/:userId/archivedTasksCount/',
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
            '\SolasMatch\API\V0\Users::getUserArchivedTasksCount'
        );

        $app->get(
            '/v0/users/:userId/trackedTasks/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::getUserTrackedTasks'
        );

        $app->post(
            '/v0/users/:userId/trackedTasks/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::addUserTrackedTasks'
        );

        $app->get(
            '/v0/users/:userId/projects/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::getUserTrackedProjects'
        );

        $app->post(
            '/v0/users/:userId/personalInfo/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::createUserPersonalInfo'
        );

        $app->put(
            '/v0/users/:userId/personalInfo/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::updateUserPersonalInfo'
        );

        $app->get(
            '/v0/users/:userId/secondaryLanguages/',
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
            '\SolasMatch\API\V0\Users::getSecondaryLanguages'
        );

        $app->post(
            '/v0/users/:userId/secondaryLanguages/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::createSecondaryLanguage'
        );

        $app->get(
            '/v0/users/:userId/organisations/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::getUserTrackedOrganisations'
        );

        $app->get(
            '/v0/users/:uuid/registered/',
            '\SolasMatch\API\V0\Users::getRegisteredUser'
        );

        $app->post(
            '/v0/users/:uuid/finishRegistration/',
            '\SolasMatch\API\V0\Users::finishRegistration'
        );

        $app->post(
            '/v0/users/:uuid/manuallyFinishRegistration/',
            '\SolasMatch\API\V0\Users::finishRegistrationManually'
        );

        $app->get(
            '/v0/users/email/:email/passwordResetRequest/time/',
            '\SolasMatch\API\V0\Users::getPasswordResetRequestTime'
        );

        $app->get(
            '/v0/users/email/:email/passwordResetRequest/',
            '\SolasMatch\API\V0\Users::hasUserRequestedPasswordReset'
        );

        $app->get(
            '/v0/users/email/:email/getBannedComment/',
            '\SolasMatch\API\Lib\Middleware::authenticateIsUserBanned',
            '\SolasMatch\API\V0\Users::getBannedComment'
        );

        $app->post(
            '/v0/users/email/:email/passwordResetRequest/',
            '\SolasMatch\API\V0\Users::createPasswordResetRequest'
        );

        $app->delete(
            '/v0/users/removeSecondaryLanguage/:userId/:languageCode/:countryCode/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::deleteSecondaryLanguage'
        );

        $app->get(
            '/v0/users/subscribedToOrganisation/:userId/:organisationId/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::userSubscribedToOrganisation'
        );

        $app->delete(
            '/v0/users/leaveOrg/:userId/:orgId/',
            '\SolasMatch\API\Lib\Middleware::authUserOrAdminForOrg',
            '\SolasMatch\API\V0\Users::userLeaveOrg'
        );

        $app->get(
            '/v0/users/:email/auth/code/',
            '\SolasMatch\API\V0\Users::getAuthCode'
        );

        $app->get(
            '/v0/users/subscribedToTask/:userId/:taskId/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::userSubscribedToTask'
        );

        $app->get(
            '/v0/users/subscribedToProject/:userId/:projectId/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::userSubscribedToProject'
        );

        $app->get(
            '/v0/users/isBlacklistedForTask/:userId/:taskId/',
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
            '\SolasMatch\API\V0\Users::isBlacklistedForTask'
        );

        $app->get(
            '/v0/users/isBlacklistedForTaskByAdmin/:userId/:taskId/',
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
            '\SolasMatch\API\V0\Users::isBlacklistedForTaskByAdmin'
        );

        $app->put(
            '/v0/users/assignBadge/:email/:badgeId/',
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgBadge',
            '\SolasMatch\API\V0\Users::assignBadge'
        );

        $app->put(
            '/v0/users/NotifyRegistered/:userId/',
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
            '\SolasMatch\API\V0\Users::NotifyRegistered'
        );

        $app->get(
            '/v0/users/getClaimedTasksCount/:userId/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::getUserClaimedTasksCount'
        );

        $app->post(
            '/v0/users/authCode/login/',
            '\SolasMatch\API\V0\Users::getAccessToken'
        );

        $app->get(
            '/v0/users/getByEmail/:email/email/',
            '\SolasMatch\API\Lib\Middleware::registerValidation',
            '\SolasMatch\API\V0\Users::getUserByEmail'
        );

        $app->get(
            '/v0/users/passwordReset/:key/',
            '\SolasMatch\API\V0\Users::getResetRequest'
        );

        $app->get(
            '/v0/users/getCurrentUser/',
            '\SolasMatch\API\V0\Users::getCurrentUser'
        );

        $app->get(
            '/v0/users/login/',
            '\SolasMatch\API\V0\Users::getLoginTemplate'
        );

        $app->post(
            '/v0/users/login/',
            '\SolasMatch\API\V0\Users::login'
        );

        $app->get(
            '/v0/users/passwordReset/',
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
            '\SolasMatch\API\V0\Users::getResetTemplate'
        );

        $app->post(
            '/v0/users/passwordReset/',
            '\SolasMatch\API\V0\Users::resetPassword'
        );

        $app->get(
            '/v0/users/register/',
            '\SolasMatch\API\V0\Users::getRegisterTemplate'
        );

        $app->post(
            '/v0/users/register/',
            '\SolasMatch\API\V0\Users::register'
        );

        $app->post(
            '/v0/users/changeEmail/',
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
            '\SolasMatch\API\V0\Users::changeEmail'
        );

        $app->put(
            '/v0/users/:userId/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::updateUser'
        );

        $app->delete(
            '/v0/users/:userId/',
            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
            '\SolasMatch\API\V0\Users::deleteUser'
        );

        $app->get(
            '/v0/users/',
            '\SolasMatch\API\V0\Users::getUsers'
        );

        // From cron
        $app->get(
            '/v0/dequeue_claim_task/',
            '\SolasMatch\API\V0\Users::dequeue_claim_task'
        );
    }

    public static function addUserTrackedTasksById($userId, $taskId)
    {
        $data = DAO\UserDao::trackTask($userId, $taskId);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function deleteUserTrackedTasksById($userId, $taskId)
    {
        $data = DAO\UserDao::ignoreTask($userId, $taskId);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function deleteUserbadgesByID($userId, $badgeId)
    {
        API\Dispatcher::sendResponse(null, DAO\BadgeDao::removeUserBadge($userId, $badgeId), null);
    }

    public static function addUserbadgesByID($userId, $badgeId)
    {
        API\Dispatcher::sendResponse(null, DAO\BadgeDao::assignBadge($userId, $badgeId), null);
    }

    public static function NotifyRegistered($userId)
    {
        API\Dispatcher::sendResponse(null, DAO\BadgeDao::NotifyRegistered($userId), null);
    }

    public static function getUserTags($userId)
    {
        $limit = API\Dispatcher::clenseArgs('limit', Common\Enums\HttpMethodEnum::GET, null);
        API\Dispatcher::sendResponse(null, DAO\UserDao::getUserTags($userId, $limit), null);
    }

    public static function addUserTag($userId)
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\Tag');
        $data = DAO\UserDao::likeTag($userId, $data->getId());
        if (is_array($data)) {
            $data = $data[0];
        }
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getUserTaskStreamNotification($userId)
    {
        $data = DAO\UserDao::getUserTaskStreamNotification($userId);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getUserTaskReview($userId, $taskId)
    {
        $reviews = DAO\TaskDao::getTaskReviews(null, $taskId, $userId);
        API\Dispatcher::sendResponse(null, $reviews[0], null);
    }

    public static function userUnClaimTask($userId, $taskId)
    {
        $feedback = API\Dispatcher::getDispatcher()->request()->getBody();
        $feedback = trim($feedback);
        if ($feedback != '') {
            API\Dispatcher::sendResponse(null, DAO\TaskDao::unClaimTask($taskId, $userId, $feedback), null);
        } else {
            API\Dispatcher::sendResponse(null, DAO\TaskDao::unClaimTask($taskId, $userId), null);
        }
        Lib\Notify::sendTaskRevokedNotifications($taskId, $userId);
    }

    public static function addUserTagById($userId, $tagId)
    {
        $data = DAO\UserDao::likeTag($userId, $tagId);
        if (is_array($data)) {
            $data = $data[0];
        }
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function deleteUserTagById($userId, $tagId)
    {
        $data = DAO\UserDao::removeTag($userId, $tagId);
        if (is_array($data)) {
            $data = $data[0];
        }
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function userTrackProject($userId, $projectId)
    {
        $data = DAO\UserDao::trackProject($projectId, $userId);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function userUnTrackProject($userId, $projectId)
    {
        $data = DAO\UserDao::unTrackProject($projectId, $userId);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function userTrackOrganisation($userId, $organisationId)
    {
        $data = DAO\UserDao::trackOrganisation($userId, $organisationId);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function userUnTrackOrganisation($userId, $organisationId)
    {
        $data = DAO\UserDao::unTrackOrganisation($userId, $organisationId);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function userRequestReference($userId)
    {
        DAO\UserDao::requestReference($userId);
        API\Dispatcher::sendResponse(null, null, null);
    }

    public static function getUserRealName($userId)
    {
        API\Dispatcher::sendResponse(null, DAO\UserDao::getUserRealName($userId), null);
    }

    public static function isUserVerified($userId)
    {
        $ret = DAO\UserDao::isUserVerified($userId);
        API\Dispatcher::sendResponse(null, $ret, null);
    }

    public static function getUserOrgs($userId)
    {
        API\Dispatcher::sendResponse(null, DAO\UserDao::findOrganisationsUserBelongsTo($userId), null);
    }

    public static function addUserbadges($userId)
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\Badge');
        API\Dispatcher::sendResponse(null, DAO\BadgeDao::assignBadge($userId, $data->getId()), null);
    }

    public static function removeUserTaskStreamNotification($userId)
    {
        $ret = DAO\UserDao::removeTaskStreamNotification($userId);
        API\Dispatcher::sendResponse(null, $ret, null);
    }

    public static function updateTaskStreamNotification($userId)
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\UserTaskStreamNotification');
        $ret = DAO\UserDao::requestTaskStreamNotification($data);
        API\Dispatcher::sendResponse(null, $ret, null);
    }

    public static function getUserTasks($userId)
    {
        $limit = API\Dispatcher::clenseArgs('limit', Common\Enums\HttpMethodEnum::GET, 10);
        $offset = API\Dispatcher::clenseArgs('offset', Common\Enums\HttpMethodEnum::GET, 0);
        API\Dispatcher::sendResponse(null, DAO\TaskDao::getUserTasks($userId, $limit, $offset), null);
    }

    public static function userClaimTask($userId, $taskId)
    {
error_log("userClaimTask($userId, $taskId)");
        API\Dispatcher::sendResponse(null, 1, null); // Don't claim here, return 1, just do notifications...
        Lib\Notify::notifyUserClaimedTask($userId, $taskId);
        Lib\Notify::notifyOrgClaimedTask($userId, $taskId);
    }

    public static function dequeue_claim_task()
    {
        $queue_claim_tasks = DAO\TaskDao::get_queue_claim_tasks();
        foreach ($queue_claim_tasks as $queue_claim_task) {
            $task_id = $queue_claim_task['task_id'];
            $user_id = $queue_claim_task['user_id'];
            $matecat_tasks = DAO\TaskDao::getMatecatLanguagePairs($queue_claim_task['task_id']);
            if (!empty($matecat_tasks) && !empty($matecat_tasks[0]['matecat_id_job'])) { // Analysis complete
                error_log("dequeue_claim_task() task_id: $task_id Removing");
                DAO\TaskDao::dequeue_claim_task($task_id);
                DAO\TaskDao::claimTask($task_id, $user_id);
                Lib\Notify::notifyUserClaimedTask($user_id, $task_id);
                Lib\Notify::notifyOrgClaimedTask($user_id, $task_id);
            } else {
                $request_for_project = DAO\TaskDao::getWordCountRequestForProject($matecat_tasks[0]['project_id']);
                if (!$request_for_project || $request_for_project['state'] == 3) { // If Project deleted or Analysis has failed
                    DAO\TaskDao::dequeue_claim_task($task_id);
                }
            }
        }
    }

    public static function getUserTopTasks($userId)
    {
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
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getUserTopTasksCount($userId)
    {
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
        $data = $dao->getUserTopTasksCount(
            $userId,
            $strict,
            $taskType,
            $sourceLanguageCode,
            $targetLanguageCode
        );
        API\Dispatcher::sendResponse(null, $data, null);
    }
    
    
    public static function getFilteredUserClaimedTasks(
            $userId,
            $orderBy,
            $limit,
            $offset,
            $taskType,
            $taskStatus
    ) {
        API\Dispatcher::sendResponse(
            null,
            DAO\TaskDao::getFilteredUserClaimedTasks(
                $userId,
                $orderBy,
                $limit,
                $offset,
                $taskType,
                $taskStatus
            ),
            null
        );
    }

    public static function getFilteredUserClaimedTasksCount(
            $userId,
            $taskType,
            $taskStatus
    ) {
        API\Dispatcher::sendResponse(
            null,
            DAO\TaskDao::getFilteredUserClaimedTasksCount(
                $userId,
                $taskType,
                $taskStatus
            ),
            null
        );
    }
    
    public static function getUserRecentTasks(
            $userId,
            $limit,
            $offset
    ) {
        API\Dispatcher::sendResponse(
        null,
        DAO\TaskDao::getUserRecentTasks(
        $userId,
        $limit,
        $offset
        ),
        null
        );
    }

    public static function getUserRecentTasksCount(
            $userId
    ) {
        API\Dispatcher::sendResponse(
        null,
        DAO\TaskDao::getUserRecentTasksCount(
        $userId
        ),
        null
        );
    }

    public static function getUserArchivedTasks($userId, $limit, $offset)
    {
        $data = DAO\TaskDao::getUserArchivedTasks($userId, $limit, $offset);
        API\Dispatcher::sendResponse(null, $data, null);
    }
    
    public static function getUserArchivedTasksCount($userId)
    {
        $data = DAO\TaskDao::getUserArchivedTasksCount($userId);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getUserTrackedTasks($userId)
    {
        $data = DAO\UserDao::getTrackedTasks($userId);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function addUserTrackedTasks($userId)
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\Task');
        $data = DAO\UserDao::trackTask($userId, $data->getId());
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getUserTrackedProjects($userId)
    {
        $data = DAO\UserDao::getTrackedProjects($userId);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function createUserPersonalInfo($userId)
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\UserPersonalInformation");
        API\Dispatcher::sendResponse(null, DAO\UserDao::savePersonalInfo($data), null);
    }

    public static function updateUserPersonalInfo($userId)
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\UserPersonalInformation');
        $data = DAO\UserDao::savePersonalInfo($data);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getSecondaryLanguages($userId)
    {
        $data = DAO\UserDao::getSecondaryLanguages($userId);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function createSecondaryLanguage($userId)
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Locale");
        API\Dispatcher::sendResponse(null, DAO\UserDao::createSecondaryLanguage($userId, $data), null);
    }

    public static function getUserTrackedOrganisations($userId)
    {
        $data = DAO\UserDao::getTrackedOrganisations($userId);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getRegisteredUser($uuid)
    {
        $user = DAO\UserDao::getRegisteredUser($uuid);
        API\Dispatcher::sendResponse(null, $user, null);
    }

    public static function finishRegistration($uuid)
    {
        $user = DAO\UserDao::getRegisteredUser($uuid);
        if ($user != null) {
            error_log("finishRegistration($uuid) " . $user->getId());
            $ret = DAO\UserDao::finishRegistration($user->getId());
            API\Dispatcher::sendResponse(null, $ret, null);
        } else {
            API\Dispatcher::sendResponse(null, "Invalid UUID", Common\Enums\HttpStatusEnum::UNAUTHORIZED);
        }
    }

    public static function finishRegistrationManually($email)
    {
        error_log("finishRegistrationManually($email)");
        $ret = DAO\UserDao::finishRegistrationManually($email);
        API\Dispatcher::sendResponse(null, $ret, null);
    }

    public static function getPasswordResetRequestTime($email)
    {
        $resetRequest = DAO\UserDao::getPasswordResetRequests($email);
        API\Dispatcher::sendResponse(null, $resetRequest->getRequestTime(), null);
    }

    public static function hasUserRequestedPasswordReset($email)
    {
        $data = DAO\UserDao::hasRequestedPasswordReset($email) ? 1 : 0;
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function createPasswordResetRequest($email)
    {
        $user = DAO\UserDao::getUser(null, $email);
        if ($user) {
            API\Dispatcher::sendResponse(null, DAO\UserDao::createPasswordReset($user), null);
            Lib\Notify::sendPasswordResetEmail($user->getId());
        } else {
            API\Dispatcher::sendResponse(null, null, null);
        }
    }

    public static function deleteSecondaryLanguage($userId, $languageCode, $countryCode)
    {
        $data = DAO\UserDao::deleteSecondaryLanguage($userId, $languageCode, $countryCode);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function userSubscribedToOrganisation($userId, $organisationId)
    {
        API\Dispatcher::sendResponse(
            null,
            DAO\UserDao::isSubscribedToOrganisation($userId, $organisationId),
            null
        );
    }

    public static function userLeaveOrg($userId, $orgId)
    {
        $data = DAO\OrganisationDao::revokeMembership($orgId, $userId);
        if (is_array($data)) {
            $data = $data[0];
        }
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getAuthCode($email)
    {
        $user = DAO\UserDao::getUser(null, $email);
        if (!$user) {
            error_log("apiRegister($email) in getAuthCode()");
            DAO\UserDao::apiRegister($email, md5($email), false);
            $user = DAO\UserDao::getUser(null, $email);
            DAO\UserDao::finishRegistration($user->getId());
            //Set new user's personal info to show their preferred language as English.
            $newUser = DAO\UserDao::getUser(null, $user->getEmail());
            $userInfo = new Common\Protobufs\Models\UserPersonalInformation();
            $english = DAO\LanguageDao::getLanguage(null, "en");
            $userInfo->setUserId($newUser->getId());
            $userInfo->setLanguagePreference($english->getId());

            if ($google = DAO\UserDao::get_google_user_details($email)) {
                $userInfo->setFirstName($google['first_name']);
                $userInfo->setLastName($google['last_name']);
                DAO\UserDao::update_terms_accepted($user->getId(), 1);
            }

            $personal_info = DAO\UserDao::savePersonalInfo($userInfo);
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
            error_log("Exception $email");
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
    }

    public static function userSubscribedToTask($userId, $taskId)
    {
        API\Dispatcher::sendResponse(null, DAO\UserDao::isSubscribedToTask($userId, $taskId), null);
    }

    public static function userSubscribedToProject($userId, $projectId)
    {
        API\Dispatcher::sendResponse(null, DAO\UserDao::isSubscribedToProject($userId, $projectId), null);
    }

    public static function isBlacklistedForTask($userId, $taskId)
    {
        API\Dispatcher::sendResponse(null, DAO\UserDao::isBlacklistedForTask($userId, $taskId), null);
    }
    
    public static function isBlacklistedForTaskByAdmin($userId, $taskId)
    {
        API\Dispatcher::sendResponse(null, DAO\UserDao::isBlacklistedForTaskByAdmin($userId, $taskId), null);
    }

    public static function assignBadge($email, $badgeId)
    {
        $ret = false;
        $user = DAO\UserDao::getUser(null, $email);
        $ret = DAO\BadgeDao::assignBadge($user->getId(), $badgeId);
        API\Dispatcher::sendResponse(null, $ret, null);
    }

    public static function getUserClaimedTasksCount($userId)
    {
        $data = DAO\TaskDao::getUserTasksCount($userId);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getAccessToken()
    {
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
            $user->setPassword("");
            $user->setNonce("");

            DAO\UserDao::logLoginAttempt($user->getId(), $user->getEmail(), 1);

            API\Dispatcher::sendResponse(null, $user, null, $oAuthToken);
        } catch (\Exception $e) {
            error_log("Exception getAccessToken");
            API\Dispatcher::sendResponse(null, $e->getMessage(), Common\Enums\HttpStatusEnum::BAD_REQUEST);
        }
    }

    public static function getUserByEmail($email)
    {
        $data = DAO\UserDao::getUser(null, $email);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getResetRequest($key)
    {
        $data = DAO\UserDao::getPasswordResetRequests(null, $key);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getCurrentUser()
    {
        $user = DAO\UserDao::getLoggedInUser();
        API\Dispatcher::sendResponse(null, $user, null);
    }

    public static function getLoginTemplate()
    {
        $data = new Common\Protobufs\Models\Login();
        $data->setEmail("sample@example.com");
        $data->setPassword("sample_password");
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function login()
    {
        $body = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
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
            $user->setPassword("");
            $user->setNonce("");
            API\Dispatcher::sendResponse(null, $user, null, $oAuthResponse);
        } catch (Common\Exceptions\SolasMatchException $e) {
            API\Dispatcher::sendResponse(null, $e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            API\Dispatcher::sendResponse(null, $e->getMessage(), Common\Enums\HttpStatusEnum::UNAUTHORIZED);
        }
    }

    public static function getResetTemplate()
    {
        $data = Common\Lib\ModelFactory::buildModel("PasswordReset", array());
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function resetPassword()
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\PasswordReset');
        $result = DAO\UserDao::passwordReset($data->getPassword(), $data->getKey());
        API\Dispatcher::sendResponse(null, $result, null);
    }

    public static function getRegisterTemplate()
    {
        $data = new Common\Protobufs\Models\Register();
        $data->setPassword("test");
        $data->setEmail("test@test.rog");
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function register()
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Register");
        error_log("apiRegister() in register() " . $data->getEmail());
        $registered = DAO\UserDao::apiRegister($data->getEmail(), $data->getPassword());
        //Set new user's personal info to show their preferred language as English.
        $newUser = DAO\UserDao::getUser(null, $data->getEmail());
        $userInfo = new Common\Protobufs\Models\UserPersonalInformation();
        $english = DAO\LanguageDao::getLanguage(null, "en");
        $userInfo->setUserId($newUser->getId());
        $userInfo->setLanguagePreference($english->getId());
        $userInfo->setFirstName($data->getFirstName());
        $userInfo->setLastName($data->getLastName());
        DAO\UserDao::insert_communications_consent($newUser->getId(), $data->getCommunicationsConsent());
        $personal_info = DAO\UserDao::savePersonalInfo($userInfo);
        
        API\Dispatcher::sendResponse(null, $registered, null);
    }

    public static function changeEmail()
    {
        $user = DAO\UserDao::getLoggedInUser();
        if (!is_null($user) && DAO\AdminDao::isAdmin($user->getId(), null)) {
            $data = API\Dispatcher::getDispatcher()->request()->getBody();
            $client = new Common\Lib\APIHelper('.json');
            $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Register");

            // password field has been repurposed to hold User for which email is to be changed
            $registered = DAO\UserDao::changeEmail($data->getPassword(), $data->getEmail());
        }
        else {
            $registered = null;
        }
        API\Dispatcher::sendResponse(null, $registered, null);
    }

    public static function getUser($userId)
    {
        $data = DAO\UserDao::getUser($userId);
        if (!is_null($data)) {
            $data->setPassword("");
            $data->setNonce("");
        }
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function updateUser($userId)
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\User');
        $data->setId($userId);
        $data = DAO\UserDao::save($data);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function deleteUser($userId)
    {
        error_log("deleteUser($userId)");
        DAO\UserDao::deleteUser($userId);
        API\Dispatcher::sendResponse(null, null, null);
    }

    public static function getUsers()
    {
        API\Dispatcher::sendResponse(null, "display all users", null);
    }
    
    public static function getBannedComment($email)
    {
        $client = new Common\Lib\APIHelper('.json');
        
        $user = DAO\UserDao::getUser(null, $email);
        $userId = $user->getId();
        $bannedUser = AdminDao::getBannedUser($userId);
        $bannedUser = $bannedUser[0];
        $comment = $bannedUser->getComment();
        
        API\Dispatcher::sendResponse(null, $comment, null);
    }
}

Users::init();
