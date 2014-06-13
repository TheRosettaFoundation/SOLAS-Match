<?php

namespace SolasMatch\UI\RouteHandlers;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\Common as Common;

class AdminRouteHandler
{
    public function init()
    {
        $app = \Slim\Slim::getInstance();
        $middleware = new Lib\Middleware();
                          
        $app->get(
            "/admin/",
            array($middleware, "isSiteAdmin"),
            array($this, "adminDashboard")
        )->via("POST")->name("site-admin-dashboard");

    }
    
    public function adminDashboard()
    {
        $app = \Slim\Slim::getInstance();
        $userId = Common\Lib\UserSession::getCurrentUserID();
        
        if ($post = $app->request()->post()) {
            $userDao = new DAO\UserDao();
            $adminDao = new DAO\AdminDao();
            if (isset($post['addAdmin'])) {
                $user = $userDao->getUserByEmail($post['userEmail']);
                if (is_object($user)) {
                    $adminDao->createSiteAdmin($user->getId());
                }
            }
            if (isset($post['revokeAdmin'])) {
                $adminDao->removeSiteAdmin($post['userId']);
            }
            
            if (isset($post['banOrg']) && $post['orgName'] != '') {
                $orgDao = new DAO\OrganisationDao();
                $bannedOrg = new Common\Protobufs\Models\BannedOrganisation();
                $org = $orgDao->getOrganisationByName(urlencode($post['orgName']));
                
                $bannedOrg->setOrgId($org->getId());
                $bannedOrg->setUserIdAdmin($userId);
                $bannedOrg->setBanType($post['banTypeOrg']);
                if (isset($post['banReasonOrg'])) {
                    $bannedOrg->setComment($post['banReasonOrg']);
                }
                $adminDao->banOrg($bannedOrg);
            }
            if (isset($post['banUser']) && $post['userEmail'] != '') {
                $bannedUser = new Common\Protobufs\Models\BannedUser();
                $user = $userDao->getUserByEmail(urlencode($post['userEmail']));
                
                $bannedUser->setUserId($user->getId());
                $bannedUser->setUserIdAdmin($userId);
                $bannedUser->setBanType($post['banTypeUser']);
                if (isset($post['banReasonUser'])) {
                    $bannedUser->setComment($post['banReasonUser']);
                }
                $adminDao->banUser($bannedUser);
            }
            
            if (isset($post['unBanOrg']) && $post['orgId'] != '') {
                $adminDao->unBanOrg($post['orgId']);
            }
            if (isset($post['unBanUser']) && $post['userId'] != '') {
                $adminDao->unBanUser($post['userId']);
            }
            if (isset($post['deleteUser']) && $post['userEmail'] != '') {
                $user = $userDao->getUserByEmail(urlencode($post['userEmail']));
                if (!is_null($user)) {
                    $userDao->deleteUser($user->getId());
                    $app->flashNow(
                        "deleteSuccess",
                        Lib\Localisation::getTranslation('site_admin_dashboard_successfully_deleted_user')
                    );
                } else {
                    $app->flashNow(
                        "deleteError",
                        Lib\Localisation::getTranslation('site_admin_dashboard_user_not_found')
                    );
                }
            }
            if (isset($post['revokeTask']) && $post['revokeTask'] != '') {
                $taskId = filter_var($post['taskId'], FILTER_VALIDATE_INT);
                $userToRevokeFrom = $userDao->getUserByEmail(urlencode($post['userEmail']));
                if ($taskId && !is_null($userToRevokeFrom)) {
                    $taskDao = new DAO\TaskDao();
                    $task = $taskDao->getTask($taskId);
                    $claimantId = $taskDao->getUserClaimedTask($taskId);
                    if ($claimantId != $userToRevokeFrom->getId()) {
                        //user specified did not claim task specified, handle error
                    } else {
                        //revoke task from user
                    }
                } 
            }
        }
        
        $adminDao = new DAO\AdminDao();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();
        
        $adminList = $adminDao->getSiteAdmins();
        
        $bannedOrgList = $adminDao->getBannedOrgs();
        if ($bannedOrgList) {
            foreach ($bannedOrgList as $bannedOrg) {
                $bannedOrg['org'] = $orgDao->getOrganisation($bannedOrg->getOrgId());
                $bannedOrg['adminUser'] = $userDao->getUser($bannedOrg->getUserIdAdmin());
            }
        }
        
        $bannedUserList = $adminDao->getBannedUsers();
        if ($bannedUserList) {
            foreach ($bannedUserList as $bannedUser) {
                $bannedUser['user'] = $userDao->getUser($bannedUser->getUserId());
                $bannedUser['adminUser'] = $userDao->getUser($bannedUser->getUserIdAdmin());
            }
        }

        $siteName = Common\Lib\Settings::get("site.name");
     
        $extra_scripts = "";
        $extra_scripts .= file_get_contents(__DIR__."/../js/site-admin.dashboard.js");

        $app->view()->appendData(array(
                    "adminUserId"   => $userId,
                    "adminList"     => $adminList,
                    "bannedOrgList" => $bannedOrgList,
                    "bannedUserList"=> $bannedUserList,
                    "current_page"  => 'site-admin-dashboard',
                    "siteName"      => $siteName,
                    "extra_scripts" => $extra_scripts
        ));

        $app->render("admin/site-admin.dashboard.tpl");
    }
}

$route_handler = new AdminRouteHandler();
$route_handler->init();
unset ($route_handler);
