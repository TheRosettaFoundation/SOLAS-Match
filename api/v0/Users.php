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
            '\SolasMatch\API\V0\Users:addUserTrackedTasksById')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->delete(
            '/v0/users/:userId/trackedTasks/:taskId/',
            '\SolasMatch\API\V0\Users:deleteUserTrackedTasksById')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->put(
            '/v0/users/:userId/badges/:badgeId/',
            '\SolasMatch\API\V0\Users:addUserbadgesByID')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateUserForOrgBadge');

        $app->delete(
            '/v0/users/:userId/badges/:badgeId/',
            '\SolasMatch\API\V0\Users:deleteUserbadgesByID')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateUserOrOrgForOrgBadge');

        $app->get(
            '/v0/users/:userId/tasks/:taskId/review/',
            '\SolasMatch\API\V0\Users:getUserTaskReview')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOrOrgForTask');

        $app->post(
            '/v0/users/:userId/tasks/:taskId/',
            '\SolasMatch\API\V0\Users:userClaimTask')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->delete(
            '/v0/users/:userId/tasks/:taskId/',
            '\SolasMatch\API\V0\Users:userUnClaimTask')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOrOrgForTask');

        $app->put(
            '/v0/users/:userId/tags/:tagId/',
            '\SolasMatch\API\V0\Users:addUserTagById')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->delete(
            '/v0/users/:userId/tags/:tagId/',
            '\SolasMatch\API\V0\Users:deleteUserTagById')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->get(
            '/v0/users/:userId/projects/:projectId/',
            '\SolasMatch\API\V0\Users:userTrackProject')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->delete(
            '/v0/users/:userId/projects/:projectId/',
            '\SolasMatch\API\V0\Users:userUnTrackProject')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->put(
            '/v0/users/:userId/projects/:projectId/',
            '\SolasMatch\API\V0\Users:userTrackProject')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->put(
            '/v0/users/:userId/organisations/:organisationId/',
            '\SolasMatch\API\V0\Users:userTrackOrganisation')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->delete(
            '/v0/users/:userId/organisations/:organisationId/',
            '\SolasMatch\API\V0\Users:userUnTrackOrganisation')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->get(
            '/v0/users/:userId/filteredClaimedTasks/:orderBy/:limit/:offset/:taskType/:taskStatus/',
            '\SolasMatch\API\V0\Users:getFilteredUserClaimedTasks')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->get(
            '/v0/users/:userId/filteredClaimedTasksCount/:taskType/:taskStatus/',
            '\SolasMatch\API\V0\Users:getFilteredUserClaimedTasksCount')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->get(
            '/v0/users/:userId/recentTasks/:limit/:offset/',
            '\SolasMatch\API\V0\Users:getUserRecentTasks')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->get(
            '/v0/users/:userId/recentTasksCount/',
            '\SolasMatch\API\V0\Users:getUserRecentTasksCount')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->put(
            '/v0/users/:userId/requestReference/',
            '\SolasMatch\API\V0\Users:userRequestReference')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->get(
            '/v0/users/:userId/realName/',
            '\SolasMatch\API\V0\Users:getUserRealName')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateUserMembership');

        $app->get(
            '/v0/users/:userId/verified/',
            '\SolasMatch\API\V0\Users:isUserVerified');

        $app->get(
            '/v0/users/:userId/orgs/',
            '\SolasMatch\API\V0\Users:getUserOrgs')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->post(
            '/v0/users/:userId/badges/',
            '\SolasMatch\API\V0\Users:addUserbadges')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/v0/users/:userId/tags/',
            '\SolasMatch\API\V0\Users:getUserTags')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->post(
            '/v0/users/:userId/tags/',
            '\SolasMatch\API\V0\Users:addUserTag')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->get(
            '/v0/users/:userId/taskStreamNotification/',
            '\SolasMatch\API\V0\Users:getUserTaskStreamNotification')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->delete(
            '/v0/users/:userId/taskStreamNotification/',
            '\SolasMatch\API\V0\Users:removeUserTaskStreamNotification')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->put(
            '/v0/users/:userId/taskStreamNotification/',
            '\SolasMatch\API\V0\Users:updateTaskStreamNotification')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->get(
            '/v0/users/:userId/tasks/',
            '\SolasMatch\API\V0\Users:getUserTasks')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->get(
            '/v0/users/:userId/topTasksCount/',
            '\SolasMatch\API\V0\Users:getUserTopTasksCount')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/v0/users/:userId/topTasks/',
            '\SolasMatch\API\V0\Users:getUserTopTasks')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/v0/users/:userId/archivedTasks/:limit/:offset/',
            '\SolasMatch\API\V0\Users:getUserArchivedTasks')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/v0/users/:userId/archivedTasksCount/',
            '\SolasMatch\API\V0\Users:getUserArchivedTasksCount')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/v0/users/:userId/trackedTasks/',
            '\SolasMatch\API\V0\Users:getUserTrackedTasks')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->post(
            '/v0/users/:userId/trackedTasks/',
            '\SolasMatch\API\V0\Users:addUserTrackedTasks')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->get(
            '/v0/users/:userId/projects/',
            '\SolasMatch\API\V0\Users:getUserTrackedProjects')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->post(
            '/v0/users/:userId/personalInfo/',
            '\SolasMatch\API\V0\Users:createUserPersonalInfo')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->put(
            '/v0/users/:userId/personalInfo/',
            '\SolasMatch\API\V0\Users:updateUserPersonalInfo')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->get(
            '/v0/users/:userId/secondaryLanguages/',
            '\SolasMatch\API\V0\Users:getSecondaryLanguages')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->post(
            '/v0/users/:userId/secondaryLanguages/',
            '\SolasMatch\API\V0\Users:createSecondaryLanguage')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->get(
            '/v0/users/:userId/organisations/',
            '\SolasMatch\API\V0\Users:getUserTrackedOrganisations')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->get(
            '/v0/users/:uuid/registered/',
            '\SolasMatch\API\V0\Users:getRegisteredUser');

        $app->post(
            '/v0/users/:uuid/finishRegistration/',
            '\SolasMatch\API\V0\Users:finishRegistration');

        $app->post(
            '/v0/users/:uuid/manuallyFinishRegistration/',
            '\SolasMatch\API\V0\Users:finishRegistrationManually');

        $app->get(
            '/v0/users/email/:email/passwordResetRequest/time/',
            '\SolasMatch\API\V0\Users:getPasswordResetRequestTime');

        $app->get(
            '/v0/users/email/:email/passwordResetRequest/',
            '\SolasMatch\API\V0\Users:hasUserRequestedPasswordReset');

        $app->get(
            '/v0/users/email/:email/getBannedComment/',
            '\SolasMatch\API\V0\Users:getBannedComment')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateIsUserBanned');

        $app->post(
            '/v0/users/email/:email/passwordResetRequest/',
            '\SolasMatch\API\V0\Users:createPasswordResetRequest');

        $app->delete(
            '/v0/users/removeSecondaryLanguage/:userId/:languageCode/:countryCode/',
            '\SolasMatch\API\V0\Users:deleteSecondaryLanguage')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->get(
            '/v0/users/subscribedToOrganisation/:userId/:organisationId/',
            '\SolasMatch\API\V0\Users:userSubscribedToOrganisation')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->delete(
            '/v0/users/leaveOrg/:userId/:orgId/',
            '\SolasMatch\API\V0\Users:userLeaveOrg')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOrAdminForOrg');

        $app->get(
            '/v0/users/:email/auth/code/',
            '\SolasMatch\API\V0\Users:getAuthCode');

        $app->get(
            '/v0/users/subscribedToTask/:userId/:taskId/',
            '\SolasMatch\API\V0\Users:userSubscribedToTask')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->get(
            '/v0/users/subscribedToProject/:userId/:projectId/',
            '\SolasMatch\API\V0\Users:userSubscribedToProject')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->get(
            '/v0/users/isBlacklistedForTask/:userId/:taskId/',
            '\SolasMatch\API\V0\Users:isBlacklistedForTask')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/v0/users/isBlacklistedForTaskByAdmin/:userId/:taskId/',
            '\SolasMatch\API\V0\Users:isBlacklistedForTaskByAdmin')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->put(
            '/v0/users/assignBadge/:email/:badgeId/',
            '\SolasMatch\API\V0\Users:assignBadge')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateUserForOrgBadge');

        $app->put(
            '/v0/users/NotifyRegistered/:userId/',
            '\SolasMatch\API\V0\Users:NotifyRegistered')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/v0/users/getClaimedTasksCount/:userId/',
            '\SolasMatch\API\V0\Users:getUserClaimedTasksCount')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->post(
            '/v0/users/authCode/login/',
            '\SolasMatch\API\V0\Users:getAccessToken');

        $app->get(
            '/v0/users/getByEmail/:email/email/',
            '\SolasMatch\API\V0\Users:getUserByEmail')
            ->add('\SolasMatch\API\Lib\Middleware:registerValidation');

        $app->get(
            '/v0/users/passwordReset/:key/',
            '\SolasMatch\API\V0\Users:getResetRequest');

        $app->get(
            '/v0/users/getCurrentUser/',
            '\SolasMatch\API\V0\Users:getCurrentUser');

        $app->get(
            '/v0/users/login/',
            '\SolasMatch\API\V0\Users:getLoginTemplate');

        $app->post(
            '/v0/users/login/',
            '\SolasMatch\API\V0\Users:login');

        $app->get(
            '/v0/users/passwordReset/',
            '\SolasMatch\API\V0\Users:getResetTemplate')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->post(
            '/v0/users/passwordReset/',
            '\SolasMatch\API\V0\Users:resetPassword');

        $app->get(
            '/v0/users/register/',
            '\SolasMatch\API\V0\Users:getRegisterTemplate');

        $app->post(
            '/v0/users/register/',
            '\SolasMatch\API\V0\Users:register');

        $app->post(
            '/v0/users/changeEmail/',
            '\SolasMatch\API\V0\Users:changeEmail')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateSiteAdmin');

        $app->put(
            '/v0/users/:userId/',
            '\SolasMatch\API\V0\Users:updateUser')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->delete(
            '/v0/users/:userId/',
            '\SolasMatch\API\V0\Users:deleteUser')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOwnsResource');

        $app->get(
            '/v0/users/',
            '\SolasMatch\API\V0\Users:getUsers');

        // From cron
        $app->get(
            '/v0/dequeue_claim_task/',
            '\SolasMatch\API\V0\Users:dequeue_claim_task');
    }

    public static function addUserTrackedTasksById(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $taskId = $args['taskId'];
        $data = DAO\UserDao::trackTask($userId, $taskId);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function deleteUserTrackedTasksById(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $taskId = $args['taskId'];
        $data = DAO\UserDao::ignoreTask($userId, $taskId);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function deleteUserbadgesByID(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $badgeId = $args['badgeId'];
        return API\Dispatcher::sendResponse($response, DAO\BadgeDao::removeUserBadge($userId, $badgeId), null);
    }

    public static function addUserbadgesByID(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $badgeId = $args['badgeId'];
        return API\Dispatcher::sendResponse($response, DAO\BadgeDao::assignBadge($userId, $badgeId), null);
    }

    public static function NotifyRegistered(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        return API\Dispatcher::sendResponse($response, DAO\BadgeDao::NotifyRegistered($userId), null);
    }

    public static function getUserTags(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $limit = API\Dispatcher::clenseArgs($request, 'limit', null);
        return API\Dispatcher::sendResponse($response, DAO\UserDao::getUserTags($userId, $limit), null);
    }

    public static function addUserTag(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\Tag');
        $data = DAO\UserDao::likeTag($userId, $data->getId());
        if (is_array($data)) {
            $data = $data[0];
        }
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getUserTaskStreamNotification(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $data = DAO\UserDao::getUserTaskStreamNotification($userId);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getUserTaskReview(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $taskId = $args['taskId'];
        $reviews = DAO\TaskDao::getTaskReviews(null, $taskId, $userId);
        return API\Dispatcher::sendResponse($response, $reviews[0], null);
    }

    public static function userUnClaimTask(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $taskId = $args['taskId'];
        $feedback = (string)$request->getBody();
        $feedback = trim($feedback);
        Lib\Notify::sendTaskRevokedNotifications($taskId, $userId);
        if ($feedback != '') {
            return API\Dispatcher::sendResponse($response, DAO\TaskDao::unClaimTask($taskId, $userId, $feedback), null);
        } else {
            return API\Dispatcher::sendResponse($response, DAO\TaskDao::unClaimTask($taskId, $userId), null);
        }
    }

    public static function addUserTagById(Request $request, Response $response, $args)
    {
        $userId= $args['userId'];
        $tagId = $args['tagId'];
        $data = DAO\UserDao::likeTag($userId, $tagId);
        if (is_array($data)) {
            $data = $data[0];
        }
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function deleteUserTagById(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $tagId = $args['tagId'];
        $data = DAO\UserDao::removeTag($userId, $tagId);
        if (is_array($data)) {
            $data = $data[0];
        }
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function userTrackProject(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $projectId = $args['projectId'];
        $data = DAO\UserDao::trackProject($projectId, $userId);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function userUnTrackProject(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $projectId = $args['projectId'];
        $data = DAO\UserDao::unTrackProject($projectId, $userId);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function userTrackOrganisation(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $organisationId = $args['organisationId'];
        $data = DAO\UserDao::trackOrganisation($userId, $organisationId);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function userUnTrackOrganisation(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $organisationId = $args['organisationId'];
        $data = DAO\UserDao::unTrackOrganisation($userId, $organisationId);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function userRequestReference(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        DAO\UserDao::requestReference($userId);
        return API\Dispatcher::sendResponse($response, null, null);
    }

    public static function getUserRealName(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        return API\Dispatcher::sendResponse($response, DAO\UserDao::getUserRealName($userId), null);
    }

    public static function isUserVerified(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $ret = DAO\UserDao::isUserVerified($userId);
        return API\Dispatcher::sendResponse($response, $ret, null);
    }

    public static function getUserOrgs(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        return API\Dispatcher::sendResponse($response, DAO\UserDao::findOrganisationsUserBelongsTo($userId), null);
    }

    public static function addUserbadges(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\Badge');
        return API\Dispatcher::sendResponse($response, DAO\BadgeDao::assignBadge($userId, $data->getId()), null);
    }

    public static function removeUserTaskStreamNotification(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $ret = DAO\UserDao::removeTaskStreamNotification($userId);
        return API\Dispatcher::sendResponse($response, $ret, null);
    }

    public static function updateTaskStreamNotification(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\UserTaskStreamNotification');
        $ret = DAO\UserDao::requestTaskStreamNotification($data);
        return API\Dispatcher::sendResponse($response, $ret, null);
    }

    public static function getUserTasks(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];

        $limit = API\Dispatcher::clenseArgs($request, 'limit', 10);
        $offset = API\Dispatcher::clenseArgs($request, 'offset', 0);
        return API\Dispatcher::sendResponse($response, DAO\TaskDao::getUserTasks($userId, $limit, $offset), null);
    }

    public static function userClaimTask(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $taskId = $args['taskId'];

error_log("userClaimTask($userId, $taskId)");
        Lib\Notify::notifyUserClaimedTask($userId, $taskId);
        Lib\Notify::notifyOrgClaimedTask($userId, $taskId);
        return API\Dispatcher::sendResponse($response, 1, null); // Don't claim here, return 1, just do notifications...
    }

    public static function dequeue_claim_task(Request $request, Response $response)
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

    public static function getUserTopTasks(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $limit = API\Dispatcher::clenseArgs($request, 'limit', 5);
        $offset = API\Dispatcher::clenseArgs($request, 'offset', 0);
        $filter = API\Dispatcher::clenseArgs($request, 'filter', '');
        $strict = API\Dispatcher::clenseArgs($request, 'strict', false);
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
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getUserTopTasksCount(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $filter = API\Dispatcher::clenseArgs($request, 'filter', '');
        $strict = API\Dispatcher::clenseArgs($request, 'strict', false);
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
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getFilteredUserClaimedTasks(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $orderBy = $args['orderBy'];
        $limit = $args['limit'];
        $offset = $args['offset'];
        $taskType = $args['taskType'];
        $taskStatus = $args['taskStatus'];
        return API\Dispatcher::sendResponse($response,
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

    public static function getFilteredUserClaimedTasksCount(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $taskType = $args['taskType'];
        $taskStatus = $args['taskStatus'];
        return API\Dispatcher::sendResponse($response,
            DAO\TaskDao::getFilteredUserClaimedTasksCount(
                $userId,
                $taskType,
                $taskStatus
            ),
            null
        );
    }
    
    public static function getUserRecentTasks(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $limit = $args['limit'];
        $offset = $args['offset'];
        return API\Dispatcher::sendResponse($response,
        DAO\TaskDao::getUserRecentTasks(
        $userId,
        $limit,
        $offset
        ),
        null
        );
    }

    public static function getUserRecentTasksCount(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        return API\Dispatcher::sendResponse($response,
        DAO\TaskDao::getUserRecentTasksCount(
        $userId
        ),
        null
        );
    }

    public static function getUserArchivedTasks(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $limit = $args['limit'];
        $offset = $args['offset'];
        $data = DAO\TaskDao::getUserArchivedTasks($userId, $limit, $offset);
        return API\Dispatcher::sendResponse($response, $data, null);
    }
    
    public static function getUserArchivedTasksCount(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $data = DAO\TaskDao::getUserArchivedTasksCount($userId);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getUserTrackedTasks(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $data = DAO\UserDao::getTrackedTasks($userId);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function addUserTrackedTasks(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\Task');
        $data = DAO\UserDao::trackTask($userId, $data->getId());
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getUserTrackedProjects(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $data = DAO\UserDao::getTrackedProjects($userId);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function createUserPersonalInfo(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\UserPersonalInformation");
        return API\Dispatcher::sendResponse($response, DAO\UserDao::savePersonalInfo($data), null);
    }

    public static function updateUserPersonalInfo(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\UserPersonalInformation');
        $data = DAO\UserDao::savePersonalInfo($data);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getSecondaryLanguages(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $data = DAO\UserDao::getSecondaryLanguages($userId);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function createSecondaryLanguage(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Locale");
        return API\Dispatcher::sendResponse($response, DAO\UserDao::createSecondaryLanguage($userId, $data), null);
    }

    public static function getUserTrackedOrganisations(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $data = DAO\UserDao::getTrackedOrganisations($userId);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getRegisteredUser(Request $request, Response $response, $args)
    {
        $uuid = $args['uuid'];
        $user = DAO\UserDao::getRegisteredUser($uuid);
        return API\Dispatcher::sendResponse($response, $user, null);
    }

    public static function finishRegistration(Request $request, Response $response, $args)
    {
        $uuid = $args['uuid'];
        $user = DAO\UserDao::getRegisteredUser($uuid);
        if ($user != null) {
            error_log("finishRegistration($uuid) " . $user->getId());
            $ret = DAO\UserDao::finishRegistration($user->getId());
            return API\Dispatcher::sendResponse($response, $ret, null);
        } else {
            return API\Dispatcher::sendResponse($response, "Invalid UUID", Common\Enums\HttpStatusEnum::UNAUTHORIZED);
        }
    }

    public static function finishRegistrationManually(Request $request, Response $response, $args)
    {
        $email = $args['email'];
        error_log("finishRegistrationManually($email)");
        $ret = DAO\UserDao::finishRegistrationManually($email);
        return API\Dispatcher::sendResponse($response, $ret, null);
    }

    public static function getPasswordResetRequestTime(Request $request, Response $response, $args)
    {
        $email = $args['email'];
        $resetRequest = DAO\UserDao::getPasswordResetRequests($email);
        return API\Dispatcher::sendResponse($response, $resetRequest->getRequestTime(), null);
    }

    public static function hasUserRequestedPasswordReset(Request $request, Response $response, $args)
    {
        $email = $args['email'];
        $data = DAO\UserDao::hasRequestedPasswordReset($email) ? 1 : 0;
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function createPasswordResetRequest(Request $request, Response $response, $args)
    {
        $email = $args['email'];
        $user = DAO\UserDao::getUser(null, $email);
        if ($user) {
            Lib\Notify::sendPasswordResetEmail($user->getId());
            return API\Dispatcher::sendResponse($response, DAO\UserDao::createPasswordReset($user), null);
        } else {
            return API\Dispatcher::sendResponse($response, null, null);
        }
    }

    public static function deleteSecondaryLanguage(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $languageCode = $args['languageCode'];
        $countryCode = $args['countryCode'];
        $data = DAO\UserDao::deleteSecondaryLanguage($userId, $languageCode, $countryCode);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function userSubscribedToOrganisation(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $organisationId = $args['organisationId'];
        return API\Dispatcher::sendResponse($response,
            DAO\UserDao::isSubscribedToOrganisation($userId, $organisationId),
            null
        );
    }

    public static function userLeaveOrg(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $orgId = $args['orgId'];
        $data = DAO\OrganisationDao::revokeMembership($orgId, $userId);
        if (is_array($data)) {
            $data = $data[0];
        }
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getAuthCode(Request $request, Response $response, $args)
    {
        $email = $args['email'];
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
                return $response->withStatus(302)->withHeader('Location', $request->getUri() . "?error=auth_failed&error_message={$e->getMessage()}");
            } else {
                return $response->withStatus(302)->withHeader('Location', $params['redirect_uri'] . "?error=auth_failed&error_message={$e->getMessage()}");
            }
        }
        return $response->withStatus(302)->withHeader('Location', $params['redirect_uri'] . "?code=$authCode");
    }

    public static function userSubscribedToTask(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $taskId = $args['taskId'];
        return API\Dispatcher::sendResponse($response, DAO\UserDao::isSubscribedToTask($userId, $taskId), null);
    }

    public static function userSubscribedToProject(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $projectId = $args['projectId'];
        return API\Dispatcher::sendResponse($response, DAO\UserDao::isSubscribedToProject($userId, $projectId), null);
    }

    public static function isBlacklistedForTask(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $taskId = $args['taskId'];
        return API\Dispatcher::sendResponse($response, DAO\UserDao::isBlacklistedForTask($userId, $taskId), null);
    }
    
    public static function isBlacklistedForTaskByAdmin(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $taskId = $args['taskId'];
        return API\Dispatcher::sendResponse($response, DAO\UserDao::isBlacklistedForTaskByAdmin($userId, $taskId), null);
    }

    public static function assignBadge(Request $request, Response $response, $args)
    {
        $email = $args['email'];
        $badgeId = $args['badgeId'];
        $ret = false;
        $user = DAO\UserDao::getUser(null, $email);
        $ret = DAO\BadgeDao::assignBadge($user->getId(), $badgeId);
        return API\Dispatcher::sendResponse($response, $ret, null);
    }

    public static function getUserClaimedTasksCount(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $data = DAO\TaskDao::getUserTasksCount($userId);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getAccessToken(Request $request, Response $response)
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

            return API\Dispatcher::sendResponse($response, $user, null, $oAuthToken);
        } catch (\Exception $e) {
            error_log("Exception getAccessToken");
            return API\Dispatcher::sendResponse($response, $e->getMessage(), Common\Enums\HttpStatusEnum::BAD_REQUEST);
        }
    }

    public static function getUserByEmail(Request $request, Response $response, $args)
    {
        $email = $args['email'];
        $data = DAO\UserDao::getUser(null, $email);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getResetRequest(Request $request, Response $response, $args)
    {
        $key = $args['key'];
        $data = DAO\UserDao::getPasswordResetRequests(null, $key);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getCurrentUser(Request $request, Response $response)
    {
        $user = DAO\UserDao::getLoggedInUser();
        return API\Dispatcher::sendResponse($response, $user, null);
    }

    public static function getLoginTemplate(Request $request, Response $response)
    {
        $data = new Common\Protobufs\Models\Login();
        $data->setEmail("sample@example.com");
        $data->setPassword("sample_password");
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function login(Request $request, Response $response)
    {
        $body = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $loginData = $client->deserialize($body, "\SolasMatch\Common\Protobufs\Models\Login");
        $params = array();
        $params['client_id'] = API\Dispatcher::clenseArgs($request, 'client_id', null);
        $params['client_secret'] = API\Dispatcher::clenseArgs($request, 'client_secret', null);
        $params['username'] = $loginData->getEmail();
        $params['password'] = $loginData->getPassword();
        try {
            $server = API\Dispatcher::getOauthServer();
            $response_oauth = $server->getGrantType('password')->completeFlow($params);
            $oAuthResponse = new Common\Protobufs\Models\OAuthResponse();
            $oAuthResponse->setToken($response_oauth['access_token']);
            $oAuthResponse->setTokenType($response_oauth['token_type']);
            $oAuthResponse->setExpires($response_oauth['expires']);
            $oAuthResponse->setExpiresIn($response_oauth['expires_in']);

            $user = DAO\UserDao::getLoggedInUser($response_oauth['access_token']);
            $user->setPassword("");
            $user->setNonce("");
            return API\Dispatcher::sendResponse($response, $user, null, $oAuthResponse);
        } catch (Common\Exceptions\SolasMatchException $e) {
            return API\Dispatcher::sendResponse($response, $e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            return API\Dispatcher::sendResponse($response, $e->getMessage(), Common\Enums\HttpStatusEnum::UNAUTHORIZED);
        }
    }

    public static function getResetTemplate(Request $request, Response $response)
    {
        $data = Common\Lib\ModelFactory::buildModel("PasswordReset", array());
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function resetPassword(Request $request, Response $response)
    {
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\PasswordReset');
        $result = DAO\UserDao::passwordReset($data->getPassword(), $data->getKey());
        return API\Dispatcher::sendResponse($response, $result, null);
    }

    public static function getRegisterTemplate(Request $request, Response $response)
    {
        $data = new Common\Protobufs\Models\Register();
        $data->setPassword("test");
        $data->setEmail("test@test.rog");
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function register(Request $request, Response $response)
    {
        $data = (string)$request->getBody();
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
        
        return API\Dispatcher::sendResponse($response, $registered, null);
    }

    public static function changeEmail(Request $request, Response $response)
    {
        $user = DAO\UserDao::getLoggedInUser();
        if (!is_null($user) && DAO\AdminDao::isAdmin($user->getId(), null)) {
            $data = (string)$request->getBody();
            $client = new Common\Lib\APIHelper('.json');
            $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Register");

            // password field has been repurposed to hold User for which email is to be changed
            $registered = DAO\UserDao::changeEmail($data->getPassword(), $data->getEmail());
        }
        else {
            $registered = null;
        }
        return API\Dispatcher::sendResponse($response, $registered, null);
    }

    public static function getUser(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $data = DAO\UserDao::getUser($userId);
        if (!is_null($data)) {
            $data->setPassword("");
            $data->setNonce("");
        }
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function updateUser(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\User');
        $data->setId($userId);
        $data = DAO\UserDao::save($data);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function deleteUser(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        error_log("deleteUser($userId)");
        DAO\UserDao::deleteUser($userId);
        return API\Dispatcher::sendResponse($response, null, null);
    }

    public static function getUsers(Request $request, Response $response)
    {
        return API\Dispatcher::sendResponse($response, "display all users", null);
    }
    
    public static function getBannedComment(Request $request, Response $response, $args)
    {
        $email = $args['email'];
        $client = new Common\Lib\APIHelper('.json');
        
        $user = DAO\UserDao::getUser(null, $email);
        $userId = $user->getId();
        $bannedUser = AdminDao::getBannedUser($userId);
        $bannedUser = $bannedUser[0];
        $comment = $bannedUser->getComment();
        
        return API\Dispatcher::sendResponse($response, $comment, null);
    }
}

Users::init();
