<?php

class AdminRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();
        $middleware = new Middleware();
        
        $app->get("/admin/:user_id", array($middleware, "authUserIsLoggedIn"),
        array($middleware, "isSiteAdmin"), array($this, "adminDashboard"))->via("POST")->name("site-admin-dashboard");
        
        $app->get("/admin/list", array($middleware, "authUserIsLoggedIn"),
        array($this, "adminList"))->via("POST")->name("admin-list");
    }
    
    public function adminDashboard($userId)
    {
        $app = Slim::getInstance();
        
        if($post = $app->request()->post()) {
            $userDao = new UserDao();
            $adminDao = new AdminDao();
            if(isset($post['addAdmin'])) {
                $user = $userDao->getUserByEmail($post['userEmail']);
                if(is_object($user)) {
                    $adminDao->createSiteAdmin($user->getId());
                }
            }
            if(isset($post['revokeAdmin'])) {
                $adminDao->removeSiteAdmin($post['userId']);
            }
            
            if(isset($post['banOrg']) && $post['orgName'] != '') {
                $orgDao = new OrganisationDao();
                $bannedOrg = new BannedOrganisation();
                $org = $orgDao->getOrganisationByName(urlencode($post['orgName']));
                
                $bannedOrg->setOrgId($org->getId());
                $bannedOrg->setUserIdAdmin($userId);
                $bannedOrg->setBanType($post['banTypeOrg']);
                if(isset($post['banReasonOrg'])) $bannedOrg->setComment($post['banReasonOrg']);
                $adminDao->banOrg($bannedOrg);
            }
            if(isset($post['banUser']) && $post['userEmail'] != '') {
                $bannedUser = new BannedUser();
                $user = $userDao->getUserByEmail(urlencode($post['userEmail']));
                
                $bannedUser->setUserId($user->getId());
                $bannedUser->setUserIdAdmin($userId);
                $bannedUser->setBanType($post['banTypeUser']);
                if(isset($post['banReasonUser'])) $bannedUser->setComment($post['banReasonUser']);
                $adminDao->banUser($bannedUser);
            }         
            
            if(isset($post['unBanOrg']) && $post['orgId'] != '') {
                $adminDao->unBanOrg($post['orgId']);
            }
            if(isset($post['unBanUser']) && $post['userId'] != '') {
                $adminDao->unBanUser($post['userId']);
            } 
            if(isset($post['deleteUser']) && $post['userEmail'] != '') {
                $user = $userDao->getUserByEmail(urlencode($post['userEmail']));
                $userDao->deleteUser($user->getId());
            }
            
            
        }               
        
        $siteName = Settings::get("site.name");
        
        $adminDao = new AdminDao();
        $userDao = new UserDao();
        $orgDao = new OrganisationDao();
        
        $adminList = $adminDao->getSiteAdmins();        
        
        $bannedOrgList = $adminDao->getBannedOrgs();
        if($bannedOrgList) {
            foreach($bannedOrgList as $bannedOrg) {
                $bannedOrg['org'] = $orgDao->getOrganisation($bannedOrg->getOrgId());
                $bannedOrg['adminUser'] = $userDao->getUser($bannedOrg->getUserIdAdmin());
            }
        }
        
        $bannedUserList = $adminDao->getBannedUsers();
        if($bannedUserList) {
            foreach($bannedUserList as $bannedUser) {
                $bannedUser['user'] = $userDao->getUser($bannedUser->getUserId());
                $bannedUser['adminUser'] = $userDao->getUser($bannedUser->getUserIdAdmin());
            }
        }
     
        $extra_scripts = "";
        $extra_scripts .= file_get_contents(__DIR__."/../js/site-admin.dashboard.js");

        $app->view()->appendData(array(
                    "adminUserId"   => $userId,
                    "siteName"      => $siteName,
                    "adminList"     => $adminList,
                    "bannedOrgList" => $bannedOrgList,
                    "bannedUserList"=> $bannedUserList,
                    "current_page"  => 'site-admin-dashboard',
                    "extra_scripts" => $extra_scripts
        ));

        $app->render("admin/site-admin.dashboard.tpl");
    }

    public function adminList()
    {
        $app = Slim::getInstance();

        $adminList = array();
        $adminDao = new AdminDao();
        
        if($post = $app->request()->post()) {
            $adminDao = new AdminDao();
            if(isset($post['revokeAdmin'])) {
                $adminDao->removeSiteAdmin($post['userId']);
            }
        }
        
        $admins = $adminDao->getSiteAdmins();
        if($admins) {
            foreach($admins as $admin) {
                $adminList[] = $admin;
            }
        }           
       
        $siteName = Settings::get("site.name");

        $app->view()->appendData(array(
                "current_page"  => "admin-list",
                "adminList"     => $adminList,
                "siteName"      => $siteName
        ));
        
        $app->render("admin/admin.site-admins-list.tpl");
    }
}

$route_handler = new AdminRouteHandler();
$route_handler->init();
unset ($route_handler);