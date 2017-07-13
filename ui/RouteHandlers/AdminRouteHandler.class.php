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
            array($middleware, 'authIsSiteAdmin'),
            array($this, "adminDashboard")
        )->via("POST")->name("site-admin-dashboard");

        $app->get(
            '/all_users/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'all_users')
        )->via('POST')->name('all_users');

        $app->get(
            '/all_users_plain/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'all_users_plain')
        )->via('POST')->name('all_users_plain');

        $app->get(
            '/active_now/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'active_now')
        )->via('POST')->name('active_now');

        $app->get(
            '/active_users/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'active_users')
        )->via('POST')->name('active_users');

        $app->get(
            '/active_users_unique/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'active_users_unique')
        )->via('POST')->name('active_users_unique');

        $app->get(
            '/unclaimed_tasks/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'unclaimed_tasks')
        )->via('POST')->name('unclaimed_tasks');

        $app->get(
            '/user_languages/:code',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'user_languages')
        )->via('POST')->name('user_languages');

        $app->get(
            '/download_user_languages/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'download_user_languages')
        )->name('download_user_languages');

        $app->get(
            '/download_all_users/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'download_all_users')
        )->name('download_all_users');

        $app->get(
            '/download_active_users/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'download_active_users')
        )->name('download_active_users');

        $app->get(
            '/community_stats/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'community_stats')
        )->name('community_stats');
    }
    
    public function adminDashboard()
    {
        $app = \Slim\Slim::getInstance();
        $userId = Common\Lib\UserSession::getCurrentUserID();

        $sesskey = Common\Lib\UserSession::getCSRFKey();
        
        if ($post = $app->request()->post()) {
            Common\Lib\UserSession::checkCSRFKey($post, 'adminDashboard');

            $userDao = new DAO\UserDao();
            $adminDao = new DAO\AdminDao();
            $taskDao = new DAO\TaskDao();

            if (isset($post['verify'])) {
                if ($userDao->finishRegistrationManually($post['userEmail'])) {
                    $app->flashNow('verifySuccess', Lib\Localisation::getTranslation('email_verification_email_verification'));
                } else {
                    $app->flashNow('verifyError', Lib\Localisation::getTranslation('site_admin_dashboard_user_not_found'));
                }
            }

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
                    $task = $taskDao->getTask($taskId);
                    $claimantId = $taskDao->getUserClaimedTask($taskId)->getId();
                    if ($claimantId != $userToRevokeFrom->getId()) {
                        //user specified did not claim the task specified
                        $app->flashNow(
                            "revokeTaskError",
                            Lib\Localisation::getTranslation('site_admin_dashboard_revoke_task_error_no_claim')
                        );
                    } else {
                        $adminDao->revokeTaskFromUser($taskId, $userId);
                        $app->flashNow(
                            "revokeTaskSuccess",
                            Lib\Localisation::getTranslation('site_admin_dashboard_revoke_task_success'));
                    }
                } else {
                    //Invalid input supplied for user email and/or task id
                    $app->flashNow(
                        "revokeTaskError",
                        Lib\Localisation::getTranslation('site_admin_dashboard_revoke_task_error_invalid'));
                }
            }
        }
        
        $adminDao = new DAO\AdminDao();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();
        
        $adminList = $adminDao->getSiteAdmins();
        
        $bannedOrgList = $adminDao->getBannedOrgs();
        $bannedOrgNames = array();
        $orgBannerAdminNames = array();
        if ($bannedOrgList) {
            foreach ($bannedOrgList as $bannedOrg) {
                $bannedOrgObj = $orgDao->getOrganisation($bannedOrg->getOrgId());
                $orgBanningAdmin = $userDao->getUser($bannedOrg->getUserIdAdmin());
                $bannedOrgNames[$bannedOrg->getOrgId()] = $bannedOrgObj->getName();
                $orgBannerAdminNames[$bannedOrg->getOrgId()] = $orgBanningAdmin->getDisplayName();
            }
        }
        
        $bannedUserList = $adminDao->getBannedUsers();
        $bannedUserNames = array();
        $bannedUserAdminNames = array(); //display names of admins that banned users
        if ($bannedUserList) {
            foreach ($bannedUserList as $bannedUser) {
                $bannedUserObj = $userDao->getUser($bannedUser->getUserId());
                $banningAdmin = $userDao->getUser($bannedUser->getUserIdAdmin());
                $bannedUserNames[$bannedUser->getUserId()] = $bannedUserObj->getDisplayName();
                $bannedUserAdminNames[$bannedUser->getUserId()] = $banningAdmin->getDisplayName();
            }
        }

        $siteName = Common\Lib\Settings::get("site.name");
     
        $extra_scripts = "";
        $extra_scripts .= file_get_contents(__DIR__."/../js/site-admin.dashboard.js");

        $app->view()->appendData(array(
                    'sesskey'       => $sesskey,
                    "adminUserId"   => $userId,
                    "adminList"     => $adminList,
                    "bannedOrgList" => $bannedOrgList,
                    "bannedUserList"=> $bannedUserList,
                    "bannedUserNames" => $bannedUserNames,
                    "bannedOrgNames" => $bannedOrgNames,
                    "bannedUserAdminNames" => $bannedUserAdminNames,
                    "orgBannerAdminNames" => $orgBannerAdminNames,
                    "current_page"  => 'site-admin-dashboard',
                    "siteName"      => $siteName,
                    "extra_scripts" => $extra_scripts
        ));

        $app->render("admin/site-admin.dashboard.tpl");
    }

    public function all_users()
    {
        $app = \Slim\Slim::getInstance();
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->getUsers();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/all_users.tpl');
    }

    public function all_users_plain()
    {
        $app = \Slim\Slim::getInstance();
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->getUsers();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/all_users_plain.tpl');
    }

    public function active_now()
    {
        $app = \Slim\Slim::getInstance();
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->active_now();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/active_now.tpl');
    }

    public function active_users()
    {
        $app = \Slim\Slim::getInstance();
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->active_users();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/active_users.tpl');
    }

    public function active_users_unique()
    {
        $app = \Slim\Slim::getInstance();
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->active_users();
        $all_users_unique = array();
        foreach($all_users as $all_user) {
            $all_users_unique[$all_user['email']] = array('email' => $all_user['email'], 'display_name' => $all_user['display_name']);
        }

        $app->view()->appendData(array('all_users' => $all_users_unique));
        $app->render('admin/active_users_unique.tpl');
    }

    public function unclaimed_tasks()
    {
        $app = \Slim\Slim::getInstance();
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->unclaimed_tasks();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/unclaimed_tasks.tpl');
    }

    public function user_languages($code)
    {
        $app = \Slim\Slim::getInstance();
        $statsDao = new DAO\StatisticsDao();

        if ($code === 'full') $code = null;
        $all_users = $statsDao->user_languages($code);

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/user_languages.tpl');
    }

    public function download_user_languages()
    {
        $statsDao = new DAO\StatisticsDao();
        $all_users = $statsDao->user_languages(null);

        $data = "\xEF\xBB\xBF" . '"Display Name","Email","Code","Language","Code","Country",""' . "\n";

        foreach ($all_users as $user_row) {
            $data .= '"' . str_replace('"', '""', $user_row['display_name']) . '","' . $user_row['email'] . '","' . $user_row['language_code'] . '","' . $user_row['language_name'] . '","' . $user_row['country_code'] . '","' . $user_row['country_name'] . '","' . $user_row['native_or_secondary'] . '"' . "\n";
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="user_languages.csv"');
        header('Content-length: ' . strlen($data));
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');
        echo $data;
        die;
    }

    public function download_all_users()
    {
        $statsDao = new DAO\StatisticsDao();
        $all_users = $statsDao->getUsers();

        $data = "\xEF\xBB\xBF" . '"ID","Name","Email","Biography","Language","City","Country","Created"' . "\n";

        foreach ($all_users as $user_row) {
            $data .= '"' . $user_row['id'] . '","' .
                str_replace('"', '""', $user_row['first_name']) . ' ' . str_replace('"', '""', $user_row['last_name']) . '","' .
                $user_row['email'] . '","' .
                str_replace(array('\r\n', '\n', '\r'), "\n", str_replace('"', '""', $user_row['biography'])) . '","' .
                $user_row['native_language'] . ' ' . $user_row['native_country'] . '","' .
                str_replace('"', '""', $user_row['city']) . '","' .
                str_replace('"', '""', $user_row['country']) . '","' .
                $user_row['created_time'] . '"' . "\n";
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="all_users.csv"');
        header('Content-length: ' . strlen($data));
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');
        echo $data;
        die;
    }

    public function download_active_users()
    {
        $statsDao = new DAO\StatisticsDao();
        $all_users = $statsDao->active_users();

        $data = "\xEF\xBB\xBF" . '"Email","Task Title","Creator Email","Created Time"' . "\n";

        foreach ($all_users as $user_row) {
            $data .= '"' . $user_row['email'] . '","' .
                str_replace('"', '""', $user_row['task_title']) . '","' .
                $user_row['creator_email'] . '","' .
                $user_row['created_time'] . '"' . "\n";
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="active_users.csv"');
        header('Content-length: ' . strlen($data));
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');
        echo $data;
        die;
    }

    public function community_stats()
    {
        $statsDao = new DAO\StatisticsDao();
        $all_users0                 = $statsDao->community_stats();
        $community_stats_secondary0 = $statsDao->community_stats_secondary();
        $community_stats_words0     = $statsDao->community_stats_words();

        $all_users = array();
        foreach ($all_users0 as $user_row) {
            $all_users[$user_row['id']] = $user_row;
        }
        unset($all_users0);

        $community_stats_secondary = array();
        foreach ($community_stats_secondary0 as $user_row) {
            $community_stats_secondary[$user_row['id']] = $user_row;
        }
        unset($community_stats_secondary0);

        $community_stats_words = array();
        foreach ($community_stats_words0 as $user_row) {
            $community_stats_words[$user_row['id']] = $user_row;
        }
        unset($community_stats_words0);

        $data = "\xEF\xBB\xBF" . '"Name","Email","Country","Created Time","Last Accessed","Words Translated","Words Proofread","Native Language","Secondary Languages"' . "\n";

        foreach ($all_users as $i => $user_row) {
            $data .= '"' . str_replace('"', '""', $user_row['display_name']) . '","' .
                $user_row['email'] . '","' .
                str_replace('"', '""', $user_row['country']) . '","' .
                $user_row['created_time'] . '","' .
                $user_row['last_accessed'] . '","' .
                $community_stats_words[$i]['words_translated'] . '","' .
                $community_stats_words[$i]['words_proofread'] . '","' .
                $user_row['native_code'] . '","' .
                $community_stats_secondary[$i]['secondary_codes'] . '"' . "\n";
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="community_stats.csv"');
        header('Content-length: ' . strlen($data));
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');
        echo $data;
        die;
    }
}

$route_handler = new AdminRouteHandler();
$route_handler->init();
unset ($route_handler);
