<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Middleware
 *
 * @author sean
 */
require_once __DIR__."/../DataAccessObjects/AdminDao.class.php";
require_once __DIR__."/../DataAccessObjects/TaskDao.class.php";
require_once __DIR__."/../DataAccessObjects/OrganisationDao.class.php";
require_once __DIR__."/../DataAccessObjects/ProjectDao.class.php";

class Middleware
{
	
    public static function isloggedIn ($request, $response, $route)
    {
    	if(!is_null(UserDao::getLoggedInUser())) {
    		return true;
		}
		else {
			Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, "The Autherization header does not match the current user or the user does not have permission to acess the current resource");
		}

	}
	  
//        $params = $route->getParams();
//      
//        
//       
//            if(isset ($params['email'])&& isset($_SERVER['HTTP_X_CUSTOM_AUTHORIZATION'])){
//                $headerHash = $_SERVER['HTTP_X_CUSTOM_AUTHORIZATION'];
//                $email =$params['email'];
//                 if (!is_numeric($email) && strstr($email, '.')) {
//                    $temp = array();
//                    $temp = explode('.', $email);
//                    $lastIndex = sizeof($temp)-1;
//                    if ($lastIndex > 1) {
//                        $format='.'.$temp[$lastIndex];
//                        $email = $temp[0];
//                        for ($i = 1; $i < $lastIndex; $i++) {
//                            $email = "{$email}.{$temp[$i]}";
//                        }
//                    }
//                }
//                $openidHash = md5($email.substr(Settings::get("session.site_key"),0,20));
//                if ($headerHash!=$openidHash) {
//                    Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, "The Autherization header does not match the current user or the user does not have permission to acess the current resource");
//                } 
//            }
        
//    } 
    
    
    public static function Registervalidation ($request, $response, $route) 
    {
        $params = $route->getParams();
        if (isset($params['email']) && isset($_SERVER['HTTP_X_CUSTOM_AUTHORIZATION'])) {
            $headerHash = $_SERVER['HTTP_X_CUSTOM_AUTHORIZATION'];
            $email =$params['email'];
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
            $openidHash = md5($email.substr(Settings::get("session.site_key"),0,20));
            if ($headerHash!=$openidHash) {
                Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, "The user does not have permission to acess the current resource");
            } 
        } else {
            self::authUserOwnsResource ($request, $response, $route);
        }
    }
	
	// Does the user Id match the Id of the resources owner
	public static function authUserOwnsResource($request, $response, $route)
    {
    	if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
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
	        if ($userId!=$user->getId()) {
	            Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
	        }
        }
    } 
    
    public static function notFound()
    {
        Dispatcher::getDispatcher()->redirect(Dispatcher::getDispatcher()->urlFor('getLoginTemplate'));
    }
    
    private static function isSiteAdmin($userId)
    {
        
        return AdminDao::isAdmin($userId,null);
    }

//	private static function getOrgIdFromProjectId($projectId)
//	{		
//		$orgId = null;
//		if ($projectId != null) {
//			$projects = ProjectDao::getProject($projectId);
//			$project = $projects[0];
//			$orgId = $project->getOrganisationId();
//		}
//		return $orgId;
//	}
	
	
	// Is the user a site admin
	public static function authenticateSiteAdmin($request, $response, $route)
    {
    	if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
		    if (self::isSiteAdmin($user->getId())) {
		    	return true;
		    }
			else {
				 Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
		                "The user does not have permission to acess the current resource");
			}
		}
    }
	
	// Is the user a member of ANY Organisation 
	public static function authenticateUserMembership($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
	        if (self::isSiteAdmin($user->getId())) {
	        	return true;
	        }
			$userId = $user->getId();
			$userOrgList = UserDao::findOrganisationsUserBelongsTo($userId);
			if($userOrgList != null && count($userOrgList) > 0) {
				return true;				
			}
			else {
				 Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
        				"The user does not have permission to acess the current resource");
			}
		}
	}
	
	// Is the user an Admin of the Organisation releated to the request
	public static function authenticateOrgAdmin($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
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
			if ($orgId != null && AdminDao::isAdmin($userId, $orgId)) {
				return true;
			}
			else {
				Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
			}
		}
	}
	
	// Is the user a member of the Organisation related to the request
	public static function authenticateOrgMember($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
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
			if ($orgId != null && (OrganisationDao::isMember($orgId, $userId) || AdminDao::isAdmin($userId, $orgId))) {
				return true;
			}
			else {
				Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
			}
		}       
	}
	
	// Is the user a member of the Organisation who created the Project in question
	public static function authenticateUserForOrgProject($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
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
				$projects = ProjectDao::getProject($projectId);
				$project = $projects[0];
			}
			
			$orgId = $project->getOrganisationId();
			if ($orgId != null && (OrganisationDao::isMember($orgId, $userId) || AdminDao::isAdmin($userId, $orgId))) {
				return true;
			}
			else {
				Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
			}
		}	
	}

	// Is the user a member of the organisation that is creating the task
	public static function authenticateUserCreateOrgTask($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
	        if (self::isSiteAdmin($user->getId())) {
	        	return true;
	        }
	        $userId = $user->getId();
			$params = $route->getParams();
			
			$task = $request->getBody();
			$format = $params['format'];
            $client = new APIHelper($format);			
            $task = $client->deserialize($task, "Task");
			
			$projectId = $task->getProjectId();
			$project = null;
			if ($projectId != null) {
				$projects = ProjectDao::getProject($projectId);
				$project = $projects[0];
			}
			
			$orgId = $project->getOrganisationId();
			
			if ($orgId != null && (OrganisationDao::isMember($orgId, $userId) || AdminDao::isAdmin($userId, $orgId))) {
				return true;
			}
			else {
				Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
			}
		}
	}
	
	//Is the user a member of the Organisation who created the Task in question
	public static function authenticateUserForOrgTask($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
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
			if($taskId != null) {
				$tasks = TaskDao::getTask($taskId);
				$task = $tasks[0];
			}
			$projectId = $task->getProjectId();
			$project = null;
			if ($projectId != null) {
				$projects = ProjectDao::getProject($projectId);
				$project = $projects[0];
			}
			
			$orgId = $project->getOrganisationId();
			
			if ($orgId != null && (OrganisationDao::isMember($orgId, $userId) || AdminDao::isAdmin($userId, $orgId))) {
				return true;
			}
			else {
				Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
			}
		}
		
	}

	// Does the user id match the current user or does the current user belong to the organisation that created the task in question
	public static function authUserOrOrgForTask($request, $response, $route)
	{			
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
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
			if($taskId != null) {
				$tasks = TaskDao::getTask($taskId);
				$task = $tasks[0];
			}
			$projectId = $task->getProjectId();
			$project = null;
			if ($projectId != null) {
				$projects = ProjectDao::getProject($projectId);
				$project = $projects[0];
			}
			
			$orgId = $project->getOrganisationId();
			
			if($userId == $user->getId()) {
				return true;
			}
			else if($orgId != null && (OrganisationDao::isMember($orgId, $userId) || AdminDao::isAdmin($userId, $orgId))) {
				return true;
			}
			else {
	            Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
	        }
        }
	}

	// Has the User claimed the task
	public static function authUserForClaimedTask($request, $response, $route)
	{			
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
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
			
    		$hasTask = TaskDao::hasUserClaimedTask($userId, $taskId);
			if($hasTask) {
			 	return true;
			}
			else {
	            Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
                "The user does not have permission to acess the current resource");
			}			
			
			
		}
	}
	
	//Is the User a member of the organisation that created the task or has the user claimed the task
	public static function authUserOrOrgForClaimedTask($request, $response, $route)
	{			
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
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
			if($taskId != null) {
				$tasks = TaskDao::getTask($taskId);
				$task = $tasks[0];
			}
			$projectId = $task->getProjectId();
			$project = null;
			if ($projectId != null) {
				$projects = ProjectDao::getProject($projectId);
				$project = $projects[0];
			}
			
			$orgId = $project->getOrganisationId();
			
    		$hasTask = TaskDao::hasUserClaimedTask($userId, $taskId);
			
			if($hasTask || OrganisationDao::isMember($orgId, $userId) || AdminDao::isAdmin($userId, $orgId)) {
			 	return true;
			}
			else {
	            Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
                "The user does not have permission to acess the current resource");
			}	
		}
	}
	
	//Test if the User is a member of the Organisation that created the task or has worked on one of the tasks of which this is a prerequisite
	public static function authenticateUserToSubmitReview($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
	        if (self::isSiteAdmin($user->getId())) {
	        	return true;
	        }
			
			$userId = $user->getId();
			$params = $route->getParams();
			
			$review = $request->getBody();
			$format = $params['format'];
            $client = new APIHelper($format);			
            $review = $client->deserialize($review, "TaskReview");
			$taskId = $review->getTaskId();
			$projectId = $review->getProjectId();			
			
			$hasFollowupTask = FALSE;
			/*
			 * If the taskId is null this indicates the user is not reviewing a task but the project file itself
			 * all users who have claimed a task on the project can review it esentialy but it may not be accessable through the UI for all cases 
			 */
			if(!is_null($taskId)) {
				$nextTasks = TaskDao::getTasksFromPreReq($taskId, $projectId);
				$nextTask = $nextTasks[0];
				
				if (!is_null($nextTask)) {
					if (TaskDao::hasUserClaimedTask($userId, $nextTask->getId())) {
						$hasFollowupTask = TRUE;
					}
				}
			}
			else 
			{
				$userTasks = TaskDao::getUserTasks($userId);
				
				foreach($userTasks as $task) {
					$testProjectId = $task->getProjectId();
					if($testProjectId == $projectId) {
						$hasFollowupTask = TRUE;
					}
					
				}
			}
			
			if ($projectId != null) {
				$projects = ProjectDao::getProject($projectId);
				$project = $projects[0];
			}
			
			$orgId = $project->getOrganisationId();
			
			if($hasFollowupTask || OrganisationDao::isMember($orgId, $userId) || AdminDao::isAdmin($userId, $orgId)) {
			 	error_log("print sucess");
			 	return true;
			}
			else {
	            Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
                "The user does not have permission to acess the current resource");
			}	
			
		}
	}

	// Has the User claimed a task on this project or is the user a member of the organisation that created the project
	public static function authenticateUserOrOrgForProjectTask($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
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
			$tasks = ProjectDao::getProjectTasks($projectId);
			$hasTask = FALSE;
			if (!is_null($tasks)) {
                foreach ($tasks as $taskObject) {
                    if (TaskDao::hasUserClaimedTask($userId, $taskObject->getId())) {
                        $hasTask = TRUE;
					}
				}
			}
						
			$project = null;
			if ($projectId != null) {
				$projects = ProjectDao::getProject($projectId);
				$project = $projects[0];
			}
			
			$orgId = $project->getOrganisationId();
			if ($hasTask || ($orgId != null && (OrganisationDao::isMember($orgId, $userId) || AdminDao::isAdmin($userId, $orgId)))) {
				return true;
			}
			else {
				Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
			}
		}	
	}

	//Is the current user a member of the Organisation who created the Badge in question
	public static function authenticateUserForOrgBadge($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
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
    			$badges = BadgeDao::getBadge($badgeId);
	    		$badge = $badges[0];
			}
			
			$orgId = $badge->getOwnerId();

					
			// cases where the orgId is null signify a system badge
			// badge ids 6, 7 and 8 refer to the user controlled system badges
			if($orgId != null && (OrganisationDao::isMember($orgId, $userId) || AdminDao::isAdmin($userId, $orgId))) {
				return true;
			} elseif ($orgId == null && in_array($badgeId, array(6, 7, 8))) {
                return true;
            } else {
	            Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
	        }
		}		
	}

	//Does the user id match the current user or is the current user a member of the Organisation who created the Badge in question
	public static function authenticateUserOrOrgForOrgBadge($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
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
			$badges = BadgeDao::getBadge($badgeId);
			$badge = $badges[0];
			}
			
			$orgId = $badge->getOwnerId();
					
			if($userId == $user->getId()) {
				return true;
			}
			
   			/*
    		 * currently this checks if the orgId is not Null
    		 * cases where the orgId is null signify a system badge
    		 * using this middleware function will lead to errors unless those are accounted for
    		 */
			else if($orgId != null && (OrganisationDao::isMember($orgId, $userId) || AdminDao::isAdmin($userId, $orgId))) {
				return true;
			}
			else {
	            Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
	        }
		}		
	}

	
	// Does User have required badge
	public static function authenticateUserHasBadge($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
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
			if ($badgeId != null && BadgeDao::validateUserBadge($userId, $badgeId)) {
				return true;
			}
			else {
				Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
			}
		}
	}
	
	/*
	 * Test if the task the user is trying to claim has already been claimed
	 * Prevents two claiments on the same task, admins will still be able to claim a task in all cases 
	 */	
	public static function authenticateTaskNotClaimed($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
	        if (self::isSiteAdmin($user->getId())) {
	        	return true;
	        }
			$params = $route->getParams();
			
			$task = $request->getBody();
			$format = $params['format'];
            $client = new APIHelper($format);			
            $task = $client->deserialize($task, "Task");			
			
			$taskId = $task->getId();
			
			
			$TaskIsUnclaimed = FALSE;
			$possibleUser = TaskDao::getUserClaimedTask($taskId);
			if(is_null($possibleUser)) {
				$TaskIsUnclaimed = TRUE;
			}
						
			if ($TaskIsUnclaimed) {
				return true;
			}
			else {
				Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "Unable to claim task. This Task has been claimed by another user");
			}
		}
	}
	


	
//    public static function authenticateUserForTask($request, $response, $route) 
//    {
//        if (self::isSiteAdmin()) {
//            return true;
//        }
//
//        $params = $route->getParams(); 
//
//        self::authUserIsLoggedIn();
//        $user_id = UserSession::getCurrentUserID();
//        $claimant = null;
//        if ($params !== null) {
//            $task_id = $params['task_id'];
//            $claimant =TaskDao::getUserClaimedTask($task_id);             
//        }
//        return !$claimant || $user_id == $claimant->getId();
//    }

//    public static function authUserForOrg($request, $response, $route) 
//    {
//        if (self::isSiteAdmin()) {
//            return true;
//        }
//        $user_id = UserSession::getCurrentUserID();
//        $params = $route->getParams();
//       if ($params !== null) {
//            $org_id = $params['org_id'];
//            if ($user_id) {
//                $user_orgs =OrganisationDao::getOrgByUser($user_id);
//                if (!is_null($user_orgs)) {
//                    foreach ($user_orgs as $orgObject) {
//                        if ($orgObject->getId() == $org_id) {
//                            return true;
//                       }
//                    }
//                }
//            }
//        }
//        
//       self::notFound();
//    }

    /*
     *  Middleware for ensuring the current user belongs to the Org that uploaded the associated Task
     *  Used for altering task details
     */
//    public static function authUserForOrgTask($request, $response, $route) 
//    {
//        if (self::isSiteAdmin()) {
//            return true;
//        }
//
//        
//        $params= $route->getParams();
//        if ($params != null) {
//            $task_id = $params['task_id'];
//            $task = TaskDao::getTask($task_id);
//            $task = is_array($task)?$task[0]:$task;
//            $project =ProjectDao::getProject($task->getProjectId());
//            $project = is_array($project)?$project[0]:$project;
//            $org_id = $project->getOrganisationId();
//            $user_id = UserSession::getCurrentUserID();
//
//            if ($user_id && OrganisationDao::isMember($org_id, $user_id)) {
//                return true;
//            }
//        }
//       
//        self::notFound();
//    } 
//    
//    public static function authUserForOrgProject($request, $response, $route) 
//    {                        
//        if ($this->isSiteAdmin()) {
//            return true;
//        }
//
//        $params = $route->getParams();
//        $userDao = new UserDao();
//        $projectDao = new ProjectDao();
//        
//        if ($params != null) {
//            $user_id = UserSession::getCurrentUserID();
//            $project_id = $params['project_id'];   
//            $userOrgs = $userDao->getUserOrgs($user_id);
//            $project = $projectDao->getProject($project_id); 
//            $project_orgid = $project->getOrganisationId();
//
//            if($userOrgs) {
//                foreach($userOrgs as $org)
//                {                
//                    if($org->getId() == $project_orgid) {
//                        return true;
//                    }
//                }
//            }
//        }
//        self::notFound();
//    }    
//
//    public static function authUserForTaskDownload($request, $response, $route)
//    {
//        if ($this->isSiteAdmin()) {
//            return true;
//        }
//
//        $taskDao = new TaskDao();
//        $projectDao = new ProjectDao();
//        $userDao = new UserDao();
//
//        $params= $route->getParams();
//        if ($params != null) {
//            $task_id = $params['task_id'];
//            $task = $taskDao->getTask($task_id);
////            if($taskDao->getUserClaimedTask($task_id) && $task->getStatus() != TaskStatusEnum::COMPLETE) return true;
//            if($taskDao->getUserClaimedTask($task_id)) return true;
//
//            $project = $projectDao->getProject($task->getProjectId());
//            
//            $org_id = $project->getOrganisationId();
//            $user_id = UserSession::getCurrentUserID();
//
//            if ($user_id) {
//                $user_orgs = $userDao->getUserOrgs($user_id);
//                if (!is_null($user_orgs)) {
//                    foreach ($user_orgs as $orgObject) {
//                        if ($orgObject->getId() == $org_id) {
//                            return true;
//                        }
//                    }
//                }                
//            }
//        }
//       
//        self::notFound();
//    }
    
    
    
    
}

?>
