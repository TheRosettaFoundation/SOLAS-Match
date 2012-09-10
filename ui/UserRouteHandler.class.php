<?php

class UserRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();

        $app->get('/', array($this, 'home'))->name('home');

        $app->get('/client/dashboard', array($this, 'clientDashboard')
        )->via('POST')->name('client-dashboard');

        $app->get('/register', array($this, 'register')
        )->via('GET', 'POST')->name('register');
        
        $app->get('/logout', array($this, 'logout'))->name('logout');
        
        $app->get('/login', array($this, 'login')
        )->via('GET','POST')->name('login');

        $app->get('/profile/:user_id', array($this, 'userPublicProfile')
        )->via('POST')->name('user-public-profile');

        $app->get('/profile', array($this, 'userPrivateProfile')
        )->via('POST')->name('user-private-profile');
    }

    public function home()
    {
        $app = Slim::getInstance();

        $app->view()->appendData(array(
            'top_tags' => TagsDao::getTopTags(30),
            'current_page' => 'home'
        ));
        
        $user_dao = new UserDao();
        $current_user = $user_dao->getCurrentUser();
        if($current_user == null) {
            $_SESSION['previous_page'] = 'home';
            
            if($tasks = TaskStream::getStream(10)) {
                $app->view()->setData('tasks', $tasks);
            }
        } else {
            if($tasks = TaskStream::getUserStream($current_user->getUserId(), 10)) {
                $app->view()->setData('tasks', $tasks);
            }
            
            $user_tags = $user_dao->getUserTags($current_user->getUserId());
            
            $app->view()->appendData(array(
                        'user_tags' => $user_tags
            ));
        }
        
        $app->render('index.tpl');
    }

    public function clientDashboard()
    {
        $app = Slim::getInstance();

        $user_dao           = new UserDao();
        $task_dao           = new TaskDao;
        $org_dao            = new OrganisationDao();
        $current_user       = $user_dao->getCurrentUser();
        if (!is_object($current_user)) {
            $app->flash('error', 'Login required to access page');
            $app->redirect($app->urlFor('login'));
        }
        $my_organisations   = $user_dao->findOrganisationsUserBelongsTo($current_user->getUserId());
        
        $org_tasks = array();
        $orgs = array();
        foreach($my_organisations as $org_id) {
            $org = $org_dao->find(array('id' => $org_id));
            $my_org_tasks = $task_dao->findTasksByOrg(array('organisation_ids' => $org_id));
            $org_tasks[$org->getId()] = $my_org_tasks;
            $orgs[$org->getId()] = $org;
        }
        
        if($app->request()->isPost()) {
            $post = (object) $app->request()->post();
            
            if(isset($post->track)) {
                $task = $task_dao->find(array('task_id' => $post->task_id));
                $task_title = '';
                if($task->getTitle() != '') {
                    $task_title = $task->getTitle();
                } else {
                    $task_title = "task ".$task->getTaskId();
                }
                if($post->track == "Ignore") {
                    if($user_dao->ignoreTask($current_user->getUserId(), $post->task_id)) {
                        $app->flashNow('success', 'No longer receiving notifications from '.$task_title.'.');
                    } else {
                        $app->flashNow('error', 'Unable to unsubscribe from '.$task_title.'\'s notifications');
                    }
                } elseif($post->track == "Track") {
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
            } else if (is_object($user_dao->find(array('email' => $post->email)))) {
                $warning = 'You have already created an account. <a href="' . $app->urlFor('login') . '">Please log in.</a>';
            }
            
            if (is_null($error) && is_null($warning)) {
                if ($user = $user_dao->create($post->email, $post->password)) {
                    if ($user_dao->login($user->getEmail(), $post->password)) {
                        
                        $badge_dao = new BadgeDao();
                        $badge = $badge_dao->find(array('badge_id' => Badge::REGISTERED));
                        $badge_dao->assignBadge($user, $badge);
                        
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
    
    public function logout()
    {
        $app = Slim::getInstance();
        
        $user_dao = new UserDao();
        $user_dao->logout();
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
                $user_dao->login($post->email, $post->password);
            } elseif($app->request()->isPost()||$openid->mode){
                $user_dao->OpenIDLogin($openid,$app);
            } else{
                $app->render('login.tpl');
                return;
            }
            $app->redirect($app->urlFor("home"));
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

        $user_dao = new UserDao();
        $user = $user_dao->getCurrentUser();
        $languages = Languages::getLanguageList();
        $countries = Languages::getCountryList();
        
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
                $badge = $badge_dao->find(array('badge_id' => Badge::NATIVE_LANGUAGE));
                $badge_dao->assignBadge($user, $badge);
            }
            $user_dao->save($user);
            
            if($user->getDisplayName() != '' && $user->getBiography() != ''
                    && $user->getNativeLanguageID() != '' && $user->getNativeRegionID() != '') {
                $badge_dao = new BadgeDao();
                $badge = $badge_dao->find(array('badge_id' => Badge::PROFILE_FILLER));
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
        $badge_dao = new BadgeDao();
        $user_dao = new UserDao();
        $user = $user_dao->find(array('user_id' => $user_id));
        
        if($app->request()->isPost()) {
            $post = (object) $app->request()->post();
            
            if(isset($post->badge_id) && $post->badge_id != '') {
                $badge = $badge_dao->find(array('badge_id' => $post->badge_id));
                $badge_dao->removeUserBadge($user, $badge);
            }
                
            if(isset($post->revoke)) {
                $org_id = $post->org_id;
                OrganisationDao::revokeMembership($org_id, $user_id);
            }
        }
                    
        $task_dao = new TaskDao();
        $activeJobs = $task_dao->getUserTasks($user, 10);

        $archivedJobs = $task_dao->getUserArchivedTasks($user, 10);
                   
        $user_tags = $user_dao->getUserTags($user->getUserId());
                    
        $org_dao = new OrganisationDao();
                    
        $orgIds = $user_dao->findOrganisationsUserBelongsTo($user->getUserId());
        $orgList = array();
                    
        if(count($orgIds) > 0) {
            foreach ($orgIds as $orgId) {
                $orgList[] = $org_dao->find(array('id' => $orgId));
            }
        }
                            
        $badgeIds = $user_dao->getUserBadges($user);
        $badges = array();
        $i = 0;
        if(count($badgeIds) > 0) {
            foreach($badgeIds as $badge) {
                $badges[$i] = $badge_dao->find(array('badge_id' => $badge['badge_id']));
                $i++;
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
                
        if($user_dao->getCurrentUser()->getUserId() === $user_id) {
            $app->view()->appendData(array('private_access' => true));
        }
                    
        $app->render('user-public-profile.tpl');
    }
}
