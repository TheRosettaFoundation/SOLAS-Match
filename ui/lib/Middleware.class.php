<?php

namespace SolasMatch\UI\Lib;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/lib/APIHelper.class.php";

class Middleware
{
    public function authUserIsLoggedIn()
    {
        $app = \Slim\Slim::getInstance();
        
        $this->isUserBanned();
        if (!Common\Lib\UserSession::getCurrentUserID()) {
            Common\Lib\UserSession::setReferer(
                $app->request()->getUrl().$app->request()->getScriptName().$app->request()->getPathInfo()
            );
            $app->flash('error', Localisation::getTranslation('common_login_required_to_access_page'));
            $app->redirect($app->urlFor('login'));
        }

        return true;
    }
    
    public static function notFound()
    {
        $app = \Slim\Slim::getInstance();
        $app->flash('error', Localisation::getTranslation('common_error_not_exist'));
        $app->redirect($app->urlFor('home'));
    }
    
    public function isSiteAdmin()
    {
        $this->isUserBanned();
        if (is_null(Common\Lib\UserSession::getCurrentUserID())) {
            return false;
        }
        $adminDao = new DAO\AdminDao();
        return $adminDao->isSiteAdmin(Common\Lib\UserSession::getCurrentUserID());
    }

    public function authIsSiteAdmin()
    {
        if ($this->isSiteAdmin()) {
            return true;
        }

        $app = \Slim\Slim::getInstance();
        $app->flash('error', Localisation::getTranslation('common_login_required_to_access_page'));
        $app->redirect($app->urlFor('login'));
    }

    public function authenticateUserForTask(\Slim\Route $route)
    {
        if ($this->isSiteAdmin()) {
            return true;
        }

        $app = \Slim\Slim::getInstance();
        $taskDao = new DAO\TaskDao();
        $params = $route->getParams();

        $this->authUserIsLoggedIn();
        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $claimant = null;
        if ($params !== null) {
            $task_id = $params['task_id'];
            $claimant = $taskDao->getUserClaimedTask($task_id);
        }
        if ($claimant) {
            if ($user_id != $claimant->getId()) {
error_log("Already claimed... task_id: $task_id, user_id: $user_id, claimant: " . $claimant->getId());
                $app->flash('error', Localisation::getTranslation('common_error_already_claimed'));
                $app->redirect($app->urlFor('home'));
            }
        }
    }


    public function authUserForOrg(\Slim\Route $route)
    {
        if ($this->isSiteAdmin()) {
            return true;
        }
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();

        $user_id = Common\Lib\UserSession::getCurrentUserID();
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

    public function authUserForOrgTask(\Slim\Route $route)
    {
        if ($this->isSiteAdmin()) {
            return true;
        }

        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $userDao = new DAO\UserDao();

        $params= $route->getParams();
        if ($params != null) {
            $task_id = $params['task_id'];
            $task = $taskDao->getTask($task_id);
            $project = $projectDao->getProject($task->getProjectId());
            
            $org_id = $project->getOrganisationId();
            $user_id = Common\Lib\UserSession::getCurrentUserID();

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
    

    public function authUserForOrgProject(\Slim\Route $route)
    {
        if ($this->isSiteAdmin()) {
            return true;
        }

        $params = $route->getParams();
        $userDao = new DAO\UserDao();
        $projectDao = new DAO\ProjectDao();
        
        if ($params != null) {
            $user_id = Common\Lib\UserSession::getCurrentUserID();
            $project_id = $params['project_id'];
            $userOrgs = $userDao->getUserOrgs($user_id);
            $project = $projectDao->getProject($project_id);
            $project_orgid = $project->getOrganisationId();

            if ($userOrgs) {
                foreach ($userOrgs as $org) {
                    if ($org->getId() == $project_orgid) {
                        return true;
                    }
                }
            }
        }
        self::notFound();
    }

    public function authUserForProjectImage(\Slim\Route $route)
    {
        if ($this->isSiteAdmin()) {
            return true;
        }
        
        $params = $route->getParams();
        $userDao = new DAO\UserDao();
        $projectDao = new DAO\ProjectDao();
        
        if ($params != null) {
            $project_id = $params['project_id'];
            $project = $projectDao->getProject($project_id);
            $projectImageApprovedAndUploaded = $project->getImageApproved() && $project->getImageUploaded();
            
            if ($projectImageApprovedAndUploaded) {
                return true;
            }
        }
        self::notFound();
    }

    public function authUserForTaskDownload(\Slim\Route $route)
    {
        if ($this->isSiteAdmin()) {
            return true;
        }

        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $userDao = new DAO\UserDao();

        $params= $route->getParams();
        if ($params != null) {
            $task_id = $params['task_id'];
            $task = $taskDao->getTask($task_id);
            if ($taskDao->getUserClaimedTask($task_id)) {
                return true;
            }

            $project = $projectDao->getProject($task->getProjectId());
            $org_id = $project->getOrganisationId();
            $user_id = Common\Lib\UserSession::getCurrentUserID();

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
        $adminDao = new DAO\AdminDao();
        if ($adminDao->isUserBanned(Common\Lib\UserSession::getCurrentUserID())) {
            $app = \Slim\Slim::getInstance();
            Common\Lib\UserSession::destroySession();
            $app->flash('error', Localisation::getTranslation('common_this_user_account_has_been_banned'));
            $app->redirect($app->urlFor('home'));
        }
    }
    
    public function isBlacklisted(\Slim\Route $route)
    {
        $isLoggedIn = $this->authUserIsLoggedIn();
        if ($isLoggedIn) {
            $params = $route->getParams();
            if (!is_null($params)) {
                $taskId = $params['task_id'];
                $userId = Common\Lib\UserSession::getCurrentUserID();
                $userDao = new DAO\UserDao();
                $isBlackListed = $userDao->isBlacklistedForTask($userId, $taskId);
                
                //Is the user blacklisted for the task?
                if ($isBlackListed) {
                    $taskDao = new DAO\TaskDao();
                    $task = $taskDao->getTask($taskId);
                    $app = \Slim\Slim::getInstance();
                    $message = null;
                    
                    $isBlackListedByAdmin = $userDao->isBlacklistedForTaskByAdmin($userId, $taskId);
                    if (!$isBlackListedByAdmin) {
                        //If it is a desegmentation task, user must have been blacklisted for it because they
                        //have worked on a prerequisite task for it.
                        if ($task->getTaskType() == Common\Enums\TaskTypeEnum::DESEGMENTATION) {
                            $message = Localisation::getTranslation("common_error_cannot_claim_desegmentation");
                        } else {
                            $message = Localisation::getTranslation('common_error_cannot_reclaim');
                        }
                    } else {
                        //An admin has previously revoked this task from the user.
                        $message = Localisation::getTranslation('common_error_cannot_reclaim_admin_revoked');
                    }
                    $app->flash(
                        'error',
                        sprintf(
                            $message,
                            $app->urlFor("task-claimed", array("task_id" => $taskId)),
                            $task->getTitle()
                        )
                    );
                    $app->response()->redirect($app->urlFor('home'));
                } else {
                    return true;
                }
            }
        }
    }
}
