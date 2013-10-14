<?php


require_once __DIR__."/../DataAccessObjects/UserDao.class.php";
require_once __DIR__."/../../Common/models/Register.php";
require_once __DIR__."/../../Common/models/Login.php";
require_once __DIR__."/../../Common/models/PasswordResetRequest.php";
require_once __DIR__."/../../Common/models/PasswordReset.php";
require_once __DIR__."/../../Common/models/Locale.php";

class UserRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();
        $middleware = new Middleware();

        $app->get("/", array($this, "home"))->via("POST")->name("home");

        $app->get("/register/", array($this, "register")
        )->via("GET", "POST")->name("register");

        $app->get("/user/:uuid/verification/", array($this, 'emailVerification')
        )->via('POST')->name('email-verification');

        $app->get("/:uid/password/reset/", array($this, "passwordReset")
        )->via("POST")->name("password-reset");

        $app->get("/password/reset/", array($this, "passResetRequest")
        )->via("POST")->name("password-reset-request");
        
        $app->get("/logout/", array($this, "logout"))->name("logout");
        
        $app->get("/login/", array($this, "login")
        )->via("GET", "POST")->name("login");

        $app->get("/:user_id/profile/", array($this, "userPublicProfile")
        )->via("POST")->name("user-public-profile");

        $app->get("/:user_id/privateProfile/", array($middleware, "authUserIsLoggedIn"), 
        array($this, "userPrivateProfile"))->via("POST")->name("user-private-profile");

        $app->get("/:user_id/notification/stream/", array($middleware, "authUserIsLoggedIn"),
        array($this, "editTaskStreamNotification"))->via("POST")->name("stream-notification-edit");
  
        $app->get("/user/task/:task_id/reviews/", array($middleware, "authenticateUserForTask"),
        array($this, "userTaskReviews"))->name("user-task-reviews");
    }
    
    public function home()
    {
        $app = Slim::getInstance();
        $langDao = new LanguageDao();
        $tagDao = new TagDao();
        $taskDao = new TaskDao();
        $projectDao = new ProjectDao();
        $orgDao = new OrganisationDao();
        $userDao = new UserDao(); 

        $use_statistics = Settings::get("site.stats"); 
        if ($use_statistics == 'y') {
            $statsDao = new StatisticsDao();
            $statistics = $statsDao->getStats();    
            $statsArray = null;
            if($statistics) {
                $statsArray = array();
                foreach($statistics as $stat) {
                    $statsArray[$stat->getName()] = $stat;
                }
            }

            $app->view()->appendData(array(
                "statsArray" => $statsArray
            ));
        }
        
        $top_tags = $tagDao->getTopTags(10);        
        $app->view()->appendData(array(
            "top_tags" => $top_tags,
            "current_page" => "home",
        ));

        $current_user_id = UserSession::getCurrentUserID();
        
        if ($current_user_id != null) {
            $user_tags = $userDao->getUserTags($current_user_id);
            $app->view()->appendData(array(
                        "user_tags" => $user_tags
            ));
        }
		
		// Added check to display info message to users on IE borwsers
		$browserData = get_browser(null, true);
		if (!is_null($browserData) && isset($browserData['browser'])) {
			$browser = $browserData['browser'];
			
			if ($browser == 'IE') {
                $app->flashNow("info", Localisation::getTranslation(Strings::INDEX_8).Localisation::getTranslation(Strings::INDEX_9));
        	}
			
		}

		
		$app->render("index.tpl");
    }

    public function videos()
    {
        $app = Slim::getInstance();
        $app->view()->appendData(array('current_page' => 'videos'));
        $app->render("videos.tpl");
    }

    public function register()
    {
        $app = Slim::getInstance();
        $userDao = new UserDao();
        
        $use_openid = Settings::get("site.openid");
        $app->view()->setData("openid", $use_openid);
        if (isset($use_openid)) {
            if ($use_openid == "y" || $use_openid == "h") {
                $extra_scripts = "
                    <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/lib/openid-jquery.js\"></script>
                    <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/lib/openid-en.js\"></script>
                    <link type=\"text/css\" rel=\"stylesheet\" media=\"all\" href=\"{$app->urlFor("home")}resources/css/openid.css\" />";
                $app->view()->appendData(array("extra_scripts" => $extra_scripts));
            }   
        }   
        $error = null;
        $warning = null;
        if (isValidPost($app)) {
            $post = $app->request()->post();
            $temp = md5($post['email'].substr(Settings::get("session.site_key"),0,20));
            UserSession::clearCurrentUserID();
            if (!TemplateHelper::isValidEmail($post['email'])) {
                $error = Localisation::getTranslation(Strings::USER_ROUTEHANDLER_1);
            } elseif (!TemplateHelper::isValidPassword($post['password'])) {
                $error = Localisation::getTranslation(Strings::USER_ROUTEHANDLER_2);
            } elseif ($user = $userDao->getUserByEmail($post['email'], $temp)) {
                if ($return = $userDao->isUserVerified($user->getId())) {
                    $error = sprintf(Localisation::getTranslation(Strings::USER_ROUTEHANDLER_3), $app->urlFor("login"));
                }
            }
            
            if (is_null($error)) {
                $userDao->register($post['email'], $post['password']);
                $app->flashNow("success", sprintf(Localisation::getTranslation(Strings::USER_ROUTEHANDLER_4), $app->urlFor("login")));
            }
        }
        if ($error !== null) {
            $app->view()->appendData(array("error" => $error));
        }
        if ($warning !== null) {
            $app->view()->appendData(array("warning" => $warning));
        }
        $app->render("user/register.tpl");
    }

    public function emailVerification($uuid)
    {
        $app = Slim::getInstance();
        $userDao = new UserDao();

        $user = $userDao->getRegisteredUser($uuid);

        if (is_null($user)) {
            $app->flash("error", Localisation::getTranslation(Strings::USER_ROUTEHANDLER_5));
            $app->redirect($app->urlFor("home"));
        }

        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            if (isset($post['verify'])) {
                $userDao->finishRegistration($uuid);
                UserSession::setSession($user->getId());
                $app->flash("success", Localisation::getTranslation(Strings::USER_ROUTEHANDLER_6));
                $app->redirect($app->urlFor("user-public-profile", array('user_id' => $user->getId())));
            }
        }

        $app->view()->appendData(array('uuid' => $uuid));

        $app->render("user/email.verification.tpl");
    }

    public function passwordReset($uid)
    {
        $app = Slim::getInstance();
        $userDao = new UserDao();
        
        $reset_request = $userDao->getPasswordResetRequest($uid);
        if (!is_object($reset_request)) {
            $app->flash("error", Localisation::getTranslation(Strings::USER_ROUTEHANDLER_7));
            $app->redirect($app->urlFor("home"));
        }
        
        $user_id = $reset_request->getUserId();
        $app->view()->setData("uid", $uid);
        if ($app->request()->isPost()) {
            $post = $app->request()->post();

            if (isset($post['new_password']) && TemplateHelper::isValidPassword($post['new_password'])) {
                if (isset($post['confirmation_password']) && 
                        $post['confirmation_password'] == $post['new_password']) {

                    $response = $userDao->resetPassword($post['new_password'], $uid);
                    if ($response) {
                        $app->flash("success", Localisation::getTranslation(Strings::USER_ROUTEHANDLER_8));
                        $app->redirect($app->urlFor("home"));
                    } else {
                        $app->flashNow("error", Localisation::getTranslation(Strings::USER_ROUTEHANDLER_9));
                    }
                } else {
                    $app->flashNow("error", Localisation::getTranslation(Strings::USER_ROUTEHANDLER_10));
                }
            } else {
                $app->flashNow("error", Localisation::getTranslation(Strings::USER_ROUTEHANDLER_11));
            }
        }        
        $app->render("user/password-reset.tpl");
    }

    public function passResetRequest()
    {
        $app = Slim::getInstance();
        $userDao = new UserDao();
        
        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            if (isset($post['password_reset'])) {
                if (isset($post['email_address']) && $post['email_address'] != '') {
                    $email = $post['email_address'];
                    $hasUserRequestedPwReset = $userDao->hasUserRequestedPasswordReset($email);
                    $message = "";
                    if (!$hasUserRequestedPwReset) {
                        //send request
                        if ($userDao->requestPasswordReset($email)) {
                            $app->flash("success", Localisation::getTranslation(Strings::USER_ROUTEHANDLER_12));
                            $app->redirect($app->urlFor("home"));
                        } else {
                            $app->flashNow("error", "Failed to request password reset, are you sure you entered your email ".
                                        "address correctly?");
                        }
                    } else {
                        //get request time
                        $response = $userDao->getPasswordResetRequestTime($email);
                        if ($response != null) {
                            $app->flashNow("info", Localisation::getTranslation(Strings::USER_ROUTEHANDLER_13), $response);
                            //Send request
                            $userDao->requestPasswordReset($email);
                        }
                    }
                //        $app->flashNow("error", Localisation::getTranslation(Strings::USER_ROUTEHANDLER_14));
                } else {
                    $app->flashNow("error", Localisation::getTranslation(Strings::USER_ROUTEHANDLER_14));
                }
            }
        }
        $app->render("user/user.reset-password.tpl");
    }
    
    public function logout()
    {
        $app = Slim::getInstance();
        UserSession::destroySession();    //TODO revisit when oauth is in place
        $app->redirect($app->urlFor("home"));
    }

    public function login()
    {
        $app = Slim::getInstance();
        $userDao = new UserDao();
        
        $error = null;
        $openid = new LightOpenID("http://".$_SERVER["HTTP_HOST"].$app->urlFor("home"));
        $use_openid = Settings::get("site.openid");
        $app->view()->setData("openid", $use_openid);
        if (isset($use_openid)) {
            if ($use_openid == "y" || $use_openid == "h") {
                $extra_scripts = "
                    <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/lib/openid-jquery.js\"></script>
                    <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/lib/openid-en.js\"></script>
                    <link type=\"text/css\" rel=\"stylesheet\" media=\"all\" href=\"{$app->urlFor("home")}resources/css/openid.css\" />";
                $app->view()->appendData(array("extra_scripts" => $extra_scripts));
            }
        }
        
        try {
            if (isValidPost($app)) {
                $post = $app->request()->post();

                if (isset($post['login'])) {    
                    if($user = $userDao->login($post['email'], $post['password'])) {
                        UserSession::setSession($user->getId());
//                        UserSession::setHash(md5("{$user->getEmail()}:{$user->getDisplayName()}"));
                    }                   
                    $request = UserSession::getReferer();
                    UserSession::clearReferer();
                    if($request && $app->request()->getRootUri() && strpos($request, $app->request()->getRootUri())) {
                        $app->redirect( $request);
                    } else $app->redirect($app->urlFor("home"));     
                    
                } elseif (isset($post['password_reset'])) {
                    $app->redirect($app->urlFor("password-reset-request"));
                }
            } elseif ($app->request()->isPost() || $openid->mode) {
                if($this->openIdLogin($openid, $app)){
                    $request = UserSession::getReferer();
                    UserSession::clearReferer();
                    if($request && $app->request()->getRootUri() && strpos($request, $app->request()->getRootUri())) {
                        $app->redirect( $request);
                    } else $app->redirect($app->urlFor("home"));                    
                }  else {
                    $app->redirect($app->urlFor("user-public-profile", array("user_id" => UserSession::getCurrentUserID())));
                }
            }
			
			// Added check to display info message to users on IE borwsers
			$browserData = get_browser(null, true);
			if (!is_null($browserData) && isset($browserData['browser'])) {
				$browser = $browserData['browser'];
			
				if ($browser == 'IE') {
	                $app->flashNow("info", Localisation::getTranslation(Strings::INDEX_8).Localisation::getTranslation(Strings::INDEX_9));
	        	}
			
			}
			
            $app->render("user/login.tpl");
        } catch (SolasMatchException $e) {
            $error = sprintf(Localisation::getTranslation(Strings::USER_ROUTEHANDLER_15), $app->urlFor("login"), $app->urlFor("register"), $e->getMessage());            
            $app->flash("error", $error);
            $app->redirect($app->urlFor("login"));
            echo $error;
        }
    }
    
    public function openIdLogin($openid, $app)
    {       
        if (!$openid->mode) {
            try {
                $openid->identity = $openid->data["openid_identifier"];
                $openid->required = array("contact/email");
                $url = $openid->authUrl();
                $app->redirect($openid->authUrl());
            } catch (ErrorException $e) {
                echo $e->getMessage();
            }
        } elseif ($openid->mode == "cancel") {
            throw new InvalidArgumentException(Localisation::getTranslation(Strings::USER_ROUTEHANDLER_16));
        } else {
            $retvals= $openid->getAttributes();
            if ($openid->validate()) {
                $userDao = new UserDao();
                $temp =$retvals['contact/email'].substr(Settings::get("session.site_key"),0,20);
                UserSession::clearCurrentUserID();
                $user = $userDao->openIdLogin($retvals['contact/email'],md5($temp));
                if(is_array($user)) $user = $user[0];
                $adminDao = new AdminDao();
                if(!$adminDao->isUserBanned($user->getId())) {
                    UserSession::setSession($user->getId());
                } else {
                    $app->flash('error', Localisation::getTranslation(Strings::COMMON_THIS_USER_ACCOUNT_HAS_BEEN_BANNED));
                    $app->redirect($app->urlFor('home'));
                }
                
            }
            return true;
        }
    }        

    public static function userPrivateProfile($userId)
    {
        $app = Slim::getInstance();
        
        $userDao = new UserDao();
        $loggedInuser = $userDao->getUser(UserSession::getCurrentUserID());
        $user = $userDao->getUser($userId);
        CacheHelper::unCache(CacheHelper::GET_USER.$userId);
        
        if (!is_object($user)) {
            $app->flash("error", Localisation::getTranslation(Strings::COMMON_LOGIN_REQUIRED_TO_ACCESS_PAGE));
            $app->redirect($app->urlFor("login"));
        }

        $app->view()->appendData(array(
            "user"              => $loggedInuser,
            "profileUser"       => $user,
            "private_access"    => true,
        ));       
       
        $app->render("user/user-private-profile.tpl");
    }

    public static function userPublicProfile($user_id)
    {
        $app = Slim::getInstance();
        $userDao = new UserDao();
        $orgDao = new OrganisationDao();
        $adminDao = new AdminDao();
        
        $app->view()->setData("isSiteAdmin", $adminDao->isSiteAdmin(UserSession::getCurrentUserID()));
        $user=null;
        try{
            CacheHelper::unCache(CacheHelper::GET_USER.$user_id);
            $user = $userDao->getUser($user_id);
        }catch (SolasMatchException $e){
             $app->flash('error', Localisation::getTranslation(Strings::COMMON_LOGIN_REQUIRED_TO_ACCESS_PAGE));
             $app->redirect($app->urlFor('login'));
        }
        $userPersonalInfo=null;
        try{
            $userPersonalInfo = $userDao->getPersonalInfo($user_id);
        }catch(SolasMatchException $e){}
        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            
            if (isset($post['revokeBadge']) && isset($post['badge_id']) && $post['badge_id'] != ""){
                $badge_id = $post['badge_id'];
                $userDao->removeUserBadge($user_id, $badge_id);
            }
                
            if (isset($post['revoke'])) {
                $org_id = $post['org_id'];
                $userDao->leaveOrganisation($user_id, $org_id); 
            } 

            if (isset($post['referenceRequest'])) {
                $userDao->requestReferenceEmail($user_id);
                $app->view()->appendData(array("requestSuccess" => true));
            }
        }
                    
        $archivedJobs = $userDao->getUserArchivedTasks($user_id, 10);
        $user_tags = $userDao->getUserTags($user_id);
        $user_orgs = $userDao->getUserOrgs($user_id);
        $badges = $userDao->getUserBadges($user_id);
        $secondaryLanguages = $userDao->getSecondaryLanguages($user_id);

        $orgList = array();
        if($badges) {
            foreach ($badges as $badge) {
                if ($badge->getOwnerId() != null) {
                    $org = $orgDao->getOrganisation($badge->getOwnerId());
                    $orgList[$badge->getOwnerId()] = $org;
                }
            }    
        }
       
        $org_creation = Settings::get("site.organisation_creation");
            
        $extra_scripts = "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}";
        $extra_scripts .= "resources/bootstrap/js/confirm-remove-badge.js\"></script>";

        $app->view()->appendData(array("badges" => $badges,
                                    "orgList"=> $orgList,
                                    "user_orgs" => $user_orgs,
                                    "current_page" => "user-profile",
                                    "archivedJobs" => $archivedJobs,
                                    "user_tags" => $user_tags,
                                    "this_user" => $user,
                                    "extra_scripts" => $extra_scripts,
                                    "org_creation" => $org_creation,
                                    "userPersonalInfo" => $userPersonalInfo,
                                    "secondaryLanguages" => $secondaryLanguages
        ));
                
        if (UserSession::getCurrentUserID() == $user_id) {
            $notifData = $userDao->getUserTaskStreamNotification($user_id);
            $interval = null;
            $lastSent = null;
            $strict = null;

            if ($notifData) {
                $interval = $notifData->getInterval();
                switch ($interval) {
                    case NotificationIntervalEnum::DAILY:
                        $interval = "daily";
                        break;
                    case NotificationIntervalEnum::WEEKLY:
                        $interval = "weekly";
                        break;
                    case NotificationIntervalEnum::MONTHLY:
                        $interval = "monthly";
                        break;
                }

                if ($notifData->getLastSent() != null) {
                    $lastSent = date(Settings::get("ui.date_format"), strtotime($notifData->getLastSent()));
                }

                $strict = $notifData->getStrict();
            }
            $app->view()->appendData(array(
                        "interval"       => $interval,
                        "lastSent"       => $lastSent,
                        "strict"         => $strict,
                        "private_access" => true
            ));
        }
                    
        $app->render("user/user-public-profile.tpl");
    }

    public function editTaskStreamNotification($userId)
    {
        $app = Slim::getInstance();
        $userDao = new UserDao();

        $user = $userDao->getUser($userId);

        if ($app->request()->isPost()) {
            $post = $app->request()->post();

            if (isset($post['interval'])) {
                $success = false;
                if ($post['interval'] == 0) {
                    $success = $userDao->removeTaskStreamNotification($userId);
                } else {
                    $notifData = new UserTaskStreamNotification();
                    $notifData->setUserId($userId);
                    $notifData->setInterval($post['interval']);
                    if (isset($post['strictMode']) && $post['strictMode'] == 'enabled') {
                        $notifData->setStrict(true);
                    } else {
                        $notifData->setStrict(false);
                    }
                    $success = $userDao->requestTaskStreamNotification($notifData);
                }

                $app->flash("success", Localisation::getTranslation(Strings::USER_ROUTEHANDLER_19));
                $app->redirect($app->urlFor("user-public-profile", array("user_id" => $userId)));
            }
        }
        
        $notifData = $userDao->getUserTaskStreamNotification($userId);
        $interval = null;
        $lastSent = null;
        if ($notifData) {
            $interval = $notifData->getInterval();
            switch ($interval) {
                case NotificationIntervalEnum::DAILY:
                    $interval = "daily";
                    break;
                case NotificationIntervalEnum::WEEKLY:
                    $interval = "weekly";
                    break;
                case NotificationIntervalEnum::MONTHLY:
                    $interval = "monthly";
                    break;
            }
            
            if ($notifData->getLastSent() != null) {
                $lastSent = date(Settings::get("ui.date_format"), strtotime($notifData->getLastSent()));
            }

            if ($notifData->hasStrict()) {
                $strict = $notifData->getStrict();
            } else {
                $strict = false;
            }

            $app->view()->appendData(array(
                        "interval"  => $interval,
                        "intervalId"=> $notifData->getInterval(),
                        "lastSent"  => $lastSent,
                        'strict'    => $strict
            ));
        }

        $app->view()->appendData(array(
                    "user" => $user
        ));

        $app->render("user/user.task-stream-notification-edit.tpl");
    }

    public function userTaskReviews($taskId)
    {
        $app = Slim::getInstance();
        $taskDao = new TaskDao();

        $task = $taskDao->getTask($taskId);
        $reviews = $taskDao->getTaskReviews($taskId);

        $extra_scripts = "";
        $extra_scripts .= "<link rel=\"stylesheet\" href=\"{$app->urlFor("home")}ui/js/RateIt/src/rateit.css\"/>";
        $extra_scripts .= "<script>".file_get_contents(__DIR__."/../js/RateIt/src/jquery.rateit.min.js")."</script>";

        $app->view()->appendData(array(
                    'task'          => $task,
                    'reviews'       => $reviews,
                    'extra_scripts' => $extra_scripts
        ));

        $app->render("user/user.task-reviews.tpl");
    }

    public static function isLoggedIn()
    {
        return (!is_null(UserSession::getCurrentUserId()));
    }     
}

$route_handler = new UserRouteHandler();
$route_handler->init();
unset ($route_handler);
