<?php

require_once 'Common/lib/APIHelper.class.php';

class Middleware
{
    public function authUserIsLoggedIn()
    {
        $app = Slim::getInstance();
        
        if (!UserSession::getCurrentUserID()) {
            $app->flash('error', "Login required to access page");
            $app->redirect($app->urlFor('login'));
        }        
        return true;
    }

    public function isSiteAdmin()
    {
        $app = Slim::getInstance();

        $userDao = new UserDao();
        $ret = $userDao->isAdmin(UserSession::getCurrentUserID());
        return $ret;
    }

    public function authenticateUserForTask($request, $response, $route) 
    {
        if ($this->isSiteAdmin()) {
            return true;
        }

        $app = Slim::getInstance();
        $taskDao = new TaskDao();
        $params = $route->getParams(); 

        $user_id = UserSession::getCurrentUserID();
        if (is_null($user_id)) {
            $app->flash('error', 'Login required to access page');
            $app->redirect($app->urlFor('login'));
        }

        $claimant = null;
        if ($params !== null) {
            $task_id = $params['task_id'];
            $claimant = $taskDao->getUserClaimedTask($task_id);             
        }
        if ($claimant) {
            if ($user_id != $claimant->getUserId()) {
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
        
        $app = Slim::getInstance();
        $org_name = 'this organisation';
        if (isset($org_id)) {
            $siteApi = Settings::get("site.api");
            $request = "$siteApi/v0/orgs/$org_id";
            $org = $orgDao->getOrganisation(array('id' => $org_id));
            $org_name = "<a href=\"".$app->urlFor('org-public-profile',
                                                    array('org_id' => $org_id))."\">".$org->getName()."</a>";
        }
        $app->flash('error', "You are not authorised to view this profile.
                    Only members of ".$org_name." may view this page.");
        $app->redirect($app->urlFor('home'));
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
        $orgDao = new OrganisationDao();

        $params= $route->getParams();
        if ($params != null) {
            $task_id = $params['task_id'];
            $task = $taskDao->getTask(array('id' => $task_id));
            $project = $projectDao->getProject(array('id' => $task->getProjectId()));
            
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
       
        $app = Slim::getInstance();
        $org_name = 'this organisation';
        if (isset($org_id)) {
            $org = $orgDao->getOrganisation(array('id' => $org_id));
            $org_name = "<a href=\"".$app->urlFor('org-public-profile',
                                                    array('org_id' => $org_id))."\">".$org->getName()."</a>";
        }
        $app->flash('error', "You are not authorised to view this page.
                    Only members of ".$org_name." may view this page.");
        $app->redirect($app->urlFor('home'));
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
            $project = $projectDao->getProject(array('id' => $project_id)); 
            $project_orgid = $project->getOrganisationId();

            foreach($userOrgs as $org)
            {                
                if($org->getId() == $project_orgid) {
                    return true;
                }
            }
        }
        $app = Slim::getInstance();
        $app->flash('error', "Only organisation members are authorised to view this page.");
        $app->redirect($app->urlFor('home'));
    }    

    public function authUserForTaskDownload($request, $response, $route)
    {
        if ($this->isSiteAdmin()) {
            return true;
        }

        $params = $route->getParams();
        $taskDao = new TaskDao();
        $userDao = new UserDao();
        if ($params != null) {
            $task_id = $params['task_id'];
            $user_id = UserSession::getCurrentUserID();
            $task = $taskDao->getTask(array('id' => $task_id));
            $user_orgs = $userDao->getUserOrgs($user_id);
            
            //If the task has not been claimed yet then anyone can download it
            $taskClaimed = $taskDao->isTaskClaimed($task_id);            
            $userClaimedTask = $taskDao->isTaskClaimed($task_id, $user_id);
            if (!$taskClaimed) {
                return true;
            } elseif ($userClaimedTask) {
                return true;
            } elseif (!is_null($user_orgs)) {
                foreach ($user_orgs as $orgObject) {
                    if ($orgObject->getId() == $task->getOrganisationId()) {
                        return true;
                    }
                }                
            }
        }

        $app = Slim::getInstance();
        $app->flash('error', "You are not authorised to download this task");
        $app->redirect($app->urlFor('home'));
    }
}
