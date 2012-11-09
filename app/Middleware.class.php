<?php

class Middleware
{
    public static function authUserIsLoggedIn()
    {
        $app = Slim::getInstance();
        
        if(UserSession::getCurrentUserID()) {
            $app->flash('error', "Login required to access page");
            $app->redirect($app->urlFor('login'));
        }
        
        return true;
    }

    public static function authenticateUserForTask($request, $response, $route) 
    {
        $app = Slim::getInstance();
        $params = $route->getParams();
        if($params !== NULL) {
            $task_id = $params['task_id'];
            $task_dao = new TaskDao();
            
            //$request = APIClient::API_VERSION."/users/$user_id/tasks";
            //$response = $client->call($request, HTTP_Request2::METHOD_POST, $task);             
            
            if($task_dao->taskIsClaimed($task_id)) {
                $user_dao = new UserDao();
                $current_user = $user_dao->getCurrentUser();
                if(!is_object($current_user)) {
                    $app->flash('error', 'Login required to access page');
                    $app->redirect($app->urlFor('login'));
                }
                if(!$task_dao->hasUserClaimedTask($current_user->getUserId(), $task_id)) {
                    $app->flash('error', 'This task has been claimed by another user');
                    $app->redirect($app->urlFor('home'));
                }
            }
            return true;
        } else {
            $app->flash('error', 'Unable to find task');
            $app->redirect($app->urlFor('home'));
        }
    }

    public static function authUserForOrg($request, $response, $route) 
    {
        $params = $route->getParams();
        if($params !== NULL) {
            $org_id = $params['org_id'];
            $user_dao = new UserDao();
            $user = $user_dao->getCurrentUser();
            if(is_object($user)) {
                $user_orgs = $user_dao->findOrganisationsUserBelongsTo($user->getUserId());
                if(!is_null($user_orgs)) {
                    if(in_array($org_id, $user_orgs)) {
                        return true;
                    }
                }
            }
        }
        
        $app = Slim::getInstance();
        $org_name = 'this organisation';
        if(isset($org_id)) {
            $org_dao = new OrganisationDao();
            $org = $org_dao->find(array('id' => $org_id));
            $org_name = "<a href=\"".$app->urlFor('org-public-profile', array('org_id' => $org_id))."\">".$org->getName()."</a>";
        }
        $app->flash('error', "You are not authorised to view this profile. Only members of ".$org_name." may view this page.");
        $app->redirect($app->urlFor('home'));
    }

    /*
     *  Middleware for ensuring the current user belongs to the Org that uploaded the associated Task
     *  Used for altering task details
     */
    public static function authUserForOrgTask($request, $response, $route) 
    {
        $params= $route->getParams();
        if($params != NULL) {
            $task_id = $params['task_id'];
            $task_dao = new TaskDao();
            $task = $task_dao->find(array('task_id' => $task_id));
            
            $org_id = $task->getOrganisationId();
            $user_dao = new UserDao();
            $user = $user_dao->getCurrentUser();
            if(is_object($user)) {
                $user_orgs = $user_dao->findOrganisationsUserBelongsTo($user->getUserId());
                if(!is_null($user_orgs) && in_array($org_id, $user_orgs)) {
                    return true;
                }
            }
        }
       
        $app = Slim::getInstance();
        $org_name = 'this organisation';
        if(isset($org_id)) {
            $org_dao = new OrganisationDao();
            $org = $org_dao->find(array('id' => $org_id));
            $org_name = "<a href=\"".$app->urlFor('org-public-profile', array('org_id' => $org_id))."\">".$org->getName()."</a>";
        }
        $app->flash('error', "You are not authorised to view this page. Only members of ".$org_name." may view this page.");
        $app->redirect($app->urlFor('home'));
    }

    public static function authUserForTaskDownload($request, $response, $route)
    {
        $params = $route->getParams();
        if($params != NULL) {
            $task_id = $params['task_id'];
            $task_dao = new TaskDao();
            $task = $task_dao->find(array('task_id' => $task_id));

            $user_dao = new UserDao();
            $user = $user_dao->getCurrentUser();
            $user_orgs = $user_dao->findOrganisationsUserBelongsTo($user->getUserId());
            
            //If the task has not been claimed yet then anyone can download it
            if(!$task_dao->taskIsClaimed($task_id)) {
                return true;
            } elseif($task_dao->hasUserClaimedTask($user->getUserId(), $task_id)) {
                return true;
            } elseif(!is_null($user_orgs) && in_array($task->getOrganisationId(), $user_orgs)) {
                return true;
            }
        }

        $app = Slim::getInstance();
        $app->flash('error', "You are not authorised to download this task");
        $app->redirect($app->urlFor('home'));
    }
}
