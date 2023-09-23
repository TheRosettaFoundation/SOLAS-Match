<?php
namespace SolasMatch\API\Lib;

use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Dispatcher;
use \SolasMatch\Common as Common;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

require_once __DIR__."/../DataAccessObjects/AdminDao.class.php";
require_once __DIR__."/../DataAccessObjects/TaskDao.class.php";
require_once __DIR__."/../DataAccessObjects/OrganisationDao.class.php";
require_once __DIR__."/../DataAccessObjects/ProjectDao.class.php";

class Middleware
{
    public static function isloggedIn(Request $request, RequestHandler $handler)
    {
        if (!is_null(DAO\UserDao::getLoggedInUser())) {
            return $handler->handle($request);
        } else {
            return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource isloggedIn');
        }
    }

    public static function registerValidation(Request $request, RequestHandler $handler)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $email = $route->getArgument('email');
        if (!empty($email) && isset($_SERVER['HTTP_X_CUSTOM_AUTHORIZATION'])) {
            $headerHash = $_SERVER['HTTP_X_CUSTOM_AUTHORIZATION'];
            $openidHash = md5($email.substr(Common\Lib\Settings::get("session.site_key"), 0, 20));
            if ($headerHash != $openidHash) {
                return self::return_error($request, 'The user does not have permission to access the current resource registerValidation');
            }
        } else {
            if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource registerValidation');
        }
        return $handler->handle($request);
    }
    
    // Does the user Id match the Id of the resources owner
    public static function authUserOwnsResource(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authUserOwnsResource');
        $user = DAO\UserDao::getLoggedInUser();
        if (DAO\AdminDao::get_roles($user->getId()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) {
            return $handler->handle($request);
        }

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $userId = $route->getArgument('userId');
        if ($userId != $user->getId()) {
            return self::return_error($request, 'The user does not have permission to access the current resource authUserOwnsResource');
        }
        return $handler->handle($request);
    }
    
    /*
     * Check for authorising users to create tasks. This function should be available to
     * orgainisation members, admins, and general users who have claimed a segmentation task on that project
     *  
     */
    public static function authUserOrOrgForTaskCreationPassingTaskId(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authUserOrOrgForTaskCreationPassingTaskId');
        $user = DAO\UserDao::getLoggedInUser();
        if (DAO\AdminDao::get_roles($user->getId()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) {
            return $handler->handle($request);
        }
        $userId = $user->getId();

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $taskId = $route->getArgument('taskId');
        $task = DAO\TaskDao::getTask($taskId);

        $projectId = $task->getProjectId();
        $project = DAO\ProjectDao::getProject($projectId);
        $orgId = $project->getOrganisationId();
        if (DAO\OrganisationDao::isMember($orgId, $userId) || DAO\AdminDao::isAdmin($userId, $orgId)) {
            return $handler->handle($request);
        }
        return self::return_error($request, 'The user does not have permission to access the current resource authUserOrOrgForTaskCreationPassingTaskId');
    }

    /*
    * Checks whether the user is an admin, if so display the image
    * Otherwise, if the image has been uploaded and approved then display the image
    */
    public static function authUserForProjectImage(Request $request, RequestHandler $handler)
    {
        if (!is_null(DAO\UserDao::getLoggedInUser())) {
            $user = DAO\UserDao::getLoggedInUser();
            if (DAO\AdminDao::get_roles($user->getId()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) {
                return $handler->handle($request);
            }
        }
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $projectId = $route->getArgument('projectId');
        $project = DAO\ProjectDao::getProject($projectId);

        $projectImageUploadedAndApproved = $project->getImageApproved() && $project->getImageUploaded();
        if ($projectImageUploadedAndApproved) {
            return $handler->handle($request);
        }
        return self::return_error($request, 'The user does not have permission to access the current resource authUserForProjectImage');
    }

    // Is the user a site admin
    public static function authenticateSiteAdmin(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authenticateSiteAdmin');
        $user = DAO\UserDao::getLoggedInUser();
        if (DAO\AdminDao::get_roles($user->getId()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) {
            return $handler->handle($request);
        }
        return self::return_error($request, 'The user does not have permission to access the current resource authenticateSiteAdmin');
    }
    
    // Is the user a member of ANY Organisation
    public static function authenticateUserMembership(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authenticateUserMembership');
        $user = DAO\UserDao::getLoggedInUser();
        if (DAO\AdminDao::get_roles($user->getId()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) {
            return $handler->handle($request);
        }
        $userId = $user->getId();
        $userOrgList = DAO\UserDao::findOrganisationsUserBelongsTo($userId);
        if ($userOrgList != null && count($userOrgList) > 0) {
            return $handler->handle($request);
        }
        return self::return_error($request, 'The user does not have permission to access the current resource authenticateUserMembership');
    }
    
    // Is the user an Admin of the Organisation releated to the request
    public static function authenticateOrgAdmin(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authenticateOrgAdmin');
        $user = DAO\UserDao::getLoggedInUser();
        if (DAO\AdminDao::get_roles($user->getId()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) {
            return $handler->handle($request);
        }
        $userId = $user->getId();

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $orgId = $route->getArgument('orgId');
        if ($orgId != null && DAO\AdminDao::isAdmin($userId, $orgId)) {
            return $handler->handle($request);
        }
        return self::return_error($request, 'The user does not have permission to access the current resource authenticateOrgAdmin');
    }
    
    // Is the user a member of the Organisation related to the request
    public static function authenticateOrgMember(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authenticateOrgMember');
        $user = DAO\UserDao::getLoggedInUser();
        if (DAO\AdminDao::get_roles($user->getId()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) {
            return $handler->handle($request);
        }
        $userId = $user->getId();

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $orgId = $route->getArgument('orgId');
        if ($orgId != null && (DAO\OrganisationDao::isMember($orgId, $userId) || DAO\AdminDao::isAdmin($userId, $orgId))) {
            return $handler->handle($request);
        }
        return self::return_error($request, 'The user does not have permission to access the current resource authenticateOrgMember');
    }
    
    // Is the user a member of the Organisation who created the Project in question
    public static function authenticateUserForOrgProject(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authenticateUserForOrgProject');
        $user = DAO\UserDao::getLoggedInUser();
        if (DAO\AdminDao::get_roles($user->getId()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) {
            return $handler->handle($request);
        }
        $userId = $user->getId();

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $projectId = $route->getArgument('projectId');
        $project = DAO\ProjectDao::getProject($projectId);

        $orgId = $project->getOrganisationId();
        if ($orgId != null && (DAO\OrganisationDao::isMember($orgId, $userId) || DAO\AdminDao::isAdmin($userId, $orgId))) {
            return $handler->handle($request);
        }
        return self::return_error($request, 'The user does not have permission to access the current resource authenticateUserForOrgProject');
    }
    
    //Is the user a member of the Organisation who created the Task in question
    public static function authenticateUserForOrgTask(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authenticateUserForOrgTask');
        $user = DAO\UserDao::getLoggedInUser();
        if (DAO\AdminDao::get_roles($user->getId()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) {
            return $handler->handle($request);
        }
        $userId = $user->getId();

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $taskId = $route->getArgument('taskId');
        $task = DAO\TaskDao::getTask($taskId);

        $projectId = $task->getProjectId();
        $project = DAO\ProjectDao::getProject($projectId);

        $orgId = $project->getOrganisationId();
        if ($orgId != null && (DAO\OrganisationDao::isMember($orgId, $userId) || DAO\AdminDao::isAdmin($userId, $orgId))) {
            return $handler->handle($request);
        }
        return self::return_error($request, 'The user does not have permission to access the current resource authenticateUserForOrgTask');
    }

    // Does the current user match the user id passed in the URL
    // or does the current user belong to the organisation that created the task id passed in the URL?
    public static function authUserOrOrgForTask(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authUserOrOrgForTask');
        $user = DAO\UserDao::getLoggedInUser();
        $current_user = $user->getId();
        if (DAO\AdminDao::get_roles($current_user) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) {
            return $handler->handle($request);
        }
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $userId = $route->getArgument('userId');
        // In this function, $userId refers to the id being tested which may not be the currently logged in user

        $taskId = $route->getArgument('taskId');
        $task = DAO\TaskDao::getTask($taskId);

        $projectId = $task->getProjectId();
        $project = DAO\ProjectDao::getProject($projectId);

        $orgId = $project->getOrganisationId();

        if ($userId == $current_user) {
            return $handler->handle($request);
        } elseif ($orgId != null && (DAO\OrganisationDao::isMember($orgId, $current_user) || DAO\AdminDao::isAdmin($current_user, $orgId))) {
            return $handler->handle($request);
        }
        return self::return_error($request, 'The user does not have permission to access the current resource authUserOrOrgForTask');
    }

    // Has the User claimed the task
    public static function authUserForClaimedTask(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authUserForClaimedTask');
        $user = DAO\UserDao::getLoggedInUser();
        if (DAO\AdminDao::get_roles($user->getId()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) {
            return $handler->handle($request);
        }
        $userId = $user->getId();

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $taskId = $route->getArgument('taskId');
            
        $hasTask = DAO\TaskDao::hasUserClaimedTask($userId, $taskId);
        if ($hasTask) {
            return $handler->handle($request);
        }
        return self::return_error($request, 'The user does not have permission to access the current resource authUserForClaimedTask');
    }
    
    //Is the User a member of the organisation that created the task or has the user claimed the task
    public static function authUserOrOrgForClaimedTask(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authUserOrOrgForClaimedTask');
        $user = DAO\UserDao::getLoggedInUser();
        if (DAO\AdminDao::get_roles($user->getId()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) {
            return $handler->handle($request);
        }
        $userId = $user->getId();

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $taskId = $route->getArgument('taskId');
            
        $task = DAO\TaskDao::getTask($taskId);

        $projectId = $task->getProjectId();
        $project = DAO\ProjectDao::getProject($projectId);

        $orgId = $project->getOrganisationId();
        $hasTask = DAO\TaskDao::hasUserClaimedTask($userId, $taskId);
        if ($hasTask || DAO\OrganisationDao::isMember($orgId, $userId) || DAO\AdminDao::isAdmin($userId, $orgId)) {
            return $handler->handle($request);
        }
        return self::return_error($request, 'The user does not have permission to access the current resource authUserOrOrgForClaimedTask');
    }
    
    //Test if the User is a member of the Organisation that created
    //the task or has worked on one of the tasks of which this is a prerequisite
    public static function authenticateUserToSubmitReview(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authenticateUserToSubmitReview');
        $user = DAO\UserDao::getLoggedInUser();
        if (DAO\AdminDao::get_roles($user->getId()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) {
            return $handler->handle($request);
        }
        $userId = $user->getId();

        $client = new Common\Lib\APIHelper('.json');
        $review = (string)$request->getBody();
        $review = $client->deserialize($review, '\SolasMatch\Common\Protobufs\Models\TaskReview');

        $hasFollowupTask = false;
        /*
         * If the taskId is null this indicates the user is not reviewing a task but the project file itself
         * all users who have claimed a task on the project can review it esentialy but it may not be
         * accessable through the UI for all cases
         */
        if (!is_null($review->getTaskId())) {
            $nextTasks = DAO\TaskDao::getTasksFromPreReq($review->getTaskId(), $review->getProjectId());
            foreach ($nextTasks as $nextTask) {
                if (!is_null($nextTask) && DAO\TaskDao::hasUserClaimedTask($userId, $nextTask->getId())) {
                    $hasFollowupTask = true;
                }
            }
        } else {
            $userTasks = DAO\TaskDao::getUserTasks($userId);
            foreach ($userTasks as $task) {
                if ($task->getProjectId() == $review->getProjectId()) {
                    $hasFollowupTask = true;
                }
            }
        }
        if ($review->getProjectId() != null) {
            $project = DAO\ProjectDao::getProject($review->getProjectId());
        }
        $orgId = $project->getOrganisationId();
            
        if ($hasFollowupTask || DAO\OrganisationDao::isMember($orgId, $userId) || DAO\AdminDao::isAdmin($userId, $orgId)) {
            return $handler->handle($request);
        }
        return self::return_error($request, 'The user does not have permission to access the current resource authenticateUserToSubmitReview');
    }

    // Has the User claimed a task on this project or is the user a member of the organisation that created the project
    public static function authenticateUserOrOrgForProjectTask(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authenticateUserOrOrgForProjectTask');
        $user = DAO\UserDao::getLoggedInUser();
        if (DAO\AdminDao::get_roles($user->getId()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) {
            return $handler->handle($request);
        }
        $userId = $user->getId();

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $projectId = $route->getArgument('projectId');

        $tasks = DAO\ProjectDao::getProjectTasks($projectId);
        $hasTask = false;
        if (!is_null($tasks)) {
            foreach ($tasks as $taskObject) {
                if (DAO\TaskDao::hasUserClaimedTask($userId, $taskObject->getId())) {
                    $hasTask = true;
                }
            }
        }

        $project = DAO\ProjectDao::getProject($projectId);
        $orgId = $project->getOrganisationId();

        if ($hasTask || ($orgId != null && (DAO\OrganisationDao::isMember($orgId, $userId) || DAO\AdminDao::isAdmin($userId, $orgId)))) {
            return $handler->handle($request);
        }
        return self::return_error($request, 'The user does not have permission to access the current resource authenticateUserOrOrgForProjectTask');
    }

    //Is the current user a member of the Organisation who created the Badge in question
    public static function authenticateUserForOrgBadge(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authenticateUserForOrgBadge');
        $user = DAO\UserDao::getLoggedInUser();
        if (DAO\AdminDao::get_roles($user->getId()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) {
            return $handler->handle($request);
        }
        $userId = $user->getId();

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $badgeId = $route->getArgument('badgeId');
        $badge = DAO\BadgeDao::getBadge($badgeId);

        $orgId = $badge->getOwnerId();
                    
        // cases where the orgId is null signify a system badge
        // badge ids 6, 7, 8... refer to the user controlled system badges
        if ($orgId != null && (DAO\OrganisationDao::isMember($orgId, $userId) || DAO\AdminDao::isAdmin($userId, $orgId))) {
            return $handler->handle($request);
        } elseif ($orgId == null && in_array($badgeId, array(6, 7, 8, 10, 11, 12, 13))) {
            return $handler->handle($request);
        }
        return self::return_error($request, 'The user does not have permission to access the current resource authenticateUserForOrgBadge');
    }

    //Does the user id (given to through the route) match the current user or is the current
    //user a member of the Organisation who created the Badge in question
    public static function authenticateUserOrOrgForOrgBadge(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authenticateUserOrOrgForOrgBadge');
        $user = DAO\UserDao::getLoggedInUser();
        if (DAO\AdminDao::get_roles($user->getId()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) {
            return $handler->handle($request);
        }

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $userId = $route->getArgument('userId');
        //in this function userId refers to the id being tested which may not be the currently logged in user
        $badgeId = $route->getArgument('badgeId');
        $badge = DAO\BadgeDao::getBadge($badgeId);

        $loggedInId = $user->getId();
        $orgId = $badge->getOwnerId();
                    
        if ($userId == $user->getId()) {
            return $handler->handle($request);
        } elseif ($orgId != null && (DAO\OrganisationDao::isMember($orgId, $loggedInId) || DAO\AdminDao::isAdmin($loggedInId, $orgId))) {
            /*
             * currently this checks if the orgId is not Null
             * cases where the orgId is null signify a system badge
             * using this middleware function will lead to errors unless those are accounted for
             */
             return $handler->handle($request);
        }
        return self::return_error($request, 'The user does not have permission to access the current resource authenticateUserOrOrgForOrgBadge');
    }
    
    public static function authenticateIsUserBanned(Request $request, RequestHandler $handler)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $userEmail = $route->getArgument('email');

        $user = DAO\UserDao::getUser(null, $userEmail);
        $userId = $user->getId();
        
        if (DAO\AdminDao::isUserBanned($userId) == true) {
            return $handler->handle($request);
        }
        return self::return_error($request, 'User is not banned. Unable to fetch banned comment.');
    }

    private static function return_error(Request $request, $text)
    {
        global $app;

        $response = $app->getResponseFactory()->createResponse();
        $response->getBody()->write($text);
        return $response->withStatus(Common\Enums\HttpStatusEnum::FORBIDDEN);
    }
}
