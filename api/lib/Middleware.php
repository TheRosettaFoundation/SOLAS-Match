<?php

namespace SolasMatch\API\Lib;

use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Dispatcher;
use \SolasMatch\Common as Common;

require_once __DIR__."/../DataAccessObjects/AdminDao.class.php";
require_once __DIR__."/../DataAccessObjects/TaskDao.class.php";
require_once __DIR__."/../DataAccessObjects/OrganisationDao.class.php";
require_once __DIR__."/../DataAccessObjects/ProjectDao.class.php";

class Middleware
{

    public static function isloggedIn ()
    {
        if (!is_null(DAO\UserDao::getLoggedInUser())) {
            return true;
        } else {
            Dispatcher::getDispatcher()->halt(
                Common\Enums\HttpStatusEnum::FORBIDDEN,
                "The Authorization header does not match the current user or ".
                "the user does not have permission to access the current resource"
            );
        }
    }
      
    public static function registerValidation (\Slim\Route $route)
    {
        $params = $route->getParams();
        if (isset($params['email']) && isset($_SERVER['HTTP_X_CUSTOM_AUTHORIZATION'])) {
            $headerHash = $_SERVER['HTTP_X_CUSTOM_AUTHORIZATION'];
            $email = $params['email'];
            if (!is_numeric($email) && strstr($email, '.')) {
                $temp = array();
                $temp = explode('.', $email);
                $lastIndex = sizeof($temp)-1;
                if ($lastIndex > 1) {
                    $format = '.'.$temp[$lastIndex];
                    $email = $temp[0];
                    for ($i = 1; $i < $lastIndex; $i++) {
                        $email = "{$email}.{$temp[$i]}";
                    }
                }
            }
            $openidHash = md5($email.substr(Common\Lib\Settings::get("session.site_key"), 0, 20));
            if ($headerHash != $openidHash) {
                Dispatcher::getDispatcher()->halt(
                    Common\Enums\HttpStatusEnum::FORBIDDEN,
                    "The user does not have permission to access the current resource"
                );
            }
        } else {
            self::authUserOwnsResource($route);
        }
    }
    
    // Does the user Id match the Id of the resources owner
    public static function authUserOwnsResource(\Slim\Route $route)
    {
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            }
            $params = $route->getParams();
            $userId = $params['userId'];
            if (!is_numeric($userId) && strstr($userId, '.')) {
                $userId = explode('.', $userId);
                $format = '.'.$userId[1];
                $userId = $userId[0];
            }
            if ($userId != $user->getId()) {
                Dispatcher::getDispatcher()->halt(
                    Common\Enums\HttpStatusEnum::FORBIDDEN,
                    "The user does not have permission to access the current resource"
                );
            }
        }
    }
    
    /*
     * Check for authorising users to create tasks. This function should be available to
     * orgainisation members, admins, and general users who have claimed a segmentation task on that project
     */
    public static function authUserOrOrgForTaskCreation(\Slim\Route $route)
    {
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            }
            $userId = $user->getId();
            $params = $route->getParams();
            
            $app = \Slim\Slim::getInstance();
            $req = $app->request;
            $task = $req->getBody();
            $format = $params['format'];
            $client = new Common\Lib\APIHelper($format);
            $task = $client->deserialize($task, "\SolasMatch\Common\Protobufs\Models\Task");
            $projectId = $task->getProjectId();
            
            $project = null;
            if ($projectId != null) {

                $project = DAO\ProjectDao::getProject($projectId);
            }
            $orgId = $project->getOrganisationId();
            
            if (DAO\OrganisationDao::isMember($orgId, $userId) || DAO\AdminDao::isAdmin($userId, $orgId)) {
                return true;
            }
            /*
             * In the case that a general user is uploading a segmentation task
             * the following will authorise that the user has claimed a segmentation task on this project
             */
            $hasUserSegmentationTask = DAO\TaskDao::hasUserClaimedSegmentationTask($userId, $projectId);
            
            if ($hasUserSegmentationTask) {
                return true;
            } else {
                Dispatcher::getDispatcher()->halt(
                    Common\Enums\HttpStatusEnum::FORBIDDEN,
                    "The user does not have permission to access the current resource"
                );
            }
        }
    }

    /*
     * Check for authorising users to create tasks. This function should be available to
     * orgainisation members, admins, and general users who have claimed a segmentation task on that project
     *  
     */
    public static function authUserOrOrgForTaskCreationPassingTaskId(\Slim\Route $route)
    {
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            }
            $userId = $user->getId();
            $params = $route->getParams();
            
            $taskId = $params['taskId'];
            if (!is_numeric($taskId) && strstr($taskId, '.')) {
                $taskId = explode('.', $taskId);
                $format = '.'.$taskId[1];
                $taskId = $taskId[0];
            }
            $task = null;
            if ($taskId != null) {
                $task = DAO\TaskDao::getTask($taskId);
            }
            $projectId = $task->getProjectId();
            $project = null;
            if ($projectId != null) {
                $project = DAO\ProjectDao::getProject($projectId);
            }
            $orgId = $project->getOrganisationId();
            if (DAO\OrganisationDao::isMember($orgId, $userId) || DAO\AdminDao::isAdmin($userId, $orgId)) {
                return true;
            }
            
            /*
             * In the case that a general user is uploading a segmentation task
             * the following will authorise that the user has claimed a segmentation task on this project
             */
            $hasUserSegmentationTask = DAO\TaskDao::hasUserClaimedSegmentationTask($userId, $projectId);
            
            if ($hasUserSegmentationTask) {
                return true;
            } else {
                Dispatcher::getDispatcher()->halt(
                    Common\Enums\HttpStatusEnum::FORBIDDEN,
                    "The user does not have permission to access the current resource"
                );
            }
        }
    }
    
    public static function authUserForProjectImage(\Slim\Route $route)
    {
            
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            }
        }
        
        $params = $route->getParams();
        $project_id = $params['project_id'];
        
        if ($params != null) {
            $project = DAO\ProjectDao::getProject($project_id);    
            $projectImageUploadedAndApproved = $project->getImageApproved() && $project->getImageUploaded() ;
            if ($projectImageUploadedAndApproved) {
                return true;
            }
        }
        self::notFound();
    }
    
    
    /*
     * Does the user Id match the Id of the resources owner
     * Or is it matching the Id of the organisations admin
     */
    public static function authUserOrAdminForOrg(\Slim\Route $route)
    {
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            }
            $params = $route->getParams();
            $userId=$params['userId'];
            if (!is_numeric($userId) && strstr($userId, '.')) {
                $userId = explode('.', $userId);
                $format = '.'.$userId[1];
                $userId = $userId[0];
            }
            
            $orgId=$params['orgId'];
            if (!is_numeric($orgId) && strstr($orgId, '.')) {
                $orgId = explode('.', $orgId);
                $format = '.'.$orgId[1];
                $orgId = $orgId[0];
            }
            
            if ($userId=$user->getId() || DAO\AdminDao::isAdmin($userId, $orgId)) {
                return true;
            } else {
                Dispatcher::getDispatcher()->halt(
                    Common\Enums\HttpStatusEnum::FORBIDDEN,
                    "The user does not have permission to access the current resource"
                );
            }
        }
    }
    
    public static function notFound()
    {
        Dispatcher::getDispatcher()->redirect(Dispatcher::getDispatcher()->urlFor('notFound'));
    }
    
    private static function isSiteAdmin($userId)
    {
        return DAO\AdminDao::isAdmin($userId, null);
    }

    // Is the user a site admin
    public static function authenticateSiteAdmin()
    {
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            } else {
                 Dispatcher::getDispatcher()->halt(
                     Common\Enums\HttpStatusEnum::FORBIDDEN,
                     "The user does not have permission to access the current resource"
                 );
            }
        }
    }
    
    // Is the user a member of ANY Organisation
    public static function authenticateUserMembership()
    {
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            }
            $userId = $user->getId();
            $userOrgList = DAO\UserDao::findOrganisationsUserBelongsTo($userId);
            if ($userOrgList != null && count($userOrgList) > 0) {
                return true;
            } else {
                 Dispatcher::getDispatcher()->halt(
                     Common\Enums\HttpStatusEnum::FORBIDDEN,
                     "The user does not have permission to access the current resource"
                 );
            }
        }
    }
    
    // Is the user an Admin of the Organisation releated to the request
    public static function authenticateOrgAdmin(\Slim\Route $route)
    {
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            }
            $userId = $user->getId();
            $params = $route->getParams();
            $orgId = null;
            if ($params != null) {
                $orgId = $params['orgId'];
                if (!is_numeric($orgId)&& strstr($orgId, '.')) {
                    $orgId = explode('.', $orgId);
                    $format = '.'.$orgId[1];
                    $orgId = $orgId[0];
                }
            }
            if ($orgId != null && DAO\AdminDao::isAdmin($userId, $orgId)) {
                return true;
            } else {
                Dispatcher::getDispatcher()->halt(
                    Common\Enums\HttpStatusEnum::FORBIDDEN,
                    "The user does not have permission to access the current resource"
                );
            }
        }
    }
    
    // Is the user a member of the Organisation related to the request
    public static function authenticateOrgMember(\Slim\Route $route)
    {
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            }
            $userId = $user->getId();
            $params = $route->getParams();
            $orgId = null;
            if ($params != null) {
                $orgId = $params['orgId'];
                if (!is_numeric($orgId)&& strstr($orgId, '.')) {
                    $orgId = explode('.', $orgId);
                    $format = '.'.$orgId[1];
                    $orgId = $orgId[0];
                }
            }
            if ($orgId != null && (DAO\OrganisationDao::isMember($orgId, $userId)
                    || DAO\AdminDao::isAdmin($userId, $orgId))) {
                return true;
            } else {
                Dispatcher::getDispatcher()->halt(
                    Common\Enums\HttpStatusEnum::FORBIDDEN,
                    "The user does not have permission to access the current resource"
                );
            }
        }
    }
    
    // Is the user a member of the Organisation who created the Project in question
    public static function authenticateUserForOrgProject(\Slim\Route $route)
    {
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            }
            $userId = $user->getId();
            $params = $route->getParams();
            
            $projectId = null;
            if ($params != null) {
                $projectId = $params['projectId'];
                if (!is_numeric($projectId)&& strstr($projectId, '.')) {
                    $projectId = explode('.', $projectId);
                    $format = '.'.$projectId[1];
                    $projectId = $projectId[0];
                }
            }
            $project = null;
            if ($projectId != null) {
                $project = DAO\ProjectDao::getProject($projectId);
            }
            
            $orgId = $project->getOrganisationId();
            if ($orgId != null && (DAO\OrganisationDao::isMember($orgId, $userId)
                    || DAO\AdminDao::isAdmin($userId, $orgId))) {
                return true;
            } else {
                Dispatcher::getDispatcher()->halt(
                    Common\Enums\HttpStatusEnum::FORBIDDEN,
                    "The user does not have permission to access the current resource"
                );
            }
        }
    }
    
    //Is the user a member of the Organisation who created the Task in question
    public static function authenticateUserForOrgTask(\Slim\Route $route)
    {
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            }
            $userId = $user->getId();
            $params = $route->getParams();
            
            $taskId = null;
            if ($params != null) {
                $taskId = $params['taskId'];
                if (!is_numeric($taskId)&& strstr($taskId, '.')) {
                    $taskId = explode('.', $taskId);
                    $format = '.'.$taskId[1];
                    $taskId = $taskId[0];
                }
            }
            
            $task = null;
            if ($taskId != null) {
                $task = DAO\TaskDao::getTask($taskId);

            }
            $projectId = $task->getProjectId();
            $project = null;
            if ($projectId != null) {
                $project = DAO\ProjectDao::getProject($projectId);

            }
            $orgId = $project->getOrganisationId();
            
            if ($orgId != null && (DAO\OrganisationDao::isMember($orgId, $userId)
                    || DAO\AdminDao::isAdmin($userId, $orgId))) {
                return true;
            } else {
                Dispatcher::getDispatcher()->halt(
                    Common\Enums\HttpStatusEnum::FORBIDDEN,
                    "The user does not have permission to access the current resource"
                );
            }
        }
    }

    // Does the user id match the current user or does the current user
    // belong to the organisation that created the task in question
    public static function authUserOrOrgForTask(\Slim\Route $route)
    {
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            }
            $params = $route->getParams();
            //in this function userId refers to the id being tested which may not be the currently logged in user
            $userId = $params['userId'];
            if (!is_numeric($userId) && strstr($userId, '.')) {
                $userId = explode('.', $userId);
                $format = '.'.$userId[1];
                $userId = $userId[0];
            }
            $taskId = $params['taskId'];
            if (!is_numeric($taskId) && strstr($taskId, '.')) {
                $taskId = explode('.', $taskId);
                $format = '.'.$taskId[1];
                $taskId = $taskId[0];
            }
            
            $task = null;
            if ($taskId != null) {
                $task = DAO\TaskDao::getTask($taskId);

            }
            $projectId = $task->getProjectId();
            $project = null;
            if ($projectId != null) {
                $project = DAO\ProjectDao::getProject($projectId);

            }
            $orgId = $project->getOrganisationId();
            
            if ($userId == $user->getId()) {
                return true;
            } elseif ($orgId != null &&
                    (DAO\OrganisationDao::isMember($orgId, $userId) || DAO\AdminDao::isAdmin($userId, $orgId))) {
                return true;
            } else {
                Dispatcher::getDispatcher()->halt(
                    Common\Enums\HttpStatusEnum::FORBIDDEN,
                    "The user does not have permission to access the current resource"
                );
            }
        }
    }

    // Has the User claimed the task
    public static function authUserForClaimedTask(\Slim\Route $route)
    {
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            }
            $userId = $user->getId();
            $params = $route->getParams();
            $taskId = $params['taskId'];
            if (!is_numeric($taskId) && strstr($taskId, '.')) {
                $taskId = explode('.', $taskId);
                $format = '.'.$taskId[1];
                $taskId = $taskId[0];
            }
            
            $hasTask = DAO\TaskDao::hasUserClaimedTask($userId, $taskId);
            if ($hasTask) {
                return true;
            } else {
                Dispatcher::getDispatcher()->halt(
                    Common\Enums\HttpStatusEnum::FORBIDDEN,
                    "The user does not have permission to access the current resource"
                );
            }
        }
    }
    
    //Is the User a member of the organisation that created the task or has the user claimed the task
    public static function authUserOrOrgForClaimedTask(\Slim\Route $route)
    {
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            }
            $userId = $user->getId();
            $params = $route->getParams();
            $taskId = $params['taskId'];
            if (!is_numeric($taskId) && strstr($taskId, '.')) {
                $taskId = explode('.', $taskId);
                $format = '.'.$taskId[1];
                $taskId = $taskId[0];
            }
            
            $task = null;
            if ($taskId != null) {
                $task = DAO\TaskDao::getTask($taskId);

            }
            $projectId = $task->getProjectId();
            $project = null;
            if ($projectId != null) {
                $project = DAO\ProjectDao::getProject($projectId);

            }
            $orgId = $project->getOrganisationId();
            $hasTask = DAO\TaskDao::hasUserClaimedTask($userId, $taskId);
            
            if ($hasTask || DAO\OrganisationDao::isMember($orgId, $userId) || DAO\AdminDao::isAdmin($userId, $orgId)) {
                return true;
            } else {
                Dispatcher::getDispatcher()->halt(
                    Common\Enums\HttpStatusEnum::FORBIDDEN,
                    "The user does not have permission to access the current resource"
                );
            }
        }
    }
    
    //Test if the User is a member of the Organisation that created
    //the task or has worked on one of the tasks of which this is a prerequisite
    public static function authenticateUserToSubmitReview(\Slim\Route $route)
    {
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            }
            
            $userId = $user->getId();
            $params = $route->getParams();

            $format = $params['format'];
            $client = new Common\Lib\APIHelper($format);
            $app = \Slim\Slim::getInstance();
            $req = $app->request;
            $review = $req->getBody();
            $review = $client->deserialize($review, "\SolasMatch\Common\Protobufs\Models\TaskReview");
            
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
            
            if ($hasFollowupTask || DAO\OrganisationDao::isMember($orgId, $userId)
                    || DAO\AdminDao::isAdmin($userId, $orgId)) {
                return true;
            } else {
                Dispatcher::getDispatcher()->halt(
                    Common\Enums\HttpStatusEnum::FORBIDDEN,
                    "The user does not have permission to access the current resource"
                );
            }
        }
    }

    // Has the User claimed a task on this project or is the user a member of the organisation that created the project
    public static function authenticateUserOrOrgForProjectTask(\Slim\Route $route)
    {
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            }
            $userId = $user->getId();
            $params = $route->getParams();
            
            $projectId = null;
            if ($params != null) {
                $projectId = $params['projectId'];
                if (!is_numeric($projectId)&& strstr($projectId, '.')) {
                    $projectId = explode('.', $projectId);
                    $format = '.'.$projectId[1];
                    $projectId = $projectId[0];
                }
            }
            $tasks = DAO\ProjectDao::getProjectTasks($projectId);
            $hasTask = false;
            if (!is_null($tasks)) {
                foreach ($tasks as $taskObject) {
                    if (DAO\TaskDao::hasUserClaimedTask($userId, $taskObject->getId())) {
                        $hasTask = true;
                    }
                }
            }
                        
            $project = null;
            if ($projectId != null) {
                $project = DAO\ProjectDao::getProject($projectId);

            }
            
            $orgId = $project->getOrganisationId();
            if ($hasTask || ($orgId != null &&
                        (DAO\OrganisationDao::isMember($orgId, $userId) || DAO\AdminDao::isAdmin($userId, $orgId)))) {
                return true;
            } else {
                Dispatcher::getDispatcher()->halt(
                    Common\Enums\HttpStatusEnum::FORBIDDEN,
                    "The user does not have permission to access the current resource"
                );
            }
        }
    }

    //Is the current user a member of the Organisation who created the Badge in question
    public static function authenticateUserForOrgBadge(\Slim\Route $route)
    {
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            }
            $userId = $user->getId();
            $params = $route->getParams();
            
            $badgeId = null;
            if ($params != null) {
                $badgeId = $params['badgeId'];
                if (!is_numeric($badgeId)&& strstr($badgeId, '.')) {
                    $badgeId = explode('.', $badgeId);
                    $format = '.'.$badgeId[1];
                    $badgeId = $badgeId[0];
                }
            }
            
            $badge = null;
            if ($badgeId != null) {
                $badge = DAO\BadgeDao::getBadge($badgeId);
            }
            $orgId = $badge->getOwnerId();
                    
            // cases where the orgId is null signify a system badge
            // badge ids 6, 7 and 8 refer to the user controlled system badges
            // maybe we could move them to a class as consts or a function
            if ($orgId != null && (DAO\OrganisationDao::isMember($orgId, $userId)
                    || DAO\AdminDao::isAdmin($userId, $orgId))) {
                return true;
            } elseif ($orgId == null && in_array($badgeId, array(6, 7, 8))) {
                return true;
            } else {
                Dispatcher::getDispatcher()->halt(
                    Common\Enums\HttpStatusEnum::FORBIDDEN,
                    "The user does not have permission to access the current resource"
                );
            }
        }
    }

    //Does the user id (given to through the route) match the current user or is the current
    //user a member of the Organisation who created the Badge in question
    public static function authenticateUserOrOrgForOrgBadge(\Slim\Route $route)
    {
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            }
            $params = $route->getParams();
            //in this function userId refers to the id being tested which may not be the currently logged in user
            $userId = $params['userId'];
            if (!is_numeric($userId) && strstr($userId, '.')) {
                $userId = explode('.', $userId);
                $format = '.'.$userId[1];
                $userId = $userId[0];
            }
            
            $badgeId = null;
            if ($params != null) {
                $badgeId = $params['badgeId'];
                if (!is_numeric($badgeId)&& strstr($badgeId, '.')) {
                    $badgeId = explode('.', $badgeId);
                    $format = '.'.$badgeId[1];
                    $badgeId = $badgeId[0];
                }
            }
            
            $badge = null;
            if ($badgeId != null) {
                $badge = DAO\BadgeDao::getBadge($badgeId);

            }
            
            $loggedInId = $user->getId();
            $orgId = $badge->getOwnerId();
                    
            if ($userId == $user->getId()) {
                return true;
            } elseif ($orgId != null &&
                    (DAO\OrganisationDao::isMember($orgId, $loggedInId)
                    || DAO\AdminDao::isAdmin($loggedInId, $orgId))) {
                /*
                 * currently this checks if the orgId is not Null
                 * cases where the orgId is null signify a system badge
                 * using this middleware function will lead to errors unless those are accounted for
                 */
                return true;
            } else {
                Dispatcher::getDispatcher()->halt(
                    Common\Enums\HttpStatusEnum::FORBIDDEN,
                    "The user does not have permission to access the current resource"
                );
            }
        }
    }
    
    // Does User have required badge
    public static function authenticateUserHasBadge(\Slim\Route $route)
    {
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            }
            $userId = $user->getId();
            $params = $route->getParams();
            
            $badgeId = null;
            if ($params != null) {
                $badgeId = $params['badgeId'];
                if (!is_numeric($badgeId)&& strstr($badgeId, '.')) {
                    $badgeId = explode('.', $badgeId);
                    $format = '.'.$badgeId[1];
                    $badgeId = $badgeId[0];
                }
            }
            if ($badgeId != null && DAO\BadgeDao::validateUserBadge($userId, $badgeId)) {
                return true;
            } else {
                Dispatcher::getDispatcher()->halt(
                    Common\Enums\HttpStatusEnum::FORBIDDEN,
                    "The user does not have permission to access the current resource"
                );
            }
        }
    }
    
    /*
     * Test if the task the user is trying to claim has already been claimed
     * Prevents two claiments on the same task, admins will still be able to claim a task in all cases 
     */
    public static function authenticateTaskNotClaimed(\Slim\Route $route)
    {
        if (self::isloggedIn()) {
            $user = DAO\UserDao::getLoggedInUser();
            if (self::isSiteAdmin($user->getId())) {
                return true;
            }
            
            $params = $route->getParams();
            
            $taskId = null;
            if ($params != null) {
                $taskId = $params['taskId'];
                if (!is_numeric($taskId)&& strstr($taskId, '.')) {
                    $taskId = explode('.', $taskId);
                    $format = '.'.$taskId[1];
                    $taskId = $taskId[0];
                }
            }
         
            $TaskIsUnclaimed = false;
            $possibleUser = DAO\TaskDao::getUserClaimedTask($taskId);
            if (is_null($possibleUser)) {
                $TaskIsUnclaimed = true;
            }
                        
            if ($TaskIsUnclaimed) {
                return true;
            } else {
                Dispatcher::getDispatcher()->halt(
                    Common\Enums\HttpStatusEnum::FORBIDDEN,
                    "Unable to claim task. This Task has been claimed by another user"
                );
            }
        }
    }
    
    public static function authenticateIsUserBanned(\Slim\Route $route)
    {
        $params = $route->getParams();
        
        if ($params != null) {
            $userEmail = $params['email'];
        }
        $user = DAO\UserDao::getUser(null, $userEmail);
        $userId = $user->getId();
        
        if (DAO\AdminDao::isUserBanned($userId) == true) {
            return true;
        } else {
            Dispatcher::getDispatcher()->halt(
                Common\Enums\HttpStatusEnum::FORBIDDEN,
                "User is not banned. Unable to fetch banned comment."
            );
        }
    }
}
