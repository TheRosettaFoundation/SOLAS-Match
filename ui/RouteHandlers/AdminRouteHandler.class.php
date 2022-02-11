<?php

namespace SolasMatch\UI\RouteHandlers;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\Common as Common;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminRouteHandler
{
    public function init()
    {
        global $app;
                          
        $app->map(['GET', 'POST'],
            '/admin[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:adminDashboard')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('site-admin-dashboard');

        $app->map(['GET', 'POST'],
            '/all_users[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:all_users')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('all_users');

        $app->map(['GET', 'POST'],
            '/all_users_plain[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:all_users_plain')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('all_users_plain');

        $app->map(['GET', 'POST'],
            '/active_now[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:active_now')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('active_now');

        $app->map(['GET', 'POST'],
            '/active_now_matecat[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:active_now_matecat')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('active_now_matecat');

        $app->map(['GET', 'POST'],
            '/testing_center[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:testing_center')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('testing_center');

        $app->map(['GET', 'POST'],
            '/download_testing_center[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_testing_center')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('download_testing_center');

        $app->map(['GET', 'POST'],
            '/matecat_analyse_status[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:matecat_analyse_status')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('matecat_analyse_status');

        $app->get(
            '/list_memsource_projects[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:list_memsource_projects')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('list_memsource_projects');

        $app->map(['GET', 'POST'],
            '/download_covid_projects[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_covid_projects')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('download_covid_projects');

        $app->map(['GET', 'POST'],
            '/download_afghanistan_2021_projects[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_afghanistan_2021_projects')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('download_afghanistan_2021_projects');

        $app->map(['GET', 'POST'],
            '/download_haiti_2021_projects[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_haiti_2021_projects')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('download_haiti_2021_projects');

        $app->map(['GET', 'POST'],
            '/late_matecat[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:late_matecat')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('late_matecat');

        $app->map(['GET', 'POST'],
            '/complete_matecat[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:complete_matecat')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('complete_matecat');

        $app->map(['GET', 'POST'],
            '/user_task_reviews[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:user_task_reviews')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('user_task_reviews');

        $app->get(
            '/peer_to_peer_vetting[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:peer_to_peer_vetting')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('peer_to_peer_vetting');

        $app->get(
            '/submitted_task_reviews[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:submitted_task_reviews')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('submitted_task_reviews');

        $app->get(
            '/tasks_no_reviews[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:tasks_no_reviews')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('tasks_no_reviews');

        $app->get(
            '/project_source_file_scores[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:project_source_file_scores')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('project_source_file_scores');

        $app->get(
            '/download_submitted_task_reviews[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_submitted_task_reviews')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('download_submitted_task_reviews');

        $app->get(
            '/download_tasks_no_reviews[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_tasks_no_reviews')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('download_tasks_no_reviews');

        $app->get(
            '/download_project_source_file_scores[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_project_source_file_scores')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('download_project_source_file_scores');

        $app->map(['GET', 'POST'],
            '/first_completed_task[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:first_completed_task')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('first_completed_task');

        $app->map(['GET', 'POST'],
            '/active_users[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:active_users')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('active_users');

        $app->map(['GET', 'POST'],
            '/active_users_unique[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:active_users_unique')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('active_users_unique');

        $app->map(['GET', 'POST'],
            '/unclaimed_tasks[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:unclaimed_tasks')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('unclaimed_tasks');

        $app->map(['GET', 'POST'],
            '/search_users_by_language_pair[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:search_users_by_language_pair')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('search_users_by_language_pair');

        $app->map(['GET', 'POST'],
            '/user_languages/{code}',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:user_languages')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('user_languages');

        $app->get(
            '/download_user_languages[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_user_languages')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('download_user_languages');

        $app->map(['GET', 'POST'],
            '/user_task_languages/{code}',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:user_task_languages')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('user_task_languages');

        $app->map(['GET', 'POST'],
            '/user_words_by_language[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:user_words_by_language')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('user_words_by_language');

        $app->get(
            '/download_user_words_by_language[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_user_words_by_language')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('download_user_words_by_language');

        $app->get(
            '/download_user_task_languages[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_user_task_languages')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('download_user_task_languages');

        $app->get(
            '/download_all_users[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_all_users')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('download_all_users');

        $app->get(
            '/download_active_users[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_active_users')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('download_active_users');

        $app->get(
            '/community_stats[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:community_stats')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('community_stats');

        $app->get(
            '/org_stats[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:org_stats')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('org_stats');

        $app->get(
            '/community_dashboard[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:community_dashboard')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('community_dashboard');

        $app->get(
            '/language_work_requested[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:language_work_requested')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('language_work_requested');

        $app->get(
            '/download_language_work_requested[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_language_work_requested')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('download_language_work_requested');

        $app->get(
            '/translators_for_language_pairs[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:translators_for_language_pairs')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('translators_for_language_pairs');

        $app->get(
            '/download_translators_for_language_pairs[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_translators_for_language_pairs')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('download_translators_for_language_pairs');
    }
    
    public function adminDashboard()
    {
        global $app;
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
        global $app;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->getUsers();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/all_users.tpl');
    }

    public function all_users_plain()
    {
        global $app;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->getUsers();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/all_users_plain.tpl');
    }

    public function active_now()
    {
        global $app;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->active_now();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/active_now.tpl');
    }

    public function active_now_matecat()
    {
        global $app;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->active_now_matecat();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/active_now_matecat.tpl');
    }

    public function testing_center()
    {
        global $app;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->testing_center();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/testing_center.tpl');
    }

    public function download_testing_center()
    {
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->testing_center();

        $data = "\xEF\xBB\xBF" . '"Task","Pair","Created","Deadline","Translator","Level","Status","Reviewer","Revision Status","Accuracy","Fluency","Terminology","Style","Design","Feedback"' . "\n";

        foreach ($all_users as $user_row) {
            $data .= '"' . str_replace('"', '""', $user_row['task_title']) . '","' .
            $user_row['language_pair'] . '","' .
            $user_row['created'] . '","' .
            $user_row['deadline'] . '","' .
            $user_row['user_email'] . '","' .
            $user_row['level'] . '","' .
            $user_row['task_status'] . '","' .
            $user_row['proofreading_email'] . '","' .
            $user_row['proofreading_task_status'] . '","' .
            $user_row['accuracy'] . '","' .
            $user_row['fluency'] . '","' .
            $user_row['terminology'] . '","' .
            $user_row['style'] . '","' .
            $user_row['design'] . '","' .
            str_replace('"', '""', $user_row['comment']) . '"' . "\n";
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="testing_center.csv"');
        header('Content-length: ' . strlen($data));
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');
        echo $data;
        die;
    }

    public function late_matecat()
    {
        global $app;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->late_matecat();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/late_matecat.tpl');
    }

    public function complete_matecat()
    {
        global $app;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->complete_matecat();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/complete_matecat.tpl');
    }

    public function user_task_reviews()
    {
        global $app;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->user_task_reviews();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/user_task_reviews.tpl');
    }

    public function submitted_task_reviews()
    {
        global $app;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->submitted_task_reviews();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/submitted_task_reviews.tpl');
    }

    public function download_submitted_task_reviews()
    {
        $statsDao = new DAO\StatisticsDao();
        $all_users = $statsDao->submitted_task_reviews();

        $data = "\xEF\xBB\xBF" . '"Completed","Revision Task","Reviser","Translator","Language Pair","Accuracy","Fluency","Terminology","Style","Design","Comment"' . "\n";

        foreach ($all_users as $user_row) {
            $data .= '"' . $user_row['complete_date'] . '","' .
            str_replace('"', '""', $user_row['task_title']) . '","' .
            str_replace('"', '""', $user_row['reviser_name']) . '","' .
            str_replace('"', '""', $user_row['translator_name']) . '","' .
            $user_row['language_pair'] . '","' .
            $user_row['accuracy'] . '","' .
            $user_row['fluency'] . '","' .
            $user_row['terminology'] . '","' .
            $user_row['style'] . '","' .
            $user_row['design'] . '","' .
            str_replace('"', '""', $user_row['comment']) . '"' . "\n";
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="submitted_task_reviews.csv"');
        header('Content-length: ' . strlen($data));
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');
        echo $data;
        die;
    }

    public function peer_to_peer_vetting()
    {
        $statsDao = new DAO\StatisticsDao();
        $all_users = $statsDao->peer_to_peer_vetting();

        $data = "\xEF\xBB\xBF" . '"Email","Native","Words Translated","Words Revised","Language Pair","Language Pairs","Average Reviews","Number","Level","Last Task"' . "\n";

        foreach ($all_users as $user_row) {
            $data .= '"' . $user_row['email'] . '","' .
            $user_row['native_language_name'] . '","' .
            $user_row['words_translated'] . '","' .
            $user_row['words_revised'] . '","' .
            $user_row['language_pair'] . '","' .
            $user_row['language_pair_list'] . '","' .
            $user_row['average_reviews'] . '","' .
            $user_row['number_reviews'] . '","' .
            $user_row['level'] . '","' .
            $user_row['last_task'] . '"' . "\n";
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="peer_to_peer_vetting.csv"');
        header('Content-length: ' . strlen($data));
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');
        echo $data;
        die;
    }

    public function tasks_no_reviews()
    {
        global $app;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->tasks_no_reviews();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/tasks_no_reviews.tpl');
    }

    public function download_tasks_no_reviews()
    {
        $statsDao = new DAO\StatisticsDao();
        $all_users = $statsDao->tasks_no_reviews();

        $data = "\xEF\xBB\xBF" . '"Completed","Revision Task","Reviser","Language Pair"' . "\n";

        foreach ($all_users as $user_row) {
            $data .= '"' . $user_row['complete_date'] . '","' .
            str_replace('"', '""', $user_row['task_title']) . '","' .
            str_replace('"', '""', $user_row['reviser_name']) . '","' .
            $user_row['language_pair'] . '"' . "\n";
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="tasks_no_reviews.csv"');
        header('Content-length: ' . strlen($data));
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');
        echo $data;
        die;
    }

    public function project_source_file_scores()
    {
        global $app;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->project_source_file_scores();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/project_source_file_scores.tpl');
    }

    public function download_project_source_file_scores()
    {
        $statsDao = new DAO\StatisticsDao();
        $all_users = $statsDao->project_source_file_scores();

        $data = "\xEF\xBB\xBF" . '"Project","Partner","Created","Delivered","Corrections","Grammar","Spelling","Consistency","Comments"' . "\n";

        foreach ($all_users as $user_row) {
            $data .= '"' . str_replace('"', '""', $user_row['title']) . '","' .
            str_replace('"', '""', $user_row['name']) . '","' .
            $user_row['created'] . '","' .
            $user_row['completed'] . '","' .
            $user_row['cor'] . '","' .
            $user_row['gram'] . '","' .
            $user_row['spell'] . '","' .
            $user_row['cons'] . '","' .
            str_replace('"', '""', $user_row['comments']) . '"' . "\n";
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="project_source_file_scores.csv"');
        header('Content-length: ' . strlen($data));
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');
        echo $data;
        die;
    }

    public function first_completed_task()
    {
        global $app;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->complete_matecat();

        $earliest = array();
        foreach ($all_users as $user) {
            if (empty($earliest[$user['user_id']])) {
                $earliest[$user['user_id']] = $user['claimed_time'];
            } else {
                if ($earliest[$user['user_id']] > $user['claimed_time']) $earliest[$user['user_id']] = $user['claimed_time'];
            }
        }
        foreach ($all_users as $key => $user) {
            if (!in_array($user['claimed_time'], $earliest)) {
                unset($all_users[$key]);
            } else {
                $all_users[$key]['first_name'] = $user['claimed_time'] . ' ' . $user['first_name'];
            }
        }

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/first_completed_task.tpl');
    }

    public function active_users()
    {
        global $app;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->active_users();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/active_users.tpl');
    }

    public function active_users_unique()
    {
        global $app;
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
        global $app;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->unclaimed_tasks();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/unclaimed_tasks.tpl');
    }

    public function search_users_by_language_pair()
    {
        global $app;
        $statsDao = new DAO\StatisticsDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        if (!empty($_POST['search_users_language_pair'])) {
            Common\Lib\UserSession::checkCSRFKey($_POST, 'search_users_by_language_pair');

            $source_target = explode('-', $_POST['search_users_language_pair']);
            if (!empty($source_target) && count($source_target) == 2) {
                $all_users = $statsDao->search_users_by_language_pair($source_target[0], $source_target[1]);
                $app->view()->appendData(array('all_users' => $all_users));
            }
        }

        $app->render('admin/search_users_by_language_pair.tpl');
    }

    public function user_languages($code)
    {
        global $app;
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

        $data = "\xEF\xBB\xBF" . '"Display Name","Email","Name","Code","Language","Code","Country","","Level"' . "\n";

        foreach ($all_users as $user_row) {
            $data .= '"' . str_replace('"', '""', $user_row['display_name']) . '","' . $user_row['email'] . '","' . str_replace('"', '""', $user_row['first_name']) . ' ' . str_replace('"', '""', $user_row['last_name']) . '","' . $user_row['language_code'] . '","' . $user_row['language_name'] . '","' . $user_row['country_code'] . '","' . $user_row['country_name'] . '","' . $user_row['native_or_secondary'] . '","' . $user_row['level'] . '"' . "\n";
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
        global $app;
        $statsDao = new DAO\StatisticsDao();

        if ($code === 'full') $code = null;
        $all_users = $statsDao->user_task_languages($code);

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/user_task_languages.tpl');
    }

    public function user_words_by_language()
    {
        global $app;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->user_words_by_language();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/user_words_by_language.tpl');
    }

    public function download_user_words_by_language()
    {
        $statsDao = new DAO\StatisticsDao();
        $all_users = $statsDao->user_words_by_language();

        $data = "\xEF\xBB\xBF" . '"Display Name","Email","Name","Pair","Qualification Level","Words Translated","Words Revised"' . "\n";

        foreach ($all_users as $user_row) {
            $data .= '"' . str_replace('"', '""', $user_row['display_name']) . '","' . $user_row['email'] . '","' . str_replace('"', '""', $user_row['first_name']) . ' ' . str_replace('"', '""', $user_row['last_name']) . '","' . $user_row['language_pair'] . '","' . $user_row['level'] . '","' . $user_row['words_translated'] . '","' . $user_row['words_proofread'] . '"' . "\n";
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="user_words_by_language.csv"');
        header('Content-length: ' . strlen($data));
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');
        echo $data;
        die;
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

        $data = "\xEF\xBB\xBF" . '"ID","Name","Email","Biography","Language","City","Country","Created","Code of Conduct","Admin"' . "\n";

        foreach ($all_users as $user_row) {
            $data .= '"' . $user_row['id'] . '","' .
                str_replace('"', '""', $user_row['first_name']) . ' ' . str_replace('"', '""', $user_row['last_name']) . '","' .
                $user_row['email'] . '","' .
                str_replace(array('\r\n', '\n', '\r'), "\n", str_replace('"', '""', $user_row['biography'])) . '","' .
                $user_row['native_language'] . ' ' . $user_row['native_country'] . '","' .
                str_replace('"', '""', $user_row['city']) . '","' .
                str_replace('"', '""', $user_row['country']) . '","' .
                $user_row['created_time'] . '","' .
                $user_row['terms'] . '","' .
                $user_row['admin'] . '"' . "\n";
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
        $org_stats_words_req = $statsDao->org_stats_words_req();
        $org_stats_languages = $statsDao->org_stats_languages();

        $all_orgs = array();
        foreach ($all_orgs0 as $org_row) {
            $all_orgs[$org_row['id']] = $org_row;
            $all_orgs[$org_row['id']]['admins']  = array();
            $all_orgs[$org_row['id']]['members'] = array();
            $all_orgs[$org_row['id']]['words_translated'] = array();
            $all_orgs[$org_row['id']]['words_proofread']  = array();
            $all_orgs[$org_row['id']]['words_translated_req'] = array();
            $all_orgs[$org_row['id']]['words_proofread_req']  = array();
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

        foreach ($org_stats_words_req as $words_row) {
            if (!empty($all_orgs[$words_row['organisation_id']])) {
                $year_list[$words_row['year']] = $words_row['year'];

                $all_orgs[$words_row['organisation_id']]['words_translated_req'][$words_row['year']] = $words_row['words_translated_req'];
                $all_orgs[$words_row['organisation_id']]['words_proofread_req'] [$words_row['year']] = $words_row['words_proofread_req'];
            }
        }
        unset($org_stats_words_req);

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
            $data .= ',"' . $year . ' Words Translated","Words Revised","Requested Words Translated","Requested Words Revised","Language Pairs"';
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
                $data .= ',"' . (empty($org_row['words_translated_req'][$year]) ? '' : $org_row['words_translated_req'][$year]) . '"';
                $data .= ',"' . (empty($org_row['words_proofread_req'] [$year]) ? '' : $org_row['words_proofread_req'][$year]) . '"';
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
        $users_who_logged_in        = $statsDao->users_who_logged_in();

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
                    'all_logins' => 0,
                    'distinct_logins' => 0,
                    );
            }
        }

        $total = 0;
        $previous = 0;
        foreach ($users_signed_up as $users_signed_up_month) { // users_signed_up is sorted oldest first!
            if (empty($users_signed_up_month['users_signed_up'])) $users_signed_up_month['users_signed_up'] = 0;
            $total += $users_signed_up_month['users_signed_up'];

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

        foreach ($users_who_logged_in as $users_who_logged_in_month) {
            if (!empty($all_months[$users_who_logged_in_month['month']])) {
                $all_months[$users_who_logged_in_month['month']]['all_logins'] = $users_who_logged_in_month['all_logins'];
                $all_months[$users_who_logged_in_month['month']]['distinct_logins'] = $users_who_logged_in_month['distinct_logins'];
            }
        }

        $data = "\xEF\xBB\xBF" . '"Kató Platform Community"';
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

        $data .= '"All Logins"';
        foreach ($all_months as $month_data) {
            $data .= ',"' . $month_data['all_logins'] . '"';
        }
        $data .= "\n";

        $data .= '"Distinct Logins"';
        foreach ($all_months as $month_data) {
            $data .= ',"' . $month_data['distinct_logins'] . '"';
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

    public function language_work_requested()
    {
        global $app;
        $statsDao = new DAO\StatisticsDao();
        $language_work_requested = $statsDao->language_work_requested();

        $years = array();
        $words = array();
        foreach ($language_work_requested as $row) {
            $years[$row['created']] = $row['created'];
            $words[$row['language_pair']] = 0;
        }
        arsort($years);
        $current_year = reset($years);

        $template = array();
        foreach ($years as $year) {
            $template[$year] = array('words' => 0, 'tasks' => 0);
        }

        foreach ($language_work_requested as $row) {
            if ($row['created'] == $current_year) $words[$row['language_pair']] = $row['words'];
        }

        arsort($words);

        foreach ($words as $language_pair => $data) {
            $words[$language_pair] = $template;
        }

        foreach ($language_work_requested as $row) {
            $words[$row['language_pair']][$row['created']]['words'] = $row['words'];
            $words[$row['language_pair']][$row['created']]['tasks'] = $row['tasks'];
        }

        $app->view()->appendData(array('words' => $words, 'years' => $years));
        $app->render('admin/language_work_requested.tpl');

    }

    public function download_language_work_requested()
    {
        $statsDao = new DAO\StatisticsDao();
        $language_work_requested = $statsDao->language_work_requested();

        $years = array();
        $words = array();
        foreach ($language_work_requested as $row) {
            $years[$row['created']] = $row['created'];
            $words[$row['language_pair']] = 0;
        }
        arsort($years);
        $current_year = reset($years);

        $template = array();
        foreach ($years as $year) {
            $template[$year] = array('words' => 0, 'tasks' => 0);
        }

        foreach ($language_work_requested as $row) {
            if ($row['created'] == $current_year) $words[$row['language_pair']] = $row['words'];
        }

        arsort($words);

        foreach ($words as $language_pair => $data) {
            $words[$language_pair] = $template;
        }

        foreach ($language_work_requested as $row) {
            $words[$row['language_pair']][$row['created']]['words'] = $row['words'];
            $words[$row['language_pair']][$row['created']]['tasks'] = $row['tasks'];
        }

        $data = "\xEF\xBB\xBF" . '"Language Pair"';

        foreach ($years as $year) {
            $data .= ',"' . $year . ' Tasks","Words"';
        }
        $data .= "\n";

        foreach ($words as $key => $row) {
            $data .= '"' . $key . '"';
            foreach ($years as $year) {
                $data .= ',"' . (empty($row[$year]['tasks']) ? '' : $row[$year]['tasks']) . '"';
                $data .= ',"' . (empty($row[$year]['words']) ? '' : $row[$year]['words']) . '"';
            }
            $data .= "\n";
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="language_work_requested.csv"');
        header('Content-length: ' . strlen($data));
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');
        echo $data;
        die;
    }

    public function translators_for_language_pairs()
    {
        global $app;
        $statsDao = new DAO\StatisticsDao();
        $translators_for_language_pairs = $statsDao->translators_for_language_pairs();

        $totals = array();
        $breakdown = array();
        foreach ($translators_for_language_pairs as $row) {
            if (empty($totals[$row['pair']])) {
                $totals[$row['pair']] = $row['number'];
                $breakdown[$row['pair']] = $row['level'] . '(' . $row['number'] . ')';
            } else {
                $totals[$row['pair']] += $row['number'];
                $breakdown[$row['pair']] .= ', ' . $row['level'] . '(' . $row['number'] . ')';
            }
        }

        $app->view()->appendData(array('totals' => $totals, 'breakdown' => $breakdown));
        $app->render('admin/translators_for_language_pairs.tpl');
    }

    public function download_translators_for_language_pairs()
    {
        $statsDao = new DAO\StatisticsDao();
        $translators_for_language_pairs = $statsDao->translators_for_language_pairs();

        $totals = array();
        $breakdown = array();
        foreach ($translators_for_language_pairs as $row) {
            if (empty($totals[$row['pair']])) {
                $totals[$row['pair']] = $row['number'];
                $breakdown[$row['pair']] = $row['level'] . '(' . $row['number'] . ')';
            } else {
                $totals[$row['pair']] += $row['number'];
                $breakdown[$row['pair']] .= ', ' . $row['level'] . '(' . $row['number'] . ')';
            }
        }

        $data = "\xEF\xBB\xBF" . '"Language Pair","Number of Translators","Breakdown"';
        $data .= "\n";

        foreach ($totals as $pair => $total) {
            $data .= '"' . $pair . '"';
            $data .= ',"' . $total . '"';
            $data .= ',"' . $breakdown[$pair] . '"';
            $data .= "\n";
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="translators_for_language_pairs.csv"');
        header('Content-length: ' . strlen($data));
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');
        echo $data;
        die;
    }

    public function matecat_analyse_status()
    {
        global $app;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->matecat_analyse_status();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/matecat_analyse_status.tpl');
    }

    public function list_memsource_projects()
    {
        global $app;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->list_memsource_projects();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('admin/list_memsource_projects.tpl');
    }

    public function download_covid_projects()
    {
        $statsDao = new DAO\StatisticsDao();
        $all_users = $statsDao->covid_projects();

        $data = "\xEF\xBB\xBF" . '"Title","Partner","Creator","Word Count","Language Pairs","Number","Created Time","Deadline","Completed Time"' . "\n";

        foreach ($all_users as $user_row) {
            $data .= '"' . str_replace('"', '""', $user_row['project_title']) . '","' .
                str_replace('"', '""', $user_row['org_name']) . '","' .
                $user_row['creator_email'] . '","' .
                $user_row['word_count'] . '","' .
                $user_row['language_pairs'] . '","' .
                $user_row['language_pairs_number'] . '","' .
                $user_row['created'] . '","' .
                $user_row['deadline'] . '","' .
                $user_row['completed'] . '"' . "\n";
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="covid_projects.csv"');
        header('Content-length: ' . strlen($data));
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');
        echo $data;
        die;
    }

    public function download_afghanistan_2021_projects()
    {
        $statsDao = new DAO\StatisticsDao();
        $all_users = $statsDao->afghanistan_2021_projects();
        if (empty($all_users)) $all_users = [];

        $data = "\xEF\xBB\xBF" . '"Title","Partner","Creator","Word Count","Language Pairs","Number","Created Time","Deadline","Completed Time"' . "\n";

        foreach ($all_users as $user_row) {
            $data .= '"' . str_replace('"', '""', $user_row['project_title']) . '","' .
                str_replace('"', '""', $user_row['org_name']) . '","' .
                $user_row['creator_email'] . '","' .
                $user_row['word_count'] . '","' .
                $user_row['language_pairs'] . '","' .
                $user_row['language_pairs_number'] . '","' .
                $user_row['created'] . '","' .
                $user_row['deadline'] . '","' .
                $user_row['completed'] . '"' . "\n";
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="afghanistan_2021_projects.csv"');
        header('Content-length: ' . strlen($data));
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');
        echo $data;
        die;
    }

    public function download_haiti_2021_projects()
    {
        $statsDao = new DAO\StatisticsDao();
        $all_users = $statsDao->haiti_2021_projects();
        if (empty($all_users)) $all_users = [];

        $data = "\xEF\xBB\xBF" . '"Title","Partner","Creator","Word Count","Language Pairs","Number","Created Time","Deadline","Completed Time"' . "\n";

        foreach ($all_users as $user_row) {
            $data .= '"' . str_replace('"', '""', $user_row['project_title']) . '","' .
                str_replace('"', '""', $user_row['org_name']) . '","' .
                $user_row['creator_email'] . '","' .
                $user_row['word_count'] . '","' .
                $user_row['language_pairs'] . '","' .
                $user_row['language_pairs_number'] . '","' .
                $user_row['created'] . '","' .
                $user_row['deadline'] . '","' .
                $user_row['completed'] . '"' . "\n";
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="haiti_2021_projects.csv"');
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
