<?php

class Middleware
{
    public static function authUserIsLoggedIn()
    {
        $app = Slim::getInstance();
        
        if (!UserSession::getCurrentUserID()) {
            $app->flash('error', "Login required to access page");
            $app->redirect($app->urlFor('login'));
        }        
        return true;
    }

    public static function authenticateUserForTask($request, $response, $route) 
    {
        $app = Slim::getInstance();
        $params = $route->getParams();        

        if ($params !== null) {
            $client = new APIClient();
            $task_id = $params['task_id'];
            $request = APIClient::API_VERSION."/tasks/$task_id/claimed";
            $taskClaimed = $client->call($request, HTTP_Request2::METHOD_GET);             
            
            if ($taskClaimed) {
                $user_id = UserSession::getCurrentUserID();
                if (is_null($user_id)) {
                    $app->flash('error', 'Login required to access page');
                    $app->redirect($app->urlFor('login'));
                }
                $request = APIClient::API_VERSION."/tasks/$task_id/claimed";
                $userClaimedTask = $client->call($request, HTTP_Request2::METHOD_GET,
                                                null, array("userID" => $user_id));

                if (!$userClaimedTask) {
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
        $client = new APIClient();        
        $user_id = UserSession::getCurrentUserID();
        $params = $route->getParams();
        if ($params !== null) {
            $org_id = $params['org_id'];
            if ($user_id) {
                $user_orgs = array();
                $request = APIClient::API_VERSION."/users/$user_id/orgs";
                $user_orgs = $client->castCall(array('Organisation'), $request, HTTP_Request2::METHOD_GET);
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
            $request = APIClient::API_VERSION."/orgs/$org_id";
            $org = $client->castCall('Organisation', $request, HTTP_Request2::METHOD_GET);
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
    public static function authUserForOrgTask($request, $response, $route) 
    {
        $client = new APIClient();
        $params= $route->getParams();
        if ($params != null) {
            $task_id = $params['task_id'];
            $request = APIClient::API_VERSION."/tasks/$task_id";
            $response = $client->call($request, HTTP_Request2::METHOD_GET);   
            $task = $client->cast('Task', $response);

            $request = APIClient::API_VERSION."/projects/".$task->getProjectId();
            $response = $client->call($request);
            $project = $client->cast("Project", $response);
            
            $org_id = $project->getOrganisationId();
            $user_id = UserSession::getCurrentUserID();

            if ($user_id) {
                $user_orgs = array();
                $request = APIClient::API_VERSION."/users/$user_id/orgs";
                $user_orgs = $client->castCall(array('Organisation'), $request, HTTP_Request2::METHOD_GET);
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
            $request = APIClient::API_VERSION."/orgs/$org_id";
            $org = $client->castCall('Organisation', $request, HTTP_Request2::METHOD_GET);
            $org_name = "<a href=\"".$app->urlFor('org-public-profile',
                                                    array('org_id' => $org_id))."\">".$org->getName()."</a>";
        }
        $app->flash('error', "You are not authorised to view this page.
                    Only members of ".$org_name." may view this page.");
        $app->redirect($app->urlFor('home'));
    } 
    
    public static function authUserForOrgProject($request, $response, $route) 
    { 
        //todo 
        return true;
    }    

    public static function authUserForTaskDownload($request, $response, $route)
    {
        $params = $route->getParams();
        if ($params != null) {
            $client = new APIClient();            
            $task_id = $params['task_id'];
            $user_id = UserSession::getCurrentUserID();
            
            $request = APIClient::API_VERSION."/tasks/$task_id";
            $task = $client->castCall('Task', $request, HTTP_Request2::METHOD_GET);

            $user_orgs = array();
            $request = APIClient::API_VERSION."/users/$user_id/orgs";
            $user_orgs = $client->castCall(array('Organisation'), $request, HTTP_Request2::METHOD_GET);
            
            //If the task has not been claimed yet then anyone can download it
            $request = APIClient::API_VERSION."/tasks/$task_id/claimed";
            $taskClaimed = $client->call($request, HTTP_Request2::METHOD_GET);            
            $request = APIClient::API_VERSION."/tasks/$task_id/claimed";
            $userClaimedTask = $client->call($request, HTTP_Request2::METHOD_GET, $user_id);
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
