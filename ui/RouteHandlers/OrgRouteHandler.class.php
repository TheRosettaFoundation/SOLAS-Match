<?php

namespace SolasMatch\UI\RouteHandlers;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\Common as Common;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__."/../lib/Validator.php";

class OrgRouteHandler
{
    public function init()
    {
        global $app;

        $app->map(['GET', 'POST'],
            '/org/create[/]',
            '\SolasMatch\UI\RouteHandlers\OrgRouteHandler:createOrg')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_or_PO')
            ->setName('create-org');

        $app->map(['GET', 'POST'],
            '/org/dashboard[/]',
            '\SolasMatch\UI\RouteHandlers\OrgRouteHandler:orgDashboard')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any_or_org_admin_or_po_for_any_org')
            ->setName('org-dashboard');

        $app->get(
            '/org/{org_id}/org_dashboard[/]',
            '\SolasMatch\UI\RouteHandlers\OrgRouteHandler:org_orgDashboard')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->setName('org-projects');

        $app->get(
            '/org/{org_id}/metabase[/]',
            '\SolasMatch\UI\RouteHandlers\OrgRouteHandler:metabase')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrg_incl_community_officer')
            ->setName('metabase_ngo');

        $app->map(['GET', 'POST'],
            '/org/{org_id}/private[/]',
            '\SolasMatch\UI\RouteHandlers\OrgRouteHandler:orgPrivateProfile')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrg_incl_community_officer')
            ->setName('org-private-profile');

        $app->map(['GET', 'POST'],
            '/org/{org_id}/profile[/]',
            '\SolasMatch\UI\RouteHandlers\OrgRouteHandler:orgPublicProfile')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->setName('org-public-profile');

        $app->map(['GET'],
            '/org/{org_id}/partner_deals[/]',
            '\SolasMatch\UI\RouteHandlers\OrgRouteHandler:partner_deals')
            ->add('\SolasMatch\UI\Lib\Middleware:auth_admin_any_or_ngo_admin')
            ->setName('partner_deals');

        $app->map(['GET', 'POST'],
            '/org/{org_id}/manage/{badge_id}[/]',
            '\SolasMatch\UI\RouteHandlers\OrgRouteHandler:orgManageBadge')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrg_incl_community_officer')
            ->setName('org-manage-badge');

        $app->map(['GET', 'POST'],
            '/org/{org_id}/create/badge[/]',
            '\SolasMatch\UI\RouteHandlers\OrgRouteHandler:orgCreateBadge')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrg_incl_community_officer')
            ->setName('org-create-badge');

        $app->map(['GET', 'POST'],
            '/org/search[/]',
            '\SolasMatch\UI\RouteHandlers\OrgRouteHandler:orgSearch')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('org-search');
        
        $app->map(['GET', 'POST'],
            '/org/{org_id}/edit/{badge_id}[/]',
            '\SolasMatch\UI\RouteHandlers\OrgRouteHandler:orgEditBadge')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrg_incl_community_officer')
            ->setName('org-edit-badge');

        $app->get(
            '/org/{org_id}/task/{task_id}/complete[/]',
            '\SolasMatch\UI\RouteHandlers\OrgRouteHandler:orgTaskComplete')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrg_incl_community_officer')
            ->setName('org-task-complete');

        $app->map(['GET', 'POST'],
            '/org/{org_id}/task/{task_id}/review[/]',
            '\SolasMatch\UI\RouteHandlers\OrgRouteHandler:orgTaskReview')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrg_incl_community_officer')
            ->setName('org-task-review');

        $app->get(
            '/org/{org_id}/task/{task_id}/reviews[/]',
            '\SolasMatch\UI\RouteHandlers\OrgRouteHandler:orgTaskReviews')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrg_incl_community_officer')
            ->setName('org-task-reviews');

        $app->get(
            '/org/{org_id}/org_members[/]',
            '\SolasMatch\UI\RouteHandlers\OrgRouteHandler:org_members')
            ->add('\SolasMatch\UI\Lib\Middleware:auth_admin_any_or_ngo_admin')
            ->setName('org_members');

        $app->map(['GET', 'POST'],
            '/set_entitlement[/]',
            '\SolasMatch\UI\RouteHandlers\OrgRouteHandler:set_entitlement')
            ->setName('set_entitlement');
    }

    public function createOrg(Request $request, Response $response)
    {
        global $app, $template_data;

        if (empty($_SESSION['SESSION_CSRF_KEY'])) {
            $_SESSION['SESSION_CSRF_KEY'] = Common\Lib\UserSession::random_string(10);
        }
        $sesskey = $_SESSION['SESSION_CSRF_KEY']; // This is a check against CSRF (Posts should come back with same sesskey)

        $org = null;
        $errorOccured = null;
        $errorList = array();
        if ($post = $request->getParsedBody()) {
            $org = new Common\Protobufs\Models\Organisation();

            if (!empty($post['sesskey']) && $post['sesskey'] === $sesskey && isset($post['orgName']) && $post['orgName'] != '') {
                if (Lib\Validator::filterSpecialChars($post['orgName'])) {
                    $org->setName($post['orgName']);
                } else {
                    $errorMsg = Lib\Localisation::getTranslation('create_org_invalid_name')
                        ." "
                        .Lib\Localisation::getTranslation('common_invalid_characters');
                    $errorList[] = $errorMsg;
                    $errorOccured = true;
                }
            } else {
                $errorOccured = true;
                array_push($errorList, Lib\Localisation::getTranslation('common_error_org_name_not_set'));
            }
            
            if (isset($post["homepage"])) {
                if (trim($post["homepage"])!="") {
                    if (Lib\Validator::validateURL($post["homepage"])) {
                        $org->setHomepage(Lib\Validator::addhttp($post['homepage']));
                    } else {
                        $errorOccured = true;
                        array_push($errorList, Lib\Localisation::getTranslation('common_invalid_url'));
                    }
                }
            }

            if (empty($post['biography'])) {
                $errorOccured = true;
                $errorList[] = Lib\Localisation::getTranslation('org_private_profile_organisation_error_overview_not_set');
            } else {
                $org->setBiography($post["biography"]);
            }
            if (isset($post["country"])) {
                $org->setCountry($post["country"]);
            }
            if (isset($post["email"])) {
                if (trim($post["email"])!="") {
                    if (Lib\Validator::validateEmail($post["email"])) {
                        $org->setEmail($post["email"]);
                    } else {
                        $errorOccured = true;
                        array_push($errorList, Lib\Localisation::getTranslation('user_reset_password_4'));
                    }
                } else {
                    $errorOccured = true;
                    $errorList[] = Lib\Localisation::getTranslation('org_private_profile_organisation_error_email_not_set');
                }
            } else {
                $errorOccured = true;
                $errorList[] = Lib\Localisation::getTranslation('org_private_profile_organisation_error_email_not_set');
            }

            if (isset($post['facebook'])) {
                if (trim($post['facebook']) != '') {
                    if (Lib\Validator::validateURL($post['facebook'])) {
                        $org->setAddress(Lib\Validator::addhttp($post['facebook']));
                    } else {
                        $errorOccured = true;
                        $errorList[] = Lib\Localisation::getTranslation('common_invalid_url');
                    }
                }
            }
            if (isset($post['linkedin'])) {
                if (trim($post['linkedin']) != '') {
                    if (Lib\Validator::validateURL($post['linkedin'])) {
                        $org->setCity(Lib\Validator::addhttp($post['linkedin']));
                    } else {
                        $errorOccured = true;
                        $errorList[] = Lib\Localisation::getTranslation('common_invalid_url');
                    }
                }
            }
            if (isset($post['twitter'])) {
                if (trim($post['twitter']) != '') {
                    if (Lib\Validator::validateURL($post['twitter'])) {
                        $org->setRegionalFocus(Lib\Validator::addhttp($post['twitter']));
                    } else {
                        $errorOccured = true;
                        $errorList[] = Lib\Localisation::getTranslation('common_invalid_url');
                    }
                }
            }

            if (is_null($errorOccured)) {
                $user_id = Common\Lib\UserSession::getCurrentUserID();
                $orgDao = new DAO\OrganisationDao();
                $projectDao = new DAO\ProjectDao();

                try {
                    error_log("Calling createOrg(, $user_id)");
                    $new_org = $orgDao->createOrg($org, $user_id);
                    if ($new_org) {
                        $org_name = $org->getName();
                        $org_biography = $org->getBiography();
                        error_log("Called createOrg() for: $org_name");
                        UserRouteHandler::flash(
                            'success',
                            sprintf(Lib\Localisation::getTranslation('create_org_created'), $org_name)
                        );

                        // Create Client on Memsource
                        $memsourceApiV1 = Common\Lib\Settings::get('memsource.api_url_v1');
                        $memsourceApiToken = Common\Lib\Settings::get('memsource.memsource_api_token');
                        $url = $memsourceApiV1 . 'clients';
                        $ch = curl_init($url);
                        $data = array(
                            'name' => $org_name,
                            'note' => $org_biography,
                            'displayNoteInProject' => true
                        );
                        $payload = json_encode($data);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                        $authorization = 'Authorization: Bearer ' . $memsourceApiToken;
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $result = curl_exec($ch);
                        curl_close($ch);
                        $res = json_decode($result, true);
                        $projectDao->set_memsource_client($new_org->getId(), $res['id'], $res['uid']);

                        return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('org-dashboard'));
                    }
                } catch (Common\Exceptions\SolasMatchException $ex) {
                    $org_name = $org->getName();
                    $errorList[] = sprintf(
                        Lib\Localisation::getTranslation('common_error_org_name_in_use'),
                        $org_name
                    );
                    $errorOccured = true;
                }
            }
        }

        $template_data = array_merge($template_data, array(
            'org'  => $org,
            'errorOccured' => $errorOccured,
            'errorList'    => $errorList,
            'sesskey'      => $sesskey,
        ));

        return UserRouteHandler::render("org/create-org.tpl", $response);
    }

    public function orgDashboard(Request $request, Response $response)
    {
        global $app, $template_data;
        $current_user_id = Common\Lib\UserSession::getCurrentUserID();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();
        $tagDao = new DAO\TagDao();
        $projectDao = new DAO\ProjectDao();
        $adminDao = new DAO\AdminDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();
        
        $current_user = $userDao->getUser($current_user_id);
        $my_organisations = $userDao->getUserOrgs($current_user_id);
        
        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'orgDashboard')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            if (isset($post['track'])) {
                $project_id = $post['project_id'];
                $project = $projectDao->getProject($project_id);

                $project_title = "";
                if ($project->getTitle() != "") {
                    $project_title = $project->getTitle();
                } else {
                    $project_title = "project ".$project->getId();
                }
                if ($post['track'] == "Ignore") {
                    $success = $userDao->untrackProject($current_user_id, $project_id);
                    if ($success) {
                        UserRouteHandler::flashNow(
                            "success",
                            sprintf(Lib\Localisation::getTranslation('org_dashboard_5'), $project_title)
                        );
                    } else {
                        UserRouteHandler::flashNow(
                            "error",
                            sprintf(Lib\Localisation::getTranslation('org_dashboard_6'), $project_title)
                        );
                    }
                } elseif ($post['track'] == "Track") {
                    $success = $userDao->trackProject($current_user_id, $project_id);
                    if ($success) {
                        UserRouteHandler::flashNow(
                            "success",
                            sprintf(Lib\Localisation::getTranslation('org_dashboard_7'), $project_title)
                        );
                    } else {
                        UserRouteHandler::flashNow(
                            "error",
                            sprintf(Lib\Localisation::getTranslation('org_dashboard_8'), $project_title)
                        );
                    }
                }
            }
        }
        $create_non_phrase = [];
        if ($my_organisations) {
            $orgs = array();
            $templateData = array();
            foreach ($my_organisations as $org) {
                $my_org_projects = $projectDao->getOrgProjects($org->getId(), 3);
                $orgs[$org->getId()] = $org;
                if (in_array($org->getId(), ORG_EXCEPTIONS) && ($adminDao->get_roles($current_user_id, $org->getId())&(NGO_ADMIN | NGO_PROJECT_OFFICER))) $create_non_phrase[] = $org->getId();

                $taskData = array();
                if ($my_org_projects) {
                    foreach ($my_org_projects as $project) {
                        $temp = array();
                        $temp['project'] = $project;
                        $taskData[]=$temp;
                    }
                } else {
                    $taskData = null;
                }
                $templateData[$org->getId()] = $taskData;
            }

            $template_data = array_merge($template_data, array(
                "orgs" => $orgs,
                "templateData" => $templateData
            ));
        }

        $extra_scripts = file_get_contents(__DIR__."/../js/TaskView.js");
        // Load Twitter JS asynch, see https://dev.twitter.com/web/javascript/loading
        $extra_scripts .= '<script>window.twttr = (function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0], t = window.twttr || {}; if (d.getElementById(id)) return t; js = d.createElement(s); js.id = id; js.src = "https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs); t._e = []; t.ready = function(f) { t._e.push(f); }; return t; }(document, "script", "twitter-wjs"));</script>';

        $template_data = array_merge($template_data, array(
            'sesskey'       => $sesskey,
            'roles'         => $adminDao->get_roles($current_user_id),
            'create_non_phrase' => $create_non_phrase,
            "extra_scripts" => $extra_scripts,
            "current_page"  => "org-dashboard"
        ));
        return UserRouteHandler::render("org/org.dashboard.tpl", $response);
    }

    public function org_orgDashboard(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $org_id = $args['org_id'];

        $current_user_id = Common\Lib\UserSession::getCurrentUserID();
        $userDao = new DAO\UserDao();
        $projectDao = new DAO\ProjectDao();
        $adminDao = new DAO\AdminDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $current_user = $userDao->getUser($current_user_id);
        $my_organisations = $userDao->getUserOrgs($current_user_id);

        $create_non_phrase = [];
        if ($my_organisations) {
            foreach ($my_organisations as $index => $org) {
                if ($org->getId() != $org_id) {
                    unset($my_organisations[$index]);
                }
            }

            $orgs = array();
            $templateData = array();
            foreach ($my_organisations as $org) {
                $my_org_projects = $projectDao->getOrgProjects($org->getId(), 500); // About 50 years
                $orgs[$org->getId()] = $org;
                if (in_array($org->getId(), ORG_EXCEPTIONS) && ($adminDao->get_roles($current_user_id, $org->getId())&(NGO_ADMIN | NGO_PROJECT_OFFICER))) $create_non_phrase[] = $org->getId();

                $taskData = array();
                if ($my_org_projects) {
                    foreach ($my_org_projects as $project) {
                        $temp = array();
                        $temp['project'] = $project;
                        $taskData[]=$temp;
                    }
                } else {
                    $taskData = null;
                }
                $templateData[$org->getId()] = $taskData;
            }

            $template_data = array_merge($template_data, array(
                'orgs'         => $orgs,
                'templateData' => $templateData
            ));
        }

        $extra_scripts = file_get_contents(__DIR__ . '/../js/TaskView.js');
        $extra_scripts .= '<script>window.twttr = (function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0], t = window.twttr || {}; if (d.getElementById(id)) return t; js = d.createElement(s); js.id = id; js.src = "https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs); t._e = []; t.ready = function(f) { t._e.push(f); }; return t; }(document, "script", "twitter-wjs"));</script>';

        $template_data = array_merge($template_data, array(
            'sesskey'         => $sesskey,
            'roles'           => $adminDao->get_roles($current_user_id),
            'create_non_phrase' => $create_non_phrase,
            'extra_scripts'   => $extra_scripts,
            'beyond_3_months' => 1,
            'current_page'    => 'org-dashboard'
        ));
        return UserRouteHandler::render('org/org.dashboard.tpl', $response);
    }

    public function metabase(Request $request, Response $response, $args)
    {
        global  $template_data;

        require_once '/repo/SOLAS-Match/analytics/vendor/autoload.php';
        $metabase = new \Metabase\Embed('https://analytics.translatorswb.org/metabase', Common\Lib\Settings::get('metabase.key'), false, '100%', '800', true, 6*60*60);

        $template_data = array_merge($template_data, ['iframe' => $metabase->dashboardIframe(291, ['org_id' => $args['org_id']])]);
        return UserRouteHandler::render('org/metabase_ngo.tpl', $response);
    }

    public function orgPrivateProfile(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $org_id = $args['org_id'];

        if (empty($_SESSION['SESSION_CSRF_KEY'])) {
            $_SESSION['SESSION_CSRF_KEY'] = Common\Lib\UserSession::random_string(10);
        }
        $sesskey = $_SESSION['SESSION_CSRF_KEY']; // This is a check against CSRF (Posts should come back with same sesskey)

        $adminDao = new DAO\AdminDao();
        $orgDao = new DAO\OrganisationDao();
        $org = $orgDao->getOrganisation($org_id);
        $userId = Common\Lib\UserSession::getCurrentUserId();
        
        $errorOccured = null;
        $errorList=array();
        
        if ($post = $request->getParsedBody()) {

            if (isset($post['updateOrgDetails'])) {
                if (!empty($post['sesskey']) && $post['sesskey'] === $sesskey && isset($post['orgName']) && $post['orgName'] != '') {
                    //Check if new org title has forbidden characters
                    if (Lib\Validator::filterSpecialChars($post['orgName'])) {
                        $org->setName($post['orgName']);
                    } else {
                        $errorOccured = true;
                        array_push(
                            $errorList,
                            Lib\Localisation::getTranslation('create_org_invalid_name')
                            ." "
                            .Lib\Localisation::getTranslation('common_invalid_characters')
                        );
                    }
                //Name is not set
                } else {
                    $errorOccured = true;
                    $errorList[] = Lib\Localisation::getTranslation('common_error_org_name_not_set');
                }
                if (empty($post['biography'])) {
                    $errorOccured = true;
                    $errorList[] = Lib\Localisation::getTranslation('org_private_profile_organisation_error_overview_not_set');
                }
                if (isset($post['homepage'])) {
                    if (trim($post["homepage"])!="") {
                        if (Lib\Validator::validateURL($post["homepage"])) {
                            $org->setHomepage(Lib\Validator::addhttp($post['homepage']));
                        } else {
                            $errorOccured = true;
                            array_push($errorList, Lib\Localisation::getTranslation('common_invalid_url'));
                        }
                    } else $org->setHomepage('https://');
                }
                if (isset($post['biography'])) {
                    $org->setBiography($post['biography']);
                }
                if (isset($post['country'])) {
                    $org->setCountry($post['country']);
                }
                if (isset($post["email"])) {
                    if (trim($post["email"])!="") {
                        if (Lib\Validator::validateEmail($post["email"])) {
                            $org->setEmail($post["email"]);
                        } else {
                            $errorOccured = true;
                            array_push($errorList, Lib\Localisation::getTranslation('user_reset_password_4'));
                        }
                    } else {
                        $errorOccured = true;
                        $errorList[] = Lib\Localisation::getTranslation('org_private_profile_organisation_error_email_not_set');
                    }
                } else {
                    $errorOccured = true;
                    $errorList[] = Lib\Localisation::getTranslation('org_private_profile_organisation_error_email_not_set');
                }

                if (isset($post['facebook'])) {
                    if (trim($post['facebook']) != '') {
                        if (Lib\Validator::validateURL($post['facebook'])) {
                            $org->setAddress(Lib\Validator::addhttp($post['facebook']));
                        } else {
                            $errorOccured = true;
                            $errorList[] = Lib\Localisation::getTranslation('common_invalid_url');
                        }
                    } else $org->setAddress('');
                }
                if (isset($post['linkedin'])) {
                    if (trim($post['linkedin']) != '') {
                        if (Lib\Validator::validateURL($post['linkedin'])) {
                            $org->setCity(Lib\Validator::addhttp($post['linkedin']));
                        } else {
                            $errorOccured = true;
                            $errorList[] = Lib\Localisation::getTranslation('common_invalid_url');
                        }
                    } else $org->setCity('');
                }
                if (isset($post['twitter'])) {
                    if (trim($post['twitter']) != '') {
                        if (Lib\Validator::validateURL($post['twitter'])) {
                            $org->setRegionalFocus(Lib\Validator::addhttp($post['twitter']));
                        } else {
                            $errorOccured = true;
                            $errorList[] = Lib\Localisation::getTranslation('common_invalid_url');
                        }
                    } else $org->setRegionalFocus('');
                }

                if (!is_null($errorOccured)) {
                    $template_data = array_merge($template_data, array(
                    "errorOccured" => $errorOccured,
                    "errorList" => $errorList
                    ));
                } else {
                    $orgDao = new DAO\OrganisationDao();
                    try {
                        $orgDao->updateOrg($org);
                        return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("org-public-profile", array("org_id" => $org->getId())));
                    } catch (Common\Exceptions\SolasMatchException $ex) {
                        $org_name = $org->getName();
                        UserRouteHandler::flashNow(
                            "error",
                            sprintf(
                                Lib\Localisation::getTranslation('common_error_org_name_in_use'),
                                $org_name
                            )
                        );
                    }
                }
            }

            if (isset($post['deleteId']) && ($adminDao->get_roles($userId) & SITE_ADMIN)) {
                $deleteId = $post['deleteId'];
                if (false && $deleteId) {
                    if (!empty($post['sesskey']) && $post['sesskey'] === $sesskey && $orgDao->deleteOrg($org->getId())) {
                        UserRouteHandler::flash(
                            "success",
                            sprintf(
                                Lib\Localisation::getTranslation('org_private_profile_delete_success'),
                                $org->getName()
                            )
                        );
                        return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("home"));
                    } else {
                        UserRouteHandler::flashNow("error", Lib\Localisation::getTranslation('org_private_profile_delete_fail'));
                    }
                }
            }
        }

        if ($adminDao->get_roles($userId) & SITE_ADMIN) {
            $template_data = array_merge($template_data, array('orgAdmin' => true));
        }
        
        $template_data = array_merge($template_data, array(
            'org'  => $org,
            'sesskey' => $sesskey,
        ));

        return UserRouteHandler::render("org/org-private-profile.tpl", $response);
    }

    public function orgPublicProfile(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $org_id = $args['org_id'];

        $adminDao = new DAO\AdminDao();
        $orgDao = new DAO\OrganisationDao();
        $projectDao = new DAO\ProjectDao();
        $userDao = new DAO\UserDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $currentUser = $userDao->getUser(Common\Lib\UserSession::getCurrentUserId());
        $current_user_id = $currentUser->getId();
        $roles = $adminDao->get_roles($current_user_id, $org_id);
        $org = $orgDao->getOrganisation($org_id);

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'orgPublicProfile')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);
                   
            if ($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | NGO_ADMIN)) {
                if (isset($post['revokeUser'])) {
                    $user_id = $post['revokeUser'];
                    $adminDao->adjust_org_admin($user_id, $org_id, NGO_ADMIN | NGO_PROJECT_OFFICER | NGO_LINGUIST, 0);
                    $adminDao->adjust_org_admin($user_id, 0, 0, LINGUIST);
                    UserRouteHandler::flashNow('success', 'Successfully revoked membership from user');
                    error_log("revokeUser($user_id, $org_id) by $current_user_id");
                } elseif (isset($post['revokeOrgAdmin'])) {
                    $user_id = $post['revokeOrgAdmin'];
                    $adminDao->adjust_org_admin($user_id, $org_id, NGO_ADMIN, NGO_PROJECT_OFFICER);
                    error_log("revokeOrgAdmin($user_id, $org_id) by $current_user_id");
                } elseif (isset($post['revokeOrgPO'])) {
                    $user_id = $post['revokeOrgPO'];
                    $adminDao->adjust_org_admin($user_id, $org_id, NGO_PROJECT_OFFICER, NGO_LINGUIST);
                    error_log("revokeOrgPO($user_id, $org_id) by $current_user_id");
                } elseif (isset($post['makeOrgAdmin'])) {
                    $user_id = $post['makeOrgAdmin'];
                    $adminDao->adjust_org_admin($user_id, $org_id, 0, NGO_ADMIN);
                    error_log("makeOrgAdmin($user_id, $org_id) by $current_user_id");
                } elseif (isset($post['makeOrgPO'])) {
                    $user_id = $post['makeOrgPO'];
                    $user_id = $post['makeOrgPO'];
                    $adminDao->adjust_org_admin($user_id, $org_id, 0, NGO_PROJECT_OFFICER);
                    error_log("makeOrgPO($user_id, $org_id) by $current_user_id");
                }
            }
            if ($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) {
                if (isset($post['asana_board'])) {
                    if (preg_match('/^\d*$/', $post['asana_board'])) $userDao->set_asana_board_for_org($org_id, $post['asana_board']);
                    else UserRouteHandler::flash('error', 'Asana ID must consist only of decimal digits.');
                }
                if (isset($post['set_mt_for_org'])) {
                    $userDao->set_mt_for_org($org_id, empty($post['mt_for_org']) ? 0 : 1);
                }
                if (isset($post['set_image_for_org'])) {
                    if (empty($_FILES['org_image']['error']) && !empty($_FILES['org_image']['tmp_name']) && (($data = file_get_contents($_FILES['org_image']['tmp_name'])) !== false)) {
                      if (in_array($_FILES['org_image']['type'], ['image/jpeg', 'image/png', 'image/webp'])) {
                        list($width, $height) = getimagesize($_FILES['org_image']['tmp_name']);
                        $ratio = min(200/$width, 200/$height);
                        $new_width  = floor($width*$ratio);
                        $new_height = floor($height*$ratio);
                        $img = imagecreatefromjpeg($_FILES['org_image']['tmp_name']);
                        $tci = imagecreatetruecolor($new_width, $new_height);
                        if (!empty($img) && $tci !== false) {
                            if (imagecopyresampled($tci, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height)) imagejpeg($tci, $_FILES['org_image']['tmp_name'], 100);
                        }
                        if (($data = file_get_contents($_FILES['org_image']['tmp_name'])) !== false) $userDao->add_org_image($org_id, $_FILES['org_image']['type'], $data, $current_user_id);
                      } else UserRouteHandler::flashNow('error', 'Only JPEG, PNG and WEBP are supported, but only JPEG will be resized to a suitable size.');
                    }
                }
            }
        }
        $orgMemberList = $adminDao->getOrgMembers($org_id);

        $template_data = array_merge($template_data, array(
                'current_page' => 'org-public-profile',
                'sesskey' => $sesskey,
                "org" => $org,
                'orgMembers' => $orgMemberList,
                'asana_board_for_org' => $userDao->get_asana_board_for_org($org_id),
                'mt_for_org' => $userDao->get_mt_for_org($org_id),
                'entitlements' => $projectDao->get_entitlements($org_id),
                'org_image' => $userDao->get_org_image($org_id),
                'extra_styles' => file_get_contents(__DIR__ . '/../../resources/css/task_page.css'),
        ));

        return UserRouteHandler::render("org/org-public-profile.tpl", $response);
    }

    public function partner_deals(Request $request, Response $response, $args)
    {
        global $template_data;
        $statsDao = new DAO\StatisticsDao();

        $template_data['deals'] = $statsDao->partner_deals($args['org_id']);
        return UserRouteHandler::render('admin/partner_deals.tpl', $response);
    }

    public function orgManageBadge(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $org_id = $args['org_id'];
        $badge_id = $args['badge_id'];

        $badgeDao = new DAO\BadgeDao();
        $userDao = new DAO\UserDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $badge = $badgeDao->getBadge($badge_id);
        $extra_scripts = "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}";
        $extra_scripts .= "resources/bootstrap/js/confirm-remove-badge.js\"></script>";
        $template_data = array_merge($template_data, array(
                    'badge'         => $badge,
                    "org_id"        => $org_id,
                    "extra_scripts" =>$extra_scripts
        ));

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'orgManageBadge')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);
            
            if (isset($post['email']) && $post['email'] != "") {
                if (Lib\Validator::validateEmail($post['email'])) {
                    $success = $userDao->assignBadge($post['email'], $badge->getId());
                    if ($success) {
                        UserRouteHandler::flashNow(
                            "success",
                            sprintf(
                                Lib\Localisation::getTranslation('org_manage_badge_29'),
                                $badge->getTitle(),
                                $post['email']
                            )
                        );
                    } else {
                        UserRouteHandler::flashNow(
                            "error",
                            sprintf(Lib\Localisation::getTranslation('org_manage_badge_30'), $post['email'])
                        );
                    }
                } else {
                    UserRouteHandler::flashNow("error", Lib\Localisation::getTranslation('common_no_valid_email'));
                }
            } elseif (isset($post['user_id']) && $post['user_id'] != "") {
                $user_id = $post['user_id'];
                $user = $userDao->getUser($user_id);
                $userDao->removeUserBadge($user_id, $badge_id);
                $user_name = "";
                if ($user->getDisplayName() != "") {
                    $user_name = $user->getDisplayName();
                } else {
                    $user_name = $user->getEmail();
                }
                UserRouteHandler::flashNow(
                    "success",
                    sprintf(Lib\Localisation::getTranslation('org_manage_badge_32'), $user_name)
                );
            }
        }
    
        $user_list = $badgeDao->getUserWithBadge($badge_id);

        $template_data = array_merge($template_data, array(
            'sesskey' => $sesskey,
            "user_list" => $user_list
        ));
        
        return UserRouteHandler::render("org/org.manage-badge.tpl", $response);
    }

    public function orgCreateBadge(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $org_id = $args['org_id'];

        $badgeDao = new DAO\BadgeDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        if ($request->getMethod() === 'POST' && sizeof($request->getParsedBody()) > 2) {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'orgCreateBadge')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);
            
            if ($post['title'] == "" || $post['description'] == "") {
                UserRouteHandler::flashNow("error", Lib\Localisation::getTranslation('common_all_fields'));
            } else {
                $badge = new Common\Protobufs\Models\Badge();
                $badge->setTitle($post['title']);
                $badge->setDescription($post['description']);
                $badge->setOwnerId($org_id);
                $badgeDao->createBadge($badge);
                
                UserRouteHandler::flash("success", Lib\Localisation::getTranslation('org_create_badge_33'));
                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("org-public-profile", array("org_id" => $org_id)));
            }
        }
        
        $template_data = array_merge($template_data, array(
            'org_id'  => $org_id,
            'sesskey' => $sesskey,
        ));
        return UserRouteHandler::render("org/org.create-badge.tpl", $response);
    }

    public function orgSearch(Request $request, Response $response)
    {
        global $app, $template_data;
        $orgDao = new DAO\OrganisationDao();
        $foundOrgs = array();

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();

            if (isset($post['search_name']) && $post['search_name'] != '') {
                $foundOrgs = $orgDao->searchForOrgByName(urlencode($post['search_name']));
                if (empty($foundOrgs)) {
                    UserRouteHandler::flashNow("error", Lib\Localisation::getTranslation('org_search_34'));
                }
                $template_data = array_merge($template_data, array('searchedText' => $post['search_name']));
            }

            if (isset($post['allOrgs'])) {
                $foundOrgs = $orgDao->getOrganisations();
            }
        }

        $template_data = array_merge($template_data, array(
                    'foundOrgs'     => $foundOrgs
        ));

        return UserRouteHandler::render("org/org-search.tpl", $response);
    }
    
    public function orgEditBadge(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $org_id = $args['org_id'];
        $badge_id = $args['badge_id'];

        $badgeDao = new DAO\BadgeDao();

        $badge = $badgeDao->getBadge($badge_id);
        $template_data = array_merge($template_data, ['badge' => $badge, 'org_id' => $org_id, 'sesskey' => Common\Lib\UserSession::getCSRFKey()]);
        
        return UserRouteHandler::render("org/org.edit-badge.tpl", $response);
    }

    public function orgTaskComplete(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $orgId = $args['org_id'];
        $taskId = $args['task_id'];

        $taskDao = new DAO\TaskDao();
        $userDao = new DAO\UserDao();
        $projectDao = new DAO\ProjectDao();
        $userName = '';
        $claimant = $taskDao->getUserClaimedTask($taskId);
        $claimantProfile = "";
        if ($claimant != null) {
            $claimantProfile = $app->getRouteCollector()->getRouteParser()->urlFor("user-public-profile", array('user_id' => $claimant->getId()));
            $userName = $claimant->getDisplayName();
        }

        $task = $taskDao->getTask($taskId);
        $memsource_task = $projectDao->get_memsource_task($taskId);
        $viewData = array(
                "task"              => $task,
                'claimant'          => $claimant,
                'userName'          => $userName,
                'claimantProfile'   => $claimantProfile,
                'allow_download'    => $taskDao->get_allow_download($task, $memsource_task),
                'memsource_task'    => $memsource_task,
                "orgId"             => $orgId
        );

        $template_data = array_merge($template_data, $viewData);
        return UserRouteHandler::render("org/org.task-complete.tpl", $response);
    }

    public function orgTaskReview(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $orgId = $args['org_id'];
        $taskId = $args['task_id'];

        $taskDao = new DAO\TaskDao();
        $userDao = new DAO\UserDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $userId = Common\Lib\UserSession::getCurrentUserID();
        $task = $taskDao->getTask($taskId);

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'orgTaskReview')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            if (isset($post['submitReview'])) {
                $review = new Common\Protobufs\Models\TaskReview();
                $review->setUserId($userId);
                $review->setTaskId($taskId);
                $review->setProjectId($task->getProjectId());

                $error = '';

                $id = $taskId;
                if (isset($post["corrections_$id"]) && ctype_digit($post["corrections_$id"])) {
                    $value = intval($post["corrections_$id"]);
                    if ($value > 0 && $value <= 5) {
                        $review->setCorrections($value);
                    } else {
                        $error = Lib\Localisation::getTranslation('org_task_review_35');
                    }
                }
                if (isset($post["grammar_$id"]) && ctype_digit($post["grammar_$id"])) {
                    $value = intval($post["grammar_$id"]);
                    if ($value > 0 && $value <= 5) {
                        $review->setGrammar($value);
                    } else {
                        $error = Lib\Localisation::getTranslation('org_task_review_36');
                    }
                }
                if (isset($post["spelling_$id"]) && ctype_digit($post["spelling_$id"])) {
                    $value = intval($post["spelling_$id"]);
                    if ($value > 0 && $value <= 5) {
                        $review->setSpelling($value);
                    } else {
                        $error = Lib\Localisation::getTranslation('org_task_review_37');
                    }
                }
                if (isset($post["consistency_$id"]) && ctype_digit($post["consistency_$id"])) {
                    $value = intval($post["consistency_$id"]);
                    if ($value > 0 && $value <= 55) {
                        $review->setConsistency($value);
                    } else {
                        $error = Lib\Localisation::getTranslation('org_task_review_38');
                    }
                }
                if (isset($post["comment_$id"]) && $post["comment_$id"] != "") {
                    $review->setComment($post["comment_$id"]);
                }
                
                if ($review->getProjectId() != null && $review->getUserId() != null && $error == '') {
                    if (!$taskDao->submitReview($review)) {
                        $error = sprintf(Lib\Localisation::getTranslation('org_task_review_39'), $task->getTitle());
                    }
                }

                if ($error != '') {
                    UserRouteHandler::flashNow("error", $error);
                } else {
                    UserRouteHandler::flash(
                        "success",
                        sprintf(
                            Lib\Localisation::getTranslation('org_task_review_40'),
                            $task->getTitle()
                        )
                    );
                    return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('project-view', array("project_id" => $task->getProjectId())));
                }
            }

            if (isset($post['skip'])) {
                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("project-view", array('project_id' => $task->getProjectId())));
            }
        }

        $taskReview = $userDao->getUserTaskReviews($userId, $taskId);
        if (!is_null($taskReview)) {
            UserRouteHandler::flashNow("info", Lib\Localisation::getTranslation('org_task_review_41'));
        }

        $translator = $taskDao->getUserClaimedTask($taskId);

        $formAction = $app->getRouteCollector()->getRouteParser()->urlFor("org-task-review", array(
                    'org_id'    => $orgId,
                    'task_id'   => $taskId
        ));

        $extra_scripts = "";
        $extra_scripts .= "<script type='text/javascript'>";
        $extra_scripts .= "var taskIds = new Array();";
        $extra_scripts .= "taskIds[0] = $taskId;";
        $extra_scripts .= "</script>";
        
        $extra_scripts .= "<link rel=\"stylesheet\" href=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/RateIt/src/rateit.css\"/>";
        $extra_scripts .= "<script>".file_get_contents(__DIR__."/../js/RateIt/src/jquery.rateit.min.js")."</script>";
        $extra_scripts .= file_get_contents(__DIR__."/../js/review.js");
        // Load Twitter JS asynch, see https://dev.twitter.com/web/javascript/loading
        $extra_scripts .= '<script>window.twttr = (function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0], t = window.twttr || {}; if (d.getElementById(id)) return t; js = d.createElement(s); js.id = id; js.src = "https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs); t._e = []; t.ready = function(f) { t._e.push(f); }; return t; }(document, "script", "twitter-wjs"));</script>';

        $template_data = array_merge($template_data, array(
                    'sesskey' => $sesskey,
                    'extra_scripts' => $extra_scripts,
                    'task'      => $task,
                    'review'    => $taskReview,
                    'translator'=> $translator,
                    'formAction'=> $formAction
        ));

        return UserRouteHandler::render("org/org.task-review.tpl", $response);
    }

    public function orgTaskReviews(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $orgId = $args['org_id'];
        $taskId = $args['task_id'];

        $viewData = array();
        $taskDao = new DAO\TaskDao();
        $task = $taskDao->getTask($taskId);
        $preReqTasks = array();
        $preReqs = $taskDao->getTaskPreReqs($taskId);
        $reviews = array();

        if (empty($preReqs) && $task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING && !empty($matecat_tasks = $taskDao->getTaskChunk($taskId))) {
            // We are a chunk, so need to manually find the matching translation task
            $matecat_id_job          = $matecat_tasks[0]['matecat_id_job'];
            $matecat_id_job_password = $matecat_tasks[0]['matecat_id_chunk_password'];
            $matching_tasks = $taskDao->getMatchingTask($matecat_id_job, $matecat_id_job_password, Common\Enums\TaskTypeEnum::TRANSLATION);
            if (!empty($matching_tasks)) {
                $dummyTask = new Common\Protobufs\Models\Task();
                $dummyTask->setId($matching_tasks[0]['id']);
                $dummyTask->setProjectId($matching_tasks[0]['projectId']);
                $dummyTask->setTitle($matching_tasks[0]['title']);
                $preReqs = array();
                $preReqs[] = $dummyTask;
                error_log('preReqs for chunked PROOFREADING Task... ' . print_r($preReqs, true));
            }
        }
        $projectDao = new DAO\ProjectDao();
        if (empty($preReqs) && $task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING && $memsource_task = $projectDao->get_memsource_task($taskId)) {
            $preReqs = [];
            $top_level = $projectDao->get_top_level($memsource_task['internalId']);
            $project_tasks = $projectDao->get_tasks_for_project($task->getProjectId());
            foreach ($project_tasks as $project_task) {
                if ($top_level == $projectDao->get_top_level($project_task['internalId'])) {
                    if ($memsource_task['workflowLevel'] > $project_task['workflowLevel']) { // Dependent on
                        if (($memsource_task['beginIndex'] <= $project_task['endIndex']) && ($project_task['beginIndex'] <= $memsource_task['endIndex'])) { // Overlap
                            $dummyTask = new Common\Protobufs\Models\Task();
                            $dummyTask->setId($project_task['id']);
                            $dummyTask->setProjectId($task->getProjectId());
                            $dummyTask->setTitle($project_task['title']);
                            $dummyTask->set_cancelled($project_task['beginIndex']);
                            $dummyTask->setTaskStatus($project_task['endIndex']);
                            $preReqs[] = $dummyTask;
                            error_log('preReqs for memsource PROOFREADING Task... ' . print_r($preReqs, true));
                        }
                    }
                }
            }
        }

        if (!is_null($preReqs) && count($preReqs) > 0) {
            foreach ($preReqs as $preReq) {
                $taskReviews = $taskDao->getTaskReviews($preReq->getId());
                if (!is_null($taskReviews) && count($taskReviews) > 0) {
                    $preReqTasks[] = $preReq;
                    $reviews[$preReq->getId()] = $taskReviews;
                }
            }
            $viewData['preReqTasks'] = $preReqTasks;
        } else {
            $projectDao= new DAO\ProjectDao();
            $project = $projectDao->getProject($task->getProjectId());
            
            $allProjectReviews = $projectDao->getProjectReviews($task->getProjectId());
            if (!is_null($allProjectReviews) && count($allProjectReviews) > 0) {
                $validReviews = array();
                foreach ($allProjectReviews as $projectReview) {
                    if ($projectReview->getTaskId() == null) {
                        $validReviews[] = $projectReview;
                    }
                }
                if (count($validReviews) > 0) {
                    $viewData['projectReviews'] = $validReviews;
                }
            }
            
            $dummyTask = new Common\Protobufs\Models\Task();        //Create a dummy task to hold the project info
            $dummyTask->setProjectId($task->getProjectId());
            $dummyTask->setTitle($project->getTitle());
            $viewData['projectData'] = $dummyTask;
        }

        $taskReviews = $taskDao->getTaskReviews($taskId);
        if (!is_null($taskReviews) && count($taskReviews) > 0) {
            $reviews[$taskId] = $taskReviews;
        }

        $extra_scripts = "<link rel=\"stylesheet\" href=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/RateIt/src/rateit.css\"/>";
        $extra_scripts .= "<script>".file_get_contents(__DIR__."/../js/RateIt/src/jquery.rateit.min.js")."</script>";

        $viewData['task'] = $task;
        $viewData['reviews'] = $reviews;
        $viewData['extra_scripts'] = $extra_scripts;

        $template_data = array_merge($template_data, $viewData);
        return UserRouteHandler::render('org/org.task-reviews.tpl', $response);
    }

    public function org_members(Request $request, Response $response, $args)
    {
        $adminDao = new DAO\AdminDao();
        $org_members = $adminDao->getOrgMembers($args['org_id']);
        $roles = $adminDao->get_roles(Common\Lib\UserSession::getCurrentUserID())&(SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER);

        $data = "\xEF\xBB\xBF" . '"user_id","Given Name","Family Name","email","Roles","Language Pairs"' . "\n";
        foreach ($org_members as $om) {
          if ($roles || $args['org_id'] != 707 || $om['source_of_user'])
            $data .= '"' . $om['id'] . '","' . $om['first_name'] . '","' . $om['last_name'] . '","' . $om['email'] . '","' . $om['roles_text'] . '","' . $om['language_pairs'] . '"' . "\n";
        }
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="org_members.csv"');
        header('Content-length: ' . strlen($data));
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');
        echo $data;
        die;
    }

    public function set_entitlement(Request $request, Response $response)
    {
        $projectDao = new DAO\ProjectDao();

        $body = (string)$request->getBody();
        $json = json_decode($body, true);
        $result = 0;
        if (!empty($json['secret']) && $json['secret'] === Common\Lib\Settings::get('retool.secret')) {
            error_log('set_entitlement: ' . print_r($json, true));
            $projectDao->set_entitlement($json);
            $result = 1;
        } else error_log("set_entitlement not decoded: $body");

        $response->getBody()->write(json_encode(['result'=> $result]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}

$route_handler = new OrgRouteHandler();
$route_handler->init();
unset ($route_handler);
