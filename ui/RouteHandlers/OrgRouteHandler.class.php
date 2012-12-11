<?php

class OrgRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();
        $middleware = new Middleware();

        $app->get('/org/create', array($middleware, 'authUserIsLoggedIn'), 
        array($this, 'createOrg'))->via('POST')->name('create-org');

        $app->get('/org/request/:org_id', array($this, 'orgRequestMembership')
        )->name('org-request-membership');

        $app->get('/org/:org_id/request/:user_id/:accept', array($middleware, 'authUserForOrg'), 
        array($this, 'orgProcessRequest'))->name('org-process-request');

        $app->get('/org/request/queue/:org_id', array($middleware, 'authUserForOrg'), 
        array($this, 'orgRequestQueue'))->via("POST")->name('org-request-queue');

        $app->get('/org/private/:org_id', array($middleware, 'authUserForOrg'), 
        array($this, 'orgPrivateProfile'))->via('POST')->name('org-private-profile');

        $app->get('/org/profile/:org_id', array($this, 'orgPublicProfile')
        )->via('POST')->name('org-public-profile');

        $app->get('/org/:org_id/manage/:badge_id/', array($middleware, 'authUserForOrg'), 
        array($this, 'orgManageBadge'))->via("POST")->name('org-manage-badge');

        $app->get('/org/create/badge/:org_id/', array($middleware, 'authUserForOrg'), 
        array($this, 'orgCreateBadge'))->via('POST')->name('org-create-badge');

        $app->get("/org/search", array($this, 'orgSearch')
        )->via('POST')->name('org-search');
        
        $app->get('/org/:org_id/edit/:badge_id', array($middleware, 'authUserForOrg'), 
        array($this, 'orgEditBadge'))->via("POST")->name('org-edit-badge');         
    }

    public function createOrg()
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();

            $org = new Organisation(null);
            if (isset($post->name) && $post->name != null) {
                $org->setName($post->name);
            }

            if (isset($post->home_page) && ($post->home_page != '' || $post->home_page != 'http://')) {
                $org->setHomePage($post->home_page);
            }

            if (isset($post->bio) && $post->bio != '') {
                $org->setBiography($post->bio);
            }

            if ($org->getName() != '') {
 
                $request = APIClient::API_VERSION."/orgs/getByName/{$org->getName()}";
                $organisation = $client->call($request, HTTP_Request2::METHOD_GET);
                  
                if (!$organisation) {
                    $request = APIClient::API_VERSION."/orgs";
                    $response = $client->call($request, HTTP_Request2::METHOD_POST, $org);    
                    $new_org = $client->cast('Organisation', $response);
                    
                    if ($new_org) {
                        $user_id = UserSession::getCurrentUserID();
                        $request = APIClient::API_VERSION."/orgs/{$new_org->getId()}/requests/$user_id";
                        $organisation = $client->call($request, HTTP_Request2::METHOD_PUT);                        
                        $org_name = $org->getName();
                        $app->flashNow('success', "Organisation \"$org_name\" has been created. 
                                            Visit the <a href='".$app->urlFor("client-dashboard").
                                            "'>client dashboard</a> to start uploading tasks.");
                    } else {
                        $app->flashNow('error', "Unable to save Organisation.");
                    }
                } else {
                    $org_name = $org->getName();
                    $app->flashNow('error', "An Organisation named \"$org_name\" is already registered
                                            with SOLAS Match. Please use a different name.");
                }
            } else {
                $app->flashNow('error', "You must specify a name for the organisation.");
            }
        }        
        $app->render('create-org.tpl');
    }

    public function orgRequestMembership($org_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $user_id = UserSession::getCurrentUserID();
        $request = APIClient::API_VERSION."/users/$user_id";
        $response = $client->call($request);
        $user = $client->cast('User', $response);
        
        $request = APIClient::API_VERSION."/users/$user_id/orgs";
        $user_orgs = (array) $client->call($request);
        if (is_null($user_orgs) || !in_array($org_id, $user_orgs)) {
            $request = APIClient::API_VERSION."/orgs/$org_id/requests/$user_id";
            $requestMembership = $client->call($request, HTTP_Request2::METHOD_POST);         
            if ($requestMembership) {
                $app->flash("success", "Successfully requested membership.");
            } else {
                $app->flash("error", "You have already sent a membership request to this Organisation");
            }   
        } else {
            $app->flash("error", "You are already a member of this organisation");
        }   
        $app->redirect($app->urlFor('org-public-profile', array('org_id' => $org_id)));
    }

    public function orgRequestQueue($org_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $request = APIClient::API_VERSION."/orgs/$org_id";
        $response = $client->call($request);
        $org = $client->cast('Organisation', $response);
        
        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();
            
            if (isset($post->email)) {
                if (TemplateHelper::isValidEmail($post->email)) {       
                    $url = APIClient::API_VERSION."/users/getByEmail/{$post->email}";
                    $response = $client->call($url);
                    $user = $client->cast('User', $response);
                
                    if (!is_null($user)) {
                        $user_id = $user->getUserId();
                        $request = APIClient::API_VERSION."/users/$user_id/orgs";
                        $user_orgs = $client->call($request);
                    
                        if ($user->getDisplayName() != '') {
                            $user_name = $user->getDisplayName();
                        } else {
                            $user_name = $user->getEmail();
                        }   
                        if (is_null($user_orgs) || !in_array($org_id, $user_orgs)) {
                            $request = APIClient::API_VERSION."/orgs/$org_id/requests/$user_id";
                            $response = $client->call($request, HTTP_Request2::METHOD_PUT);
                            if ($org->getName() != '') {
                                $org_name = $org->getName();
                            } else {
                                $org_name = "Organisation $org_id";
                            }   
                            $app->flashNow('success', "Successfully added $user_name as a member of $org_name");
                        } else {
                            $app->flashNow('error', "$user_name is already a member of this organisation");
                        }   
                    } else {
                        $email = $post->email;
                        $app->flashNow('error',
                            "The email address $email is not registered with this system.
                            Are you sure you have the right email addess?"
                        );
                    }
                } else {
                    $app->flashNow('error', 'You did not enter a valid email address');
                }
            } elseif (isset($post->accept)) {
                if ($user_id = $post->user_id) {
                    
                    $request = APIClient::API_VERSION."/orgs/$org_id/requests/$user_id";
                    $response = $client->call($request, HTTP_Request2::METHOD_PUT);
                    $request = APIClient::API_VERSION."/users/$user_id";
                    $response = $client->call($request);
                    $user = $client->cast('User', $response);
                } else {
                    $app->flashNow("error", "Invalid User ID: $user_id");
                }
            } elseif (isset($post->refuse)) {
                if ($user_id = $post->user_id) {
                    $request = APIClient::API_VERSION."/orgs/$org_id/requests/$user_id";
                    $response = $client->call($request, HTTP_Request2::METHOD_DELETE);
                    
                    $request = APIClient::API_VERSION."/users/$user_id";
                    $response = $client->call($request);
                    $user = $client->cast('User', $response);
                } else {
                    $app->flashNow("error", "Invalid User ID: $user_id");
                }
            }
        }

        $request = APIClient::API_VERSION."/orgs/$org_id/requests";
        $requests = $client->call($request, HTTP_Request2::METHOD_GET);

        $user_list = array();
        if (count($requests) > 0) {
            foreach ($requests as $request) {
                $memRequest =$client->cast('MembershipRequest', $request);
                $request = APIClient::API_VERSION."/users/{$memRequest->getUserId()}";
                $user = $client->call($request);
                $user_list[] =  $client->cast('User', $user);
            }
        }
        
        $app->view()->setData('org', $org);
        $app->view()->appendData(array('user_list' => $user_list));
        
        $app->render('org.request_queue.tpl');
    }

    public function orgPrivateProfile($org_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $request = APIClient::API_VERSION."/orgs/$org_id";
        $response = $client->call($request);
        $org = $client->cast('Organisation', $response);
        
        if ($app->request()->isPost()) {
            $name = $app->request()->post('name');
            if ($name != null) {
                $org->setName($name);
            }   
            
            $home_page = $app->request()->post('home_page');
            if ($home_page != null) {
                $org->setHomePage($home_page);
            }   
            
            $bio = $app->request()->post('bio');
            if ($bio != null) {
                $org->setBiography($bio);
            }  
            
            $request = APIClient::API_VERSION."/orgs/$org_id";
            $response = $client->call($request, HTTP_Request2::METHOD_PUT, $org); 
            $app->redirect($app->urlFor('org-public-profile', array('org_id' => $org->getId())));
        }   
        
        $app->view()->setData('org', $org);        
        $app->render('org-private-profile.tpl');
    }

    public function orgPublicProfile($org_id)
    {
        $app = Slim::getInstance();
        $client = new APICLient();

        $request = APIClient::API_VERSION."/orgs/$org_id";
        $response = $client->call($request);
        $org = $client->cast('Organisation', $response);
        
        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();
                   
            if (isset($post->deleteBadge)) {
                $badge_id = $post->badge_id;
                $request = APIClient::API_VERSION."/badges/$badge_id";
                $response = $client->call($request, HTTP_Request2::METHOD_DELETE);
            } 
            
            if (isset($post->title) && isset($post->description)) {
                
                if ($post->title == '' || $post->description == '') {
                    $app->flash('error', "All fields must be filled out");
                } else {
                    $params = array();
                    $params['badge_id'] = $post->badge_id;             
                    $params['title'] = $post->title;
                    $params['description'] = $post->description;
                    $params['owner_id'] = null; 

                    $updatedBadge = ModelFactory::buildModel("Badge", $params);
                    $request = APIClient::API_VERSION."/badges/{$post->badge_id}";
                    $response = $client->call($request, HTTP_Request2::METHOD_PUT, $updatedBadge); 
                    $app->redirect($app->urlFor('org-public-profile', array('org_id' => $org_id)));
                }
            }
        }       
      
        $request = APIClient::API_VERSION."/orgs/$org_id/badges";
        $badgeList = $client->call($request);
     
        $org_badges = array();
        $request = APIClient::API_VERSION."/orgs/$org_id/badges";
        $response = $client->call($request);
        if ($response) {
            foreach ($response as $stdObject) {
                $org_badges[] = $client->cast('Badge', $stdObject);
            }        
        }

        $request = APIClient::API_VERSION."/orgs/$org_id/members";
        $orgMemberList = $client->call($request);
        
        $org_members = array();
        if (count($orgMemberList) > 0) {
            $i = 0;
            foreach ($orgMemberList as $stdObject) {
                $org_members[] = $orgMemberList[$i]->user_id;
                $i++;
            }
        }        

        $app->view()->setData('current_page', 'org-public-profile');
        $app->view()->appendData(array('org' => $org,
                'org_members' => $org_members,
                'org_badges' => $org_badges
        ));
        
        $app->render('org-public-profile.tpl');
    }

    public function orgManageBadge($org_id, $badge_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $request = APIClient::API_VERSION."/badges/$badge_id";
        $response = $client->call($request);
        $badge = $client->cast('Badge', $response);

        $user_list = array();
        $request = APIClient::API_VERSION."/badges/$badge_id/users";
        $response = $client->call($request);
        if ($response) {
            foreach ($response as $stdObject) {
                $user_list[] = $client->cast('User', $stdObject);
            }
        }

        $extra_scripts = "<script type=\"text/javascript\" src=\"".$app->urlFor("home");
        $extra_scripts .= "resources/bootstrap/js/confirm-remove-badge.js\"></script>";
        
        $app->view()->setData('badge', $badge);
        $app->view()->appendData(array(
                    'org_id'        => $org_id,
                    'extra_scripts' =>$extra_scripts
        ));

        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();
            
            if (isset($post->email) && $post->email != '') {
                if (TemplateHelper::isValidEmail($post->email)) {
                    
                    $request = APIClient::API_VERSION."/users/getByEmail/{$post->email}";
                    $response = $client->call($request, HTTP_Request2::METHOD_GET);
                    
                    if (!is_null($response)) {
                        $user = $client->cast('User', $response);
                        $user_badges = array();
                        $user_id = $user->getUserId();
                        $request = APIClient::API_VERSION."/users/$user_id/badges";
                        $response = $client->call($request);
                        foreach ($response as $badge_data) {
                            $user_badges[] = $client->cast('Badge', $badge_data);                           
                        }
                        $badge_ids = array();
                        if (count($user_badges) > 0) {
                            foreach ($user_badges as $badge_tmp) {
                                $badge_ids[] = $badge_tmp->getId();
                            }
                        }
                        
                        if (!in_array($badge_id, $badge_ids)) {
                            $request = APIClient::API_VERSION."/users/$user_id/badges";
                            $response = $client->call($request, HTTP_Request2::METHOD_POST, $badge);
                            
                            $user_name = '';
                            if ($user->getDisplayName() != '') {
                                $user_name = $user->getDisplayName();
                            } else {
                                $user_name = $user->getEmail();
                            }
                            
                            $app->flashNow('success', "Successfully Assigned Badge \"".
                                            $badge->getTitle()."\" to user $user_name");
                        } else {
                            $app->flashNow('error', 'The user '.$post->email.' already has that badge');
                        }
                    } else {
                        $app->flashNow('error',
                            'The email address '.$post->email.' is not registered on the system. 
                            Are you using the correct email address?'
                        );
                    }
                } else {
                    $app->flashNow('error', "You did not enter a valid email address");
                }
            } elseif (isset($post->user_id) && $post->user_id != '') {
                $user_id = $post->user_id;
                $request = APIClient::API_VERSION."/users/$user_id";
                $response = $client->call($request);
                $user = $client->cast('User', $response);
                
                $request = APIClient::API_VERSION."/users/".$user_id."/badges/$badge_id";
                $response = $client->call($request, HTTP_Request2::METHOD_DELETE);
                
                $user_name = '';
                if ($user->getDisplayName() != '') {
                    $user_name = $user->getDisplayName();
                } else {
                    $user_name = $user->getEmail();
                }
                $app->flashNow('success', "Successfully removed badge form user $user_name");
            } else {
                $app->flashNow('error', "Incorrect POST data");
            }
        }
    
        $user_list = array();
        $request = APIClient::API_VERSION."/badges/{$badge->getId()}/users";
        $response = $client->call($request);        
        if ($response) {
            foreach ($response as $stdObject) {
                $user_list[] = $client->cast('User', $stdObject);
            }
        }

        $app->view()->appendData(array(
            'user_list' => $user_list
        ));
        
        $app->render('org.manage-badge.tpl');
    }

    public function orgCreateBadge($org_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        if (isValidPost($app)) {
            $post = (object) $app->request()->post();
            
            if ($post->title == '' || $post->description == '') {
                $app->flash('error', "All fields must be filled out");
            } else {
                $params = array();
                $params['title'] = $post->title;
                $params['description'] = $post->description;
                $params['owner_id'] = $org_id;

                $badge = ModelFactory::buildModel("Badge", $params);
                $request = APIClient::API_VERSION."/badges/{$badge->getId()}";
                $response = $client->call($request, HTTP_Request2::METHOD_PUT, $badge);                
                
                $app->redirect($app->urlFor('org-public-profile', array('org_id' => $org_id)));
            }
        }
        
        $app->view()->setData('org_id', $org_id);        
        $app->render('org.create-badge.tpl');
    }

    public function orgSearch()
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();
            
            if (isset($post->search_name) && $post->search_name != '') {                
                $found_orgs = array();
                $request = APIClient::API_VERSION."/orgs/getByName/{$post->search_name}";
                $response = $client->call($request);
                if ($response) {
                    foreach ($response as $stdObject) {
                        $found_orgs[] = $client->cast('Organisation', $stdObject);
                    }
                }                
                
                if (count($found_orgs) < 1) {
                    $app->flashNow('error', 'No Organisations found');
                } else {
                    $app->view()->setData('found_orgs', $found_orgs);
                }
            }
        }        
        $app->render('org-search.tpl');
    }
    
    public function orgEditBadge($org_id, $badge_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();        

        $request = APIClient::API_VERSION."/badges/$badge_id";
        $response = $client->call($request);
        $badge = $client->cast('Badge', $response);

        $app->view()->setData('badge', $badge);        
        $app->view()->appendData(array('org_id' => $org_id));        
        
        $app->render('org.edit-badge.tpl');        
    }    
}