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

    public static function authenticateSiteAdmin(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authenticateSiteAdmin');
        $user = DAO\UserDao::getLoggedInUser();
        if (DAO\AdminDao::get_roles($user->getId()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) {
            return $handler->handle($request);
        }
        return self::return_error($request, 'The user does not have permission to access the current resource authenticateSiteAdmin');
    }

    public static function authenticateOrgAdmin(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authenticateOrgAdmin');
        $user = DAO\UserDao::getLoggedInUser();
        $userId = $user->getId();

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $orgId = $route->getArgument('orgId');
        if ($orgId != null && (DAO\AdminDao::get_roles($userId, $orgId) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER))) return $handler->handle($request);

        return self::return_error($request, 'The user does not have permission to access the current resource authenticateOrgAdmin');
    }
    
    public static function authenticateUserForOrgProject(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authenticateUserForOrgProject');
        $user = DAO\UserDao::getLoggedInUser();
        $userId = $user->getId();

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $projectId = $route->getArgument('projectId');
        $project = DAO\ProjectDao::getProject($projectId);
        $orgId = $project->getOrganisationId();
        if ($orgId != null && (DAO\AdminDao::get_roles($userId, $orgId) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER))) return $handler->handle($request);

        return self::return_error($request, 'The user does not have permission to access the current resource authenticateUserForOrgProject');
    }
    
    public static function authenticateUserForOrgTask(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authenticateUserForOrgTask');
        $user = DAO\UserDao::getLoggedInUser();
        $userId = $user->getId();

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $taskId = $route->getArgument('taskId');
        $task = DAO\TaskDao::getTask($taskId);
        $projectId = $task->getProjectId();
        $project = DAO\ProjectDao::getProject($projectId);
        $orgId = $project->getOrganisationId();
        if ($orgId != null && (DAO\AdminDao::get_roles($userId, $orgId) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER))) return $handler->handle($request);

        return self::return_error($request, 'The user does not have permission to access the current resource authenticateUserForOrgTask');
    }

    // Does the current user match the user id passed in the URL
    // or does the current user belong to the organisation that created the task id passed in the URL?
    public static function authUserOrOrgForTask(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authUserOrOrgForTask');
        $user = DAO\UserDao::getLoggedInUser();
        $current_user = $user->getId();

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $userId = $route->getArgument('userId');
        // In this function, $userId refers to the id being tested which may not be the currently logged in user
        $taskId = $route->getArgument('taskId');
        $task = DAO\TaskDao::getTask($taskId);
        $projectId = $task->getProjectId();
        $project = DAO\ProjectDao::getProject($projectId);
        $orgId = $project->getOrganisationId();

        if ($userId == $current_user) return $handler->handle($request);
        if ($orgId != null && (DAO\AdminDao::get_roles($current_user, $orgId) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER))) return $handler->handle($request);

        return self::return_error($request, 'The user does not have permission to access the current resource authUserOrOrgForTask');
    }

    // Has the User claimed the task
    public static function authUserForClaimedTask(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authUserForClaimedTask');
        $user = DAO\UserDao::getLoggedInUser();
        $userId = $user->getId();
        if (DAO\AdminDao::get_roles($userId) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) {
            return $handler->handle($request);
        }
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $taskId = $route->getArgument('taskId');
            
        if (DAO\TaskDao::hasUserClaimedTask($userId, $taskId)) return $handler->handle($request);

        return self::return_error($request, 'The user does not have permission to access the current resource authUserForClaimedTask');
    }
    
    //Is the User a member of the organisation that created the task or has the user claimed the task
    public static function authUserOrOrgForClaimedTask(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authUserOrOrgForClaimedTask');
        $user = DAO\UserDao::getLoggedInUser();
        $userId = $user->getId();

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $taskId = $route->getArgument('taskId');
        $task = DAO\TaskDao::getTask($taskId);
        $projectId = $task->getProjectId();
        $project = DAO\ProjectDao::getProject($projectId);
        $orgId = $project->getOrganisationId();

        if (DAO\TaskDao::hasUserClaimedTask($userId, $taskId)) return $handler->handle($request);
        if ($orgId != null && (DAO\AdminDao::get_roles($userId, $orgId) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER))) return $handler->handle($request);

        return self::return_error($request, 'The user does not have permission to access the current resource authUserOrOrgForClaimedTask');
    }
    
    //Is the current user a member of the Organisation who created the Badge in question
    public static function authenticateUserForOrgBadge(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authenticateUserForOrgBadge');
        $user = DAO\UserDao::getLoggedInUser();
        $userId = $user->getId();

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $badgeId = $route->getArgument('badgeId');
        $badge = DAO\BadgeDao::getBadge($badgeId);
        $orgId = $badge->getOwnerId();
                    
        // cases where the orgId is null signify a system badge
        // badge ids 6, 7, 8... refer to the user controlled system badges
        if ($orgId != null && (DAO\AdminDao::get_roles($userId, $orgId) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER))) return $handler->handle($request);
        elseif ($orgId == null && in_array($badgeId, array(6, 7, 8, 10, 11, 12, 13))) return $handler->handle($request);

        return self::return_error($request, 'The user does not have permission to access the current resource authenticateUserForOrgBadge');
    }

    //Does the user id (given to through the route) match the current user or is the current
    //user a member of the Organisation who created the Badge in question
    public static function authenticateUserOrOrgForOrgBadge(Request $request, RequestHandler $handler)
    {
        if (is_null(DAO\UserDao::getLoggedInUser())) return self::return_error($request, 'The Authorization header does not match the current user or the user does not have permission to access the current resource authenticateUserOrOrgForOrgBadge');
        $user = DAO\UserDao::getLoggedInUser();
        $loggedInId = $user->getId();

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $userId = $route->getArgument('userId');
        //in this function userId refers to the id being tested which may not be the currently logged in user
        $badgeId = $route->getArgument('badgeId');
        $badge = DAO\BadgeDao::getBadge($badgeId);
        $orgId = $badge->getOwnerId();
                    
        if ($userId == $loggedInId) return $handler->handle($request);
        if ($orgId != null && (DAO\AdminDao::get_roles($loggedInId, $orgId) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER))) return $handler->handle($request);
            /*
             * currently this checks if the orgId is not Null
             * cases where the orgId is null signify a system badge
             * using this middleware function will lead to errors unless those are accounted for
             */
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
