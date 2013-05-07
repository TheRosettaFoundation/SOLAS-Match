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

        $app->get("/register", array($this, "register")
        )->via("GET", "POST")->name("register");

        $app->get("/user/:uuid/verification", array($this, 'emailVerification')
        )->via('POST')->name('email-verification');

        $app->get("/:uid/password/reset", array($this, "passwordReset")
        )->via("POST")->name("password-reset");

        $app->get("/password/reset", array($this, "passResetRequest")
        )->via("POST")->name("password-reset-request");
        
        $app->get("/logout", array($this, "logout"))->name("logout");
        
        $app->get("/login", array($this, "login")
        )->via("GET", "POST")->name("login");

        $app->get("/:user_id/profile", array($this, "userPublicProfile")
        )->via("POST")->name("user-public-profile");

        $app->get("/:user_id/privateProfile", array($middleware, "authUserIsLoggedIn"), 
        array($this, "userPrivateProfile"))->via("POST")->name("user-private-profile");

        $app->get("/:user_id/notification/stream", array($middleware, "authUserIsLoggedIn"),
        array($this, "editTaskStreamNotification"))->via("POST")->name("stream-notification-edit");
        
  
    }

   
     
    
    public function home()
    {
        $app = Slim::getInstance();
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
        
        if ($current_user_id == null) {
            $tasks = $taskDao->getTopTasks(10);
            for ($i = 0; $i < count($tasks); $i++) {
                $tasks[$i]['Project'] = $projectDao->getProject($tasks[$i]->getProjectId());
                $tasks[$i]['Org'] = $orgDao->getOrganisation($tasks[$i]['Project']->getOrganisationId());
            }

            $app->view()->appendData(array(
                "tasks" => $tasks
            ));

        } else {
            $taskTypes = array();
            $taskTypes[TaskTypeEnum::SEGMENTATION] = "Segmentation";
            $taskTypes[TaskTypeEnum::TRANSLATION] = "Translation";
            $taskTypes[TaskTypeEnum::PROOFREADING] = "Proofreading";
            $taskTypes[TaskTypeEnum::DESEGMENTATION] = "Desegmentation";

            $languageList = TemplateHelper::getLanguageList();

            $filter = array();
            $selectedType = "";
            $selectedSource = "";
            $selectedTarget = "";
            if ($app->request()->isPost()) {
                $post = (object) $app->request()->post();

                if (isset($post->taskType) && $post->taskType != '') {
                    $selectedType = $post->taskType;
                    $filter['taskType'] = $post->taskType;
                }

                if (isset($post->sourceLanguage) && $post->sourceLanguage != '') {
                    $selectedSource = $post->sourceLanguage;
                    $filter['sourceLanguage'] = $post->sourceLanguage;
                }

                if (isset($post->targetLanguage) && $post->targetLanguage != '') {
                    $selectedTarget = $post->targetLanguage;
                    $filter['targetLanguage'] = $post->targetLanguage;
                }
            }

            $tasks = $userDao->getUserTopTasks($current_user_id, 10, $filter);
            for ($i = 0; $i < count($tasks); $i++) {
                $tasks[$i]['Project'] = $projectDao->getProject($tasks[$i]->getProjectId());
                $tasks[$i]['Org'] = $orgDao->getOrganisation($tasks[$i]['Project']->getOrganisationId());
            }
            
            $app->view()->appendData(array(
                "taskTypes" => $taskTypes,
                "languageList" => $languageList,
                "selectedType" => $selectedType,
                "selectedSource" => $selectedSource,
                "selectedTarget" => $selectedTarget,
                "tasks" => $tasks
            ));
            
            $user_tags = $userDao->getUserTags($current_user_id);
            $app->view()->appendData(array(
                        "user_tags" => $user_tags
            ));
        }
        
        $numTaskTypes = Settings::get("ui.task_types");
        $taskTypeColours = array();
        
        for($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
        }  
        
        $app->view()->appendData(array(
                     "taskTypeColours" => $taskTypeColours
        ));
        
        $app->render("index.tpl");
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
                    <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/openid-jquery.js\"></script>
                    <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/openid-en.js\"></script>
                    <link type=\"text/css\" rel=\"stylesheet\" media=\"all\" href=\"{$app->urlFor("home")}resources/css/openid.css\" />";
                $app->view()->appendData(array("extra_scripts" => $extra_scripts));
            }   
        }   
        $error = null;
        $warning = null;
        if (isValidPost($app)) {
            $post = (object) $app->request()->post();
            
            if (!TemplateHelper::isValidEmail($post->email)) {
                $error = "The email address you entered was not valid. Please cheak for typos and try again.";
            } elseif (!TemplateHelper::isValidPassword($post->password)) {
                $error = "You didn\"t enter a password. Please try again.";
            } elseif ($user = $userDao->getUserByEmail($post->email)) {
                if ($return = $userDao->isUserVerified($user->getId())) {
                    $error = "You are already a verified user. Please "
                            ."<a href=\"{$app->urlFor("login")}\">log in</a>.";
                }
            }
            
            if (is_null($error)) {
                $userDao->register($post->email, $post->password);
                $app->flashNow("success", "A verification email has been sent to the email address you registered with. "
                            ."Please follow the link in that email to finish registration. Once you have verified your "
                            ."email address you can log in <a href=\"{$app->urlFor("login")}\">here</a>.");
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
            $app->flash("error", "Invalid registration id. Please try to register again");
            $app->redirect($app->urlFor("home"));
        }

        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            if (isset($post['verify'])) {
                $userDao->finishRegistration($user->getId());
                UserSession::setSession($user->getId());
                $app->flash("success", "Registration complete");
                $app->redirect($app->urlFor("home"));
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
            $app->flash("error", "Incorrect Unique ID. Are you sure you copied the URL correctly?");
            $app->redirect($app->urlFor("home"));
        }
        
        $user_id = $reset_request->getUserId();
        $app->view()->setData("uid", $uid);
        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();

            if (isset($post->new_password) && TemplateHelper::isValidPassword($post->new_password)) {
                if (isset($post->confirmation_password) && 
                        $post->confirmation_password == $post->new_password) {

                    $response = $userDao->resetPassword($post->new_password, $uid);
                    if ($response) {
                        $app->flash("success", "You have successfully changed your password");
                        $app->redirect($app->urlFor("home"));
                    } else {
                        $app->flashNow("error", "Unable to change Password");
                    }
                } else {
                    $app->flashNow("error", "The passwords entered do not match.
                                        Please try again.");
                }
            } else {
                $app->flashNow("error", "Please check the password provided, and try again.
                                It was not found to be valid.");
            }
        }        
        $app->render("user/password-reset.tpl");
    }

    public function passResetRequest()
    {
        $app = Slim::getInstance();
        $userDao = new UserDao();
        
        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();
            if (isset($post->password_reset)) {
                if (isset($post->email_address) && $post->email_address != '') {
                    $user = $userDao->getUserByEmail($post->email_address); 
                    if ($user) {  
                        $hasUserRequestedPwReset = $userDao->hasUserRequestedPasswordReset($user->getId());
                        $message = "";
                        if (!$hasUserRequestedPwReset) {
                            //send request
                            $userDao->requestPasswordReset($user->getId());
                            $app->flash("success", "Password reset request sent. Check your email
                                                    for further instructions.");
                            $app->redirect($app->urlFor("home"));
                        } else {
                            //get request time
                            $response = $userDao->getPasswordResetRequestTime($user->getId());
                            $app->flashNow("info", "Password reset request was already sent on $response.
                                                     Another email has been sent to your contact address.
                                                     Follow the link in this email to reset your password");
                            //Send request
                            $userDao->requestPasswordReset($user->getId());
                        }
                    } else {
                        $app->flashNow("error", "Please enter a valid email address");
                    }
                } else {
                    $app->flashNow("error", "Please enter a valid email address");
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
                    <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/openid-jquery.js\"></script>
                    <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/openid-en.js\"></script>
                    <link type=\"text/css\" rel=\"stylesheet\" media=\"all\" href=\"{$app->urlFor("home")}resources/css/openid.css\" />";
                $app->view()->appendData(array("extra_scripts" => $extra_scripts));
            }
        }
        
        try {
            if (isValidPost($app)) {
                $post = (object) $app->request()->post();

                if (isset($post->login)) {                    
                    $user = $userDao->login($post->email, $post->password);
                    
                    if (!is_array($user) && !is_null($user)) {
                        UserSession::setSession($user->getId());
                    } else {
                        throw new InvalidArgumentException("Sorry, the username or password entered is incorrect.
                            Please check the credentials used and try again.");    
                    }
                    
                    $app->redirect($app->urlFor("home"));
                } elseif (isset($post->password_reset)) {
                    $app->redirect($app->urlFor("password-reset-request"));
                }
            } elseif ($app->request()->isPost() || $openid->mode) {
                if($this->openIdLogin($openid, $app)){
                   $app->redirect($app->urlFor("home"));
                }  else {
                    $app->redirect($app->urlFor("user-public-profile", array("user_id" => UserSession::getCurrentUserID())));
                }
            }
            $app->render("user/login.tpl");
        } catch (InvalidArgumentException $e) {
            $error = "<p>Unable to log in. Please check your email and password.";
            $error .= " <a href=\"{$app->urlFor("login")}\">Try logging in again</a>";
            $error .= " or <a href=\"{$app->urlFor("register")}\">register</a> for an account.</p>";
            $error .= "<b>{$e->getMessage()}</b></p>";
            
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
            throw new InvalidArgumentException("User has canceled authentication!");
        } else {
            $retvals= $openid->getAttributes();
            if ($openid->validate()) {
                $userDao = new UserDao();
                $user = $userDao->getUserByEmail($retvals['contact/email']);
                if(is_array($user)) $user = $user[0];                    
                if(is_null($user)) {
                    $user = $userDao->register($retvals["contact/email"], md5($retvals["contact/email"]));
                    if(is_array($user)) $user = $user[0]; 
                    UserSession::setSession($user->getId());
                    return false;
                }
                $adminDao = new AdminDao();
                if(!$adminDao->isUserBanned($user->getId())) {
                    UserSession::setSession($user->getId());
                } else {
                    $app->flash('error', "This user account has been banned.");
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
        $user = $userDao->getUser($userId);
        $userPersonalInfo = $userDao->getPersonalInfo($userId);
        
        if (!is_object($user)) {
            $app->flash("error", "Login required to access page");
            $app->redirect($app->urlFor("login"));
        }

        $languageDao = new LanguageDao();
        $countryDao = new CountryDao();
        $languages=null;
        $languages=$languageDao->getLanguages();

        $countries =null;
        $countries=$countryDao->getCountries();

        
        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            $personalInfo = new UserPersonalInformation(); 
            $personalInfo->setUserId($userId);
            
            if(isset($post["displayName"])) $user->setDisplayName($post["displayName"]);
            if(isset($post["biography"])) $user->setBiography($post["biography"]);
            
            if(isset($post["firstName"])) $personalInfo->setFirstName($post["firstName"]);
            if(isset($post["lastName"])) $personalInfo->setLastName($post["lastName"]);
            if(isset($post["mobileNumber"])) $personalInfo->setMobileNumber($post["mobileNumber"]);
            if(isset($post["businessNumber"])) $personalInfo->setBusinessNumber($post["businessNumber"]);
            if(isset($post["sip"])) $personalInfo->setSip($post["sip"]);
            if(isset($post["jobTitle"])) $personalInfo->setJobTitle($post["jobTitle"]);
            if(isset($post["address"])) $personalInfo->setAddress($post["address"]);
            if(isset($post["city"])) $personalInfo->setCity($post["city"]);
            if(isset($post["country"])) $personalInfo->setCountry($post["country"]);
            
            $userInfo = $userDao->getPersonalInfo($userId);
            if($userInfo) {
                $personalInfo->setId($userInfo->getId());
                $userDao->updatePersonalInfo($userId, $personalInfo);
            } else {
                $userDao->createPersonalInfo($userId, $personalInfo);
            }
            
            $nativeLang = $post["nativeLanguage"];
            $langCountry = $post["nativeCountry"];
            if (isset($nativeLang) && isset($langCountry)) {
                $nativeLocal = new Locale();
                
                $nativeLocal->setLanguageCode($nativeLang);
                $nativeLocal->setCountryCode($langCountry);
                $user->setNativeLocale($nativeLocal);

                $badge_id = BadgeTypes::NATIVE_LANGUAGE;
                $userDao->addUserBadgeById($userId, $badge_id);               
            }            

            if(isset($post["displayName"]) && isset($post["nativeLanguage"]) && isset($post["nativeCountry"])) {
                $badgeId = BadgeTypes::PROFILE_FILLER;
                $userDao->addUserBadgeById($userId, $badgeId);               
            }
            
            $currentSecondaryLocales = $userDao->getSecondaryLanguages($userId);
            
            $csl= array();
            if($currentSecondaryLocales){
                foreach($currentSecondaryLocales as $currLocale) {
                    $csl[$currLocale->getLanguageCode().'-'.$currLocale->getCountryCode()] = $currLocale;
                }
            }
            $newSecondaryLocales = array();

            for($i=0; $i < $post["secondaryLanguagesArraySize"]; $i++) {               
                $key = $post["secondaryLanguage_$i"].'-'.$post["secondaryCountry_$i"];

                $locale = new Locale();
                $locale->setLanguageCode($post["secondaryLanguage_$i"]);
                $locale->setCountryCode($post["secondaryCountry_$i"]);
                if(!key_exists($key, $csl)) $userDao->createSecondaryLanguage($userId, $locale);
                $newSecondaryLocales[$key] = $locale;
            }

            foreach($csl as $key => $newLocale) {
                if(!key_exists($key, $newSecondaryLocales)) {
                    $userDao->deleteSecondaryLanguage($userId, $newLocale);
                }
            }
            
            if ($user->getDisplayName() != ""
                    && $user->getNativeLocale() != null) {
                $badge_id = BadgeTypes::NATIVE_LANGUAGE;
                $userDao->addUserBadgeById($userId, $badge_id);               
                $badge_id = BadgeTypes::PROFILE_FILLER;
                $userDao->addUserBadgeById($userId, $badge_id);               

            }
            
            $userDao->updateUser($user);
            
            $app->redirect($app->urlFor("user-public-profile", array("user_id" => $user->getId())));
        }
        
        $extraScripts = file_get_contents(__DIR__."/../js/user-private-profile.js");
        $secondaryLanguages = $userDao->getSecondaryLanguages($userId);
        
        $app->view()->appendData(array(
            "user"              => $user,
            "private_access"    => true,
            "languages"         => $languages,
            "countries"         => $countries,
            "extra_scripts"     => $extraScripts,
            "userPersonalInfo"  => $userPersonalInfo,
            "secondaryLanguages" => $secondaryLanguages
        ));       
       
        $app->render("user/user-private-profile.tpl");
    }

    public static function userPublicProfile($user_id)
    {
        $app = Slim::getInstance();
        $userDao = new UserDao();
        $orgDao = new OrganisationDao();
        $adminDao = new AdminDao();
        
        $app->view()->setData("isSiteAdmin", $adminDao->isSiteAdmin($user_id));
        $user = $userDao->getUser($user_id);
        $userPersonalInfo = $userDao->getPersonalInfo($user_id);
        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();
            
            if (isset($post->revokeBadge) && isset($post->badge_id) && $post->badge_id != ""){
                $badge_id = $post->badge_id;
                $userDao->removeUserBadge($user_id, $badge_id);
            }
                
            if (isset($post->revoke)) {
                $org_id = $post->org_id;
                $userDao->leaveOrganisation($user_id, $org_id); 
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

        $app->view()->setData("orgList", $orgList);
        $app->view()->appendData(array("badges" => $badges,
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
            }
            $app->view()->appendData(array(
                        "interval"       => $interval,
                        "lastSent"       => $lastSent,
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
            $post = (object) $app->request()->post();

            if (isset($post->interval)) {
                $success = false;
                if ($post->interval == 0) {
                    $success = $userDao->removeTaskStreamNotification($userId);
                } else {
                    $success = $userDao->requestTaskStreamNotification($userId, $post->interval);
                }

                $app->flash("success", "Successfully updated user task stream notification subscription");
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

            $app->view()->appendData(array(
                        "interval"  => $interval,
                        "intervalId"=> $notifData->getInterval(),
                        "lastSent"  => $lastSent
            ));
        }

        $app->view()->appendData(array(
                    "user" => $user
        ));

        $app->render("user/user.task-stream-notification-edit.tpl");
    }

    public static function isLoggedIn()
    {
        return (!is_null(UserSession::getCurrentUserId()));
    }     
}

$route_handler = new UserRouteHandler();
$route_handler->init();
unset ($route_handler);
