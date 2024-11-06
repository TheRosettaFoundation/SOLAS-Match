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

        $app->get(
            '/list_memsource_projects[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:list_memsource_projects')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('list_memsource_projects');

        $app->get(
            '/community_dashboard[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:community_dashboard')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('community_dashboard');

        $app->map(['GET'],
            '/analytics[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:analytics')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any_or_FINANCE')
            ->setName('analytics');

        $app->map(['GET'],
            '/metabase/{report}',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:metabase')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any_or_FINANCE')
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
            '/po_readyness_report[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:po_readyness_report')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any_or_FINANCE')
            ->setName('po_readyness_report');

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

        $app->get(
            '/po_report[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:po_report')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any_or_FINANCE')
            ->setName('po_report');

        $app->map(['GET', 'POST'],
            '/set_invoice_paid/{invoice_number}[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:set_invoice_paid')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_or_FINANCE')
            ->setName('set_invoice_paid');

        $app->map(['GET', 'POST'],
            '/set_invoice_bounced/{invoice_number}[/]',
            '\SolasMatch\UI\RouteHandlers\AdminRouteHandler:set_invoice_bounced')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_or_FINANCE')
            ->setName('set_invoice_bounced');

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
                    $projectDao = new DAO\projectDao();
                    if ($projectDao->getUserClaimedTask($taskId) != $userToRevokeFrom->getId()) {
                        UserRouteHandler::flashNow('revokeTaskError', Lib\Localisation::getTranslation('site_admin_dashboard_revoke_task_error_no_claim'));
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

    public function list_memsource_projects(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $all_users = $statsDao->list_memsource_projects();

        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('admin/list_memsource_projects.tpl', $response);
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

    public function po_readyness_report(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $template_data['tasks'] = $statsDao->po_readyness_report();
        return UserRouteHandler::render('admin/po_readyness_report.tpl', $response);
    }

    public function sow_report(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $parms = $request->getQueryParams();

        $template_data['tasks']   = $statsDao->sow_report();
        $template_data['po']      = !empty($parms['po']) ? $parms['po'] : 0;
        $template_data['claimed'] = !empty($parms['claimed']) ? 1 : 0;
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

    public function po_report(Request $request, Response $response)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $template_data['pos'] = $statsDao->po_report();
        return UserRouteHandler::render('admin/po_report.tpl', $response);
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

    public function set_invoice_bounced(Request $request, Response $response, $args)
    {
        $taskDao = new DAO\TaskDao();

        $result = 1;
        if (Common\Lib\UserSession::checkCSRFKey($request->getParsedBody(), 'set_invoice_bounced')) $result = 0;
        if ($result) $taskDao->set_invoice_bounced($args['invoice_number']);
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
