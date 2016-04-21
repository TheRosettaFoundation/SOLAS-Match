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
        $errorOccured = null;
        $errorList = array();
        if ($post = $app->request()->post()) {

            $org = new Common\Protobufs\Models\Organisation();

            if (isset($post["orgName"]) && $post["orgName"] != '') {
                if (Lib\Validator::filterSpecialChars($post["orgName"])) {
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
            if (isset($post["biography"])) {
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
                }
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
            
            if (is_null($errorOccured)) {
                $user_id = Common\Lib\UserSession::getCurrentUserID();
                $orgDao = new DAO\OrganisationDao();

                try {
                    error_log("Calling createOrg(, $user_id)");
                    $new_org = $orgDao->createOrg($org, $user_id);
                    if ($new_org) {
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

                $app->view()->appendData(array(
                    "org"     => $org,
                    "errorOccured" => $errorOccured,
                    "errorList" => $errorList
                ));
            }
        }
        $app->view()->appendData(array(
            "org"     => null,
            "errorOccured" => $errorOccured,
            "errorList" => $errorList
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
        
        $current_user = $userDao->getUser($current_user_id);
        $my_organisations = $userDao->getUserOrgs($current_user_id);
        $org_projects = array();
        
        if ($app->request()->isPost()) {
            $post = $app->request()->post();
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
                        $temp['userSubscribedToProject'] = $userDao->isSubscribedToProject(
                            Common\Lib\UserSession::getCurrentUserID(),
                            $project->getId()
                        );
                        $taskData[]=$temp;
                    }
                } else {
                    $taskData = null;
                }
                $templateData[$org->getId()] = $taskData;
            }

            $app->view()->appendData(array(
                "orgs" => $orgs,
                "templateData" => $templateData
            ));
        }

        $extra_scripts = file_get_contents(__DIR__."/../js/TaskView.js");
        // Load Twitter JS asynch, see https://dev.twitter.com/web/javascript/loading
        $extra_scripts .= '<script>window.twttr = (function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0], t = window.twttr || {}; if (d.getElementById(id)) return t; js = d.createElement(s); js.id = id; js.src = "https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs); t._e = []; t.ready = function(f) { t._e.push(f); }; return t; }(document, "script", "twitter-wjs"));</script>';
        
        $app->view()->appendData(array(
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

        $org = $orgDao->getOrganisation($org_id);
        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            
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
        $app->view()->appendData(array("user_list" => $user_list));
        
        $app->render("org/org.request_queue.tpl");
    }

    public function orgPrivateProfile($org_id)
    {
        $app = \Slim\Slim::getInstance();
        $orgDao = new DAO\OrganisationDao();
        $org = $orgDao->getOrganisation($org_id);
        $org2 = $orgDao->getOrganisationExtendedProfile($org_id)
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
                if (isset($post['orgName']) && $post['orgName'] != '') {
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
                if (empty($post['primarycontactname'])) {
                    $errorOccured = true;
                    $errorList[] = Lib\Localisation::getTranslation('org_private_profile_organisation_error_name_not_set');
                }
                if (empty($post['primarycontactemail'])) {
                    $errorOccured = true;
                    $errorList[] = Lib\Localisation::getTranslation('org_private_profile_organisation_error_email_not_set');
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
                    }
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
                
                if (!is_null($errorOccured)) {
                    $app->view()->appendData(array(
                    "org"     => $org,
                    "errorOccured" => $errorOccured,
                    "errorList" => $errorList
                    ));
                } else {
                    $orgDao = new DAO\OrganisationDao();
                    try {
                        $orgDao->updateOrg($org);

                        if (isset($post['facebook'])) {
                            $org2->setFacebook($post['facebook']);
                        }
                        if (isset($post['linkedin'])) {
                            $org2->setLinkedin($post['linkedin']);
                        }
                        if (isset($post['primarycontactname'])) {
                            $org2->setPrimaryContactName($post['primarycontactname']);
                        }
                        if (isset($post['primarycontacttitle'])) {
                            $org2->setPrimaryContactTitle($post['primarycontacttitle']);
                        }
                        if (isset($post['primarycontactemail'])) {
                            $org2->setPrimaryContactEmail($post['primarycontactemail']);
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
                        if (isset($post['urlvideo1'])) {
                            $org2->setUrlVideo1($post['urlvideo1']);
                        }
                        if (isset($post['urlvideo2'])) {
                            $org2->setUrlVideo2($post['urlvideo2']);
                        }
                        if (isset($post['urlvideo3'])) {
                            $org2->setUrlVideo3($post['urlvideo3']);
                        }
                        if (isset($post['subjectmatters'])) {
                            $org2->setSubjectMatters($post['subjectmatters']);
                        }
                        if (isset($post['activitys'])) {
                            $org2->setActivitys(implode(',' , $post['activitys']));
                        }
                        if (isset($post['employees'])) {
                            $org2->setEmployees(implode(',' , $post['employees']));
                        }
                        if (isset($post['fundings'])) {
                            $org2->setFundings(implode(',' , $post['fundings']));
                        }
                        if (isset($post['finds'])) {
                            $org2->setFinds(implode(',' , $post['finds']));
                        }
                        if (isset($post['translations'])) {
                            $org2->setTranslations(implode(',' , $post['translations']));
                        }
                        if (isset($post['requests'])) {
                            $org2->setRequests(implode(',' , $post['requests']));
                        }
                        if (isset($post['contents'])) {
                            $org2->setContents(implode(',' , $post['contents']));
                        }
                        if (isset($post['pages'])) {
                            $org2->setPages(implode(',' , $post['pages']));
                        }
                        if (isset($post['sources'])) {
                            $org2->setSources(implode(',' , $post['sources']));
                        }
                        if (isset($post['targets'])) {
                            $org2->setTargets(implode(',' , $post['targets']));
                        }
                        if (isset($post['oftens'])) {
                            $org2->setOftens(implode(',' , $post['oftens']));
                        }
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
                $deleteId = $post["deleteId"];
                if ($deleteId) {
                    if ($orgDao->deleteOrg($org->getId())) {
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
        if ($adminDao->isOrgAdmin($org->getId(), $userId) || $adminDao->isSiteAdmin($userId)) {
            $app->view()->appendData(array('orgAdmin' => true));
        }
        
        $possibleActivitys = array(
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
            'pris' => 'Prisioners/Offenders/Ex-offenders',
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
        $possibleEmployees = array(
            '0'    => '0',
            '1'    => '1',
            '5'    => '2-5',
            '20'   => '6-20',
            '100'  => '21-100',
            '1000' => '101+',
        );
        $possibleFundings = array(
            'publ' => 'Public',
            'priv' => 'Private',
            'corp' => 'Corporate',
        );
        $possibleFinds = array(
            'webs' => 'Web Search',
            'face' => 'Facebook',
            'twit' => 'Twitter',
            'link' => 'LinkedIn',
            'sdve' => 'Advertisement',
            'cont' => 'Through a Contact',
        );
        $possibleTranslations = array(
            'paid' => 'Paid commercial services',
            'volu' => 'Volunteers',
            'univ' => 'Universities',
            'none' => 'None of the above',
        );
        $possibleRequests = array(
            'allt' => 'Looking for a volunteer-based solution for all our translation needs',
            'addi' => 'Looking for a volunteer-based solution to provide additional capacity to our volunteers',
        );
        $possibleContents = array(
            'webs' => 'Website',
            'stra' => 'Strategy',
            'advo' => 'Advocacy',
            'manu' => 'Manuals',
            'proj' => 'Projects',
            'camp' => 'Campaigns',
            'othe' => 'Other',
        );
        $possiblegetPages = array(
            '10' => '1-10',
            '100' => '11-100',
            '1000' => '100-1000',
            '10000' => '1000-10000',
            '100000' => '10000+',
        );
        $langDao = new DAO\LanguageDao();
        $languages = $langDao->getLanguages();
        $possibleLanguages = array();
        foreach ($languages as $language) {
            $possibleLanguages[$language->getCode()] = $language->getName();
        }
        $possibleOftens = array(
            'mont' => 'Every month',
            'quar' => 'Every quarter',
            'once' => 'Once or twice per year',
            'othe' => 'Other',
        );

        $app->view()->appendData(array(
            'org'  => $org,
            'org2' => $org2,
            'activitys'    => $this->generateOptions($possibleActivitys, $org2->getActivitys()),
            'employees'    => $this->generateOptions($possibleEmployees, $org2->getEmployees()),
            'fundings'     => $this->generateOptions($possibleFundings, $org2->getFundings()),
            'finds'        => $this->generateOptions($possibleFinds, $org2->getFinds()),
            'translations' => $this->generateOptions($possibleTranslations, $org2->getTranslations()),
            'requests'     => $this->generateOptions($possibleRequests, $org2->getRequests()),
            'contents'     => $this->generateOptions($possibleContents, $org2->getContents()),
            'pages'        => $this->generateOptions($possiblePages, $org2->getPages()),
            'sources'      => $this->generateOptions($possibleLanguages, $org2->getSources()),
            'targets'      => $this->generateOptions($possibleLanguages, $org2->getTargets()),
            'oftens'       => $this->generateOptions($possibleOftens, $org2->getOftens()),
        ));

        $app->render("org/org-private-profile.tpl");
    }

    private function generateOptions($possibleOptions, $selectedCodes)
    {
        $selectedCodesArray = explode(',' , $selectedCodes);
        $options = array();
        foreach($possibleOptions as $code => $option) {
            $options[] = array('code' => $code, 'selected' => in_array($code, $selectedCodesArray), 'value' => $option);
        }
        return $options;
    }

    public function orgPublicProfile($org_id)
    {
        $app = \Slim\Slim::getInstance();
        $adminDao = new DAO\AdminDao();
        $orgDao = new DAO\OrganisationDao();
        $userDao = new DAO\UserDao();
        $badgeDao = new DAO\BadgeDao();

        $currentUser = $userDao->getUser(Common\Lib\UserSession::getCurrentUserId());
        $org = $orgDao->getOrganisation($org_id);
        $memberIsAdmin = array();

        if ($app->request()->isPost()) {
            $post = $app->request()->post();
                   
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

        $siteName = Common\Lib\Settings::get("site.name");
        $app->view()->setData("current_page", "org-public-profile");
        $app->view()->appendData(array(
                "org" => $org,
                'isMember'  => $isMember,
                'orgMembers' => $orgMemberList,
                'adminAccess' => $adminAccess,
                'memberIsAdmin' => $memberIsAdmin,
                "org_badges" => $org_badges,
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
            "user_list" => $user_list
        ));
        
        $app->render("org/org.manage-badge.tpl");
    }

    public function orgCreateBadge($org_id)
    {
        $app = \Slim\Slim::getInstance();
        $badgeDao = new DAO\BadgeDao();

        if (\SolasMatch\UI\isValidPost($app)) {
            $post = $app->request()->post();
            
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

        $userId = Common\Lib\UserSession::getCurrentUserID();
        $task = $taskDao->getTask($taskId);

        if ($app->request()->isPost()) {
            $post = $app->request()->post();

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
