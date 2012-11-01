<?php


class UserRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();
        $middleware = new Middleware();

        $app->get('/', array($this, 'home'))->name('home');

        $app->get('/client/dashboard', array($middleware, 'authUserIsLoggedIn'), 
        array($this, 'clientDashboard'))->via('POST')->name('client-dashboard');

        $app->get('/register', array($this, 'register')
        )->via('GET', 'POST')->name('register');

        $app->get('/:uid/password/reset', array($this, 'passwordReset')
        )->via('POST')->name('password-reset');

        $app->get('/password/reset', array($this, 'passResetRequest')
        )->via('POST')->name('password-reset-request');
        
        $app->get('/logout', array($this, 'logout'))->name('logout');
        
        $app->get('/login', array($this, 'login')
        )->via('GET','POST')->name('login');

        $app->get('/profile/:user_id', array($this, 'userPublicProfile')
        )->via('POST')->name('user-public-profile');

        $app->get('/profile', array($middleware, 'authUserIsLoggedIn'), 
        array($this, 'userPrivateProfile'))->via('POST')->name('user-private-profile');
    }

    public function home()
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $app->view()->appendData(array(
            'top_tags' => TagsDao::getTopTags(30), //TODO use getTopTags api funchtion /v0/tags/topTags  optional limit=x
            'current_page' => 'home'
        ));

        $current_user_id = UserSession::getCurrentUserID();
        
        if($current_user_id == null) {
            $tasks = TaskStream::getStream(10);

            if($tasks) {
                $app->view()->appendData(array('tasks' => $tasks));
            }
        } else {
            $url = APIClient::API_VERSION."/users/$current_user_id/top_tasks";
            $response = $client->call($url, HTTP_Request2::METHOD_GET, null,
                                    array('limit' => 10));
            
            $tasks = array();
            if($response) {
                foreach($response as $stdObject) {
                    $tasks[] = $client->cast('Task', $stdObject);
                }
            }

            if($tasks) {
                $app->view()->setData('tasks', $tasks);
            }

            $url = APIClient::API_VERSION."/users/$current_user_id/tags";
            $response = $client->call($url);
            
            $user_tags = array();
            if($response) {
                foreach($response as $stdObject) {
                    $user_tags[] = $client->cast('Tag', $stdObject);
                }
            }
            
            $app->view()->appendData(array(
                        'user_tags' => $user_tags
            ));
        }
        
        $app->render('index.tpl');
    }

    public function clientDashboard()
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $user_dao           = new UserDao();
        $task_dao           = new TaskDao;
        $org_dao            = new OrganisationDao();
        $current_user_id    = UserSession::getCurrentUserID();
        $current_user       = $user_dao->getCurrentUser();
        if (is_null($current_user_id)) {
            $app->flash('error', 'Login required to access page');
            $app->redirect($app->urlFor('login'));
        }

        $url = APIClient::API_VERSION."/users/$current_user_id/orgs";
        $my_organisations = (array)$client->call($url);
        
        $org_tasks = array();
        $orgs = array();
        foreach($my_organisations as $org_id) {
            $url = APIClient::API_VERSION."/orgs/$org_id";
            $org_data = $client->call($url);
            $org = $client->cast('Organisation', $org_data);

//          Wait for this to be exposed by API
//            $url = APIClient::API_VERSION."/
            $my_org_tasks = $task_dao->findTasksByOrg(array('organisation_ids' => $org_id));
            $org_tasks[$org->getId()] = $my_org_tasks;
            $orgs[$org->getId()] = $org;
        }    
        
        if($app->request()->isPost()) {
            $post = (object) $app->request()->post();
            
            if(isset($post->track)) {
                $task_id = $post->task_id;
                $url = APICLient::API_VERSION."/tasks/$task_id";
                $response = $client->call($url);
                $task = $client->cast('Task', $response);

                $task_title = '';
                if($task->getTitle() != '') {
                    $task_title = $task->getTitle();
                } else {
                    $task_title = "task ".$task->getTaskId();
                }
                if($post->track == "Ignore") {
                    //Not currently exposed by API
                    if($user_dao->ignoreTask($current_user->getUserId(), $post->task_id)) {
                        $app->flashNow('success', 'No longer receiving notifications from '.$task_title.'.');
                    } else {
                        $app->flashNow('error', 'Unable to unsubscribe from '.$task_title.'\'s notifications');
                    }
                } elseif($post->track == "Track") {
                    //Not currently supported by API
                    if($user_dao->trackTask($current_user->getUserId(), $post->task_id)) {
                        $app->flashNow('success', 'You will now receive notifications for '.$task_title.'.');
                    } else {
                        $app->flashNow('error', 'Unable to subscribe to '.$task_title.'.');
                    }
                } else {
                    $app->flashNow('error', 'Invalid POST type');
                }
            }
        }
        if(count($org_tasks) > 0) {
            $app->view()->appendData(array(
                'org_tasks' => $org_tasks,
                'orgs' => $orgs,
                'task_dao' => $task_dao,
                'user_dao' => $user_dao
            ));
        }
        
        $app->view()->appendData(array(
            'current_page'  => 'client-dashboard'
        ));
        $app->render('client.dashboard.tpl');
    }

    public function register()
    {
        $app = Slim::getInstance();
        $client = new APIClient();
        
        $tempSettings=new Settings();
        $use_openid = $tempSettings->get("site.openid");
        $app->view()->setData('openid',$use_openid);
        if(isset($use_openid)) {
            if($use_openid == 'y' || $use_openid == 'h') {
                $extra_scripts = "
                    <script type=\"text/javascript\" src=\"".$app->urlFor("home")."resources/bootstrap/js/jquery-1.2.6.min.js\"></script>
                    <script type=\"text/javascript\" src=\"".$app->urlFor("home")."resources/bootstrap/js/openid-jquery.js\"></script>
                    <script type=\"text/javascript\" src=\"".$app->urlFor("home")."resources/bootstrap/js/openid-en.js\"></script>
                    <link type=\"text/css\" rel=\"stylesheet\" media=\"all\" href=\"".$app->urlFor("home")."resources/css/openid.css\" />";
                $app->view()->appendData(array('extra_scripts' => $extra_scripts));
            }   
        }   
        $error = null;
        $warning = null;
        if (isValidPost($app)) {
            $post = (object)$app->request()->post();
            $user_dao = new UserDao();
            if (!User::isValidEmail($post->email)) {
                $error = 'The email address you entered was not valid. Please cheak for typos and try again.';
            } else if (!User::isValidPassword($post->password)) {
                $error = 'You didn\'t enter a password. Please try again.';
            } else if (is_object($user_dao->find(array('email' => $post->email)))) {    //wait for API support
                $warning = 'You have already created an account. <a href="' . $app->urlFor('login') . '">Please log in.</a>';
            }
            
            if (is_null($error) && is_null($warning)) {
                if ($user = $user_dao->create($post->email, $post->password)) {     //wait for API support
                    if ($user_dao->login($user->getEmail(), $post->password)) {     //wait for API support
                        
                        $badge_dao = new BadgeDao();
                        $badge_id = Badge::REGISTERED;
                        $url = APIClient::API_VERSION."/badges/$badge_id";
                        $response = $client->call($url);
                        $badge = $client->cast('Badge', $response);
                        $badge_dao->assignBadge($user, $badge);     //wait for API support || put in create function
                        
                        if(isset($_SESSION['previous_page'])) {
                            if(isset($_SESSION['old_page_vars'])) {
                                $app->redirect($app->urlFor($_SESSION['previous_page'], $_SESSION['old_page_vars']));
                            } else {
                                $app->redirect($app->urlFor($_SESSION['previous_page']));
                            }
                        }
                        $app->redirect($app->urlFor('home'));
                    } else {
                        $error = 'Tried to log you in immediately, but was unable to.';
                    }
                } else {
                    $error = 'Unable to register.';
                }
            }
        }
        if ($error !== null) {
            $app->view()->appendData(array('error' => $error));
        }
        if ($warning !== null) {
            $app->view()->appendData(array('warning' => $warning));
        }
        $app->render('register.tpl');
    }

    public function passwordReset($uid)
    {
        $app = Slim::getInstance();

        $user_dao = new UserDao();
        $reset_request = $user_dao->getPasswordResetRequests(array('uid' => $uid));     //wait for API support

        if(!isset($reset_request['user_id']) || $reset_request['user_id'] == '') {
            $app->flash('error', "Incorrect Unique ID. Are you sure you copied the URL correctly?");
            $app->redirect($app->urlFor('home'));
        }
        
        $user_id = $reset_request['user_id'];

        if($app->request()->isPost()) {
            $post = (object) $app->request()->post();

            if(isset($post->new_password) && User::isValidPassword($post->new_password)) {
                if(isset($post->confirmation_password) && 
                        $post->confirmation_password == $post->new_password) {
                    if($user_dao->changePassword($user_id, $post->new_password)) {
                        $user_dao->removePasswordResetRequest($user_id);
                        $app->flash('success', "You have successfully changed your password");
                        $app->redirect($app->urlFor('home'));
                    } else {
                        $app->flashNow('error', "Unable to change Password");
                    }
                } else {
                    $app->flashNow('error', "The passwords entered do not match.
                                        Please try again.");
                }
            } else {
                $app->flashNow('error', "Please check the password provided, and try again. It was not found to be valid.");
            }
        }

        $app->view()->setData('uid', $uid);
        $app->render('password-reset.tpl');
    }

    public function passResetRequest()
    {
        $app = Slim::getInstance();

        $user_dao = new UserDao();

        if($app->request()->isPost()) {
            $post = (object)$app->request()->post();
            if(isset($post->password_reset)) {
                if(isset($post->email_address) && $post->email_address != '')       //wait for API support
                {
                    if($user = $user_dao->find(array('email' => $post->email_address))) {
                        if(!$user_dao->hasRequestedPasswordReset($user)) {          //wait for API support
                            $uid = md5(uniqid(rand()));
                            $user_dao->addPasswordResetRequest($uid, $user->getUserId());   //wait for API support
                            Notify::sendPasswordResetEmail($uid, $user);
                            $app->flash('success', "Password reset request sent. Check your email
                                                    for further instructions.");
                            $app->redirect($app->urlFor('home'));
                        } else {
                            $app->flashNow('info', "Password reset request has already been sent.
                                                     Follow the link in the email that was sent to
                                                     you to reset your password");
                        }
                    } else {
                        $app->flashNow("error", "Please enter a valid email address");
                    }
                } else {
                    $app->flashNow("error", "Please enter a valid email address");
                }
            }
        }
        $app->render('user.reset-password.tpl');
    }
    
    public function logout()
    {
        $app = Slim::getInstance();
        UserSession::destroySession();    //TODO revisit when oauth is in place
        $app->redirect($app->urlFor('home'));
    }

    public function login()
    {
        $app = Slim::getInstance();
        
        $error = null;
        $tempSettings=new Settings();
        $openid = new LightOpenID($tempSettings->get("site.url"));
        $use_openid = $tempSettings->get("site.openid");
        $app->view()->setData('openid', $use_openid);
        if(isset($use_openid)) {
            if($use_openid == 'y' || $use_openid == 'h') {
                $extra_scripts = "
                    <script type=\"text/javascript\" src=\"".$app->urlFor("home")."resources/bootstrap/js/jquery-1.2.6.min.js\"></script>
                    <script type=\"text/javascript\" src=\"".$app->urlFor("home")."resources/bootstrap/js/openid-jquery.js\"></script>
                    <script type=\"text/javascript\" src=\"".$app->urlFor("home")."resources/bootstrap/js/openid-en.js\"></script>
                    <link type=\"text/css\" rel=\"stylesheet\" media=\"all\" href=\"".$app->urlFor("home")."resources/css/openid.css\" />";
                $app->view()->appendData(array('extra_scripts' => $extra_scripts));
            }
        }
        
        try {
            $user_dao = new UserDao();
            if (isValidPost($app)){
                $post = (object)$app->request()->post();

                if(isset($post->login)) {
                    $user_dao->login($post->email, $post->password);        //wait for API support
                    $app->redirect($app->urlFor("home"));
                } elseif(isset($post->password_reset)) {
                    $app->redirect($app->urlFor('password-reset-request'));
                }
            } elseif($app->request()->isPost()||$openid->mode){
                $user_dao->OpenIDLogin($openid,$app);       //wait for API support
                $app->redirect($app->urlFor("home"));
            }
            $app->render('login.tpl');
        } catch (InvalidArgumentException $e) {
            $error = '<p>Unable to log in. Please check your email and password.';
            $error .= ' <a href="' . $app->urlFor('login') . '">Try logging in again</a>';
            $error .= ' or <a href="'.$app->urlFor('register').'">register</a> for an account.</p>';
            $error .= '<p>System error: <em>' . $e->getMessage() .'</em></p>';
            
            $app->flash('error', $error);
            $app->redirect($app->urlFor('login'));
            echo $error;
        }
    }

    public static function userPrivateProfile()
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $user_dao = new UserDao();

        $user_id = UserSession::getCurrentUserID();
        $url = APIClient::API_VERSION."/users/$user_id";
        $response = $client->call($url);
        $user = $client->cast('User', $response);
        $languages = Languages::getLanguageList();      //wait for API support
        $countries = Languages::getCountryList();       //wait for API support
        
        if (!is_object($user)) {
            $app->flash('error', 'Login required to access page');
            $app->redirect($app->urlFor('login'));
        }
        
        if($app->request()->isPost()) {
            $displayName = $app->request()->post('name');
            if($displayName != NULL) {
                $user->setDisplayName($displayName);
            }
            
            $userBio = $app->request()->post('bio');
            if($userBio != NULL) {
                $user->setBiography($userBio);
            }
            
            $nativeLang = $app->request()->post('nLanguage');
            $langCountry= $app->request()->post('nLanguageCountry');
            if($nativeLang != NULL&&$langCountry!= NULL) {
                $user->setNativeLanguageID($nativeLang);
                $user->setNativeRegionID($langCountry);
                //assign a badge
                $badge_dao = new BadgeDao();
                $badge_id = Badge::NATIVE_LANGUAGE;
                $url = APIClient::API_VERSION."/badges/$badge_id";
                $response = $client->call($url);
                $badge = $client->cast('Badge', $response);
                $badge_dao->assignBadge($user, $badge);     //wait for API support || move to back end
            }
            $user_dao->save($user);
            
            if($user->getDisplayName() != '' && $user->getBiography() != ''
                    && $user->getNativeLanguageID() != '' && $user->getNativeRegionID() != '') {
                $badge_dao = new BadgeDao();
                $badge_id = Badge::PROFILE_FILLER;
                $url = APIClient::API_VERSION."/badges/$badge_id";
                $response = $client->call($url);
                $badge = $client->cast('Badge', $response);
                $badge_dao->assignBadge($user, $badge);
            }
            
            $app->redirect($app->urlFor('user-public-profile', array('user_id' => $user->getUserId())));
        }
        
        $app->view()->setData('languages',$languages);
        $app->view()->setData('countries',$countries);
        
       
        $app->render('user-private-profile.tpl');
    }

    public static function userPublicProfile($user_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $badge_dao = new BadgeDao();
        $user_dao = new UserDao();

        $url = APIClient::API_VERSION."/users/$user_id";
        $response = $client->call($url);
        $user = $client->cast('User', $response);
        
        if($app->request()->isPost()) {
            $post = (object) $app->request()->post();
            
            if(isset($post->badge_id) && $post->badge_id != '') {
                $badge_id = $post->badge_id;
                $url = APIClient::API_VERSION."/badges/$badge_id";
                $response = $client->call($url);
                $badge = $client->cast('Badge', $response);
                $badge_dao->removeUserBadge($user, $badge);     //wait for API support
            }
                
            if(isset($post->revoke)) {
                $org_id = $post->org_id;
                OrganisationDao::revokeMembership($org_id, $user_id);   //wait for API support
            } 
        }
                    
        $task_dao = new TaskDao();
        $activeJobs = array();
        $user_id = $user->getUserId();
        $request = APIClient::API_VERSION."/users/$user_id/tasks";
        $response = $client->call($request);
        
        if($response) {
            foreach($response as $stdObject) {
                $activeJobs[] = $client->cast('Task', $stdObject);
            }
        }

        $archivedJobs = $task_dao->getUserArchivedTasks($user, 10);     //wait for API support
         
        $user_tags = array();
        $request = APIClient::API_VERSION."/users/$user_id/tags";
        $response = $client->call($request);
        
        if($response) {
            foreach($response as $stdObject) {
                $user_tags[] = $client->cast('Tag', $stdObject);
            }
        }
            
        $org_dao = new OrganisationDao();
                   
        $request = APIClient::API_VERSION."/users/$user_id/orgs";
        $orgIds = $client->call($request);        
        
        $orgList = array();
        if(count($orgIds) > 0) {
            foreach ($orgIds as $orgId) {
                $request = APIClient::API_VERSION."/orgs/$orgId";
                $response = $client->call($request);
                $orgList[] = $client->cast('Organisation', $response);
            }
        }
        
        $badgeIds = $user_dao->getUserBadges($user);
        $badges = array();
        if(count($badgeIds) > 0) {
            foreach($badgeIds as $badge) {
                $request = APIClient::API_VERSION."/badges/".$badge['badge_id'];
                $response = $client->call($request);
                $badges[] = $client->cast('Badge', $response);
            }
        }
            
        $extra_scripts = "<script type=\"text/javascript\" src=\"".$app->urlFor("home");
        $extra_scripts .= "resources/bootstrap/js/confirm-remove-badge.js\"></script>";
              
        $app->view()->setData('orgList',  $orgList);
        $app->view()->appendData(array('badges' => $badges,
                                    'current_page' => 'user-profile',
                                    'activeJobs' => $activeJobs,
                                    'archivedJobs' => $archivedJobs,
                                    'user_tags' => $user_tags,
                                    'this_user' => $user,
                                    'extra_scripts' => $extra_scripts
        ));
                
        if(UserSession::getCurrentUserID() === $user_id) {
            $app->view()->appendData(array('private_access' => true));
        }
                    
        $app->render('user-public-profile.tpl');
    }
}
