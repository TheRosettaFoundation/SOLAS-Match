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
            "/paged/:page_no/tt/:tt/sl/:sl/tl/:tl/",
            array($this, "home")
        )->via("POST")->name("home-paged");

        $app->get(
            "/register/",
            array($this, "register")
        )->via("GET", "POST")->name("register");

        $app->get(
            "/:user_id/change_email/",
            array($this, "changeEmail")
        )->via("GET", "POST")->name("change-email");

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
            '/loggedin/',
            array($this, "login_proz")
        )->via('GET', 'POST')->name('loggedin');

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
    
    public function home($currentScrollPage = 1, $selectedTaskType = 0, $selectedSourceLanguageCode = 0, $selectedTargetLanguageCode = 0)
    {
        $app = \Slim\Slim::getInstance();
        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();
        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();

        $languageDao = new DAO\LanguageDao();
        $activeSourceLanguages = $languageDao->getActiveSourceLanguages();
        $activeTargetLanguages = $languageDao->getActiveTargetLanguages();

        $taskTypeTexts = array();
        $taskTypeTexts[Common\Enums\TaskTypeEnum::SEGMENTATION]   = Lib\Localisation::getTranslation('common_segmentation');
        $taskTypeTexts[Common\Enums\TaskTypeEnum::TRANSLATION]    = Lib\Localisation::getTranslation('common_translation');
        $taskTypeTexts[Common\Enums\TaskTypeEnum::PROOFREADING]   = Lib\Localisation::getTranslation('common_proofreading');
        $taskTypeTexts[Common\Enums\TaskTypeEnum::DESEGMENTATION] = Lib\Localisation::getTranslation('common_desegmentation');

        $numTaskTypes = Common\Lib\Settings::get('ui.task_types');
        $taskTypeColours = array();
        for ($i = 1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }

        $viewData = array();
        $viewData['current_page'] = 'home';

        $tagDao = new DAO\TagDao();
        $top_tags = $tagDao->getTopTags(10);
        $viewData['top_tags'] = $top_tags;

        $use_statistics = Common\Lib\Settings::get('site.stats');
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
            $viewData['statsArray'] = $statsArray;
        }

        if ($user_id != null) {
            $user_tags = $userDao->getUserTags($user_id);
            $viewData['user_tags'] = $user_tags;
        }

        $maintenance_msg = Common\Lib\Settings::get('maintenance.maintenance_msg');
        if ($maintenance_msg == 'y') {
            $maintenanceCustomMsg = Common\Lib\Settings::get('maintenance.maintenance_custom_msg');
            if ($maintenanceCustomMsg == 'n') {
                $maintenanceDate     = Common\Lib\Settings::get('maintenance.maintenance_date');
                $maintenanceTime     = Common\Lib\Settings::get('maintenance.maintenance_time');
                $maintenanceDuration = Common\Lib\Settings::get('maintenance.maintenance_duration');
                $msg = sprintf(
                    Lib\Localisation::getTranslation('common_maintenance_message'),
                    $maintenanceDate,
                    $maintenanceTime,
                    $maintenanceDuration
                );
            } elseif ($maintenanceCustomMsg == 'y') {
                $msg = Common\Lib\Settings::get('maintenance.maintenance_custom_message');
            }
            $app->flashNow('warning', $msg);
        }

        $app->view()->appendData($viewData);

        $siteLocation = Common\Lib\Settings::get('site.location');
        $itemsPerScrollPage = 6;
        $offset = ($currentScrollPage - 1) * $itemsPerScrollPage;
        $topTasksCount = 0;

        $filter = array();
        if ($app->request()->isPost()) {
            $post = $app->request()->post();

            if (isset($post['taskTypes'])) {
                $selectedTaskType = $post['taskTypes'];
            }
            if (isset($post['sourceLanguage'])) {
                $selectedSourceLanguageCode = $post['sourceLanguage'];
            }
            if (isset($post['targetLanguage'])) {
                $selectedTargetLanguageCode = $post['targetLanguage'];
            }
        }
        // Post or route handler may return '0', need an explicit zero
        $selectedTaskType = (int)$selectedTaskType;
        if ($selectedSourceLanguageCode === '0') $selectedSourceLanguageCode = 0;
        if ($selectedTargetLanguageCode === '0') $selectedTargetLanguageCode = 0;

        // Identity tests (also in template) because a language code string evaluates to zero; (we use '0' because URLs look better that way)
        if ($selectedTaskType           !== 0) $filter['taskType']       = $selectedTaskType;
        if ($selectedSourceLanguageCode !== 0) $filter['sourceLanguage'] = $selectedSourceLanguageCode;
        if ($selectedTargetLanguageCode !== 0) $filter['targetLanguage'] = $selectedTargetLanguageCode;

        try {
            if ($user_id) {
                $strict = false;
                $topTasks      = $userDao->getUserTopTasks($user_id, $strict, $itemsPerScrollPage, $filter, $offset);
                $topTasksCount = $userDao->getUserTopTasksCount($user_id, $strict, $filter);
            }
            else {
                $topTasks      = $taskDao->getTopTasks($itemsPerScrollPage, $offset);
                $topTasksCount = $taskDao->getTopTasksCount();
            }
        } catch (\Exception $e) {
            $topTasks = array();
            $topTasksCount = 0;
        }

        $taskTags = array();
        $created_timestamps = array();
        $deadline_timestamps = array();
        $projectAndOrgs = array();
        $taskImages = array();

        $lastScrollPage = ceil($topTasksCount / $itemsPerScrollPage);
        if ($currentScrollPage <= $lastScrollPage) {
            foreach ($topTasks as $topTask) {
                $taskId = $topTask->getId();
                $project = $projectDao->getProject($topTask->getProjectId());
                $org_id = $project->getOrganisationId();
                $org = $orgDao->getOrganisation($org_id);

                $taskTags[$taskId] = $taskDao->getTaskTags($taskId);

                $created = $topTask->getCreatedTime();
                $selected_year   = (int)substr($created,  0, 4);
                $selected_month  = (int)substr($created,  5, 2);
                $selected_day    = (int)substr($created,  8, 2);
                $selected_hour   = (int)substr($created, 11, 2); // These are UTC, they will be recalculated to local time by JavaScript (we do not what the local time zone is)
                $selected_minute = (int)substr($created, 14, 2);
                $created_timestamps[$taskId] = gmmktime($selected_hour, $selected_minute, 0, $selected_month, $selected_day, $selected_year);

                $deadline = $topTask->getDeadline();
                $selected_year   = (int)substr($deadline,  0, 4);
                $selected_month  = (int)substr($deadline,  5, 2);
                $selected_day    = (int)substr($deadline,  8, 2);
                $selected_hour   = (int)substr($deadline, 11, 2); // These are UTC, they will be recalculated to local time by JavaScript (we do not what the local time zone is)
                $selected_minute = (int)substr($deadline, 14, 2);
                $deadline_timestamps[$taskId] = gmmktime($selected_hour, $selected_minute, 0, $selected_month, $selected_day, $selected_year);

                $projectUri = "{$siteLocation}project/{$project->getId()}/view";
                $projectName = $project->getTitle();
                $orgUri = "{$siteLocation}org/{$org_id}/profile";
                $orgName = $org->getName();
                $projectAndOrgs[$taskId]=sprintf(
                    Lib\Localisation::getTranslation('common_part_of_for'),
                    $projectUri,
                    htmlspecialchars($projectName, ENT_COMPAT, 'UTF-8'),
                    $orgUri,
                    htmlspecialchars($orgName, ENT_COMPAT, 'UTF-8')
                );

                $taskImages[$taskId] = '';
                if ($project->getImageApproved() && $project->getImageUploaded()) {
                    $taskImages[$taskId] = "{$siteLocation}project/{$project->getId()}/image";
                }
            }
        }

        if ($currentScrollPage == $lastScrollPage && ($topTasksCount % $itemsPerScrollPage != 0)) {
            $itemsPerScrollPage = $topTasksCount % $itemsPerScrollPage;
        }
        $extra_scripts  = "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/lib/jquery-ias.min.js\"></script>";
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/Parameters.js\"></script>";
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/Home.js\"></script>";


        $app->view()->appendData(array(
            'siteLocation' => $siteLocation,
            'activeSourceLanguages' => $activeSourceLanguages,
            'activeTargetLanguages' => $activeTargetLanguages,
            'selectedTaskType' => $selectedTaskType,
            'selectedSourceLanguageCode' => $selectedSourceLanguageCode,
            'selectedTargetLanguageCode' => $selectedTargetLanguageCode,
            'topTasks' => $topTasks,
            'taskTypeTexts' => $taskTypeTexts,
            'taskTypeColours' => $taskTypeColours,
            'taskTags' => $taskTags,
            'created_timestamps' => $created_timestamps,
            'deadline_timestamps' => $deadline_timestamps,
            'projectAndOrgs' => $projectAndOrgs,
            'taskImages' => $taskImages,
            'currentScrollPage' => $currentScrollPage,
            'itemsPerScrollPage' => $itemsPerScrollPage,
            'lastScrollPage' => $lastScrollPage,
            'extra_scripts' => $extra_scripts,
            'user_id' => $user_id,
        ));
        $app->render('index.tpl');
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
        $langDao = new DAO\LanguageDao();
        
        $use_openid = Common\Lib\Settings::get("site.openid");
        $app->view()->setData("openid", $use_openid);
        
        $use_google_plus = Common\Lib\Settings::get("googlePlus.enabled");
        $app->view()->setData("gplus", $use_google_plus);
        $appendExtraScripts = False;
        if (isset($use_openid)) {
            if ($use_openid == "y" || $use_openid == "h") {
                $extra_scripts = "
                    <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/lib/openid-jquery.js\"></script>
                    <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/lib/openid-en.js\"></script>
                    <link type=\"text/css\" rel=\"stylesheet\" media=\"all\" href=\"{$app->urlFor("home")}resources/css/openid.css\" />";
                $appendExtraScripts = True;
            }
        }
        
        if (isset($use_google_plus) && ($use_google_plus == 'y')) {
            $extra_scripts = $extra_scripts.self::createGooglePlusJavaScript();
            $appendExtraScripts = True;
        }
        
        if ($appendExtraScripts) {
            $app->view()->appendData(array("extra_scripts" => $extra_scripts));
        }
        
        $error = null;
        $warning = null;
        if (\SolasMatch\UI\isValidPost($app)) {
            $post = $app->request()->post();
            $temp = md5($post['email'].substr(Common\Lib\Settings::get("session.site_key"), 0, 20));
            Common\Lib\UserSession::clearCurrentUserID();
            if (!Lib\Validator::validateEmail($post['email'])) {
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

        $app->view()->appendData(array(
            'client_id'    => Common\Lib\Settings::get('proz.client_id'),
            'redirect_uri' => urlencode(Common\Lib\Settings::get('proz.redirect_uri')),
        ));

        $app->render("user/register.tpl");
    }

    public function changeEmail($user_id)
    {
        $app = \Slim\Slim::getInstance();
        $userDao = new DAO\UserDao();
        $adminDao = new DAO\AdminDao();
        $loggedInUserId = Common\Lib\UserSession::getCurrentUserID();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $error = null;
        $warning = null;
        if ($app->request()->isPost() && sizeof($app->request()->post()) > 1) {
            $post = $app->request()->post();
            Common\Lib\UserSession::checkCSRFKey($post, 'changeEmail');

            if (!Lib\Validator::validateEmail($post['email'])) {
                $error = Lib\Localisation::getTranslation('register_1');
            } elseif ($userDao->getUserByEmail($post['email'])) {
                $error = Lib\Localisation::getTranslation('common_new_email_already_used');
            }

            if (is_null($error) && !is_null($loggedInUserId) && $adminDao->isSiteAdmin($loggedInUserId)) {
                if ($userDao->changeEmail($user_id, $post['email'])) {
                    $app->flashNow('success', '');
                } else {
                    $app->flashNow('error', '');
                }
            }
        }
        if ($error !== null) {
            $app->view()->appendData(array("error" => $error));
        }
        if ($warning !== null) {
            $app->view()->appendData(array("warning" => $warning));
        }

        $app->view()->appendData(array('user_id' => $user_id, 'sesskey' => $sesskey));
        $app->render("user/change-email.tpl");
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
                        $app->flash("success", Lib\Localisation::getTranslation('password_reset_2'));
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
        Common\Lib\UserSession::destroySession();
        $app->redirect($app->urlFor("home"));
    }

    public function login()
    {
        $app = \Slim\Slim::getInstance();
        $userDao = new DAO\UserDao();
        $langDao = new DAO\LanguageDao();
        
        $error = null;
        $openid = new \LightOpenID("https://".$_SERVER["HTTP_HOST"].$app->urlFor("home"));
        $use_openid = Common\Lib\Settings::get("site.openid");
        $use_google_plus = Common\Lib\Settings::get("googlePlus.enabled");
        $app->view()->setData("openid", $use_openid);
        $app->view()->setData("gplus", $use_google_plus);
        
        if ($app->request()->isPost() || $openid->mode) {
            $post = $app->request()->post();

            if (isset($post['login'])) {
                $user = null;
                try {
                    $user = $userDao->login($post['email'], $post['password']);
                } catch (Common\Exceptions\SolasMatchException $e) {
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

                    // Check have we previously been redirected from SAML to do login, if so get return address so we can redirect to it below
                    if (!$request) {
                        if (!empty($_SESSION['return_to_SAML_url'])) {
                            $request = $_SESSION['return_to_SAML_url'];
                        }
                    }
                    unset($_SESSION['return_to_SAML_url']);

                    //Set site language to user's preferred language if it is not already
                    $currentSiteLang = $langDao->getLanguageByCode(Common\Lib\UserSession::getUserLanguage());
                    $userInfo = $userDao->getPersonalInfo($user->getId());
                    $langPrefId = $userInfo->getLanguagePreference();
                    $preferredLang = $langDao->getLanguage($langPrefId);
                    if ($currentSiteLang != $preferredLang) {
                        Common\Lib\UserSession::setUserLanguage($preferredLang->getCode());
                    }
                    
                    //Redirect to homepage, or the page the page user was previously on e.g. if their
                    //session timed out and they are logging in again.
                    if ($request) {
                        $app->redirect($request);
                    } else {
                        $nativeLocale = $user->getNativeLocale();
                        if ($nativeLocale && $nativeLocale->getLanguageCode()) {
                            $app->redirect($app->urlFor("home"));
                        } else {
                            $app->redirect($app->urlFor('user-private-profile', array('user_id' => $user->getId())));
                        }
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

                // Check have we previously been redirected from SAML to do login, if so get return address so we can redirect to it below
                if (!$request) {
                    if (!empty($_SESSION['return_to_SAML_url'])) {
                        $request = $_SESSION['return_to_SAML_url'];
                    }
                }
                unset($_SESSION['return_to_SAML_url']);

                //Set site language to user's preferred language if it is not already
                $currentSiteLang = $langDao->getLanguageByCode(Common\Lib\UserSession::getUserLanguage());
                $userInfo = $userDao->getPersonalInfo($user->getId());
                $langPrefId = $userInfo->getLanguagePreference();
                $preferredLang = $langDao->getLanguage($langPrefId);
                if ($currentSiteLang != $preferredLang) {
                    Common\Lib\UserSession::setUserLanguage($preferredLang->getCode());
                }
                
                if ($request) {
                    $app->redirect($request);
                } else {
                    $nativeLocale = $user->getNativeLocale();
                    if ($nativeLocale && $nativeLocale->getLanguageCode()) {
                        $app->redirect($app->urlFor("home"));
                    } else {
                        $app->redirect($app->urlFor('user-private-profile', array('user_id' => $user->getId())));
                    }
                }
            }
            
            $params = $app->request()->params();
            if (isset($params["gplustoken"])) //if sign in using google plus
            {
                $access_token = $params["gplustoken"];
                if (!empty($access_token)) {
                    try {
                        $userDao->loginWithGooglePlus($access_token);
                    } catch (\Exception $e) {
                        $error = sprintf(
                            Lib\Localisation::getTranslation('gplus_error'),
                            $app->urlFor("login"),
                            $app->urlFor("register"),
                            "[".$e->getMessage()."]"
                        );
                        
                        if ($e->getCode() == 400 || $e->getMessage() != "") {
                            $app->flash('error', $error);
                            $app->redirect($app->urlFor('home'));
                        }
                    }
                } else {
                    $error = sprintf(
                            Lib\Localisation::getTranslation('gplus_error'),
                            $app->urlFor("login"),
                            $app->urlFor("register"),
                            "[An empty access token received.]"
                        );
                    $app->flash('error', $error);
                    $app->redirect($app->urlFor('home'));   
                }
            }

            $return_to_SAML_url = $app->request()->get('ReturnTo');
            if (!empty($return_to_SAML_url)) {
                $_SESSION['return_to_SAML_url'] = $return_to_SAML_url;
            }
            
            $error = $app->request()->get('error');
            if (!is_null($error)) {
                $app->flashNow('error', $app->request()->get('error_message'));
            }
        }

        $appendExtraScripts = False;
        $extra_scripts = "";
        if (isset($use_openid)) {
            if ($use_openid == "y" || $use_openid == "h") {
                $extra_scripts = "
        <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/lib/openid-jquery.js\"></script>
        <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/lib/openid-en.js\"></script>
        <link type=\"text/css\" rel=\"stylesheet\" media=\"all\" href=\"{$app->urlFor("home")}resources/css/openid.css\" />";
                $appendExtraScripts = True;
            }
        }
        
        if (isset($use_google_plus) && ($use_google_plus == 'y')) {
            $extra_scripts = $extra_scripts.self::createGooglePlusJavaScript();
            $appendExtraScripts = True;
        }
        
        if ($appendExtraScripts) {
            $app->view()->appendData(array("extra_scripts" => $extra_scripts));
        }

        $app->view()->appendData(array(
            'client_id'    => Common\Lib\Settings::get('proz.client_id'),
            'redirect_uri' => urlencode(Common\Lib\Settings::get('proz.redirect_uri')),
        ));

        $app->render("user/login.tpl");
    }

    public function login_proz()
    {
        $app = \Slim\Slim::getInstance();
        $userDao = new DAO\UserDao();

        $bad_message = '';

        $code = $app->request()->get('code');
        if (!empty($code)) {
            // Exchange the authorization code for an access token
            $client_id = Common\Lib\Settings::get('proz.client_id');
            $client_secret = Common\Lib\Settings::get('proz.client_secret');
            $redirect_uri = urlencode(Common\Lib\Settings::get('proz.redirect_uri'));

            $curl = curl_init('https://www.proz.com/oauth/token');
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, "$client_id:$client_secret");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=authorization_code&code=$code&redirect_uri=$redirect_uri");
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_AUTOREFERER, true);

            $curl_response = curl_exec($curl);

            $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            if ($responseCode == 200) {
                $response_data = json_decode($curl_response);

                $access_token = $response_data->access_token;

                $curl = curl_init('https://api.proz.com/v2/user');
                curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer $access_token"));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($curl, CURLOPT_AUTOREFERER, true);

                $curl_response = curl_exec($curl);

                $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                curl_close($curl);

                if ($responseCode == 200) {

                    $response_data = json_decode($curl_response);

                    if (!empty($response_data->email)) {
                        error_log("Got email from ProZ SSO: {$response_data->email}");

                        $userDao->requestAuthCode($response_data->email);
                        // This does not return,
                        // it redirects to API /v0/users/$email/auth/code
                        // which starts "normal" Trommons authorization process
                        // (and may register a user if the email is new),
                        // which then redirects to /login URL with a different Trommons 'code',
                        // which completes login and
                        // redirects to UserSession::getReferer() or home.
                    } else {
                        $bad_message = 'email not set /user';
                    }
                } else {
                    $bad_message = "BAD responseCode /user: $responseCode";
                }
            } else {
                $bad_message = "BAD responseCode /oauth/token: $responseCode";
            }
        } else {
            $bad_message = 'An empty access token received.';
        }

        $error = sprintf(
            Lib\Localisation::getTranslation('proz_error'),
            $app->urlFor('login'),
            $app->urlFor('register'),
            "[$bad_message]"
            );
        error_log($bad_message);

        $app->flash('error', $error);
        $app->redirect($app->urlFor('home'));
    }

    private static function createGooglePlusJavaScript()
    {
        $app = \Slim\Slim::getInstance();    
        $client_id = Common\Lib\Settings::get("googlePlus.client_id");
        $scope = Common\Lib\Settings::get("googlePlus.scope");
        $redirectUri = '';
        if (isset($_SERVER['HTTPS']) && !is_null($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $redirectUri = 'https://';
        } else {
            $redirectUri = 'http://';
        }
        $redirectUri .= $_SERVER['SERVER_NAME'].$app->urlFor('login');
        
        $script = <<<EOD
            <script type="text/javascript">
            function render() {
                gapi.signin.render('customGplusBtn', {
                    'callback': 'signInCallback',
                    'clientid': '$client_id',
                    'cookiepolicy': 'single_host_origin',
                    'scope': '$scope'
                });
            }
            function signInCallback(authResult) {
                if (authResult['code']) {
                    $('#gSignInWrapper').attr('style', 'display: none');
                    window.location.replace('$redirectUri?gplustoken='+authResult['access_token']);
                } else if (authResult['error']) {
                    if (authResult['error'] != 'immediate_failed') {
                        console.log('There was an error: ' + authResult['error']);
                    }
                }
            }
            
            </script>
            <script src="https://apis.google.com/js/client:platform.js?onload=render" async defer></script>
EOD;
        
        return $script;
    }
    
    public function openIdLogin($openid, $app)
    {
        if (!$openid->mode) {
            $openid->identity = $openid->data["openid_identifier"];
            $openid->required = array("contact/email");
            $url = $openid->authUrl();
            $app->redirect($openid->authUrl());
        } elseif ($openid->mode == "cancel") {
            $app->flash('error', (Lib\Localisation::getTranslation('login_2')));
            $app->redirect($app->urlFor('login'));
        } else {
            $retvals = $openid->getAttributes();
            if ($openid->validate()) {
                // Request Auth code and redirect
                $userDao = new DAO\UserDao();
                $userDao->requestAuthCode($retvals['contact/email']);
            }
        }
    }

    public static function userPrivateProfile($user_id)
    {
        $app = \Slim\Slim::getInstance();
        
        $userDao = new DAO\UserDao();
        $adminDao = new DAO\AdminDao();
        $langDao = new DAO\LanguageDao();
        $countryDao = new DAO\CountryDao();

        if (empty($_SESSION['SESSION_CSRF_KEY'])) {
            $_SESSION['SESSION_CSRF_KEY'] = UserRouteHandler::random_string(10);
        }
        $sesskey = $_SESSION['SESSION_CSRF_KEY']; // This is a check against CSRF (Posts should come back with same sesskey)

        $user = $userDao->getUser($user_id);
        Common\Lib\CacheHelper::unCache(Common\Lib\CacheHelper::GET_USER.$user_id);

        if (!is_object($user)) {
            $app->flash("error", Lib\Localisation::getTranslation('common_login_required_to_access_page'));
            $app->redirect($app->urlFor("login"));
        }

        $userPersonalInfo = null;
        try {
            $userPersonalInfo = $userDao->getPersonalInfo($user_id);
        } catch (Common\Exceptions\SolasMatchException $e) {
            // error_log("Error getting user personal info: $e");
        }

        $languages = $langDao->getLanguages();
        $countries = $countryDao->getCountries();

        $nativeLocale = $user->getNativeLocale();
        if ($nativeLocale) {
            $nativeLanguageSelectCode = $nativeLocale->getLanguageCode();
            $nativeCountrySelectCode = $nativeLocale->getCountryCode();
        }
        else {
            $nativeLanguageSelectCode = '999999999';
            $nativeCountrySelectCode = '999999999';
        }

        $userQualifiedPairs = $userDao->getUserQualifiedPairs($user_id);
        if (empty($userQualifiedPairs)) {
            $userQualifiedPairs[] = array('language_code_source' => '', 'country_code_source' => '--', 'language_code_target' => '', 'country_code_target' => '--', 'qualification_level' => 1);
        }
        $userQualifiedPairsCount = count($userQualifiedPairs);

        $langPref = $langDao->getLanguage($userPersonalInfo->getLanguagePreference());
        $langPrefSelectCode = $langPref->getCode();

        $badges = $userDao->getUserBadges($user_id);
        $translator = false;
        $proofreader = false;
        $interpreter = false;
        if (!empty($badges)) {
            foreach ($badges as $badge) {
                if ($badge->getId() == 6) {
                    $translator = true;
                } elseif ($badge->getId() == 7) {
                    $proofreader = true;
                } elseif ($badge->getId() == 8) {
                    $interpreter = true;
                }
            }
        }

        $loggedInUserId = Common\Lib\UserSession::getCurrentUserID();
        if (!is_null($loggedInUserId)) {
            $isSiteAdmin = $adminDao->isSiteAdmin($loggedInUserId);
        } else {
            $isSiteAdmin = false;
        }

        if ($post = $app->request()->post()) {
            if (empty($post['sesskey']) || $post['sesskey'] !== $sesskey || empty($post['displayName'])) {
                $app->flashNow('error', Lib\Localisation::getTranslation('user_private_profile_2'));
            } else {
                $user->setDisplayName($post['displayName']);
                $user->setBiography($post['biography']);

                if (!empty($post['nativeLanguageSelect']) && !empty($post['nativeCountrySelect'])) {
                    $locale = new Common\Protobufs\Models\Locale();
                    $locale->setLanguageCode($post['nativeLanguageSelect']);
                    $locale->setCountryCode($post['nativeCountrySelect']);
                    foreach ($languages as $language) {
                        if ($language->getCode() == $post['nativeLanguageSelect']) {
                            $locale->setLanguageName($language->getName());
                        }
                    }
                    foreach ($countries as $country) {
                        if ($country->getCode() == $post['nativeCountrySelect']) {
                            $locale->setCountryName($country->getName());
                        }
                    }
                    $user->setNativeLocale($locale);
                }

                if (!empty($post['langPrefSelect'])) {
                    $lang = $langDao->getLanguageByCode($post['langPrefSelect']);
                    $userPersonalInfo->setLanguagePreference($lang->getId());
                }

                $userPersonalInfo->setFirstName($post['firstName']);
                $userPersonalInfo->setLastName($post['lastName']);
                $userPersonalInfo->setMobileNumber($post['mobileNumber']);
                $userPersonalInfo->setBusinessNumber($post['businessNumber']);
                $userPersonalInfo->setJobTitle($post['jobTitle']);
                $userPersonalInfo->setAddress($post['address']);
                $userPersonalInfo->setCity($post['city']);
                $userPersonalInfo->setCountry($post['country']);

                if (!empty($post['receiveCredit'])) {
                    $userPersonalInfo->setReceiveCredit(true);
                } else {
                    $userPersonalInfo->setReceiveCredit(false);
                }

                try {
                    $i = 0;
                    while (!empty($post["language_code_source_$i"]) && !empty($post["language_code_target_$i"])) {
                        $post["language_code_source_$i"] = strtolower($post["language_code_source_$i"]); // Just in case browser is manipulated...
                        $post["language_code_target_$i"] = strtolower($post["language_code_target_$i"]);
                        $post["country_code_source_$i"]  = strtoupper($post["country_code_source_$i"]);
                        $post["country_code_target_$i"]  = strtoupper($post["country_code_target_$i"]);
                        if ($post["country_code_source_$i"] == '') $post["country_code_source_$i"] = '--'; // Any Language
                        if ($post["country_code_target_$i"] == '') $post["country_code_target_$i"] = '--';

                        $found = false;
                        foreach ($userQualifiedPairs as $userQualifiedPair) {
                            if (($post["language_code_source_$i"] == $userQualifiedPair['language_code_source']) &&
                                ($post["country_code_source_$i"]  == $userQualifiedPair['country_code_source'])  &&
                                ($post["language_code_target_$i"] == $userQualifiedPair['language_code_target']) &&
                                ($post["country_code_target_$i"]  == $userQualifiedPair['country_code_target'])) {
                                $found = true;

error_log($post["language_code_source_$i"] . $post["country_code_source_$i"] . $post["language_code_target_$i"] . $post["country_code_target_$i"] . $post["qualification_level_$i"]);
                                if ($isSiteAdmin && ($post["qualification_level_$i"] != $userQualifiedPair['qualification_level'])) {
error_log($post["language_code_source_$i"] . $post["country_code_source_$i"] . $post["language_code_target_$i"] . $post["country_code_target_$i"] . $post["qualification_level_$i"]);

                                    $userDao->updateUserQualifiedPair($user_id,
                                        $post["language_code_source_$i"], $post["country_code_source_$i"],
                                        $post["language_code_target_$i"], $post["country_code_target_$i"],
                                        $post["qualification_level_$i"]);
                                }
                            }
                        }
                        if (!$found) {
                            if (!$isSiteAdmin) $post["qualification_level_$i"] = 0;

                            $userDao->createUserQualifiedPair($user_id,
                                $post["language_code_source_$i"], $post["country_code_source_$i"],
                                $post["language_code_target_$i"], $post["country_code_target_$i"],
                                $post["qualification_level_$i"]);
                        }
                        $i++;
                    }

                    foreach ($userQualifiedPairs as $userQualifiedPair) {
                        $i = 0;
                        $found = false;
                        while (!empty($post["language_code_source_$i"]) && !empty($post["language_code_target_$i"])) {
                            if (($post["language_code_source_$i"] == $userQualifiedPair['language_code_source']) &&
                                ($post["country_code_source_$i"]  == $userQualifiedPair['country_code_source'])  &&
                                ($post["language_code_target_$i"] == $userQualifiedPair['language_code_target']) &&
                                ($post["country_code_target_$i"]  == $userQualifiedPair['country_code_target'])) {
                                $found = true;
                            }
                            $i++;
                        }
                        if (!$found) {
                            $userDao->removeUserQualifiedPair($user_id,
                                $userQualifiedPair['language_code_source'], $userQualifiedPair['country_code_source'],
                                $userQualifiedPair['language_code_target'], $userQualifiedPair['country_code_target']);
                        }
                    }

                    $userDao->updateUser($user);
                    $userDao->updatePersonalInfo($user_id, $userPersonalInfo);

                    if (isset($post['interval'])) {
                        if ($post['interval'] == 0) {
                            $userDao->removeTaskStreamNotification($user_id);
                        } else {
                            $notifData = new Common\Protobufs\Models\UserTaskStreamNotification();
                            $notifData->setUserId($user_id);
                            $notifData->setInterval($post['interval']);
                            if (isset($post['strictMode']) && $post['strictMode'] == 'enabled') {
                                $notifData->setStrict(true);
                            } else {
                                $notifData->setStrict(false);
                            }
                            $userDao->requestTaskStreamNotification($notifData);
                        }
                    }

                    if ($translator && empty($post['translator'])) {
                        $userDao->removeUserBadge($user_id, 6);
                    } elseif (!$translator && !empty($post['translator'])) {
                        $userDao->addUserBadgeById($user_id, 6);
                    }
                    if ($proofreader && empty($post['proofreader'])) {
                        $userDao->removeUserBadge($user_id, 7);
                    } elseif (!$proofreader && !empty($post['proofreader'])) {
                        $userDao->addUserBadgeById($user_id, 7);
                    }
                    if ($interpreter && empty($post['interpreter'])) {
                        $userDao->removeUserBadge($user_id, 8);
                    } elseif (!$interpreter && !empty($post['interpreter'])) {
                        $userDao->addUserBadgeById($user_id, 8);
                    }

                    $app->redirect($app->urlFor('user-public-profile', array('user_id' => $user_id)));
                } catch (\Exception $e) {
                    $app->flashNow('error', Lib\Localisation::getTranslation('user_private_profile_2'));
                }
            }
        }

        $notifData = $userDao->getUserTaskStreamNotification($user_id);
        if ($notifData) {
            if ($notifData->hasStrict()) {
                $strict = $notifData->getStrict();
            } else {
                $strict = false;
            }

            $app->view()->appendData(array(
                'intervalId' => $notifData->getInterval(),
                'strict'     => $strict,
            ));
        }

        $extra_scripts  = "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/Parameters.js\"></script>";
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/UserPrivateProfile.js\"></script>";

        $app->view()->appendData(array(
            'siteLocation'     => Common\Lib\Settings::get('site.location'),
            'siteAPI'          => Common\Lib\Settings::get('site.api'),
            'isSiteAdmin'      => $isSiteAdmin,
            'user'             => $user,
            'user_id'          => $user_id,
            'userPersonalInfo' => $userPersonalInfo,
            'languages' => $languages,
            'countries' => $countries,
            'nativeLanguageSelectCode' => $nativeLanguageSelectCode,
            'nativeCountrySelectCode'  => $nativeCountrySelectCode,
            'userQualifiedPairs'       => $userQualifiedPairs,
            'userQualifiedPairsCount'  => $userQualifiedPairsCount,
            'langPrefSelectCode'       => $langPrefSelectCode,
            'translator'  => $translator,
            'proofreader' => $proofreader,
            'interpreter' => $interpreter,
            'extra_scripts' => $extra_scripts,
            'sesskey'       => $sesskey,
        ));
       
        $app->render('user/user-private-profile.tpl');
    }

    /**
     * Generate and return a random string of the specified length.
     *
     * @param int $length The length of the string to be created.
     * @return string
     */
    private static function random_string($length=15) {
        $pool  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pool .= 'abcdefghijklmnopqrstuvwxyz';
        $pool .= '0123456789';
        $poollen = strlen($pool);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($pool, (mt_rand()%($poollen)), 1);
        }
        return $string;
    }

    public static function userPublicProfile($user_id)
    {
        $app = \Slim\Slim::getInstance();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();
        $adminDao = new DAO\AdminDao();
        $langDao = new DAO\LanguageDao();
        $loggedInUserId = Common\Lib\UserSession::getCurrentUserID();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        if (!is_null($loggedInUserId)) {
            $app->view()->setData("isSiteAdmin", $adminDao->isSiteAdmin($loggedInUserId));
        } else {
            $app->view()->setData('isSiteAdmin', 0);
        }
        $user = null;
        try {
            Common\Lib\CacheHelper::unCache(Common\Lib\CacheHelper::GET_USER.$user_id);
            $user = $userDao->getUser($user_id);
        } catch (Common\Exceptions\SolasMatchException $e) {
            $app->flash('error', Lib\Localisation::getTranslation('common_login_required_to_access_page'));
            $app->redirect($app->urlFor('login'));
        }
        $userPersonalInfo = null;
        try {
            $userPersonalInfo = $userDao->getPersonalInfo($user_id);
        } catch (Common\Exceptions\SolasMatchException $e) {
            // error_log("Error getting user personal info: $e");
        }
        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            Common\Lib\UserSession::checkCSRFKey($post, 'userPublicProfile');
            
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
                    
        $archivedJobs = $userDao->getUserArchivedTasks($user_id, 0, 10);
        $user_tags = $userDao->getUserTags($user_id);
        $user_orgs = $userDao->getUserOrgs($user_id);
        $badges = $userDao->getUserBadges($user_id);
        $userQualifiedPairs = $userDao->getUserQualifiedPairs($user_id);

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
        
        $numTaskTypes = Common\Lib\Settings::get("ui.task_types");
        $taskTypeColours = array();

        for ($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }

        if (isset($userPersonalInfo)) {
            $langPref = $langDao->getLanguage($userPersonalInfo->getLanguagePreference());
            $langPrefName = $langPref->getName();
        }
        else {
            $langPrefName = '';
        }
        
        $app->view()->appendData(array(
            'sesskey' => $sesskey,
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
            "langPrefName" => $langPrefName,
            "userQualifiedPairs" => $userQualifiedPairs,
            "taskTypeColours" => $taskTypeColours
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
                        $interval = Lib\Localisation::getTranslation('user_task_stream_notification_edit_daily');
                        break;
                    case Common\Enums\NotificationIntervalEnum::WEEKLY:
                        $interval = Lib\Localisation::getTranslation('user_task_stream_notification_edit_weekly');
                        break;
                    case Common\Enums\NotificationIntervalEnum::MONTHLY:
                        $interval = Lib\Localisation::getTranslation('user_task_stream_notification_edit_monthly'); 
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

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $user = $userDao->getUser($userId);

        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            Common\Lib\UserSession::checkCSRFKey($post, 'editTaskStreamNotification');

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
            'sesskey' => $sesskey,
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
