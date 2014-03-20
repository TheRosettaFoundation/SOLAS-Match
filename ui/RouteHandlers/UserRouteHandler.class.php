<?php

namespace SolasMatch\UI\RouteHandlers;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\Common as Common;

require_once __DIR__."/../DataAccessObjects/UserDao.class.php";
require_once __DIR__."/../../Common/protobufs/models/Register.php";
require_once __DIR__."/../../Common/protobufs/models/Login.php";
require_once __DIR__."/../../Common/protobufs/models/PasswordResetRequest.php";
require_once __DIR__."/../../Common/protobufs/models/PasswordReset.php";
require_once __DIR__."/../../Common/protobufs/models/Locale.php";

class UserRouteHandler
{
    public function init()
    {
        $app = \Slim\Slim::getInstance();
        $middleware = new Lib\Middleware();

        $app->get(
            "/",
            array($this, "home")
        )->via("POST")->name("home");

        $app->get(
            "/register/",
            array($this, "register")
        )->via("GET", "POST")->name("register");

        $app->get(
            "/user/:uuid/verification/",
            array($this, 'emailVerification')
        )->via('POST')->name('email-verification');

        $app->get(
            "/:uid/password/reset/",
            array($this, "passwordReset")
        )->via("POST")->name("password-reset");

        $app->get(
            "/password/reset/",
            array($this, "passResetRequest")
        )->via("POST")->name("password-reset-request");
        
        $app->get(
            "/logout/",
            array($this, "logout")
        )->name("logout");
        
        $app->get(
            "/login/",
            array($this, "login")
        )->via("GET", "POST")->name("login");

        $app->get(
            "/:user_id/profile/",
            array($middleware, 'authUserIsLoggedIn'),
            array($this, "userPublicProfile")
        )->via("POST")->name("user-public-profile");

        $app->get(
            "/:user_id/privateProfile/",
            array($middleware, "authUserIsLoggedIn"),
            array($this, "userPrivateProfile")
        )->via("POST")->name("user-private-profile");

        $app->get(
            "/:user_id/notification/stream/",
            array($middleware, "authUserIsLoggedIn"),
            array($this, "editTaskStreamNotification")
        )->via("POST")->name("stream-notification-edit");
  
        $app->get(
            "/user/task/:task_id/reviews/",
            array($middleware, "authenticateUserForTask"),
            array($this, "userTaskReviews")
        )->name("user-task-reviews");
    }
    
    public function home()
    {
        $app = \Slim\Slim::getInstance();
        $viewData = array();
        $langDao = new DAO\LanguageDao();
        $tagDao = new DAO\TagDao();
        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $orgDao = new DAO\OrganisationDao();
        $userDao = new DAO\UserDao();

        $use_statistics = Common\Lib\Settings::get("site.stats");
        if ($use_statistics == 'y') {
            $statsDao = new DAO\StatisticsDao();
            $statistics = $statsDao->getStats();
            $statsArray = null;
            if ($statistics) {
                $statsArray = array();
                foreach ($statistics as $stat) {
                    $statsArray[$stat->getName()] = $stat;
                }
            }
            $viewData["statsArray"] = $statsArray;
        }
        
        $top_tags = $tagDao->getTopTags(10);
        $viewData["top_tags"] = $top_tags;
        $viewData["current_page"] = "home";

        $current_user_id = Common\Lib\UserSession::getCurrentUserID();
        
        if ($current_user_id != null) {
            $user_tags = $userDao->getUserTags($current_user_id);
            $viewData["user_tags"] = $user_tags;
        }

        if ($current_user_id == null) {
            $app->flashNow('info', Lib\Localisation::getTranslation('index_dont_use_ie'));
        }

        $extra_scripts = "
<script src=\"{$app->urlFor("home")}ui/dart/build/packages/shadow_dom/shadow_dom.debug.js\"></script>
<script src=\"{$app->urlFor("home")}ui/dart/build/packages/custom_element/custom-elements.debug.js\"></script>
<script src=\"{$app->urlFor("home")}ui/dart/build/packages/browser/interop.js\"></script>
<script src=\"{$app->urlFor("home")}ui/dart/build/Routes/Users/home.dart.js\"></script>
<span class=\"hidden\">
";
        $extra_scripts .= file_get_contents("ui/dart/web/Routes/Users/TaskStream.html");
        $extra_scripts .= "</span>";

        $viewData['extra_scripts'] = $extra_scripts;

        $app->view()->appendData($viewData);
        $app->render("index.tpl");
    }

    public function videos()
    {
        $app = \Slim\Slim::getInstance();
        $app->view()->appendData(array('current_page' => 'videos'));
        $app->render("videos.tpl");
    }

    public function register()
    {
        $app = \Slim\Slim::getInstance();
        $userDao = new DAO\UserDao();
        
        $use_openid = Common\Lib\Settings::get("site.openid");
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
        if (\SolasMatch\UI\isValidPost($app)) {
            $post = $app->request()->post();
            $temp = md5($post['email'].substr(Common\Lib\Settings::get("session.site_key"), 0, 20));
            Common\Lib\UserSession::clearCurrentUserID();
            if (!Lib\TemplateHelper::isValidEmail($post['email'])) {
                $error = Lib\Localisation::getTranslation('register_1');
            } elseif (!Lib\TemplateHelper::isValidPassword($post['password'])) {
                $error = Lib\Localisation::getTranslation('register_2');
            } elseif ($user = $userDao->getUserByEmail($post['email'], $temp)) {
                if ($userDao->isUserVerified($user->getId())) {
                    $error = sprintf(Lib\Localisation::getTranslation('register_3'), $app->urlFor("login"));
                } else {
                    $error = "User is not verified";
                    // notify user that they are not yet verified an resent verification email
                }
            }
            
            if (is_null($error)) {
                if ($userDao->register($post['email'], $post['password'])) {
                    $app->flashNow(
                        "success",
                        sprintf(Lib\Localisation::getTranslation('register_4'), $app->urlFor("login"))
                    );
                } else {
                    $app->flashNow(
                        'error',
                        'Failed to register'
                    );
                }
            }
        }
        if ($error !== null) {
            $app->view()->appendData(array("error" => $error));
        }
        if ($warning !== null) {
            $app->view()->appendData(array("warning" => $warning));
        }

        // Added check to display info message to users on IE borwsers
        $browserData = get_browser(null, true);
        if (!is_null($browserData) && isset($browserData['browser'])) {
            $browser = $browserData['browser'];
            
            if ($browser == 'IE') {
                $app->flashNow(
                    "info",
                    Lib\Localisation::getTranslation('index_8').Lib\Localisation::getTranslation('index_9')
                );
            }
        }

        $app->render("user/register.tpl");
    }

    public function emailVerification($uuid)
    {
        $app = \Slim\Slim::getInstance();
        $userDao = new DAO\UserDao();

        $user = $userDao->getRegisteredUser($uuid);

        if (is_null($user)) {
            $app->flash("error", Lib\Localisation::getTranslation('email_verification_7'));
            $app->redirect($app->urlFor("home"));
        }

        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            if (isset($post['verify'])) {
                if ($userDao->finishRegistration($uuid)) {
                    $app->flash('success', Lib\Localisation::getTranslation('email_verification_8'));
                } else {
                    $app->flash('error', 'Failed to finish registration');  // TODO: remove inline text
                }
                $app->redirect($app->urlFor('login'));
            }
        }

        $app->view()->appendData(array('uuid' => $uuid));

        $app->render("user/email.verification.tpl");
    }

    public function passwordReset($uid)
    {
        $app = \Slim\Slim::getInstance();
        $userDao = new DAO\UserDao();
        
        $reset_request = $userDao->getPasswordResetRequest($uid);
        if (!is_object($reset_request)) {
            $app->flash("error", Lib\Localisation::getTranslation('password_reset_1'));
            $app->redirect($app->urlFor("home"));
        }
        
        $user_id = $reset_request->getUserId();
        $app->view()->setData("uid", $uid);
        if ($app->request()->isPost()) {
            $post = $app->request()->post();

            if (isset($post['new_password']) && Lib\TemplateHelper::isValidPassword($post['new_password'])) {
                if (isset($post['confirmation_password']) &&
                        $post['confirmation_password'] == $post['new_password']) {

                    $response = $userDao->resetPassword($post['new_password'], $uid);
                    if ($response) {
                        $app->flash("success", Lib\Localisation::getTranslation('password_reset_1'));
                        $app->redirect($app->urlFor("home"));
                    } else {
                        $app->flashNow("error", Lib\Localisation::getTranslation('password_reset_1'));
                    }
                } else {
                    $app->flashNow("error", Lib\Localisation::getTranslation('password_reset_1'));
                }
            } else {
                $app->flashNow("error", Lib\Localisation::getTranslation('password_reset_1'));
            }
        }
        $app->render("user/password-reset.tpl");
    }

    public function passResetRequest()
    {
        $app = \Slim\Slim::getInstance();
        $userDao = new DAO\UserDao();
        
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
                            $app->flash("success", Lib\Localisation::getTranslation('user_reset_password_2'));
                            $app->redirect($app->urlFor("home"));
                        } else {
                            $app->flashNow(
                                "error",
                                "Failed to request password reset, are you sure you entered your email ".
                                "address correctly?"
                            );
                        }
                    } else {
                        //get request time
                        $response = $userDao->getPasswordResetRequestTime($email);
                        if ($response != null) {
                            $app->flashNow(
                                "info",
                                Lib\Localisation::getTranslation('user_reset_password_3'),
                                $response
                            );
                            //Send request
                            $userDao->requestPasswordReset($email);
                        }
                    }

                } else {
                    $app->flashNow("error", Lib\Localisation::getTranslation('user_reset_password_4'));
                }
            }
        }
        $app->render("user/user.reset-password.tpl");
    }
    
    public function logout()
    {
        $app = \Slim\Slim::getInstance();
        Common\Lib\UserSession::destroySession();    //TODO revisit when oauth is in place
        $app->redirect($app->urlFor("home"));
    }

    public function login()
    {
        $app = \Slim\Slim::getInstance();
        $userDao = new DAO\UserDao();
        
        $error = null;
        $openid = new \LightOpenID("http://".$_SERVER["HTTP_HOST"].$app->urlFor("home"));
        $use_openid = Common\Lib\Settings::get("site.openid");
        $app->view()->setData("openid", $use_openid);
        
        if ($app->request()->isPost() || $openid->mode) {
            $post = $app->request()->post();

            if (isset($post['login'])) {
                $user = null;
                try {
                    $user = $userDao->login($post['email'], $post['password']);
                } catch (\Exception $e) {
                    $error = sprintf(
                        Lib\Localisation::getTranslation('login_1'),
                        $app->urlFor("login"),
                        $app->urlFor("register"),
                        $e->getMessage()
                    );
                    $app->flashNow('error', $error);
                }
                if (!is_null($user)) {
                    Common\Lib\UserSession::setSession($user->getId());
                    $request = Common\Lib\UserSession::getReferer();
                    Common\Lib\UserSession::clearReferer();
                    if ($request && $app->request()->getRootUri() && strpos($request, $app->request()->getRootUri())) {
                        $app->redirect($request);
                    } else {
                        $app->redirect($app->urlFor("home"));
                    }
                }
            } elseif (isset($post['password_reset'])) {
                $app->redirect($app->urlFor("password-reset-request"));
            } else {
                try {
                    $this->openIdLogin($openid, $app);
                } catch (Exception $e) {
                    $error = sprintf(
                        Lib\Localisation::getTranslation('login_1'),
                        $app->urlFor("login"),
                        $app->urlFor("register"),
                        $e->getMessage()
                    );
                    $app->flashNow('error', $error);
                }
            }
        } else {
            $authCode = $app->request()->get('code');
            if (!is_null($authCode)) {
                // Exchange auth code for access token
                $user = null;
                try {
                    $user = $userDao->loginWithAuthCode($authCode);
                } catch (\Exception $e) {
                    $error = sprintf(
                        Lib\Localisation::getTranslation('login_1'),
                        $app->urlFor("login"),
                        $app->urlFor("register"),
                        $e->getMessage()
                    );
                    $app->flash('error', $error);
                    $app->redirect($app->urlFor('login'));
                }
                Common\Lib\UserSession::setSession($user->getId());
                $request = Common\Lib\UserSession::getReferer();
                Common\Lib\UserSession::clearReferer();
                if ($request && $app->request()->getRootUri() && strpos($request, $app->request()->getRootUri())) {
                    $app->redirect($request);
                } else {
                    $app->redirect($app->urlFor("home"));
                }
            }
            $error = $app->request()->get('error');
            if (!is_null($error)) {
                $app->flashNow('error', $app->request()->get('error_message'));
            }
        }

        // Added check to display info message to users on IE borwsers
        $browserData = get_browser(null, true);
        if (!is_null($browserData) && isset($browserData['browser'])) {
            $browser = $browserData['browser'];

            if ($browser == 'IE') {
                $app->flashNow(
                    "info",
                    Lib\Localisation::getTranslation('index_8').Lib\Localisation::getTranslation('index_9')
                );
            }
        }

        if (isset($use_openid)) {
            if ($use_openid == "y" || $use_openid == "h") {
                $extra_scripts = "
<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/lib/openid-jquery.js\"></script>
<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/lib/openid-en.js\"></script>
<link type=\"text/css\" rel=\"stylesheet\" media=\"all\" href=\"{$app->urlFor("home")}resources/css/openid.css\" />";
                $app->view()->appendData(array("extra_scripts" => $extra_scripts));
            }
        }
        
        $app->render("user/login.tpl");
    }
    
    public function openIdLogin($openid, $app)
    {
        if (!$openid->mode) {
            $openid->identity = $openid->data["openid_identifier"];
            $openid->required = array("contact/email");
            $url = $openid->authUrl();
            $app->redirect($openid->authUrl());
        } elseif ($openid->mode == "cancel") {
            throw new InvalidArgumentException(Lib\Localisation::getTranslation('login_2'));
        } else {
            $retvals = $openid->getAttributes();
            if ($openid->validate()) {
                // Request Auth code and redirect
                $userDao = new DAO\UserDao();
                $userDao->requestAuthCode($retvals['contact/email']);
            }
        }
    }

    public static function userPrivateProfile($userId)
    {
        $app = \Slim\Slim::getInstance();
        
        $userDao = new DAO\UserDao();
        $loggedInuser = $userDao->getUser(Common\Lib\UserSession::getCurrentUserID());
        $user = $userDao->getUser($userId);
        Common\Lib\CacheHelper::unCache(Common\Lib\CacheHelper::GET_USER.$userId);
        
        if (!is_object($user)) {
            $app->flash("error", Lib\Localisation::getTranslation('common_login_required_to_access_page'));
            $app->redirect($app->urlFor("login"));
        }

        $extraScripts = "
<script src=\"{$app->urlFor("home")}ui/dart/build/packages/custom_element/custom-elements.debug.js\"></script>
<script src=\"{$app->urlFor("home")}ui/dart/build/packages/browser/interop.js\"></script>
<script src=\"{$app->urlFor("home")}ui/dart/build/Routes/Users/UserPrivateProfile.dart.js\"></script>
<span class=\"hidden\">
";
        $extraScripts .= file_get_contents("ui/dart/web/Routes/Users/UserPrivateProfileForm.html");
        $extraScripts .= "</span>";

        $app->view()->appendData(array(
            "user"              => $loggedInuser,
            "profileUser"       => $user,
            "private_access"    => true,
            'extra_scripts'     => $extraScripts
        ));
       
        $app->render("user/user-private-profile.tpl");
    }

    public static function userPublicProfile($user_id)
    {
        $app = \Slim\Slim::getInstance();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();
        $adminDao = new DAO\AdminDao();
        
        $loggedInUserId = Common\Lib\UserSession::getCurrentUserID();
        if (!is_null($loggedInUserId)) {
            $app->view()->setData("isSiteAdmin", $adminDao->isSiteAdmin($loggedInUserId));
        } else {
            $app->view()->setData('isSiteAdmin', 0);
        }
        $user=null;
        try {
            Common\Lib\CacheHelper::unCache(Common\Lib\CacheHelper::GET_USER.$user_id);
            $user = $userDao->getUser($user_id);
        } catch (Common\Exceptions\SolasMatchException $e) {
            $app->flash('error', Lib\Localisation::getTranslation('common_login_required_to_access_page'));
            $app->redirect($app->urlFor('login'));
        }
        $userPersonalInfo=null;
        try {
            $userPersonalInfo = $userDao->getPersonalInfo($user_id);
        } catch (Common\Exceptions\SolasMatchException $e) {
            // should handle the error here or at least error_log it
        }
        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            
            if (isset($post['revokeBadge']) && isset($post['badge_id']) && $post['badge_id'] != "") {
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
        if ($badges) {
            foreach ($badges as $badge) {
                if ($badge->getOwnerId() != null) {
                    $org = $orgDao->getOrganisation($badge->getOwnerId());
                    $orgList[$badge->getOwnerId()] = $org;
                }
            }
        }
       
        $org_creation = Common\Lib\Settings::get("site.organisation_creation");
            
        $extra_scripts = "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}";
        $extra_scripts .= "resources/bootstrap/js/confirm-remove-badge.js\"></script>";

        $app->view()->appendData(array(
            "badges" => $badges,
            "orgList" => $orgList,
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
                
        if (Common\Lib\UserSession::getCurrentUserID() == $user_id) {
            $notifData = $userDao->getUserTaskStreamNotification($user_id);
            $interval = null;
            $lastSent = null;
            $strict = null;

            if ($notifData) {
                $interval = $notifData->getInterval();
                switch ($interval) {
                    case Common\Enums\NotificationIntervalEnum::DAILY:
                        $interval = "daily";
                        break;
                    case Common\Enums\NotificationIntervalEnum::WEEKLY:
                        $interval = "weekly";
                        break;
                    case Common\Enums\NotificationIntervalEnum::MONTHLY:
                        $interval = "monthly";
                        break;
                }

                if ($notifData->getLastSent() != null) {
                    $lastSent = date(Common\Lib\Settings::get("ui.date_format"), strtotime($notifData->getLastSent()));
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
        $app = \Slim\Slim::getInstance();
        $userDao = new DAO\UserDao();

        $user = $userDao->getUser($userId);

        if ($app->request()->isPost()) {
            $post = $app->request()->post();

            if (isset($post['interval'])) {
                $success = false;
                if ($post['interval'] == 0) {
                    $success = $userDao->removeTaskStreamNotification($userId);
                } else {
                    $notifData = new Common\Protobufs\Models\UserTaskStreamNotification();
                    $notifData->setUserId($userId);
                    $notifData->setInterval($post['interval']);
                    if (isset($post['strictMode']) && $post['strictMode'] == 'enabled') {
                        $notifData->setStrict(true);
                    } else {
                        $notifData->setStrict(false);
                    }
                    $success = $userDao->requestTaskStreamNotification($notifData);
                }

                $app->flash("success", Lib\Localisation::getTranslation('user_public_profile_17'));
                $app->redirect($app->urlFor("user-public-profile", array("user_id" => $userId)));
            }
        }
        
        $notifData = $userDao->getUserTaskStreamNotification($userId);
        $interval = null;
        $lastSent = null;
        if ($notifData) {
            $interval = $notifData->getInterval();
            switch ($interval) {
                case Common\Enums\NotificationIntervalEnum::DAILY:
                    $interval = "daily";
                    break;
                case Common\Enums\NotificationIntervalEnum::WEEKLY:
                    $interval = "weekly";
                    break;
                case Common\Enums\NotificationIntervalEnum::MONTHLY:
                    $interval = "monthly";
                    break;
            }
            
            if ($notifData->getLastSent() != null) {
                $lastSent = date(Common\Lib\Settings::get("ui.date_format"), strtotime($notifData->getLastSent()));
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
        $app = \Slim\Slim::getInstance();
        $taskDao = new DAO\TaskDao();

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
}

$route_handler = new UserRouteHandler();
$route_handler->init();
unset ($route_handler);
