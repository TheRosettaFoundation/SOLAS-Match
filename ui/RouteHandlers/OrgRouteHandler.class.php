<?php

namespace SolasMatch\UI\RouteHandlers;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\Common as Common;

require_once __DIR__."/../lib/Validator.php";

class OrgRouteHandler
{
    public function init()
    {
        $app = \Slim\Slim::getInstance();
        $middleware = new Lib\Middleware();

        $app->get(
            "/org/create/",
            array($middleware, "authUserIsLoggedIn"),
            array($this, "createOrg")
        )->via("POST")->name("create-org");

        $app->get(
            "/org/dashboard/",
            array($middleware, "authUserIsLoggedIn"),
            array($this, "orgDashboard")
        )->via("POST")->name("org-dashboard");

        $app->get(
            "/org/:org_id/request/",
            array($middleware, "authUserIsLoggedIn"),
            array($this, "orgRequestMembership")
        )->name("org-request-membership");

        $app->get(
            "/org/:org_id/request/queue/",
            array($middleware, "authUserForOrg"),
            array($this, "orgRequestQueue")
        )->via("POST")->name("org-request-queue");

        $app->get(
            "/org/:org_id/private/",
            array($middleware, "authUserForOrg"),
            array($this, "orgPrivateProfile")
        )->via("POST")->name("org-private-profile");

        $app->get(
            "/org/:org_id/profile/",
            array($middleware, "authUserIsLoggedIn"),
            array($this, "orgPublicProfile")
        )->via("POST")->name("org-public-profile");

        $app->get(
            "/org/:org_id/manage/:badge_id/",
            array($middleware, "authUserForOrg"),
            array($this, "orgManageBadge")
        )->via("POST")->name("org-manage-badge");

        $app->get(
            "/org/:org_id/create/badge/",
            array($middleware, "authUserForOrg"),
            array($this, "orgCreateBadge")
        )->via("POST")->name("org-create-badge");

        $app->get(
            "/org/search/",
            array($middleware, "authUserIsLoggedIn"),
            array($this, "orgSearch")
        )->via("POST")->name("org-search");
        
        $app->get(
            "/org/:org_id/edit/:badge_id/",
            array($middleware, "authUserForOrg"),
            array($this, "orgEditBadge")
        )->via("POST")->name("org-edit-badge");

        $app->get(
            "/org/:org_id/task/:task_id/complete/",
            array($middleware, "authUserForOrg"),
            array($this, "orgTaskComplete")
        )->name("org-task-complete");

        $app->get(
            "/org/:org_id/task/:task_id/review/",
            array($middleware, "authUserForOrg"),
            array($this, "orgTaskReview")
        )->via("POST")->name("org-task-review");

        $app->get(
            "/org/:org_id/task/:task_id/reviews/",
            array($middleware, "authUserForOrg"),
            array($this, "orgTaskReviews")
        )->name("org-task-reviews");
    }

    public function createOrg()
    {
        $app = \Slim\Slim::getInstance();

        if (empty($_SESSION['SESSION_CSRF_KEY'])) {
            $_SESSION['SESSION_CSRF_KEY'] = Common\Lib\UserSession::random_string(10);
        }
        $sesskey = $_SESSION['SESSION_CSRF_KEY']; // This is a check against CSRF (Posts should come back with same sesskey)

        $org2 = new Common\Protobufs\Models\OrganisationExtendedProfile();
        $org2->setFacebook('');
        $org2->setLinkedin('');
        $org2->setPrimaryContactName('');
        $org2->setPrimaryContactTitle('');
        $org2->setPrimaryContactEmail('');
        $org2->setPrimaryContactPhone('');
        $org2->setOtherContacts('');
        $org2->setStructure('');
        $org2->setAffiliations('');
        $org2->setUrlVideo1('');
        $org2->setUrlVideo2('');
        $org2->setUrlVideo3('');
        $org2->setSubjectMatters('');
        $org2->setActivitys('');
        $org2->setEmployees('');
        $org2->setFundings('');
        $org2->setFinds('');
        $org2->setTranslations('');
        $org2->setRequests('');
        $org2->setContents('');
        $org2->setPages('');
        $org2->setSources('');
        $org2->setTargets('');
        $org2->setOftens('');

        $org = null;
        $errorOccured = null;
        $errorList = array();
        if ($post = $app->request()->post()) {
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
                        $org->setHomepage($post["homepage"]);
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
            if (isset($post["address"])) {
                $org->setAddress($post["address"]);
            }
            if (isset($post["city"])) {
                $org->setCity($post["city"]);
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

            $regionalFocus = array();
            if (isset($post["africa"])) {
                $regionalFocus[] = "Africa";
            }
            if (isset($post["asia"])) {
                $regionalFocus[] = "Asia";
            }
            if (isset($post["australia"])) {
                $regionalFocus[] = "Australia";
            }
            if (isset($post["europe"])) {
                $regionalFocus[] .= "Europe";
            }
            if (isset($post["northAmerica"])) {
                $regionalFocus[] .= "North-America";
            }
            if (isset($post["southAmerica"])) {
                $regionalFocus[] .= "South-America";
            }
            if (!empty($regionalFocus)) {
                $org->setRegionalFocus(implode(",", $regionalFocus));
            }

            if (isset($post['facebook'])) {
                if (trim($post['facebook']) != '') {
                    if (Lib\Validator::validateURL($post['facebook'])) {
                        $org2->setFacebook(Lib\Validator::addhttp($post['facebook']));
                    } else {
                        $errorOccured = true;
                        $errorList[] = Lib\Localisation::getTranslation('common_invalid_url');
                    }
                }
            }
            if (isset($post['linkedin'])) {
                if (trim($post['linkedin']) != '') {
                    if (Lib\Validator::validateURL($post['linkedin'])) {
                        $org2->setLinkedin(Lib\Validator::addhttp($post['linkedin']));
                    } else {
                        $errorOccured = true;
                        $errorList[] = Lib\Localisation::getTranslation('common_invalid_url');
                    }
                }
            }
            if (isset($post['twitter'])) {
                if (trim($post['twitter']) != '') {
                    if (Lib\Validator::validateURL($post['twitter'])) {
                        $org2->setPrimaryContactEmail(Lib\Validator::addhttp($post['twitter']));
                    } else {
                        $errorOccured = true;
                        $errorList[] = Lib\Localisation::getTranslation('common_invalid_url');
                    }
                }
            }
            if (isset($post['urlvideo1'])) {
                if (trim($post['urlvideo1']) != '') {
                    if (Lib\Validator::validateURL($post['urlvideo1'])) {
                        $org2->setUrlVideo1(Lib\Validator::addhttp($post['urlvideo1']));
                    } else {
                        $errorOccured = true;
                        $errorList[] = Lib\Localisation::getTranslation('common_invalid_url');
                    }
                }
            }
            if (isset($post['urlvideo2'])) {
                if (trim($post['urlvideo2']) != '') {
                    if (Lib\Validator::validateURL($post['urlvideo2'])) {
                        $org2->setUrlVideo2(Lib\Validator::addhttp($post['urlvideo2']));
                    } else {
                        $errorOccured = true;
                        $errorList[] = Lib\Localisation::getTranslation('common_invalid_url');
                    }
                }
            }
            if (isset($post['urlvideo3'])) {
                if (trim($post['urlvideo3']) != '') {
                    if (Lib\Validator::validateURL($post['urlvideo3'])) {
                        $org2->setUrlVideo3(Lib\Validator::addhttp($post['urlvideo3']));
                    } else {
                        $errorOccured = true;
                        $errorList[] = Lib\Localisation::getTranslation('common_invalid_url');
                    }
                }
            }
            if (empty($post['primarycontactname'])) {
                $errorOccured = true;
                $errorList[] = Lib\Localisation::getTranslation('org_private_profile_organisation_error_name_not_set');
            }
            else {
                $org2->setPrimaryContactName($post['primarycontactname']);
            }
            if (isset($post['primarycontacttitle'])) {
                $org2->setPrimaryContactTitle($post['primarycontacttitle']);
            }
            if (isset($post['primarycontactphone'])) {
                $org2->setPrimaryContactPhone($post['primarycontactphone']);
            }
            if (isset($post['othercontacts'])) {
                $org2->setOtherContacts($post['othercontacts']);
            }
            if (isset($post['structure'])) {
                $org2->setStructure($post['structure']);
            }
            if (isset($post['affiliations'])) {
                $org2->setAffiliations($post['affiliations']);
            }
            if (isset($post['subjectmatters'])) {
                $org2->setSubjectMatters($post['subjectmatters']);
            }
            if (isset($post['activitys'])) {
                $org2->setActivitys(implode(',', $post['activitys']));
            }
            if (isset($post['employees'])) {
                $org2->setEmployees(implode(',', $post['employees']));
            }
            if (isset($post['fundings'])) {
                $org2->setFundings(implode(',', $post['fundings']));
            }
            if (isset($post['finds'])) {
                $org2->setFinds(implode(',', $post['finds']));
            }
            if (isset($post['translations'])) {
                $org2->setTranslations(implode(',', $post['translations']));
            }
            if (isset($post['requests'])) {
                $org2->setRequests(implode(',', $post['requests']));
            }
            if (isset($post['contents'])) {
                $org2->setContents(implode(',', $post['contents']));
            }
            if (isset($post['pages'])) {
                $org2->setPages(implode(',', $post['pages']));
            }
            if (isset($post['sources'])) {
                $org2->setSources(implode(',', $post['sources']));
            }
            if (isset($post['targets'])) {
                $org2->setTargets(implode(',', $post['targets']));
            }
            if (isset($post['oftens'])) {
                $org2->setOftens(implode(',', $post['oftens']));
            }

            if (is_null($errorOccured)) {
                $user_id = Common\Lib\UserSession::getCurrentUserID();
                $orgDao = new DAO\OrganisationDao();

                try {
                    error_log("Calling createOrg(, $user_id)");
                    $new_org = $orgDao->createOrg($org, $user_id);
                    if ($new_org) {
                        $org2->setId($new_org->getId());
                        $orgDao->updateOrgExtendedProfile($org2);
                        $org_name = $org->getName();
                        error_log("Called createOrg() for: $org_name");
                        $app->flash(
                            "success",
                            sprintf(Lib\Localisation::getTranslation('create_org_created'), $org_name)
                        );
                        $app->redirect($app->urlFor("org-dashboard"));
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

        $adminDao = new DAO\AdminDao();
        $isSiteAdmin = $adminDao->isSiteAdmin(Common\Lib\UserSession::getCurrentUserID());

        $app->view()->appendData(array(
            'org'  => $org,
            'org2' => $org2,
            'activitys'    => $this->generateOptions($this->possibleActivitys(), $org2->getActivitys()),
            'employees'    => $this->generateOptions($this->possibleEmployees(), $org2->getEmployees()),
            'fundings'     => $this->generateOptions($this->possibleFundings(), $org2->getFundings()),
            'finds'        => $this->generateOptions($this->possibleFinds(), $org2->getFinds()),
            'translations' => $this->generateOptions($this->possibleTranslations(), $org2->getTranslations()),
            'requests'     => $this->generateOptions($this->possibleRequests(), $org2->getRequests()),
            'contents'     => $this->generateOptions($this->possibleContents(), $org2->getContents()),
            'pages'        => $this->generateOptions($this->possiblePages(), $org2->getPages()),
            'sources'      => $this->generateOptions($this->possibleLanguages(), $org2->getSources()),
            'targets'      => $this->generateOptions($this->possibleLanguages(), $org2->getTargets()),
            'oftens'       => $this->generateOptions($this->possibleOftens(), $org2->getOftens()),
            'errorOccured' => $errorOccured,
            'errorList'    => $errorList,
            'isSiteAdmin'  => $isSiteAdmin,
            'sesskey'      => $sesskey,
        ));

        $app->render("org/create-org.tpl");
    }

    public function orgDashboard()
    {
        $app = \Slim\Slim::getInstance();
        $current_user_id = Common\Lib\UserSession::getCurrentUserID();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();
        $tagDao = new DAO\TagDao();
        $projectDao = new DAO\ProjectDao();
        $adminDao = new DAO\AdminDao();
        $isSiteAdmin = $adminDao->isSiteAdmin($current_user_id);

        $sesskey = Common\Lib\UserSession::getCSRFKey();
        
        $current_user = $userDao->getUser($current_user_id);
        $my_organisations = $userDao->getUserOrgs($current_user_id);
        $org_projects = array();
        
        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            Common\Lib\UserSession::checkCSRFKey($post, 'orgDashboard');

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
                        $app->flashNow(
                            "success",
                            sprintf(Lib\Localisation::getTranslation('org_dashboard_5'), $project_title)
                        );
                    } else {
                        $app->flashNow(
                            "error",
                            sprintf(Lib\Localisation::getTranslation('org_dashboard_6'), $project_title)
                        );
                    }
                } elseif ($post['track'] == "Track") {
                    $success = $userDao->trackProject($current_user_id, $project_id);
                    if ($success) {
                        $app->flashNow(
                            "success",
                            sprintf(Lib\Localisation::getTranslation('org_dashboard_7'), $project_title)
                        );
                    } else {
                        $app->flashNow(
                            "error",
                            sprintf(Lib\Localisation::getTranslation('org_dashboard_8'), $project_title)
                        );
                    }
                }
            }
        }
        
        if ($my_organisations) {
            $orgs = array();
            $templateData = array();
            foreach ($my_organisations as $org) {
                $my_org_projects = $orgDao->getOrgProjects($org->getId());
                $org_projects[$org->getId()] = $my_org_projects;
                $orgs[$org->getId()] = $org;

                $taskData = array();
                if ($my_org_projects) {
                    foreach ($my_org_projects as $project) {
                        $temp = array();
                        $temp['project'] = $project;
                        //$temp['userSubscribedToProject'] = $userDao->isSubscribedToProject(
                        //    Common\Lib\UserSession::getCurrentUserID(),
                        //    $project->getId()
                        //);
                        $taskData[]=$temp;
                    }
                } else {
                    $taskData = null;
                }
                $templateData[$org->getId()] = $taskData;
            }

            $adminForOrg = array();
            foreach ($orgs as $orgAdminTest) {
                $adminForOrg[$orgAdminTest->getId()] = false;
                if ($isSiteAdmin || $adminDao->isOrgAdmin($orgAdminTest->getId(), $current_user_id)) {
                    $adminForOrg[$orgAdminTest->getId()] = true;
                }
            }

            $app->view()->appendData(array(
                "orgs" => $orgs,
                'adminForOrg' => $adminForOrg,
                "templateData" => $templateData
            ));
        }

        $extra_scripts = file_get_contents(__DIR__."/../js/TaskView.js");
        // Load Twitter JS asynch, see https://dev.twitter.com/web/javascript/loading
        $extra_scripts .= '<script>window.twttr = (function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0], t = window.twttr || {}; if (d.getElementById(id)) return t; js = d.createElement(s); js.id = id; js.src = "https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs); t._e = []; t.ready = function(f) { t._e.push(f); }; return t; }(document, "script", "twitter-wjs"));</script>';

        $app->view()->appendData(array(
            'sesskey'       => $sesskey,
            "isSiteAdmin"   => $isSiteAdmin,
            "extra_scripts" => $extra_scripts,
            "current_page"  => "org-dashboard"
        ));
        $app->render("org/org.dashboard.tpl");
    }

    public function orgRequestMembership($org_id)
    {
        $app = \Slim\Slim::getInstance();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();

        $userId = Common\Lib\UserSession::getCurrentUserID();
        $user = $userDao->getUser($userId);
        $user_orgs = $userDao->getUserOrgs($userId);
        if (is_null($user_orgs) || !in_array($org_id, $user_orgs)) {
            $requestMembership = $orgDao->createMembershipRequest($org_id, $userId);
            if ($requestMembership) {
                $app->flash("success", Lib\Localisation::getTranslation('org_public_profile_membership_requested'));
            } else {
                $app->flash(
                    "error",
                    Lib\Localisation::getTranslation('org_public_profile_membership_already_requested')
                );
            }
        } else {
            $app->flash("error", Lib\Localisation::getTranslation('org_public_profile_13'));
        }
        $app->redirect($app->urlFor("org-public-profile", array("org_id" => $org_id)));
    }

    public function orgRequestQueue($org_id)
    {
        $app = \Slim\Slim::getInstance();
        $orgDao = new DAO\OrganisationDao();
        $userDao = new DAO\UserDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $org = $orgDao->getOrganisation($org_id);
        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            Common\Lib\UserSession::checkCSRFKey($post, 'orgRequestQueue');
            
            if (isset($post['email'])) {
                if (Lib\Validator::validateEmail($post['email'])) {
                    $new_org_member = $userDao->getUserByEmail($post['email']);
                    if (!is_null($new_org_member))
                    {
                        $success = $orgDao->addMember($post['email'], $org_id);
                        if ($success) {
                            $app->flashNow(
                                "success",
                                sprintf(
                                    Lib\Localisation::getTranslation('common_successfully_added_member'),
                                    $post['email'],
                                    $org->getName()
                                )
                            );
                        } else {
                            $app->flashNow(
                                "error",
                                sprintf(Lib\Localisation::getTranslation('org_public_profile_20'), $post['email'])
                            );
                        }
                    } else {
                        $email = $post['email'];
                        $app->flashNow(
                            "error",
                            sprintf(Lib\Localisation::getTranslation('org_public_profile_21'), $email)
                        );
                    }
                } else {
                    $app->flashNow("error", Lib\Localisation::getTranslation('common_no_valid_email'));
                }
            } elseif (isset($post['accept'])) {
                if ($user_id = $post['user_id']) {
                    $orgDao->acceptMembershipRequest($org_id, $user_id);
                } else {
                    $app->flashNow(
                        "error",
                        sprintf(Lib\Localisation::getTranslation('org_request_queue_7'), $user_id)
                    );
                }
            } elseif (isset($post['refuse'])) {
                if ($user_id = $post['user_id']) {
                    $orgDao->rejectMembershipRequest($org_id, $user_id);
                } else {
                    $app->flashNow(
                        "error",
                        sprintf(Lib\Localisation::getTranslation('org_request_queue_7'), $user_id)
                    );
                }
            }
        }
        $requests = $orgDao->getMembershipRequests($org_id);
        $user_list = array();
        if (count($requests) > 0) {
            foreach ($requests as $memRequest) {
                $user_list[] =  $userDao->getUser($memRequest->getId());
            }
        }
        
        $app->view()->setData("org", $org);
        $app->view()->appendData(array("user_list" => $user_list, 'sesskey' => $sesskey));
        
        $app->render("org/org.request_queue.tpl");
    }

    public function orgPrivateProfile($org_id)
    {
        $app = \Slim\Slim::getInstance();

        if (empty($_SESSION['SESSION_CSRF_KEY'])) {
            $_SESSION['SESSION_CSRF_KEY'] = Common\Lib\UserSession::random_string(10);
        }
        $sesskey = $_SESSION['SESSION_CSRF_KEY']; // This is a check against CSRF (Posts should come back with same sesskey)

        $orgDao = new DAO\OrganisationDao();
        $org = $orgDao->getOrganisation($org_id);
        $org2 = $orgDao->getOrganisationExtendedProfile($org_id);
        if (empty($org2)) {
            $org2 = new Common\Protobufs\Models\OrganisationExtendedProfile();
            $org2->setId($org_id);
            $org2->setFacebook('');
            $org2->setLinkedin('');
            $org2->setPrimaryContactName('');
            $org2->setPrimaryContactTitle('');
            $org2->setPrimaryContactEmail('');
            $org2->setPrimaryContactPhone('');
            $org2->setOtherContacts('');
            $org2->setStructure('');
            $org2->setAffiliations('');
            $org2->setUrlVideo1('');
            $org2->setUrlVideo2('');
            $org2->setUrlVideo3('');
            $org2->setSubjectMatters('');
            $org2->setActivitys('');
            $org2->setEmployees('');
            $org2->setFundings('');
            $org2->setFinds('');
            $org2->setTranslations('');
            $org2->setRequests('');
            $org2->setContents('');
            $org2->setPages('');
            $org2->setSources('');
            $org2->setTargets('');
            $org2->setOftens('');
        }
        $userId = Common\Lib\UserSession::getCurrentUserId();
        
        $errorOccured = null;
        $errorList=array();
        
        if ($post = $app->request()->post()) {

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
                            $org->setHomepage($post["homepage"]);
                        } else {
                            $errorOccured = true;
                            array_push($errorList, Lib\Localisation::getTranslation('common_invalid_url'));
                        }
                    }
                }
                if (isset($post['biography'])) {
                    $org->setBiography($post['biography']);
                }
                if (isset($post['address'])) {
                    $org->setAddress($post['address']);
                }
                if (isset($post['city'])) {
                    $org->setCity($post['city']);
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

                $regionalFocus = array();
                if (isset($post["africa"])) {
                    $regionalFocus[] = "Africa";
                }
                if (isset($post["asia"])) {
                    $regionalFocus[] = "Asia";
                }
                if (isset($post["australia"])) {
                    $regionalFocus[] = "Australia";
                }
                if (isset($post["europe"])) {
                    $regionalFocus[] .= "Europe";
                }
                if (isset($post["northAmerica"])) {
                    $regionalFocus[] .= "North-America";
                }
                if (isset($post["southAmerica"])) {
                    $regionalFocus[] .= "South-America";
                }
                if (!empty($regionalFocus)) {
                    $org->setRegionalFocus(implode(",", $regionalFocus));
                }
                if (isset($post['facebook'])) {
                    if (trim($post['facebook']) != '') {
                        if (Lib\Validator::validateURL($post['facebook'])) {
                            $org2->setFacebook(Lib\Validator::addhttp($post['facebook']));
                        } else {
                            $errorOccured = true;
                            $errorList[] = Lib\Localisation::getTranslation('common_invalid_url');
                        }
                    }
                }
                if (isset($post['linkedin'])) {
                    if (trim($post['linkedin']) != '') {
                        if (Lib\Validator::validateURL($post['linkedin'])) {
                            $org2->setLinkedin(Lib\Validator::addhttp($post['linkedin']));
                        } else {
                            $errorOccured = true;
                            $errorList[] = Lib\Localisation::getTranslation('common_invalid_url');
                        }
                    }
                }
                if (isset($post['twitter'])) {
                    if (trim($post['twitter']) != '') {
                        if (Lib\Validator::validateURL($post['twitter'])) {
                            $org2->setPrimaryContactEmail(Lib\Validator::addhttp($post['twitter']));
                        } else {
                            $errorOccured = true;
                            $errorList[] = Lib\Localisation::getTranslation('common_invalid_url');
                        }
                    }
                }
                if (isset($post['urlvideo1'])) {
                    if (trim($post['urlvideo1']) != '') {
                        if (Lib\Validator::validateURL($post['urlvideo1'])) {
                            $org2->setUrlVideo1(Lib\Validator::addhttp($post['urlvideo1']));
                        } else {
                            $errorOccured = true;
                            $errorList[] = Lib\Localisation::getTranslation('common_invalid_url');
                        }
                    }
                }
                if (isset($post['urlvideo2'])) {
                    if (trim($post['urlvideo2']) != '') {
                        if (Lib\Validator::validateURL($post['urlvideo2'])) {
                            $org2->setUrlVideo2(Lib\Validator::addhttp($post['urlvideo2']));
                        } else {
                            $errorOccured = true;
                            $errorList[] = Lib\Localisation::getTranslation('common_invalid_url');
                        }
                    }
                }
                if (isset($post['urlvideo3'])) {
                    if (trim($post['urlvideo3']) != '') {
                        if (Lib\Validator::validateURL($post['urlvideo3'])) {
                            $org2->setUrlVideo3(Lib\Validator::addhttp($post['urlvideo3']));
                        } else {
                            $errorOccured = true;
                            $errorList[] = Lib\Localisation::getTranslation('common_invalid_url');
                        }
                    }
                }
                if (empty($post['primarycontactname'])) {
                    $errorOccured = true;
                    $errorList[] = Lib\Localisation::getTranslation('org_private_profile_organisation_error_name_not_set');
                }
                else {
                    $org2->setPrimaryContactName($post['primarycontactname']);
                }
                if (isset($post['primarycontacttitle'])) {
                    $org2->setPrimaryContactTitle($post['primarycontacttitle']);
                }
                if (isset($post['primarycontactphone'])) {
                    $org2->setPrimaryContactPhone($post['primarycontactphone']);
                }
                if (isset($post['othercontacts'])) {
                    $org2->setOtherContacts($post['othercontacts']);
                }
                if (isset($post['structure'])) {
                    $org2->setStructure($post['structure']);
                }
                if (isset($post['affiliations'])) {
                    $org2->setAffiliations($post['affiliations']);
                }
                if (isset($post['subjectmatters'])) {
                    $org2->setSubjectMatters($post['subjectmatters']);
                }
                if (isset($post['activitys'])) {
                    $org2->setActivitys(implode(',', $post['activitys']));
                }
                if (isset($post['employees'])) {
                    $org2->setEmployees(implode(',', $post['employees']));
                }
                if (isset($post['fundings'])) {
                    $org2->setFundings(implode(',', $post['fundings']));
                }
                if (isset($post['finds'])) {
                    $org2->setFinds(implode(',', $post['finds']));
                }
                if (isset($post['translations'])) {
                    $org2->setTranslations(implode(',', $post['translations']));
                }
                if (isset($post['requests'])) {
                    $org2->setRequests(implode(',', $post['requests']));
                }
                if (isset($post['contents'])) {
                    $org2->setContents(implode(',', $post['contents']));
                }
                if (isset($post['pages'])) {
                    $org2->setPages(implode(',', $post['pages']));
                }
                if (isset($post['sources'])) {
                    $org2->setSources(implode(',', $post['sources']));
                }
                if (isset($post['targets'])) {
                    $org2->setTargets(implode(',', $post['targets']));
                }
                if (isset($post['oftens'])) {
                    $org2->setOftens(implode(',', $post['oftens']));
                }

                if (!is_null($errorOccured)) {
                    $app->view()->appendData(array(
                    "errorOccured" => $errorOccured,
                    "errorList" => $errorList
                    ));
                } else {
                    $orgDao = new DAO\OrganisationDao();
                    try {
                        $orgDao->updateOrg($org);
                        $orgDao->updateOrgExtendedProfile($org2);
                        $app->redirect($app->urlFor("org-public-profile", array("org_id" => $org->getId())));
                    } catch (Common\Exceptions\SolasMatchException $ex) {
                        $org_name = $org->getName();
                        $app->flashNow(
                            "error",
                            sprintf(
                                Lib\Localisation::getTranslation('common_error_org_name_in_use'),
                                $org_name
                            )
                        );
                    }
                }
            }

            if (isset($post['deleteId'])) {
                $deleteId = $post['deleteId'];
                if ($deleteId) {
                    if (!empty($post['sesskey']) && $post['sesskey'] === $sesskey && $orgDao->deleteOrg($org->getId())) {
                        $app->flash(
                            "success",
                            sprintf(
                                Lib\Localisation::getTranslation('org_private_profile_delete_success'),
                                $org->getName()
                            )
                        );
                        $app->redirect($app->urlFor("home"));
                    } else {
                        $app->flashNow("error", Lib\Localisation::getTranslation('org_private_profile_delete_fail'));
                    }
                }
            }
        }

        $adminDao = new DAO\AdminDao();
        //if ($adminDao->isOrgAdmin($org->getId(), $userId) || $adminDao->isSiteAdmin($userId)) { [}]
        if ($adminDao->isSiteAdmin($userId)) {
            $app->view()->appendData(array('orgAdmin' => true));
        }
        
        $app->view()->appendData(array(
            'org'  => $org,
            'org2' => $org2,
            'activitys'    => $this->generateOptions($this->possibleActivitys(), $org2->getActivitys()),
            'employees'    => $this->generateOptions($this->possibleEmployees(), $org2->getEmployees()),
            'fundings'     => $this->generateOptions($this->possibleFundings(), $org2->getFundings()),
            'finds'        => $this->generateOptions($this->possibleFinds(), $org2->getFinds()),
            'translations' => $this->generateOptions($this->possibleTranslations(), $org2->getTranslations()),
            'requests'     => $this->generateOptions($this->possibleRequests(), $org2->getRequests()),
            'contents'     => $this->generateOptions($this->possibleContents(), $org2->getContents()),
            'pages'        => $this->generateOptions($this->possiblePages(), $org2->getPages()),
            'sources'      => $this->generateOptions($this->possibleLanguages(), $org2->getSources()),
            'targets'      => $this->generateOptions($this->possibleLanguages(), $org2->getTargets()),
            'oftens'       => $this->generateOptions($this->possibleOftens(), $org2->getOftens()),
            'sesskey'      => $sesskey,
        ));

        $app->render("org/org-private-profile.tpl");
    }

    private function possibleActivitys()
    {
        $lang = Common\Lib\UserSession::getUserLanguage();
        if ($lang === 'es') return $this->possibleActivitys_es();
        if ($lang === 'de') return $this->possibleActivitys_de();
        if ($lang === 'it') return $this->possibleActivitys_it();
        if ($lang === 'fr') return $this->possibleActivitys_fr();

        return array(
            'agri' => 'Agriculture, Food & Nutrition',
            'anim' => 'Animals & Wildlife',
            'arts' => 'Arts & Culture',
            'busi' => 'Business & Industry',
            'chil' => 'Children',
            'civi' => 'Civil Society Development',
            'comm' => 'Community Development',
            'crim' => 'Crime & Safety',
            'demo' => 'Democracy & Good Governance',
            'disa' => 'Disabilities - Special Needs',
            'drug' => 'Drugs & Addiction',
            'econ' => 'Economic Development',
            'educ' => 'Education & Literacy',
            'empl' => 'Employment & Labor',
            'envi' => 'Environment & Climate Change',
            'fami' => 'Family',
            'heal' => 'Health & Wellbeing',
            'hiva' => 'HIV/AIDS',
            'hous' => 'Housing & Shelter',
            'huma' => 'Human Rights',
            'humr' => 'Humanitarian Relief',
            'immi' => 'Immigration',
            'indi' => 'Indigenous Communities',
            'inte' => 'International Cooperation & International Relations',
            'info' => 'Information and communications technology (ICT)',
            'lgbt' => 'LGBTQ (Lesbian, Gay, Bi-sexual and Transgender)',
            'live' => 'Livelihood',
            'olde' => 'Older People & Active Ageing',
            'othe' => 'Other/General',
            'peac' => 'Peace & Conflict Resolution',
            'pove' => 'Poverty Alleviation',
            'pris' => 'Prisoners/Offenders/Ex-offenders',
            'publ' => 'Public Affairs',
            'reli' => 'Religion & Faith based',
            'refu' => 'Refugees & Asylum Seekers',
            'scie' => 'Science',
            'soci' => 'Social Sciences',
            'spor' => 'Sports & Recreation',
            'tour' => 'Tourism & Travel',
            'volu' => 'Volunteerism & Active Citizenship',
            'wate' => 'Water & Sanitation',
            'wome' => 'Women & Gender',
            'yout' => 'Youth & Adolescents',
        );
    }

    private function possibleEmployees()
    {
        return array(
            'N0'    => '0',
            'N1'    => '1',
            'N5'    => '2-5',
            'N20'   => '6-20',
            'N100'  => '21-100',
            'N1000' => '101+',
        );
    }

    private function possibleFundings()
    {
        $lang = Common\Lib\UserSession::getUserLanguage();
        if ($lang === 'es') return $this->possibleFundings_es();
        if ($lang === 'de') return $this->possibleFundings_de();
        if ($lang === 'it') return $this->possibleFundings_it();
        if ($lang === 'fr') return $this->possibleFundings_fr();

        return array(
            'publ' => 'Public',
            'priv' => 'Private',
            'corp' => 'Corporate',
            'none' => 'No funding',
        );
    }

    private function possibleFinds()
    {
        $lang = Common\Lib\UserSession::getUserLanguage();
        if ($lang === 'es') return $this->possibleFinds_es();
        if ($lang === 'de') return $this->possibleFinds_de();
        if ($lang === 'it') return $this->possibleFinds_it();
        if ($lang === 'fr') return $this->possibleFinds_fr();

        return array(
            'webs' => 'Web Search',
            'face' => 'Facebook',
            'twit' => 'Twitter',
            'link' => 'LinkedIn',
            'adve' => 'Advertisement',
            'publ' => 'Public event',
            'cont' => 'Through a contact',
        );
    }

    private function possibleTranslations()
    {
        $lang = Common\Lib\UserSession::getUserLanguage();
        if ($lang === 'es') return $this->possibleTranslations_es();
        if ($lang === 'de') return $this->possibleTranslations_de();
        if ($lang === 'it') return $this->possibleTranslations_it();
        if ($lang === 'fr') return $this->possibleTranslations_fr();

        return array(
            'paid' => 'Paid commercial services',
            'volu' => 'Volunteers',
            'univ' => 'Universities',
            'none' => 'No translations so far',
        );
    }

    private function possibleRequests()
    {
        $lang = Common\Lib\UserSession::getUserLanguage();
        if ($lang === 'es') return $this->possibleRequests_es();
        if ($lang === 'de') return $this->possibleRequests_de();
        if ($lang === 'it') return $this->possibleRequests_it();
        if ($lang === 'fr') return $this->possibleRequests_fr();

        return array(
            'allt' => 'Looking for a volunteer-based solution for all our translation needs',
            'addi' => 'Looking for a volunteer-based solution to provide additional capacity',
        );
    }

    private function possibleContents()
    {
        $lang = Common\Lib\UserSession::getUserLanguage();
        if ($lang === 'es') return $this->possibleContents_es();
        if ($lang === 'de') return $this->possibleContents_de();
        if ($lang === 'it') return $this->possibleContents_it();
        if ($lang === 'fr') return $this->possibleContents_fr();

        return array(
            'webs' => 'Website',
            'stra' => 'Strategy',
            'advo' => 'Advocacy',
            'manu' => 'Manuals',
            'proj' => 'Projects',
            'camp' => 'Campaigns',
            'othe' => 'Other',
        );
    }

    private function possiblePages()
    {
        return array(
            'N10' => '1-10',
            'N100' => '11-100',
            'N1000' => '100-1000',
            'N10000' => '1000-10000',
            'N100000' => '10000+',
        );
    }

    private function possibleLanguages()
    {
        $langDao = new DAO\LanguageDao();
        $languages = $langDao->getLanguages();
        $possibleLanguagesArray = array();
        foreach ($languages as $language) {
            $possibleLanguagesArray[$language->getCode()] = $language->getName();
        }
        return $possibleLanguagesArray;
    }


    private function possibleOftens()
    {
        $lang = Common\Lib\UserSession::getUserLanguage();
        if ($lang === 'es') return $this->possibleOftens_es();
        if ($lang === 'de') return $this->possibleOftens_de();
        if ($lang === 'it') return $this->possibleOftens_it();
        if ($lang === 'fr') return $this->possibleOftens_fr();

        return array(
            'mont' => 'Every month',
            'quar' => 'Every quarter',
            'once' => 'Once or twice per year',
            'othe' => 'Other',
        );
    }

    private function generateOptions($possibleOptions, $selectedCodes)
    {
        $selectedCodesArray = explode(',', $selectedCodes);
        $options = array();
        foreach($possibleOptions as $code => $option) {
            $options[] = array('code' => $code, 'selected' => in_array($code, $selectedCodesArray, true), 'value' => $option);
        }
        return $options;
    }

    private function possibleActivitys_it()
    {
        return array(
            "wate" => "Acqua & Servizi igienici",
            "agri" => "Agricoltura, Alimentazione & Nutrizione",
            "humr" => "Aiuto umanitario",
            "hous" => "Alloggi & Accoglienza",
            "othe" => "Altro/Generico",
            "envi" => "Ambiente & Cambiamenti climatici",
            "arts" => "Arte & Cultura",
            "indi" => "Comunità indigene ",
            "inte" => "Cooperazione internazionale & Relazioni internazionali",
            "crim" => "Criminalità & Sicurezza",
            "demo" => "Democrazia & Buona Governance",
            "huma" => "Diritti umani",
            "disa" => "Disabilità – Bisogni specifici",
            "wome" => "Donne & Genere",
            "drug" => "Droghe & Dipendenze",
            "fami" => "Famiglia",
            "anim" => "Fauna selvatica",
            "reli" => "Fede & Religione",
            "yout" => "Gioventù & Adolescenti",
            "hiva" => "HIV/AIDS",
            "immi" => "Immigrazione",
            "busi" => "Industria & Business",
            "chil" => "Infanzia",
            "educ" => "Istruzione & Alfabetismo",
            "empl" => "Lavoro & Occupazione",
            "lgbt" => "LGBTQ (Lesbian, Gay, Bi-sexual and Transgender)",
            "live" => "Mezzi di sostentamento",
            "peac" => "Pace & Risoluzione dei conflitti",
            "publ" => "Politiche pubbliche",
            "pris" => "Prigionieri/Delinquenti/Ex delinquenti",
            "pove" => "Riduzione della povertà",
            "refu" => "Rifugiati & Richiedenti asilo",
            "heal" => "Salute & Benessere",
            "scie" => "Scienza",
            "soci" => "Scienze sociali",
            "spor" => "Sport & Tempo libero",
            "civi" => "Sviluppo della società civile",
            "comm" => "Sviluppo delle comunità",
            "econ" => "Sviluppo economico",
            "info" => "Tecnologie dell'informazione e della comunicazione (TIC)",
            "olde" => "Terza età e invecchiamento attivo",
            "tour" => "Viaggi & Turismo",
            "volu" => "Volontariato & Cittadinanza attiva",
        );
    }

    private function possibleFundings_it()
    {
        return array(
            "publ" => "Settore pubblico",
            "priv" => "Settore privato",
            "corp" => "Fondi aziendali",
            "none" => "Nessuno",
        );
    }

    private function possibleFinds_it()
    {
        return array(
            "webs" => "Ricerca online",
            "face" => "Facebook",
            "twit" => "Twitter",
            "link" => "LinkedIn",
            "adve" => "Annunci",
            "publ" => "Eventi pubblici",
            "cont" => "Attraverso un contatto",
        );
    }

    private function possibleTranslations_it()
    {
        return array(
            "paid" => "Servizi commerciali a pagamento",
            "volu" => "Volontari",
            "univ" => "Università",
            "none" => "Non richiedevamo traduzioni",
        );
    }

    private function possibleRequests_it()
    {
        return array(
            "allt" => "Cerchiamo una soluzione su base volontaria per le nostre necessità di traduzioni",
            "addi" => "Cerchiamo una soluzione su base volontaria per aumentare le nostre capacità",
        );
    }

    private function possibleContents_it()
    {
        return array(
            "webs" => "Siti Web",
            "stra" => "Pianificazione dell'attività",
            "advo" => "Protezione",
            "manu" => "Manuali",
            "proj" => "Progetti",
            "camp" => "Campagne",
            "othe" => "Altro",
        );
    }

    private function possibleOftens_it()
    {
        return array(
            "mont" => "Ogni mese",
            "quar" => "Ogni tre mesi",
            "once" => "Una o due volte l'anno",
            "othe" => "Altro",
        );
    }

    private function possibleActivitys_de()
    {
        return array(
            "olde" => "Ältere Menschen & Aktives Altern",
            "pove" => "Armutsbekämpfung",
            "disa" => "Behinderungen - besondere Bedürfnisse",
            "empl" => "Beschäftigung & Arbeit",
            "educ" => "Bildung & Alphabetisierung",
            "demo" => "Demokratie & Verantwortungsbewusste Regierung",
            "drug" => "Drogen & Sucht",
            "volu" => "Ehrenamt & Aktivbürger",
            "civi" => "Entwicklung der Zivilgesellschaft",
            "fami" => "Familie",
            "refu" => "Flüchtlinge & Asylbewerber",
            "wome" => "Frauen & Gender",
            "peac" => "Frieden & Konfliktlösung",
            "comm" => "Gemeinschaftsentwicklung",
            "heal" => "Gesundheit & Wohlbefinden",
            "pris" => "Häftlinge/Straftäter/Ehemalige Straftäter",
            "anim" => "Haus- & Wildtiere",
            "hiva" => "HIV/AIDS",
            "humr" => "Humanitäre Hilfe",
            "immi" => "Immigration",
            "indi" => "Indigene Gemeinschaften",
            "info" => "Informations- und kommunikationstechnologie (IKT)",
            "inte" => "Internationale Kooperation & Internationale Beziehungen",
            "yout" => "Jugend & Heranwachsende",
            "chil" => "Kinder",
            "crim" => "Kriminalität & Sicherheit",
            "arts" => "Kunst & Kultur",
            "agri" => "Landwirtschaft, Nahrungsmittel & Ernährung",
            "live" => "Lebensunterhalt",
            "lgbt" => "LGBTQ (Lesben, Schwule, Bisexuelle, Transgender und Queer)",
            "huma" => "Menschenrechte",
            "publ" => "Öffentliche Angelegenheiten",
            "reli" => "Religion & Religiöses",
            "othe" => "Sonstiges/Allgemein",
            "soci" => "Sozialwissenschaften",
            "spor" => "Sport & Freizeit",
            "tour" => "Tourismus & Reisen",
            "envi" => "Umwelt & Klimawandel",
            "busi" => "Unternehmen & Industrie",
            "wate" => "Wasser & Sanitäre Einrichtungen",
            "econ" => "Wirtschaftliche Entwicklung",
            "scie" => "Wissenschaft",
            "hous" => "Wohnen & Unterkunft",
        );
    }

    private function possibleFundings_de()
    {
        return array(
            "publ" => "Öffentlich",
            "priv" => "Privat",
            "corp" => "Unternehmen",
            "none" => "Keine Finanzierung",
        );
    }

    private function possibleFinds_de()
    {
        return array(
            "webs" => "Internetrecherche",
            "face" => "Facebook",
            "twit" => "Twitter",
            "link" => "LinkedIn",
            "adve" => "Werbung",
            "publ" => "Öffentliche Veranstaltung",
            "cont" => "Über eine Kontaktperson",
        );
    }

    private function possibleTranslations_de()
    {
        return array(
            "paid" => "Kostenpflichtige kommerzielle Dienstleistungen",
            "volu" => "Freiwillige",
            "univ" => "Universitäten",
            "none" => "Bisher keine Übersetzungen",
        );
    }

    private function possibleRequests_de()
    {
        return array(
            "allt" => "Wir suchen nach einer auf Freiwilligen basierenden Lösung für alle Übersetzungsaufgaben",
            "addi" => "Wir suchen nach einer auf Freiwilligen basierenden Lösung für zusätzliche Übersetzungsaufgaben",
        );
    }

    private function possibleContents_de()
    {
       return array(
            "webs" => "Webseite",
            "stra" => "Strategie",
            "advo" => "Interessenvertretung",
            "manu" => "Anleitungen",
            "proj" => "Projekte",
            "camp" => "Kampagnen",
            "othe" => "Sonstiges",
       );
    }

    private function possibleOftens_de()
    {
        return array(
            "mont" => "Jeden Monat",
            "quar" => "Jedes Quartal",
            "once" => "Ein- bis zweimal im Jahr",
            "othe" => "Sonstiges",
        );
    }

    private function possibleActivitys_es()
    {
        return array(
            "agri" => "Agricultura, alimentación y nutrición",
            "wate" => "Agua y saneamiento",
            "pove" => "Alivio de la pobreza",
            "hous" => "Alojamiento y abrigo",
            "publ" => "Ámbito público",
            "anim" => "Animales y vida salvaje",
            "arts" => "Arte y cultura",
            "humr" => "Ayuda humanitaria",
            "scie" => "Ciencia",
            "soci" => "Ciencias sociales",
            "indi" => "Comunidades indígenas",
            "pris" => "Convictos/delincuentes/ex delincuentes",
            "inte" => "Cooperación internacional y relaciones internacionales",
            "crim" => "Delincuencia y seguridad",
            "demo" => "Democracia y buen gobierno",
            "spor" => "Deportes y recreo",
            "huma" => "Derechos humanos",
            "comm" => "Desarrollo de la comunidad",
            "civi" => "Desarrollo de la sociedad civil",
            "econ" => "Desarrollo económico",
            "disa" => "Discapacidades y necesidades especiales",
            "drug" => "Drogas y adicción",
            "educ" => "Educación y enseñanza",
            "fami" => "Familia",
            "othe" => "General/Otros",
            "busi" => "Industria y empresa",
            "immi" => "Inmigración",
            "yout" => "Jóvenes y adolescentes",
            "lgbt" => "LGBTQ (comunidad lesbiana, gay, bisexual y transexual)",
            "olde" => "Mayores y envejecimiento saludable",
            "envi" => "Medioambiente y cambio climático",
            "wome" => "Mujer y asuntos de género",
            "chil" => "Niños",
            "peac" => "Paz y resolución de conflictos",
            "refu" => "Refugiados y solicitantes de asilo",
            "reli" => "Religión y fe",
            "heal" => "Salud y bienestar",
            "live" => "Subsistencia",
            "info" => "Tecnologías de la información y comunicaciones",
            "empl" => "Trabajo y empleo",
            "tour" => "Turismo y viajes",
            "hiva" => "VIH/SIDA",
            "volu" => "Voluntariado y ciudadanía activa",
        );
    }

    private function possibleFundings_es()
    {
        return array(
            "publ" => "Fondos públicos",
            "priv" => "Fondos privados",
            "corp" => "Financiación empresarial",
            "none" => "Ninguna",
        );
    }

    private function possibleFinds_es()
    {
        return array(
            "webs" => "Búsqueda en Internet",
            "face" => "Facebook",
            "twit" => "Twitter",
            "link" => "LinkedIn",
            "adve" => "Anuncio",
            "publ" => "Evento público",
            "cont" => "A través de otra persona",
        );
    }

    private function possibleTranslations_es()
    {
        return array(
            "paid" => "Servicios de traducción de pago",
            "volu" => "Voluntarios",
            "univ" => "Universidades",
            "none" => "Aún no hemos traducido nada",
        );
    }

    private function possibleRequests_es()
    {
        return array(
            "allt" => "Buscamos voluntarios para todas las traducciones",
            "addi" => "Buscamos voluntarios para ampliar la capacidad del equipo existente",
        );
    }

    private function possibleContents_es()
    {
       return array(
            "webs" => "Sitio web",
            "stra" => "Planificación/estrategia",
            "advo" => "Legal",
            "manu" => "Guías y tutoriales",
            "proj" => "Proyectos",
            "camp" => "Campañas",
            "othe" => "Otros",
       );
    }

    private function possibleOftens_es()
    {
        return array(
            "mont" => "Todos los meses",
            "quar" => "Cada tres meses",
            "once" => "Una o dos veces al año",
            "othe" => "Otra frecuencia",
        );
    }

    private function possibleActivitys_fr()
    {
        return array(
            "publ" => "Affaires publiques",
            "agri" => "Agriculture, Alimentation & Nutrition",
            "humr" => "Aide humanitaire",
            "anim" => "Animaux & Faune",
            "arts" => "Arts & Culture",
            "othe" => "Autres/Général",
            "busi" => "Commerce & Industrie",
            "indi" => "Communautés autochtones",
            "inte" => "Coopération internationale & Relations internationales",
            "crim" => "Délits & sécurité",
            "demo" => "Démocracie & Bonne gouvernance",
            "pris" => "Détenus/Délinquants/Ex-délinquants",
            "comm" => "Développement communautaire",
            "civi" => "Développement de la société civile",
            "econ" => "Développement économique",
            "drug" => "Drogues & Dépendance",
            "huma" => "Droits humains",
            "wate" => "Eau & Assainissement",
            "educ" => "Éducation & Alphabétisation",
            "empl" => "Emploi & Travail",
            "chil" => "Enfants",
            "envi" => "Environnement & Changement climatique",
            "fami" => "Famille",
            "wome" => "Femmes & Égalité des sexes",
            "disa" => "Handicaps - Besoins particuliers",
            "immi" => "Immigration",
            "yout" => "Jeunes & Adolescents",
            "lgbt" => "LGBTQ (Lesbiennes, Gays, Bisexuels et Transgenres)",
            "hous" => "Logement & Hébergement",
            "pove" => "Lutte contre la pauvreté",
            "live" => "Moyens de subsistance",
            "peac" => "Paix & Règlement des conflits",
            "olde" => "Personnes âgées & Vieillissement actif",
            "refu" => "Refugiés & Demandeurs d'asile",
            "reli" => "Religion & Foi",
            "heal" => "Santé & Bien-être",
            "scie" => "Science",
            "soci" => "Sciences sociales",
            "spor" => "Sports & Loisirs",
            "info" => "Technologies de l'information et de la communication (TIC)",
            "tour" => "Tourisme & Voyage",
            "hiva" => "VIH/SIDA",
            "volu" => "Volontariat & Citoyenneté active",
        );
    }

    private function possibleFundings_fr()
    {
        return array(
            "publ" => "Public",
            "priv" => "Privé",
            "corp" => "Entreprise",
            "none" => "Pas de financement",
        );
    }

    private function possibleFinds_fr()
    {
        return array(
            "webs" => "Moteur de recherche",
            "face" => "Facebook",
            "twit" => "Twitter",
            "link" => "LinkedIn",
            "adve" => "Publicité",
            "publ" => "Événement public",
            "cont" => "Par le biais d'un contact",
        );
    }

    private function possibleTranslations_fr()
    {
        return array(
            "paid" => "Services de traduction payants",
            "volu" => "Bénévoles",
            "univ" => "Universités",
            "none" => "Pas de traductions pour le moment",
        );
    }

    private function possibleRequests_fr()
    {
        return array(
            "allt" => "À la recherche d'une solution basée sur le volontariat pour tous nos besoins en traduction",
            "addi" => "À la recherche d'une solution basée sur le volontariat pour acquérir des moyens supplémentaires",
        );
    }

    private function possibleContents_fr()
    {
       return array(
            "webs" => "Site internet",
            "stra" => "Stratégie",
            "advo" => "Défense",
            "manu" => "Manuels",
            "proj" => "Projets",
            "camp" => "Campagnes",
            "othe" => "Autre",
       );
    }

    private function possibleOftens_fr()
    {
        return array(
            "mont" => "Tous les mois",
            "quar" => "Tous les trimestres",
            "once" => "Une ou deux fois par an",
            "othe" => "Autre",
        );
    }

    public function orgPublicProfile($org_id)
    {
        $app = \Slim\Slim::getInstance();
        $adminDao = new DAO\AdminDao();
        $orgDao = new DAO\OrganisationDao();
        $userDao = new DAO\UserDao();
        $badgeDao = new DAO\BadgeDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $currentUser = $userDao->getUser(Common\Lib\UserSession::getCurrentUserId());
        $isSiteAdmin = $adminDao->isSiteAdmin($currentUser->getId());
        $start_dateError = '';
        if ($isSiteAdmin) {
            $extra_scripts = "
            <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/lib/jquery-ui-timepicker-addon.js\"></script>
            <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/start_datePicker.js\"></script>";
        } else {
            $extra_scripts = '';
        }

        $org = $orgDao->getOrganisation($org_id);
        $org2 = $orgDao->getOrganisationExtendedProfile($org_id);
        if (empty($org2)) {
            $org2 = new Common\Protobufs\Models\OrganisationExtendedProfile();
            $org2->setId($org_id);
            $org2->setFacebook('');
            $org2->setLinkedin('');
            $org2->setPrimaryContactName('');
            $org2->setPrimaryContactTitle('');
            $org2->setPrimaryContactEmail('');
            $org2->setPrimaryContactPhone('');
            $org2->setOtherContacts('');
            $org2->setStructure('');
            $org2->setAffiliations('');
            $org2->setUrlVideo1('');
            $org2->setUrlVideo2('');
            $org2->setUrlVideo3('');
            $org2->setSubjectMatters('');
            $org2->setActivitys('');
            $org2->setEmployees('');
            $org2->setFundings('');
            $org2->setFinds('');
            $org2->setTranslations('');
            $org2->setRequests('');
            $org2->setContents('');
            $org2->setPages('');
            $org2->setSources('');
            $org2->setTargets('');
            $org2->setOftens('');
        }

        $memberIsAdmin = array();

        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            Common\Lib\UserSession::checkCSRFKey($post, 'orgPublicProfile');
                   
            if (isset($post['deleteBadge'])) {
                $badgeDao->deleteBadge($post['badge_id']);
            }
            
            if (isset($post['title']) && isset($post['description'])) {
                if ($post['title'] == "" || $post['description'] == "") {
                    $app->flash("error", sprintf(Lib\Localisation::getTranslation('org_public_profile_19')));
                } else {
                    $badge = new Common\Protobufs\Models\Badge();
                    $badge->setId($post['badge_id']);
                    $badge->setTitle($post['title']);
                    $badge->setDescription($post['description']);
                    $badge->setOwnerId("");
                    $badgeDao->updateBadge($badge);
                    $app->redirect($app->urlFor("org-public-profile", array("org_id" => $org_id)));
                }
            }
            
            if (isset($post['email'])) {
                if (Lib\Validator::validateEmail($post['email'])) {
                    $user = $userDao->getUserByEmail($post['email']);
                
                    if (!is_null($user)) {
                        $user_orgs = $userDao->getUserOrgs($user->getId());
                        if ($user->getDisplayName() != "") {
                            $user_name = $user->getDisplayName();
                        } else {
                            $user_name = $user->getEmail();
                        }
                        if (is_null($user_orgs) || !in_array($org_id, $user_orgs)) {
                            $orgDao->acceptMembershipRequest($org_id, $user->getId());
                            if ($org->getName() != "") {
                                $org_name = $org->getName();
                            } else {
                                $org_name = "Organisation $org_id";
                            }
                            $app->flashNow(
                                "success",
                                sprintf(
                                    Lib\Localisation::getTranslation('common_successfully_added_member'),
                                    $user_name,
                                    $org_name
                                )
                            );
                        } else {
                            $app->flashNow(
                                "error",
                                sprintf(Lib\Localisation::getTranslation('org_public_profile_20'), $user_name)
                            );
                        }
                    } else {
                        $email = $post['email'];
                        $app->flashNow(
                            "error",
                            sprintf(Lib\Localisation::getTranslation('org_public_profile_21'), $email)
                        );
                    }
                } else {
                    $app->flashNow("error", Lib\Localisation::getTranslation('common_no_valid_email'));
                }
            } elseif (isset($post['accept'])) {
                if ($user_id = $post['user_id']) {
                    if ($orgDao->acceptMembershipRequest($org_id, $user_id)) {
                        $user = $userDao->getUser($user_id);
                        $user_name = $user->getDisplayName();
                        $org_name = $org->getName();
                        $app->flashNow(
                            "success",
                            sprintf(
                                Lib\Localisation::getTranslation('org_public_profile_23'),
                                $app->urlFor("user-public-profile", array("user_id" => $user_id)),
                                $user_name,
                                $org_name
                            )
                        );
                    } else {
                        $app->flashNow("error", Lib\Localisation::getTranslation('org_public_profile_24'));
                    }

                } else {
                    $app->flashNow(
                        "error",
                        sprintf(Lib\Localisation::getTranslation('common_invalid_userid'), $user_id)
                    );
                }
            } elseif (isset($post['refuse'])) {
                if ($user_id = $post['user_id']) {
                    $orgDao->rejectMembershipRequest($org_id, $user_id);
                    $user = $userDao->getUser($user_id);
                    $user_name = $user->getDisplayName();
                    $app->flashNow(
                        "success",
                        sprintf(
                            Lib\Localisation::getTranslation('org_public_profile_25'),
                            $app->urlFor("user-public-profile", array("user_id" => $user_id)),
                            $user_name
                        )
                    );
                } else {
                    $app->flashNow(
                        "error",
                        sprintf(Lib\Localisation::getTranslation('common_invalid_userid'), $user_id)
                    );
                }
            } elseif (isset($post['revokeUser'])) {
                $userId = $post['revokeUser'];
                $user = $userDao->getUser($userId);
                if ($user) {
                    $userName = $user->getDisplayName();
                    if ($userDao->leaveOrganisation($userId, $org_id)) {
                        $app->flashNow(
                            "success",
                            sprintf(
                                Lib\Localisation::getTranslation('org_public_profile_26'),
                                $app->urlFor("user-public-profile", array("user_id" => $userId)),
                                $userName
                            )
                        );
                    } else {
                        $app->flashNow(
                            "error",
                            sprintf(
                                Lib\Localisation::getTranslation('org_public_profile_27'),
                                $app->urlFor("user-public-profile", array("user_id" => $userId)),
                                $userName
                            )
                        );
                    }
                } else {
                    $app->flashNow("error", Lib\Localisation::getTranslation('org_public_profile_28'));
                }
            } elseif (isset($post['revokeOrgAdmin'])) {
                $userId = $post['revokeOrgAdmin'];
                error_log("Called revokeOrgAdmin($userId, $org_id)");
                $adminDao->removeOrgAdmin($userId, $org_id);
            } elseif (isset($post['makeOrgAdmin'])) {
                $userId = $post['makeOrgAdmin'];
                error_log("Called createOrgAdmin($userId, $org_id)");
                $adminDao->createOrgAdmin($userId, $org_id);
            } elseif (isset($post['trackOrganisation'])) {
                $user_id = $currentUser->getId();
                if ($post['trackOrganisation']) {
                    $userTrackOrganisation = $userDao->trackOrganisation($user_id, $org_id);
                    if ($userTrackOrganisation) {
                        $app->flashNow(
                            "success",
                            Lib\Localisation::getTranslation('org_public_profile_org_track_success')
                        );
                    } else {
                        $app->flashNow("error", Lib\Localisation::getTranslation('org_public_profile_org_track_error'));
                    }
                } else {
                    $userUntrackOrganisation = $userDao->unTrackOrganisation($user_id, $org_id);
                    if ($userUntrackOrganisation) {
                        $app->flashNow(
                            "success",
                            Lib\Localisation::getTranslation('org_public_profile_org_untrack_success')
                        );
                    } else {
                        $app->flashNow(
                            "error",
                            Lib\Localisation::getTranslation('org_public_profile_org_untrack_error')
                        );
                    }
                }
            } elseif ($isSiteAdmin && isset($post['start_date']) && $post['start_date'] != '' && isset($post['level']) && ($post['level'] == 10 || $post['level'] == 20 || $post['level'] == 30 || $post['level'] == 100 || $post['level'] == 1000)) {
                if ($validTime = Lib\TemplateHelper::isValidDateTime($post['start_date'])) {
                    $start_date = date("Y-m-d H:i:s", $validTime);
                    $comment = '';
                    if (!empty($post['comment'])) $comment = $post['comment'];
                    $level = $post['level'];
                    error_log("updateSubscription($org_id, $level, 0, $start_date, $comment)");
                    $orgDao->updateSubscription($org_id, $post['level'], 0, $start_date, $comment);
                } else {
                    $start_dateError = Lib\Localisation::getTranslation('task_alter_8');
                }
            } elseif (($isSiteAdmin || $adminDao->isOrgAdmin($org_id, $currentUser->getId())) && !empty($post['required_qualification_level'])) {
                $userDao->updateRequiredOrgQualificationLevel($org_id, $post['required_qualification_level']);
            }
        }
        $isMember = false;
        $orgMemberList = $orgDao->getOrgMembers($org_id);
        if (count($orgMemberList) > 0) {
            foreach ($orgMemberList as $member) {
                if ($currentUser->getId() ==  $member->getId()) {
                    $isMember = true;
                }
            }
        }

        $userSubscribedToOrganisation = $userDao->isSubscribedToOrganisation($currentUser->getId(), $org_id);

        $adminAccess = false;
        if ($adminDao->isSiteAdmin($currentUser->getId()) == 1 ||
                $adminDao->isOrgAdmin($org->getId(), $currentUser->getId()) == 1) {
            $adminAccess = true;
        }

        $org_badges = array();
        $user_list = array();

        if ($isMember || $adminAccess) {
            $requests = $orgDao->getMembershipRequests($org_id);
            if (count($requests) > 0) {
                foreach ($requests as $memRequest) {
                    $user = $userDao->getUser($memRequest->getUserId());
                    $user_list[] = $user;
                }
            }

            $org_badges = $orgDao->getOrgBadges($org_id);
            
            if ($orgMemberList) {
                foreach ($orgMemberList as $orgMember) {
                    $memberIsAdmin[$orgMember->getId()] = $adminDao->isOrgAdmin($org_id, $orgMember->getId());
                }
            }
        }

        $no_subscription = true;
        if ($isSiteAdmin) {
            $subscription = $orgDao->getSubscription($org_id);
            if (empty($subscription)) {
                $subscription = array(
                    'organisation_id' => $org_id,
                    'level' => 1000,
                    'spare' => 0,
                    'start_date' => gmdate('Y-m-d H:i:s'),
                    'comment' => '');
            } else {
                $no_subscription = false;
            }
        } else {
            $subscription = array();
        }

        $siteName = Common\Lib\Settings::get("site.name");
        $app->view()->setData("current_page", "org-public-profile");
        $app->view()->appendData(array(
                'sesskey' => $sesskey,
                "org" => $org,
                'org2' => $org2,
                'activitys'    => $this->generateOptions($this->possibleActivitys(), $org2->getActivitys()),
                'employees'    => $this->generateOptions($this->possibleEmployees(), $org2->getEmployees()),
                'fundings'     => $this->generateOptions($this->possibleFundings(), $org2->getFundings()),
                'finds'        => $this->generateOptions($this->possibleFinds(), $org2->getFinds()),
                'translations' => $this->generateOptions($this->possibleTranslations(), $org2->getTranslations()),
                'requests'     => $this->generateOptions($this->possibleRequests(), $org2->getRequests()),
                'contents'     => $this->generateOptions($this->possibleContents(), $org2->getContents()),
                'pages'        => $this->generateOptions($this->possiblePages(), $org2->getPages()),
                'sources'      => $this->generateOptions($this->possibleLanguages(), $org2->getSources()),
                'targets'      => $this->generateOptions($this->possibleLanguages(), $org2->getTargets()),
                'oftens'       => $this->generateOptions($this->possibleOftens(), $org2->getOftens()),
                'isMember'  => $isMember,
                'orgMembers' => $orgMemberList,
                'adminAccess' => $adminAccess,
                'memberIsAdmin' => $memberIsAdmin,
                "org_badges" => $org_badges,
                'isSiteAdmin' => $isSiteAdmin,
                'start_date_error' => $start_dateError,
                'extra_scripts' => $extra_scripts,
                'no_subscription' => $no_subscription,
                'subscription' => $subscription,
                'required_qualification_level' => $userDao->getRequiredOrgQualificationLevel($org_id),
                'siteName' => $siteName,
                "membershipRequestUsers" => $user_list,
                'userSubscribedToOrganisation' => $userSubscribedToOrganisation
        ));

        $app->render("org/org-public-profile.tpl");
    }

    public function orgManageBadge($org_id, $badge_id)
    {
        $app = \Slim\Slim::getInstance();
        $badgeDao = new DAO\BadgeDao();
        $userDao = new DAO\UserDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $badge = $badgeDao->getBadge($badge_id);
        $extra_scripts = "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}";
        $extra_scripts .= "resources/bootstrap/js/confirm-remove-badge.js\"></script>";
        $app->view()->setData("badge", $badge);
        $app->view()->appendData(array(
                    "org_id"        => $org_id,
                    "extra_scripts" =>$extra_scripts
        ));

        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            Common\Lib\UserSession::checkCSRFKey($post, 'orgManageBadge');
            
            if (isset($post['email']) && $post['email'] != "") {
                if (Lib\Validator::validateEmail($post['email'])) {
                    $success = $userDao->assignBadge($post['email'], $badge->getId());
                    if ($success) {
                        $app->flashNow(
                            "success",
                            sprintf(
                                Lib\Localisation::getTranslation('org_manage_badge_29'),
                                $badge->getTitle(),
                                $post['email']
                            )
                        );
                    } else {
                        $app->flashNow(
                            "error",
                            sprintf(Lib\Localisation::getTranslation('org_manage_badge_30'), $post['email'])
                        );
                    }
                } else {
                    $app->flashNow("error", Lib\Localisation::getTranslation('common_no_valid_email'));
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
                $app->flashNow(
                    "success",
                    sprintf(Lib\Localisation::getTranslation('org_manage_badge_32'), $user_name)
                );
            }
        }
    
        $user_list = $badgeDao->getUserWithBadge($badge_id);

        $app->view()->appendData(array(
            'sesskey' => $sesskey,
            "user_list" => $user_list
        ));
        
        $app->render("org/org.manage-badge.tpl");
    }

    public function orgCreateBadge($org_id)
    {
        $app = \Slim\Slim::getInstance();
        $badgeDao = new DAO\BadgeDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        if (\SolasMatch\UI\isValidPost($app)) {
            $post = $app->request()->post();
            Common\Lib\UserSession::checkCSRFKey($post, 'orgCreateBadge');
            
            if ($post['title'] == "" || $post['description'] == "") {
                $app->flashNow("error", Lib\Localisation::getTranslation('common_all_fields'));
            } else {
                $badge = new Common\Protobufs\Models\Badge();
                $badge->setTitle($post['title']);
                $badge->setDescription($post['description']);
                $badge->setOwnerId($org_id);
                $badgeDao->createBadge($badge);
                
                $app->flash("success", Lib\Localisation::getTranslation('org_create_badge_33'));
                $app->redirect($app->urlFor("org-public-profile", array("org_id" => $org_id)));
            }
        }
        
        $app->view()->setData("org_id", $org_id);
        $app->view()->appendData(array(
            'sesskey'       => $sesskey,
        ));
        $app->render("org/org.create-badge.tpl");
    }

    public function orgSearch()
    {
        $app = \Slim\Slim::getInstance();
        $orgDao = new DAO\OrganisationDao();
        $foundOrgs = array();

        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            
            if (isset($post['search_name']) && $post['search_name'] != '') {
                $foundOrgs = $orgDao->searchForOrgByName(urlencode($post['search_name']));
                if (count($foundOrgs) < 1) {
                    $app->flashNow("error", Lib\Localisation::getTranslation('org_search_34'));
                }
                $app->view()->appendData(array('searchedText' => $post['search_name']));
            }

            if (isset($post['allOrgs'])) {
                $foundOrgs = $orgDao->getOrganisations();
            }
        }

        $app->view()->appendData(array(
                    'foundOrgs'     => $foundOrgs
        ));

        $app->render("org/org-search.tpl");
    }
    
    public function orgEditBadge($org_id, $badge_id)
    {
        $app = \Slim\Slim::getInstance();
        $badgeDao = new DAO\BadgeDao();

        $badge = $badgeDao->getBadge($badge_id);
        $app->view()->setData("badge", $badge);
        $app->view()->appendData(array("org_id" => $org_id));
        
        $app->render("org/org.edit-badge.tpl");
    }

    public function orgTaskComplete($orgId, $taskId)
    {
        $app = \Slim\Slim::getInstance();
        $taskDao = new DAO\TaskDao();
        $userDao = new DAO\UserDao();
        $userName = '';
        $claimant = $taskDao->getUserClaimedTask($taskId);
        $claimantProfile = "";
        if ($claimant != null) {
            $claimantProfile = $app->urlFor("user-public-profile", array('user_id' => $claimant->getId()));
            $userName = $userDao->getUserRealName($claimant->getId());
            if (is_null($userName) || $userName == '') {
                $userName = $claimant->getDisplayName();
            }
        }

        $task = $taskDao->getTask($taskId);
        $viewData = array(
                "task"              => $task,
                'claimant'          => $claimant,
                'userName'          => $userName,
                'claimantProfile'   => $claimantProfile,
                "orgId"             => $orgId
        );

        $app->view()->appendData($viewData);
        $app->render("org/org.task-complete.tpl");
    }

    public function orgTaskReview($orgId, $taskId)
    {
        $app = \Slim\Slim::getInstance();
        $taskDao = new DAO\TaskDao();
        $userDao = new DAO\UserDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $userId = Common\Lib\UserSession::getCurrentUserID();
        $task = $taskDao->getTask($taskId);

        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            Common\Lib\UserSession::checkCSRFKey($post, 'orgTaskReview');

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
                    if ($value > 0 && $value <= 5) {
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
                    $app->flashNow("error", $error);
                } else {
                    $app->flash(
                        "success",
                        sprintf(
                            Lib\Localisation::getTranslation('org_task_review_40'),
                            $task->getTitle()
                        )
                    );
                    $app->redirect($app->urlFor('project-view', array("project_id" => $task->getProjectId())));
                }
            }

            if (isset($post['skip'])) {
                $app->redirect($app->urlFor("project-view", array('project_id' => $task->getProjectId())));
            }
        }

        $taskReview = $userDao->getUserTaskReviews($userId, $taskId);
        if (!is_null($taskReview)) {
            $app->flashNow("info", Lib\Localisation::getTranslation('org_task_review_41'));
        }

        $translator = $taskDao->getUserClaimedTask($taskId);

        $formAction = $app->urlFor("org-task-review", array(
                    'org_id'    => $orgId,
                    'task_id'   => $taskId
        ));

        $extra_scripts = "";
        $extra_scripts .= "<script type='text/javascript'>";
        $extra_scripts .= "var taskIds = new Array();";
        $extra_scripts .= "taskIds[0] = $taskId;";
        $extra_scripts .= "</script>";
        
        $extra_scripts .= "<link rel=\"stylesheet\" href=\"{$app->urlFor("home")}ui/js/RateIt/src/rateit.css\"/>";
        $extra_scripts .= "<script>".file_get_contents(__DIR__."/../js/RateIt/src/jquery.rateit.min.js")."</script>";
        $extra_scripts .= file_get_contents(__DIR__."/../js/review.js");
        // Load Twitter JS asynch, see https://dev.twitter.com/web/javascript/loading
        $extra_scripts .= '<script>window.twttr = (function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0], t = window.twttr || {}; if (d.getElementById(id)) return t; js = d.createElement(s); js.id = id; js.src = "https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs); t._e = []; t.ready = function(f) { t._e.push(f); }; return t; }(document, "script", "twitter-wjs"));</script>';

        $app->view()->appendData(array(
                    'sesskey' => $sesskey,
                    'extra_scripts' => $extra_scripts,
                    'task'      => $task,
                    'review'    => $taskReview,
                    'translator'=> $translator,
                    'formAction'=> $formAction
        ));

        $app->render("org/org.task-review.tpl");
    }

    public function orgTaskReviews($orgId, $taskId)
    {
        $app = \Slim\Slim::getInstance();
        $viewData = array();
        $taskDao = new DAO\TaskDao();
        $task = $taskDao->getTask($taskId);
        $preReqTasks = array();
        $preReqs = $taskDao->getTaskPreReqs($taskId);
        $reviews = array();
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

        $extra_scripts = "<link rel=\"stylesheet\" href=\"{$app->urlFor("home")}ui/js/RateIt/src/rateit.css\"/>";
        $extra_scripts .= "<script>".file_get_contents(__DIR__."/../js/RateIt/src/jquery.rateit.min.js")."</script>";

        $viewData['task'] = $task;
        $viewData['reviews'] = $reviews;
        $viewData['extra_scripts'] = $extra_scripts;

        $app->view()->appendData($viewData);
        $app->render('org/org.task-reviews.tpl');
    }
}

$route_handler = new OrgRouteHandler();
$route_handler->init();
unset ($route_handler);
