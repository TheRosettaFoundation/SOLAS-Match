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
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any_or_org_admin_or_po_for_any_org')
            ->setName('site-admin-dashboard');

        $app->map(['GET', 'POST'],
            '/all_users[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:all_users')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('all_users');

        $app->map(['GET', 'POST'],
            '/all_users_plain[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:all_users_plain')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('all_users_plain');

        $app->map(['GET', 'POST'],
            '/active_now[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:active_now')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('active_now');

        $app->map(['GET', 'POST'],
            '/testing_center[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:testing_center')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('testing_center');

        $app->map(['GET', 'POST'],
            '/download_testing_center[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_testing_center')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('download_testing_center');

        $app->map(['GET', 'POST'],
            '/matecat_analyse_status[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:matecat_analyse_status')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('matecat_analyse_status');

        $app->get(
            '/list_memsource_projects[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:list_memsource_projects')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('list_memsource_projects');

        $app->map(['GET', 'POST'],
            '/download_covid_projects[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_covid_projects')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('download_covid_projects');

        $app->map(['GET', 'POST'],
            '/download_afghanistan_2021_projects[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_afghanistan_2021_projects')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('download_afghanistan_2021_projects');

        $app->map(['GET', 'POST'],
            '/download_haiti_2021_projects[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_haiti_2021_projects')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('download_haiti_2021_projects');

        $app->map(['GET', 'POST'],
            '/complete_matecat[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:complete_matecat')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('complete_matecat');

        $app->map(['GET', 'POST'],
            '/user_task_reviews[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:user_task_reviews')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('user_task_reviews');

        $app->get(
            '/peer_to_peer_vetting[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:peer_to_peer_vetting')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('peer_to_peer_vetting');

        $app->get(
            '/submitted_task_reviews[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:submitted_task_reviews')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('submitted_task_reviews');

        $app->get(
            '/tasks_no_reviews[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:tasks_no_reviews')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('tasks_no_reviews');

        $app->get(
            '/project_source_file_scores[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:project_source_file_scores')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('project_source_file_scores');

        $app->get(
            '/download_submitted_task_reviews[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_submitted_task_reviews')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('download_submitted_task_reviews');

        $app->get(
            '/download_tasks_no_reviews[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_tasks_no_reviews')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('download_tasks_no_reviews');

        $app->get(
            '/download_project_source_file_scores[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_project_source_file_scores')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('download_project_source_file_scores');

        $app->map(['GET', 'POST'],
            '/first_completed_task[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:first_completed_task')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('first_completed_task');

        $app->map(['GET', 'POST'],
            '/active_users[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:active_users')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('active_users');

        $app->map(['GET', 'POST'],
            '/active_users_unique[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:active_users_unique')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('active_users_unique');

        $app->map(['GET', 'POST'],
            '/unclaimed_tasks[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:unclaimed_tasks')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('unclaimed_tasks');

        $app->map(['GET', 'POST'],
            '/search_users_by_language_pair[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:search_users_by_language_pair')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('search_users_by_language_pair');

        $app->map(['GET', 'POST'],
            '/user_languages/{code}',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:user_languages')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('user_languages');

        $app->get(
            '/download_user_languages[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_user_languages')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('download_user_languages');

        $app->map(['GET', 'POST'],
            '/user_task_languages/{code}',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:user_task_languages')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('user_task_languages');

        $app->map(['GET', 'POST'],
            '/user_words_by_language[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:user_words_by_language')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('user_words_by_language');

        $app->get(
            '/download_user_words_by_language[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_user_words_by_language')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('download_user_words_by_language');

        $app->get(
            '/download_user_task_languages[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_user_task_languages')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('download_user_task_languages');

        $app->get(
            '/download_all_users[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_all_users')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('download_all_users');

        $app->get(
            '/download_active_users[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_active_users')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('download_active_users');

        $app->get(
            '/community_stats[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:community_stats')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('community_stats');

        $app->get(
            '/org_stats[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:org_stats')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('org_stats');

        $app->get(
            '/community_dashboard[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:community_dashboard')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('community_dashboard');

        $app->get(
            '/language_work_requested[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:language_work_requested')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('language_work_requested');

        $app->get(
            '/download_language_work_requested[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_language_work_requested')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('download_language_work_requested');

        $app->get(
            '/translators_for_language_pairs[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:translators_for_language_pairs')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('translators_for_language_pairs');

        $app->get(
            '/download_translators_for_language_pairs[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:download_translators_for_language_pairs')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('download_translators_for_language_pairs');

        $app->map(['GET'],
            '/analytics[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:analytics')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('analytics');

        $app->map(['GET'],
            '/metabase/{report}',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:metabase')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('metabase');

        $app->get(
            '/deal/{deal_id}/report[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:deal_id_report')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_or_PO_or_FINANCE')
            ->setName('deal_id_report');

        $app->get(
            '/paid_projects[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:paid_projects')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_or_PO_or_FINANCE')
            ->setName('paid_projects');

        $app->get(
            '/all_deals_report[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:all_deals_report')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_or_PO_or_FINANCE')
            ->setName('all_deals_report');

        $app->get(
            '/sow_report[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:sow_report')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any_or_FINANCE')
            ->setName('sow_report');

        $app->get(
            '/sow_linguist_report[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:sow_linguist_report')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any_or_FINANCE')
            ->setName('sow_linguist_report');

        $app->map(['GET', 'POST'],
            '/set_invoice_paid/{invoice_number}[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:set_invoice_paid')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_or_FINANCE')
            ->setName('set_invoice_paid');

        $app->map(['GET', 'POST'],
            '/set_invoice_revoked/{invoice_number}[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:set_invoice_revoked')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_or_FINANCE')
            ->setName('set_invoice_revoked');
    }

    public function adminDashboard(Request $request, Response $response)
    {
        global $template_data;
        $userId = Common\Lib\UserSession::getCurrentUserID();
        $adminDao = new DAO\AdminDao();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();

        $roles = $adminDao->isSiteAdmin_any_or_org_admin_any_for_any_org($userId);

        $sesskey = Common\Lib\UserSession::getCSRFKey();
        
        if ($post = $request->getParsedBody()) {
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'adminDashboard')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            $taskDao = new DAO\TaskDao();
            $statsDao = new DAO\StatisticsDao();

            if (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) && !empty($post['search_user'])) {
                $items_found = $statsDao->search_user($post['search_user']);
                if (!empty($items_found)) {
                    UserRouteHandler::flashNow('search_user_results', $items_found);
                } else {
                    UserRouteHandler::flashNow('search_user_fail', 'Not Found');
                }
            }

            if (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER))) {
            if (!empty($post['search_organisation'])) {
                $items_found = $statsDao->search_organisation($post['search_organisation']);
                if (!empty($items_found)) {
                    UserRouteHandler::flashNow('search_organisation_results', $items_found);
                } else {
                    UserRouteHandler::flashNow('search_organisation_fail', 'Not Found');
                }
            }

            if (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) && !empty($post['search_project'])) {
                $items_found = $statsDao->search_project($post['search_project']);
                if (!empty($items_found)) {
                    UserRouteHandler::flashNow('search_project_results', $items_found);
                } else {
                    UserRouteHandler::flashNow('search_project_fail', 'Not Found');
                }
            }
            }

            if (isset($post['verify']) && (($roles & (SITE_ADMIN | COMMUNITY_OFFICER)) || (($roles & (NGO_ADMIN)) && $adminDao->current_user_is_NGO_admin_or_PO_for_special_registration_email($userId, $post['userEmail'])))) {
                if ($userDao->finishRegistrationManually($post['userEmail'])) {
                    UserRouteHandler::flashNow('verifySuccess', 'Email verified, the user can now login with email and password.');
                } else {
                    UserRouteHandler::flashNow('verifyError', 'Not found, either the user never registered with email and password or they have already been verified.');
                }
            }

            if (($roles & (SITE_ADMIN | PROJECT_OFFICER)) && isset($post['sync_po'])) {
                if ($number = $taskDao->sync_po()) {
                    $number--;
                    UserRouteHandler::flashNow('sync_po_success', "Purchase Orders Synchronized (Payment Status for $number Tasks Changed)");
                } else {
                    UserRouteHandler::flashNow('sync_po_error', 'Purchase Orders NOT Synchronized');
                }
            }

            if (($roles & (SITE_ADMIN | PROJECT_OFFICER)) && isset($post['sync_hubspot'])) {
                if ($taskDao->update_hubspot_deals(0)) {
                    UserRouteHandler::flashNow('sync_hubspot_success', 'HubSpot Synchronized');
                } else {
                    UserRouteHandler::flashNow('sync_hubspot_error', 'HubSpot NOT Synchronized');
                }
            }

            if (($roles & (SITE_ADMIN | FINANCE)) && isset($post['generate_invoices'])) {
                [$tasks, $invoices] = $taskDao->generate_invoices();
                if ($invoices) {
                    UserRouteHandler::flashNow('generate_invoices_success', "$tasks Tasks Invoiced in $invoices Invoices");
                } else {
                    UserRouteHandler::flashNow('generate_invoices_error', 'No Invoices Generated');
                }
            }

            if (($roles & SITE_ADMIN) && isset($post['addAdmin'])) {
                $user = $userDao->getUserByEmail($post['userEmail']);
                if (is_object($user)) {
                    $adminDao->adjust_org_admin($user->getId(), 0, 0, $post['admin_type']);
                    UserRouteHandler::flashNow('add_admin_success', 'Role Added Successfully');
                } else UserRouteHandler::flashNow('add_admin_error', 'User NOT Found');
            }
            if (($roles & SITE_ADMIN) && isset($post['revokeAdmin'])) {
                $adminDao->adjust_org_admin($post['userId'], 0, SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER, 0);
                UserRouteHandler::flashNow('revoke_admin_success', 'Site Admin Roles Revoked Successfully');
            }
            
            if (($roles & SITE_ADMIN) && isset($post['banOrg']) && $post['orgName'] != '') {
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
            if (($roles & (SITE_ADMIN | COMMUNITY_OFFICER)) && isset($post['banUser']) && $post['userEmail'] != '') {
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
            
            if (($roles & (SITE_ADMIN | PROJECT_OFFICER)) && isset($post['unBanOrg']) && $post['orgId'] != '') {
                $adminDao->unBanOrg($post['orgId']);
            }
            if (($roles & (SITE_ADMIN | COMMUNITY_OFFICER)) && isset($post['unBanUser']) && $post['userId'] != '') {
                $adminDao->unBanUser($post['userId']);
            }
            if (($roles & (SITE_ADMIN | COMMUNITY_OFFICER)) && isset($post['deleteUser']) && $post['userEmail'] != '') {
                $user = $userDao->getUserByEmail(urlencode($post['userEmail']));
                if (!is_null($user)) {
                    $userDao->deleteUser($user->getId());
                    UserRouteHandler::flashNow(
                        "deleteSuccess",
                        Lib\Localisation::getTranslation('site_admin_dashboard_successfully_deleted_user')
                    );
                } else {
                    UserRouteHandler::flashNow(
                        "deleteError",
                        Lib\Localisation::getTranslation('site_admin_dashboard_user_not_found')
                    );
                }
            }
            if (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) && isset($post['revokeTask']) && $post['revokeTask'] != '') {
                $taskId = filter_var($post['taskId'], FILTER_VALIDATE_INT);
                $userToRevokeFrom = $userDao->getUserByEmail(urlencode($post['userEmail']));
                if ($taskId && !is_null($userToRevokeFrom)) {
                    $task = $taskDao->getTask($taskId);
                    $claimantId = $taskDao->getUserClaimedTask($taskId)->getId();
                    if ($claimantId != $userToRevokeFrom->getId()) {
                        //user specified did not claim the task specified
                        UserRouteHandler::flashNow(
                            "revokeTaskError",
                            Lib\Localisation::getTranslation('site_admin_dashboard_revoke_task_error_no_claim')
                        );
                    } else {
                        $adminDao->revokeTaskFromUser($taskId, $userId);
                        UserRouteHandler::flashNow(
                            "revokeTaskSuccess",
                            Lib\Localisation::getTranslation('site_admin_dashboard_revoke_task_success'));
                    }
                } else {
                    //Invalid input supplied for user email and/or task id
                    UserRouteHandler::flashNow(
                        "revokeTaskError",
                        Lib\Localisation::getTranslation('site_admin_dashboard_revoke_task_error_invalid'));
                }
            }
        }
        
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

        $template_data = array_merge($template_data, array(
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
                    'roles'         => $roles,
                    "extra_scripts" => $extra_scripts
        ));

        return UserRouteHandler::render("admin/site-admin.dashboard.tpl", $response);
    }

    public function all_users(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->getUsers();

        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('admin/all_users.tpl', $response);
    }

    public function all_users_plain(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->getUsers();

        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('admin/all_users_plain.tpl', $response);
    }

    public function active_now(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->active_now();

        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('admin/active_now.tpl', $response);
    }

    public function testing_center(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->testing_center();

        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('admin/testing_center.tpl', $response);
    }

    public function download_testing_center(Request $request, Response $response)
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

    public function complete_matecat(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->complete_matecat();

        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('admin/complete_matecat.tpl', $response);
    }

    public function user_task_reviews(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->user_task_reviews();

        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('admin/user_task_reviews.tpl', $response);
    }

    public function submitted_task_reviews(Request $request, Response $response, $args)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->submitted_task_reviews();

        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('admin/submitted_task_reviews.tpl', $response);
    }

    public function download_submitted_task_reviews(Request $request, Response $response)
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

    public function peer_to_peer_vetting(Request $request, Response $response)
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

    public function tasks_no_reviews(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->tasks_no_reviews();

        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('admin/tasks_no_reviews.tpl', $response);
    }

    public function download_tasks_no_reviews(Request $request, Response $response)
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

    public function project_source_file_scores(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->project_source_file_scores();

        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('admin/project_source_file_scores.tpl', $response);
    }

    public function download_project_source_file_scores(Request $request, Response $response)
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

    public function first_completed_task(Request $request, Response $response)
    {
        global $template_data;
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

        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('admin/first_completed_task.tpl', $response);
    }

    public function active_users(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->active_users();

        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('admin/active_users.tpl', $response);
    }

    public function active_users_unique(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->active_users();
        $all_users_unique = array();
        foreach($all_users as $all_user) {
            $all_users_unique[$all_user['email']] = array('email' => $all_user['email'], 'display_name' => $all_user['display_name']);
        }

        $template_data = array_merge($template_data, array('all_users' => $all_users_unique));
        return UserRouteHandler::render('admin/active_users_unique.tpl', $response);
    }

    public function unclaimed_tasks(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->unclaimed_tasks();

        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('admin/unclaimed_tasks.tpl', $response);
    }

    public function search_users_by_language_pair(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        if (!empty($_POST['search_users_language_pair'])) {
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($_POST, 'search_users_by_language_pair')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            $source_target = explode('-', $_POST['search_users_language_pair']);
            if (!empty($source_target) && count($source_target) == 2) {
                $all_users = $statsDao->search_users_by_language_pair($source_target[0], $source_target[1]);
                $template_data = array_merge($template_data, array('all_users' => $all_users));
            }
        }

        return UserRouteHandler::render('admin/search_users_by_language_pair.tpl', $response);
    }

    public function user_languages(Request $request, Response $response, $args)
    {
        global $template_data;
        $code = $args['code'];

        $statsDao = new DAO\StatisticsDao();

        if ($code === 'full') $code = null;
        $all_users = $statsDao->user_languages($code);

        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('admin/user_languages.tpl', $response);
    }

    public function download_user_languages(Request $request, Response $response)
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

    public function user_task_languages(Request $request, Response $response, $args)
    {
        global $template_data;
        $code = $args['code'];

        $statsDao = new DAO\StatisticsDao();

        if ($code === 'full') $code = null;
        $all_users = $statsDao->user_task_languages($code);

        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('admin/user_task_languages.tpl', $response);
    }

    public function user_words_by_language(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->user_words_by_language();

        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('admin/user_words_by_language.tpl', $response);
    }

    public function download_user_words_by_language(Request $request, Response $response)
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

    public function download_user_task_languages(Request $request, Response $response)
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

    public function download_all_users(Request $request, Response $response)
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

    public function download_active_users(Request $request, Response $response)
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

    public function community_stats(Request $request, Response $response)
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

    public function org_stats(Request $request, Response $response)
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

    public function community_dashboard(Request $request, Response $response)
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

        $data = "\xEF\xBB\xBF" . '"TWB Platform Community"';
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

    public function language_work_requested(Request $request, Response $response)
    {
        global $template_data;
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

        $template_data = array_merge($template_data, array('words' => $words, 'years' => $years));
        return UserRouteHandler::render('admin/language_work_requested.tpl', $response);
    }

    public function download_language_work_requested(Request $request, Response $response)
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

    public function translators_for_language_pairs(Request $request, Response $response)
    {
        global $template_data;
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

        $template_data = array_merge($template_data, array('totals' => $totals, 'breakdown' => $breakdown));
        return UserRouteHandler::render('admin/translators_for_language_pairs.tpl', $response);
    }

    public function download_translators_for_language_pairs(Request $request, Response $response)
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

    public function matecat_analyse_status(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->matecat_analyse_status();

        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('admin/matecat_analyse_status.tpl', $response);
    }

    public function list_memsource_projects(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->list_memsource_projects();

        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('admin/list_memsource_projects.tpl', $response);
    }

    public function download_covid_projects(Request $request, Response $response)
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

    public function download_afghanistan_2021_projects(Request $request, Response $response)
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

    public function download_haiti_2021_projects(Request $request, Response $response)
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

    public function analytics()
    {
        echo file_get_contents('/repo/TWB Analytics.html');
        die;
    }

    public function metabase(Request $request, Response $response, $args)
    {
        require_once '/repo/metabase_reports/' . $args['report'];
        die;
    }

    public function deal_id_report(Request $request, Response $response, $args)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $template_data['pos'] = $statsDao->deal_id_report($args['deal_id']);
        return UserRouteHandler::render('admin/deal_id_report.tpl', $response);
    }

    public function paid_projects(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $template_data['paid_projects'] = $statsDao->paid_projects();
        return UserRouteHandler::render('admin/paid_projects.tpl', $response);
    }

    public function all_deals_report(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $template_data['all_deals'] = $statsDao->all_deals_report();
        return UserRouteHandler::render('admin/all_deals_report.tpl', $response);
    }

    public function sow_report(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $template_data['tasks'] = $statsDao->sow_report();
        return UserRouteHandler::render('admin/sow_report.tpl', $response);
    }

    public function sow_linguist_report(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();
        $adminDao = new DAO\AdminDao();

        $template_data['tasks'] = $statsDao->sow_linguist_report();
        $template_data['roles'] = $adminDao->get_roles(Common\Lib\UserSession::getCurrentUserID());
        $template_data['sesskey'] = Common\Lib\UserSession::getCSRFKey();
        return UserRouteHandler::render('admin/sow_linguist_report.tpl', $response);
    }

    public function set_invoice_paid(Request $request, Response $response, $args)
    {
        $taskDao = new DAO\TaskDao();

        $result = 1;
        if (Common\Lib\UserSession::checkCSRFKey($request->getParsedBody(), 'set_invoice_paid')) $result = 0;
        if ($result) $taskDao->set_invoice_paid($args['invoice_number']);
        $results = json_encode(['result'=> $result]);
        $response->getBody()->write($results);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function set_invoice_revoked(Request $request, Response $response, $args)
    {
        $taskDao = new DAO\TaskDao();

        $result = 1;
        if (Common\Lib\UserSession::checkCSRFKey($request->getParsedBody(), 'set_invoice_revoked')) $result = 0;
        if ($result) $taskDao->set_invoice_revoked($args['invoice_number']);
        $results = json_encode(['result'=> $result]);
        $response->getBody()->write($results);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

$route_handler = new AdminRouteHandler();
$route_handler->init();
unset ($route_handler);
