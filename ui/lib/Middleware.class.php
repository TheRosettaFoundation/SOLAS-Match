<?php

require_once __DIR__."/../../Common/lib/APIHelper.class.php";

class Middleware
{
    public function authUserIsLoggedIn()
    {
        $app = Slim::getInstance();
        
        $this->isUserBanned();        
        if (!UserSession::getCurrentUserID()) {
            $app->flash('error', "Login required to access page");
            $app->redirect($app->urlFor('login'));
        }

        return true;
    }    
    
    public static function notFound()
    {
        $app = Slim::getInstance();
        $app->flash('error', "You do not have sufficient privileges to access this page or the page does not exist.");
        $app->redirect($app->urlFor('home'));
    }
    
    public function authenticateSiteAdmin()
    {
        if(!$this->isSiteAdmin()) {
            self::notFound();          
        }
        return true;
    }
    
    private function isSiteAdmin()
    {
        $this->isUserBanned(); 
        if(is_null(UserSession::getCurrentUserID())) return false;
        $adminDao = new AdminDao();
        return $adminDao->isSiteAdmin(UserSession::getCurrentUserID());
    }

    public function authenticateUserForTask($request, $response, $route) 
    {
        if ($this->isSiteAdmin()) {
            return true;
        }

        $app = Slim::getInstance();
        $taskDao = new TaskDao();
        $params = $route->getParams(); 

        $this->authUserIsLoggedIn();
        $user_id = UserSession::getCurrentUserID();
        $claimant = null;
        if ($params !== null) {
            $task_id = $params['task_id'];
            $claimant = $taskDao->getUserClaimedTask($task_id);             
        }
        if ($claimant) {
            if ($user_id != $claimant->getId()) {
                $app->flash('error', 'This task has been claimed by another user');
                $app->redirect($app->urlFor('home'));
            }

        }
    }

    public function authUserForOrg($request, $response, $route) 
    {
        if ($this->isSiteAdmin()) {
            return true;
        }

        $userDao = new UserDao();
        $orgDao = new OrganisationDao();

        $user_id = UserSession::getCurrentUserID();
        $params = $route->getParams();
        if ($params !== null) {
            $org_id = $params['org_id'];
            if ($user_id) {
                $user_orgs = $userDao->getUserOrgs($user_id);
                if (!is_null($user_orgs)) {
                    foreach ($user_orgs as $orgObject) {
                        if ($orgObject->getId() == $org_id) {
                            return true;
                        }
                    }
                }
            }
        }
        
        self::notFound();
    }

    /*
     *  Middleware for ensuring the current user belongs to the Org that uploaded the associated Task
     *  Used for altering task details
     */
    public function authUserForOrgTask($request, $response, $route) 
    {
        if ($this->isSiteAdmin()) {
            return true;
        }

        $taskDao = new TaskDao();
        $projectDao = new ProjectDao();
        $userDao = new UserDao();

        $params= $route->getParams();
        if ($params != null) {
            $task_id = $params['task_id'];
            $task = $taskDao->getTask($task_id);
            $project = $projectDao->getProject($task->getProjectId());
            
            $org_id = $project->getOrganisationId();
            $user_id = UserSession::getCurrentUserID();

            if ($user_id) {
                $user_orgs = $userDao->getUserOrgs($user_id);
                if (!is_null($user_orgs)) {
                    foreach ($user_orgs as $orgObject) {
                        if ($orgObject->getId() == $org_id) {
                            return true;
                        }
                    }
                }                
            }
        }
       
        self::notFound();
    } 
    
    public function authUserForOrgProject($request, $response, $route) 
    {                        
        if ($this->isSiteAdmin()) {
            return true;
        }

        $params = $route->getParams();
        $userDao = new UserDao();
        $projectDao = new ProjectDao();
        
        if ($params != null) {
            $user_id = UserSession::getCurrentUserID();
            $project_id = $params['project_id'];   
            $userOrgs = $userDao->getUserOrgs($user_id);
            $project = $projectDao->getProject($project_id); 
            $project_orgid = $project->getOrganisationId();

            if($userOrgs) {
                foreach($userOrgs as $org)
                {                
                    if($org->getId() == $project_orgid) {
                        return true;
                    }
                }
            }
        }
        self::notFound();
    }    

    public function authUserForTaskDownload($request, $response, $route)
    {
        if ($this->isSiteAdmin()) {
            return true;
        }

        $taskDao = new TaskDao();
        $projectDao = new ProjectDao();
        $userDao = new UserDao();

        $params= $route->getParams();
        if ($params != null) {
            $task_id = $params['task_id'];
            $task = $taskDao->getTask($task_id);
//            if($taskDao->getUserClaimedTask($task_id) && $task->getStatus() != TaskStatusEnum::COMPLETE) return true;
            if($taskDao->getUserClaimedTask($task_id)) return true;

            $project = $projectDao->getProject($task->getProjectId());
            
            $org_id = $project->getOrganisationId();
            $user_id = UserSession::getCurrentUserID();

            if ($user_id) {
                $user_orgs = $userDao->getUserOrgs($user_id);
                if (!is_null($user_orgs)) {
                    foreach ($user_orgs as $orgObject) {
                        if ($orgObject->getId() == $org_id) {
                            return true;
                        }
                    }
                }                
            }
        }
       
        self::notFound();
    }
    
    public function isUserBanned()
    {        
        $adminDao = new AdminDao();        
        if($adminDao->isUserBanned(UserSession::getCurrentUserID())) {
            $app = Slim::getInstance();
            UserSession::destroySession();
            $app->flash('error', "This user account has been banned.");
            $app->redirect($app->urlFor('home'));
        }       
    }
}
