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
            '/active_now_matecat/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'active_now_matecat')
        )->via('POST')->name('active_now_matecat');

        $app->get(
            '/complete_matecat/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'complete_matecat')
        )->via('POST')->name('complete_matecat');

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
            '/user_task_languages/:code',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'user_task_languages')
        )->via('POST')->name('user_task_languages');

        $app->get(
            '/download_user_task_languages/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'download_user_task_languages')
        )->name('download_user_task_languages');

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

        $app->get(
            '/org_stats/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'org_stats')
        )->name('org_stats');

        $app->get(
            '/community_dashboard/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'community_dashboard')
        )->name('community_dashboard');
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
            $statsDao = new DAO\StatisticsDao();

            if (!empty($post['search_user'])) {
                $items_found = $statsDao->search_user($post['search_user']);
                if (!empty($items_found)) {
                    $app->flashNow('search_user_results', $items_found);
                } else {
                    $app->flashNow('search_user_fail', 'Not Found');
                }
            }

            if (!empty($post['search_organisation'])) {
                $items_found = $statsDao->search_organisation($post['search_organisation']);
                if (!empty($items_found)) {
                    $app->flashNow('search_organisation_results', $items_found);
                } else {
                    $app->flashNow('search_organisation_fail', 'Not Found');
                }
            }

            if (!empty($post['search_project'])) {
                $items_found = $statsDao->search_project($post['search_project']);
                if (!empty($items_found)) {
                    $app->flashNow('search_project_results', $items_found);
                } else {
                    $app->flashNow('search_project_fail', 'Not Found');
                }
            }

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

    public function active_now_matecat()
    {
        $app = \Slim\Slim::getInstance();
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->active_now_matecat();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/active_now_matecat.tpl');
    }

    public function complete_matecat()
    {
        $app = \Slim\Slim::getInstance();
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->complete_matecat();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/active_now_matecat.tpl');
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

        $data = "\xEF\xBB\xBF" . '"Display Name","Email","Name","Code","Language","Code","Country",""' . "\n";

        foreach ($all_users as $user_row) {
            $data .= '"' . str_replace('"', '""', $user_row['display_name']) . '","' . $user_row['email'] . '","' . str_replace('"', '""', $user_row['first_name']) . ' ' . str_replace('"', '""', $user_row['last_name']) . '","' . $user_row['language_code'] . '","' . $user_row['language_name'] . '","' . $user_row['country_code'] . '","' . $user_row['country_name'] . '","' . $user_row['native_or_secondary'] . '"' . "\n";
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

    public function user_task_languages($code)
    {
        $app = \Slim\Slim::getInstance();
        $statsDao = new DAO\StatisticsDao();

        if ($code === 'full') $code = null;
        $all_users = $statsDao->user_task_languages($code);

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/user_task_languages.tpl');
    }

    public function download_user_task_languages()
    {
        $statsDao = new DAO\StatisticsDao();
        $all_users = $statsDao->user_task_languages(null);

        $data = "\xEF\xBB\xBF" . '"Display Name","Email","Name","Task Title","Task Type","Word Count","Date Claimed","Codes","Source","Target"' . "\n";

        foreach ($all_users as $user_row) {
            $data .= '"' . str_replace('"', '""', $user_row['display_name']) . '","' .
            $user_row['email'] . '","' .
            str_replace('"', '""', $user_row['first_name']) . ' ' . str_replace('"', '""', $user_row['last_name']) . '","' .
            str_replace('"', '""', $user_row['task_title']) . '","' .
            $user_row['task_type'] . '","' .
            $user_row['word_count'] . '","' .
            substr($user_row['claimed_time'], 0, 10) . '","' .
            $user_row['language_pair'] . '","' .
            $user_row['language_name_source'] . '","' .
            $user_row['language_name_target'] . '"' . "\n";
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="user_task_languages.csv"');
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

        $data = "\xEF\xBB\xBF" . '"Name","Email","Country","City","Created Time","Last Accessed","Words Translated","Words Proofread","Native Language","Source Languages","Target Languages"' . "\n";

        foreach ($all_users as $i => $user_row) {
            $data .= '"' . str_replace('"', '""', $user_row['display_name']) . '","' .
                $user_row['email'] . '","' .
                str_replace('"', '""', $user_row['country']) . '","' .
                str_replace('"', '""', $user_row['city']) . '","' .
                $user_row['created_time'] . '","' .
                $user_row['last_accessed'] . '","' .
                $community_stats_words[$i]['words_translated'] . '","' .
                $community_stats_words[$i]['words_proofread'] . '","' .
                $user_row['native_code'] . '","' .
                $community_stats_secondary[$i]['source_codes'] . '","' .
                $community_stats_secondary[$i]['target_codes'] . '"' . "\n";
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

    public function org_stats()
    {
        $statsDao = new DAO\StatisticsDao();
        $all_orgs0           = $statsDao->all_orgs();
        $all_org_admins      = $statsDao->all_org_admins();
        $all_org_members     = $statsDao->all_org_members();
        $org_stats_words     = $statsDao->org_stats_words();
        $org_stats_languages = $statsDao->org_stats_languages();

        $all_orgs = array();
        foreach ($all_orgs0 as $org_row) {
            $all_orgs[$org_row['id']] = $org_row;
            $all_orgs[$org_row['id']]['admins']  = array();
            $all_orgs[$org_row['id']]['members'] = array();
            $all_orgs[$org_row['id']]['words_translated'] = array();
            $all_orgs[$org_row['id']]['words_proofread']  = array();
            $all_orgs[$org_row['id']]['language_pairs']   = array();
        }
        unset($all_orgs0);

        foreach ($all_org_admins as $user_row) {
            if (!empty($all_orgs[$user_row['organisation_id']])) {
                $all_orgs[$user_row['organisation_id']]['admins'][$user_row['email']] = $user_row['email'];
            }
        }

        foreach ($all_org_members as $user_row) {
            if (!empty($all_orgs[$user_row['organisation_id']])) {
                if (!in_array($user_row['email'] , $all_orgs[$user_row['organisation_id']]['admins'])) {
                    $all_orgs[$user_row['organisation_id']]['members'][$user_row['email']] = $user_row['email'];
                }
            }
        }

        $year_list = array();
        foreach ($org_stats_words as $words_row) {
            if (!empty($all_orgs[$words_row['organisation_id']])) {
                $year_list[$words_row['year']] = $words_row['year'];

                $all_orgs[$words_row['organisation_id']]['words_translated'][$words_row['year']] = $words_row['words_translated'];
                $all_orgs[$words_row['organisation_id']]['words_proofread'] [$words_row['year']] = $words_row['words_proofread'];
            }
        }
        unset($org_stats_words);

        foreach ($org_stats_languages as $words_row) {
            if (!empty($all_orgs[$words_row['organisation_id']])) {
                $year_list[$words_row['year']] = $words_row['year'];

                $all_orgs[$words_row['organisation_id']]['language_pairs'][$words_row['year']][$words_row['language_pair']] = $words_row['language_pair'];
            }
        }
        unset($org_stats_languages);

        $data = "\xEF\xBB\xBF" . '"Name","Email","Website","Admins","Members"';

        rsort($year_list);
        foreach ($year_list as $year) {
            $data .= ',"' . $year . ' Words Translated","Words Proofread","Language Pairs"';
        }
        $data .= "\n";

        foreach ($all_orgs as $org_row) {
            $data .= '"' . str_replace('"', '""', $org_row['name']) . '","' .
                str_replace('"', '""', $org_row['email']) . '","' .
                str_replace('"', '""', $org_row['homepage']) . '","' .
                str_replace('"', '""', implode(', ', $org_row['admins'])) . '","' .
                str_replace('"', '""', implode(', ', $org_row['members'])) . '"';
            foreach ($year_list as $year) {
                $data .= ',"' . (empty($org_row['words_translated'][$year]) ? '' : $org_row['words_translated'][$year]) . '"';
                $data .= ',"' . (empty($org_row['words_proofread'] [$year]) ? '' : $org_row['words_proofread'][$year]) . '"';
                $data .= ',"' . (empty($org_row['language_pairs']  [$year]) ? '' : implode(', ', $org_row['language_pairs'][$year])) . '"';
            }
            $data .= "\n";
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="org_stats.csv"');
        header('Content-length: ' . strlen($data));
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');
        echo $data;
        die;
    }

    public function community_dashboard()
    {
        $statsDao = new DAO\StatisticsDao();
        $users_active               = $statsDao->users_active();
        $users_signed_up            = $statsDao->users_signed_up();
        $new_tasks                  = $statsDao->new_tasks();
        $average_time_to_assign     = $statsDao->average_time_to_assign();
        $average_time_to_turnaround = $statsDao->average_time_to_turnaround();

        $all_months = array();
        foreach ($new_tasks as $new_tasks_month) { // new_tasks sorted newest first (like most of these)
            if ($new_tasks_month['month'] != 'May-12') { // Hack to remove bad data
                $all_months[$new_tasks_month['month']] = array(
                    'total_translators' => 0,
                    'users_active' => 0,
                    'users_signed_up' => 0,
                    'monthly_community_growth' => 0,
                    'new_tasks' => $new_tasks_month['new_tasks'],
                    'average_time_to_assign' => 0,
                    'average_time_to_turnaround' => 0,
                    );
            }
        }

        $total = 0;
        $previous = 0;
        foreach ($users_signed_up as $users_signed_up_month) { // users_signed_up is sorted oldest first!
            $total+= $users_signed_up_month['users_signed_up'];

            if (!empty($all_months[$users_signed_up_month['month']])) { // Don't add partial data
                $all_months[$users_signed_up_month['month']]['users_signed_up'] = $users_signed_up_month['users_signed_up'];
                $all_months[$users_signed_up_month['month']]['total_translators'] = $total;
                if ($previous) {
                    $percent = number_format(($total/$previous - 1.0) * 100., 2);
                    $percent = "$percent%";
                } else {
                    $percent = '100%';
                }
                $all_months[$users_signed_up_month['month']]['monthly_community_growth'] = $percent;
                $previous = $total;
            }
        }

        foreach ($users_active as $users_active_month) {
            if (!empty($all_months[$users_active_month['month']])) {
                $all_months[$users_active_month['month']]['users_active'] = $users_active_month['users_active'];
            }
        }

        foreach ($average_time_to_assign as $average_time_to_assign_month) {
            if (!empty($all_months[$average_time_to_assign_month['month']])) {
                $all_months[$average_time_to_assign_month['month']]['average_time_to_assign'] = $average_time_to_assign_month['average_time_to_assign'];
            }
        }

        foreach ($average_time_to_turnaround as $average_time_to_turnaround_month) {
            if (!empty($all_months[$average_time_to_turnaround_month['month']])) {
                $all_months[$average_time_to_turnaround_month['month']]['average_time_to_turnaround'] = $average_time_to_turnaround_month['average_time_to_turnaround'];
            }
        }

        $data = "\xEF\xBB\xBF" . '"trommons.org Community"';
        foreach ($all_months as $month => $month_data) {
            $data .= ',"' . $month . '"';
        }
        $data .= "\n";

        $data .= '"Total Translators"';
        foreach ($all_months as $month_data) {
            $data .= ',"' . $month_data['total_translators'] . '"';
        }
        $data .= "\n";

        $data .= '"Active Translators"';
        foreach ($all_months as $month_data) {
            $data .= ',"' . $month_data['users_active'] . '"';
        }
        $data .= "\n";

        $data .= '"New Sign-ups"';
        foreach ($all_months as $month_data) {
            $data .= ',"' . $month_data['users_signed_up'] . '"';
        }
        $data .= "\n";

        $data .= '"Monthly Community Growth"';
        foreach ($all_months as $month_data) {
            $data .= ',"' . $month_data['monthly_community_growth'] . '"';
        }
        $data .= "\n";

        $data .= '"Total New Tasks"';
        foreach ($all_months as $month_data) {
            $data .= ',"' . $month_data['new_tasks'] . '"';
        }
        $data .= "\n";

        $data .= '"Average Time to Assign (hours)"';
        foreach ($all_months as $month_data) {
            $data .= ',"' . $month_data['average_time_to_assign'] . '"';
        }
        $data .= "\n";

        $data .= '"Average Turnaround (hours)"';
        foreach ($all_months as $month_data) {
            $data .= ',"' . $month_data['average_time_to_turnaround'] . '"';
        }
        $data .= "\n";

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="community_dashboard.csv"');
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
