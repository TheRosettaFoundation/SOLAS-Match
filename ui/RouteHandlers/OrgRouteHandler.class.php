<?php

class OrgRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();
        $middleware = new Middleware();

        $app->get("/org/create", array($middleware, "authUserIsLoggedIn"), 
        array($this, "createOrg"))->via("POST")->name("create-org");
        
        $app->get("/org/dashboard", array($middleware, "authUserIsLoggedIn"), 
        array($this, "orgDashboard"))->via("POST")->name("org-dashboard");        

        $app->get("/org/:org_id/request", array($this, "orgRequestMembership")
        )->name("org-request-membership");

        $app->get("/org/:org_id/request/:user_id/:accept", array($middleware, "authUserForOrg"), 
        array($this, "orgProcessRequest"))->name("org-process-request");

        $app->get("/org/:org_id/request/queue", array($middleware, "authUserForOrg"), 
        array($this, "orgRequestQueue"))->via("POST")->name("org-request-queue");

        $app->get("/org/:org_id/private", array($middleware, "authUserForOrg"), 
        array($this, "orgPrivateProfile"))->via("POST")->name("org-private-profile");

        $app->get("/org/:org_id/profile", array($middleware, "authUserIsLoggedIn"),
        array($this, "orgPublicProfile"))->via("POST")->name("org-public-profile");

        $app->get("/org/:org_id/manage/:badge_id", array($middleware, "authUserForOrg"), 
        array($this, "orgManageBadge"))->via("POST")->name("org-manage-badge");

        $app->get("/org/:org_id/create/badge", array($middleware, "authUserForOrg"), 
        array($this, "orgCreateBadge"))->via("POST")->name("org-create-badge");

        $app->get("/org/search", array($this, "orgSearch")
        )->via("POST")->name("org-search");
        
        $app->get("/org/:org_id/edit/:badge_id", array($middleware, "authUserForOrg"), 
        array($this, "orgEditBadge"))->via("POST")->name("org-edit-badge");         
    }

    public function createOrg()
    {
        $app = Slim::getInstance();   
        
        if ($post = $app->request()->post()) {
            $nameErr = null;

            $org = new Organisation();

            if(isset($post["orgName"]) && $post["orgName"] != '') {
                $org->setName($post['orgName']); 
            } else {
                $nameErr = "<strong>Organisation Name</strong> must be set.";
            }
            
            if(isset($post["homepage"])) $org->setHomePage($post["homepage"]); 
            if(isset($post["biography"])) $org->setBiography($post["biography"]);
            if(isset($post["address"])) $org->setAddress($post["address"]);
            if(isset($post["city"])) $org->setCity($post["city"]);
            if(isset($post["country"])) $org->setCountry($post["country"]);
            if(isset($post["email"])) $org->setEmail($post["email"]);

            $regionalFocus = array();
            if(isset($post["africa"])) $regionalFocus[] = "Africa";             
            if(isset($post["asia"])) $regionalFocus[] = "Asia";             
            if(isset($post["australia"])) $regionalFocus[] = "Australia"; 
            if(isset($post["europe"])) $regionalFocus[] .= "Europe"; 
            if(isset($post["northAmerica"])) $regionalFocus[] .= "North-America"; 
            if(isset($post["southAmerica"])) $regionalFocus[] .= "South-America"; 
            
            if(!empty($regionalFocus)) {
                $regionalFocusString = "";
                foreach($regionalFocus as $region) {
                    $regionalFocusString .= $region.", ";
                }
                $lastComma = strrpos($regionalFocusString, ",");
                $regionalFocusString[$lastComma] = "";
                $org->setRegionalFocus($regionalFocusString);
            }
            
            if(is_null($nameErr)) {
                $user_id = UserSession::getCurrentUserID();
                $orgDao = new OrganisationDao();
                $organisation = $orgDao->getOrganisationByName($org->getName());

                if (!$organisation) {
                    $new_org = $orgDao->createOrg($org, $user_id);
                    if ($new_org) {
                        
                        $orgDao->createMembershipRequest($new_org->getId(), $user_id);
                        $orgDao->acceptMembershipRequest($new_org->getId(), $user_id);
                        $org_name = $org->getName();
                        $app->flash("success", "The organisation <strong>$org_name</strong> has been created.");
                        $app->redirect($app->urlFor("org-dashboard"));
                    } else {
                        $app->flashNow("error", "Unable to save Organisation.");
                    }
                } else {
                    $org_name = $org->getName();
                    $app->flashNow("error", "An Organisation named <strong>$org_name</strong> is already registered
                                            with SOLAS Match. Please use a different name.");
                }         
            } else {
                $app->view()->appendData(array(
                    "org"     => $org,
                    "nameErr" => $nameErr
                ));
            }
        }   
        $app->view()->appendData(array(
            "org"     => null
        ));
        $app->render("create-org.tpl");
    }    

    public function orgDashboard()
    {
        $app = Slim::getInstance();
        $current_user_id    = UserSession::getCurrentUserID();
        $userDao = new UserDao();
        $orgDao = new OrganisationDao();
        $tagDao = new TagDao();
        $projectDao = new ProjectDao();
        
        $current_user = $userDao->getUser($current_user_id);        
        $my_organisations = $userDao->getUserOrgs($current_user_id);
        $org_projects = array();
        
        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();
            if (isset($post->track)) {
                $project_id = $post->project_id;
                $project = $projectDao->getProject($project_id);

                $project_title = "";
                if ($project->getTitle() != "") {
                    $project_title = $project->getTitle();
                } else {
                    $project_title = "project ".$project->getId();
                }
                if ($post->track == "Ignore") {
                    $success = $userDao->untrackProject($current_user_id, $project_id);                    
                    if ($success) {
                        $app->flashNow("success", "No longer receiving notifications from $project_title.");
                    } else {
                        $app->flashNow("error", "Unable to unsubscribe from $project_title 's notifications.");
                    }                    
                } elseif ($post->track == "Track") {
                    $success = $userDao->trackProject($current_user_id, $project_id);                    
                    if ($success) {
                        $app->flashNow("success", "You will now receive notifications for $project_title.");
                    } else {
                        $app->flashNow("error", "Unable to subscribe to $project_title.");
                    }
                } else {
                    $app->flashNow("error", "Invalid POST type");
                }
            }
        }
        
        $orgs = array();
        foreach ($my_organisations as $org) {
            $my_org_projects = $orgDao->getOrgProjects($org->getId());
            $org_projects[$org->getId()] = $my_org_projects;
            $orgs[$org->getId()] = $org;
        }
        
        if (count($org_projects) > 0) {
            $templateData = array();
            foreach ($org_projects as $org => $projectArray) {
                $taskData = array();
                if ($projectArray) {
                    foreach ($projectArray as $project) {
                        $temp = array();
                        $temp['project'] = $project;
                        $temp['userSubscribedToProject'] = $userDao->isSubscribedToProject(
                                UserSession::getCurrentUserID(), $project->getId());
                        $taskData[]=$temp;
                    }
                } else {
                    $taskData = null;
                }
                $templateData[$org] = $taskData;
            }
            
            $app->view()->appendData(array(
                "orgs" => $orgs,
                "templateData" => $templateData
            ));
        }
        
        $app->view()->appendData(array(
            "current_page"  => "org-dashboard"
        ));
        $app->render("org.dashboard.tpl");
    }    
    

    public function orgRequestMembership($org_id)
    {
        $app = Slim::getInstance();
        $userDao = new UserDao();
        $orgDao = new OrganisationDao();

        $userId = UserSession::getCurrentUserID();
        $user = $userDao->getUser($userId);
        $user_orgs = $userDao->getUserOrgs($userId);
        if (is_null($user_orgs) || !in_array($org_id, $user_orgs)) {
            $requestMembership = $orgDao->createMembershipRequest($org_id, $userId);
            if ($requestMembership) {
                $app->flash("success", "Successfully requested membership.");
            } else {
                $app->flash("error", "You have already sent a membership request to this Organisation.");
            }   
        } else {
            $app->flash("error", "You are already a member of this organisation.");
        }   
        $app->redirect($app->urlFor("org-public-profile", array("org_id" => $org_id)));
    }

    public function orgRequestQueue($org_id)
    {
        $app = Slim::getInstance();
        $orgDao = new OrganisationDao();
        $userDao = new UserDao();

        $org = $orgDao->getOrganisation($org_id);
        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();
            
            if (isset($post->email)) {
                if (TemplateHelper::isValidEmail($post->email)) {       
                    $user = $userDao->getUserByEmail($post->email);
                    if (!is_null($user)) {
                        $user_id = $user->getId();
                        $user_orgs = $userDao->getUserOrgs($user_id);
                    
                        if ($user->getDisplayName() != "") {
                            $user_name = $user->getDisplayName();
                        } else {
                            $user_name = $user->getEmail();
                        }   
                        if (is_null($user_orgs) || !in_array($org_id, $user_orgs)) {
                            $orgDao->acceptMembershipRequest($org_id, $user_id);
                            if ($org->getName() != "") {
                                $org_name = $org->getName();
                            } else {
                                $org_name = "Organisation $org_id";
                            }   
                            $app->flashNow("success", "Successfully added $user_name as a member of $org_name.");
                        } else {
                            $app->flashNow("error", "$user_name is already a member of this organisation.");
                        }   
                    } else {
                        $email = $post->email;
                        $app->flashNow("error",
                            "The email address $email is not registered with this system.
                            Are you sure you have the right email addess?"
                        );
                    }
                } else {
                    $app->flashNow("error", "You did not enter a valid email address.");
                }
            } elseif (isset($post->accept)) {
                if ($user_id = $post->user_id) {
                    $orgDao->acceptMembershipRequest($org_id, $user_id);
                } else {
                    $app->flashNow("error", "Invalid User ID: $user_id");
                }
            } elseif (isset($post->refuse)) {
                if ($user_id = $post->user_id) {
                    $orgDao->rejectMembershipRequest($org_id, $user_id);
                } else {
                    $app->flashNow("error", "Invalid User ID: $user_id");
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
        
        $app->render("org.request_queue.tpl");
    }

    public function orgPrivateProfile($org_id)
    {
        $app = Slim::getInstance();
        $orgDao = new OrganisationDao();
        $org = $orgDao->getOrganisation($org_id);
        if($post = $app->request()->post()) {

            if(isset($post['displayName'])) $org->setName($post['displayName']); 
            if(isset($post['homepage'])) $org->setHomePage($post['homepage']); 
            if(isset($post['biography'])) $org->setBiography($post['biography']);
            if(isset($post['address'])) $org->setAddress($post['address']);
            if(isset($post['city'])) $org->setCity($post['city']);
            if(isset($post['country'])) $org->setCountry($post['country']);
            if(isset($post['email'])) $org->setEmail($post['email']);

            $regionalFocus = array();
            if(isset($post["africa"])) $regionalFocus[] = "Africa";             
            if(isset($post["asia"])) $regionalFocus[] = "Asia";             
            if(isset($post["australia"])) $regionalFocus[] = "Australia"; 
            if(isset($post["europe"])) $regionalFocus[] .= "Europe"; 
            if(isset($post["northAmerica"])) $regionalFocus[] .= "North-America"; 
            if(isset($post["southAmerica"])) $regionalFocus[] .= "South-America"; 

            if(!empty($regionalFocus)) {
                $regionalFocusString = "";
                foreach($regionalFocus as $region) {
                    $regionalFocusString .= $region.", ";
                }
                $lastComma = strrpos($regionalFocusString, ",");
                $regionalFocusString[$lastComma] = "";
                $org->setRegionalFocus($regionalFocusString);
            }


            $orgDao->updateOrg($org); 
            $app->redirect($app->urlFor("org-public-profile", array("org_id" => $org->getId())));
        }

        $deleteId = $app->request()->post("deleteId");
        if ($deleteId) {
            if ($orgDao->deleteOrg($org->getId())) {
                $app->flash("success", "Successfully deleted org ".$org->getName());
                $app->redirect($app->urlFor("home"));
            } else {
                $app->flashNow("error", "Unable to delete organisation. Please try again later.");
            }
        }
        

        $userDao = new UserDao();
        if ($userDao->isAdmin(UserSession::getCurrentUserId(), $org->getId())) {
            $app->view()->appendData(array('orgAdmin' => true));
        }
        
        $app->view()->setData("org", $org);        
        $app->render("org-private-profile.tpl");
    }

    public function orgPublicProfile($org_id)
    {
        $app = Slim::getInstance();
        $orgDao = new OrganisationDao();
        $userDao = new UserDao();
        $badgeDao = new BadgeDao();

        $org = $orgDao->getOrganisation($org_id);
        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();
                   
            if (isset($post->deleteBadge)) {
                $badgeDao->deleteBadge($post->badge_id);
            } 
            
            if (isset($post->title) && isset($post->description)) {
                if ($post->title == "" || $post->description == "") {
                    $app->flash("error", "All fields must be filled out.");
                } else {
                    $badge = new Badge();
                    $badge->setId($post->badge_id);
                    $badge->setTitle($post->title);
                    $badge->setDescription($post->description);
                    $badge->setOwnerId(null);
                    $badgeDao->updateBadge($badge); 
                    $app->redirect($app->urlFor("org-public-profile", array("org_id" => $org_id)));
                }
            }
            
            if (isset($post->email)) {
                if (TemplateHelper::isValidEmail($post->email)) {       
                    $user = $userDao->getUserByEmail($post->email);
                
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
                            $app->flashNow("success", "Successfully added $user_name as a member of $org_name");
                        } else {
                            $app->flashNow("error", "$user_name is already a member of this organisation.");
                        }   
                    } else {
                        $email = $post->email;
                        $app->flashNow("error",
                            "The email address $email is not registered with this system.
                            Are you sure you have the right email addess?"
                        );
                    }
                } else {
                    $app->flashNow("error", "You did not enter a valid email address");
                }
            } elseif (isset($post->accept)) {
                if ($user_id = $post->user_id) {
                    if ($orgDao->acceptMembershipRequest($org_id, $user_id)){
                        $user = $userDao->getUser($user_id);
                        $user_name = $user->getDisplayName();
                        $org_name = $org->getName();
                        $app->flashNow("success", "Successfully added ".
                                "<a href=\"{$app->urlFor("user-public-profile", array("user_id" => $user_id))}\">".
                                "$user_name</a> as a member of $org_name");
                    } else {
                        $app->flashNow("error", "Unable to add user to member list. Please try again later.");
                    }

                } else {
                    $app->flashNow("error", "Invalid User ID: $user_id");
                }
            } elseif (isset($post->refuse)) {
                if ($user_id = $post->user_id) {
                    $orgDao->rejectMembershipRequest($org_id, $user_id);
                    $user = $userDao->getUser($user_id);
                    $user_name = $user->getDisplayName();
                    $app->flashNow("success", "Successfully rejected 
                            <a href=\"{$app->urlFor("user-public-profile", array("user_id" => $user_id))}\">
                            $user_name's</a> membership request.");
                } else {
                    $app->flashNow("error", "Invalid User ID: $user_id");
                }
            } elseif (isset($post->revokeUser)) {
                $userId = $post->revokeUser;
                $user = $userDao->getUser($userId);
                if ($user) {
                    $userName = $user->getDisplayName();
                    if ($userDao->leaveOrganisation($userId, $org_id)) {
                        $app->flashNow("success", "Successfully rvoked membership from user
                                <a href=\"{$app->urlFor("user-public-profile", array("user_id" => $userId))}\">
                                $userName</a>.");
                    } else {
                        $app->flashNow("error", "Unable to revoke membership from user
                                <a href=\"{$app->urlFor("user-public-profile", array("user_id" => $userId))}\">
                                $userName</a>.");
                    }
                } else {
                    $app->flashNow("error", "Unable to find user in system");
                }
            }
        }       
        
        $requests = $orgDao->getMembershipRequests($org_id);
        $user_list = array();
        if (count($requests) > 0) {
            foreach ($requests as $memRequest) {
                $user = $userDao->getUser($memRequest->getId());
                $user_list[] = $user;
            }
        }

        $currentUser = $userDao->getUser(UserSession::getCurrentUserId());

        $org_badges = $orgDao->getOrgBadges($org_id);
        $orgMemberList = $orgDao->getOrgMembers($org_id);
        $isMember = false;
        if (count($orgMemberList) > 0) {
            if (in_array($currentUser, $orgMemberList)) {
                $isMember = true;

            }
        }

        $adminAccess = false;
        if ($userDao->isAdmin($currentUser->getId(), $org->getId())) {
            $adminAccess = true;
        }

        $app->view()->setData("current_page", "org-public-profile");
        $app->view()->appendData(array(
                "org" => $org,
                'isMember'  => $isMember,
                'orgMembers' => $orgMemberList,
                'adminAccess' => $adminAccess,
                "org_badges" => $org_badges,
                "user_list" => $user_list
        ));
        
        $app->render("org-public-profile.tpl");
    }

    public function orgManageBadge($org_id, $badge_id)
    {
        $app = Slim::getInstance();
        $badgeDao = new BadgeDao();
        $userDao = new UserDao();

        $badge = $badgeDao->getBadge($badge_id);
        $extra_scripts = "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}";
        $extra_scripts .= "resources/bootstrap/js/confirm-remove-badge.js\"></script>";
        $app->view()->setData("badge", $badge);
        $app->view()->appendData(array(
                    "org_id"        => $org_id,
                    "extra_scripts" =>$extra_scripts
        ));

        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();
            
            if (isset($post->email) && $post->email != "") {
                if (TemplateHelper::isValidEmail($post->email)) {
                    $user = $userDao->getUserByEmail($post->email);
                    if ($user) {
                        $user_badges = $userDao->getUserBadges($user->getId());
                        $badge_ids = array();
                        if (count($user_badges) > 0) {
                            foreach ($user_badges as $badge_tmp) {
                                $badge_ids[] = $badge_tmp->getId();
                            }
                        }
                        
                        if (!in_array($badge_id, $badge_ids)) {
                            $userDao->addUserBadge($user->getId(), $badge);
                            $user_name = "";
                            if ($user->getDisplayName() != "") {
                                $user_name = $user->getDisplayName();
                            } else {
                                $user_name = $user->getEmail();
                            }
                            
                            $app->flashNow("success", "Successfully Assigned Badge 
                                            {$badge->getTitle()} to user $user_name");
                        } else {
                            $app->flashNow("error", "The user $post->email already has that badge.");
                        }
                    } else {
                        $app->flashNow("error",
                            "The email address $post->email is not registered on the system. 
                            Are you using the correct email address?"
                        );
                    }
                } else {
                    $app->flashNow("error", "You did not enter a valid email address.");
                }
            } elseif (isset($post->user_id) && $post->user_id != "") {
                $user_id = $post->user_id;
                $user = $userDao->getUser($user_id);
                $userDao->removeUserBadge($user_id, $badge_id);
                $user_name = "";
                if ($user->getDisplayName() != "") {
                    $user_name = $user->getDisplayName();
                } else {
                    $user_name = $user->getEmail();
                }
                $app->flashNow("success", "Successfully removed badge form user $user_name.");
            } else {
                $app->flashNow("error", "Incorrect POST data.");
            }
        }
    
        $user_list = $badgeDao->getUserWithBadge($badge_id);

        $app->view()->appendData(array(
            "user_list" => $user_list
        ));
        
        $app->render("org.manage-badge.tpl");
    }

    public function orgCreateBadge($org_id)
    {
        $app = Slim::getInstance();
        $badgeDao = new BadgeDao();

        if (isValidPost($app)) {
            $post = (object) $app->request()->post();
            
            if ($post->title == "" || $post->description == "") {
                $app->flashNow("error", "All fields must be filled out.");
            } else {
                $badge = new Badge();
                $badge->setTitle($post->title);
                $badge->setDescription($post->description);
                $badge->setOwnerId($org_id);
                $badgeDao->createBadge($badge);                
                
                $app->flash("success", "Successfully created new Organisation Badge.");
                $app->redirect($app->urlFor("org-public-profile", array("org_id" => $org_id)));
            }
        }
        
        $app->view()->setData("org_id", $org_id);        
        $app->render("org.create-badge.tpl");
    }

    public function orgSearch()
    {
        $app = Slim::getInstance();
        $orgDao = new OrganisationDao();

        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();
            
            if (isset($post->search_name) && $post->search_name != '') {                
                $found_orgs = $orgDao->searchForOrgByName($post->search_name);
                if (count($found_orgs) < 1) {
                    $app->flashNow("error", "No Organisations found.");
                } else {
                    $app->view()->setData("found_orgs", $found_orgs);
                }
            }
        }        
        $app->render("org-search.tpl");
    }
    
    public function orgEditBadge($org_id, $badge_id)
    {
        $app = Slim::getInstance();
        $badgeDao = new BadgeDao();

        $badge = $badgeDao->getBadge($badge_id);
        $app->view()->setData("badge", $badge);        
        $app->view()->appendData(array("org_id" => $org_id));        
        
        $app->render("org.edit-badge.tpl");        
    }    
}

$route_handler = new OrgRouteHandler();
$route_handler->init();
unset ($route_handler);
