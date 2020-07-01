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
            '/register_track/:track_code/',
            array($this, 'register')
        )->via('GET', 'POST')->name('register_track');

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
            '/:key/key/',
            array($this, "profile_shared_with_key")
        )->name('shared_with_key');

        $app->get(
            "/:user_id/privateProfile/",
            array($middleware, "authUserIsLoggedInNoProfile"),
            array($this, "userPrivateProfile")
        )->via("POST")->name("user-private-profile");

        $app->get(
            '/:user_id/user-code-of-conduct/',
            array($middleware, 'authUserIsLoggedInNoProfile'),
            array($this, 'userCodeOfConduct')
        )->via("POST")->name('user-code-of-conduct');

        $app->get(
            '/:user_id/user-uploads/:cert_id/',
            array($middleware, 'authUserIsLoggedInNoProfile'),
            array($this, 'userUploads')
        )->via("POST")->name('user-uploads');

        $app->get(
            '/:id/user-download/',
            array($middleware, 'authUserIsLoggedIn'),
            array($this, 'userDownload')
        )->name('user-download');

        $app->get(
            '/users_review/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'users_review')
        )->name('users_review');

        $app->get(
            '/users_new/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'users_new')
        )->via('POST')->name('users_new');

        $app->get(
            '/users_tracked/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'users_tracked')
        )->name('users_tracked');

        $app->get(
            '/download_users_new/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'download_users_new')
        )->name('download_users_new');

        $app->get(
            '/download_users_new_unreviewed/',
            array($middleware, 'authIsSiteAdmin'),
            array($this, 'download_users_new_unreviewed')
        )->name('download_users_new_unreviewed');

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

        $app->get(
            '/no_application/',
            array($this, 'no_application')
        )->name('no_application');

        $app->get(
            '/no_application_error/',
            array($this, 'no_application_error')
        )->name('no_application_error');

        $app->get(
            '/neonwebhook/',
            array($this, 'neonwebhook')
        )->via('POST')->name('neonwebhook');
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
        $discourse_slug = array();
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
                $discourse_slug[$taskId] = $projectDao->discourse_parameterize($project);

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
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/Home2.js\"></script>";

        $org_admin = false;
        if (empty($topTasks) && !empty($user_id)) {
            $org_admin = $userDao->is_admin_or_org_member($user_id);
        }

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
            'discourse_slug' => $discourse_slug,
            'taskImages' => $taskImages,
            'currentScrollPage' => $currentScrollPage,
            'itemsPerScrollPage' => $itemsPerScrollPage,
            'lastScrollPage' => $lastScrollPage,
            'extra_scripts' => $extra_scripts,
            'user_id' => $user_id,
            'org_admin' => $org_admin,
        ));
        $app->render('index.tpl');
    }

    public function videos()
    {
        $app = \Slim\Slim::getInstance();
        $app->view()->appendData(array('current_page' => 'videos'));
        $app->render("videos.tpl");
    }

    public function register($track_code = '')
    {
        if (!empty($track_code)) $_SESSION['track_code'] = $track_code;

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
            $extra_scripts .= '<script type="text/javascript">function compareEmails() {if (document.getElementById("email").value != document.getElementById("email2").value) {window.alert("Entered emails must be identical."); return false;} return true;}</script>';
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
                    $userInfo = $userDao->getUserPersonalInformation($user->getId());
                    $langPrefId = $userInfo->getLanguagePreference();
                    $preferredLang = $langDao->getLanguage($langPrefId);
                    if ($currentSiteLang != $preferredLang) {
                        Common\Lib\UserSession::setUserLanguage($preferredLang->getCode());
                    }

                    $userDao->setRequiredProfileCompletedinSESSION($user->getId());

                    //Redirect to homepage, or the page the page user was previously on e.g. if their
                    //session timed out and they are logging in again.
                    if ($request) {
                        $app->redirect($request);
                    } else {
                      if ($userDao->is_admin_or_org_member($user->getId())) {
                          $app->redirect($app->urlFor('home'));
                      } else {
                        $nativeLocale = $user->getNativeLocale();
                        if ($nativeLocale && $nativeLocale->getLanguageCode()) {
                            $app->redirect($app->urlFor("home"));
                        } else {
                            $app->redirect($app->urlFor('user-private-profile', array('user_id' => $user->getId())));
                        }
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
                $userInfo = $userDao->getUserPersonalInformation($user->getId());
                $langPrefId = $userInfo->getLanguagePreference();
                $preferredLang = $langDao->getLanguage($langPrefId);
                if ($currentSiteLang != $preferredLang) {
                    Common\Lib\UserSession::setUserLanguage($preferredLang->getCode());
                }

                $userDao->setRequiredProfileCompletedinSESSION($user->getId());
                
                if ($request) {
                    $app->redirect($request);
                } else {
                  if ($userDao->is_admin_or_org_member($user->getId())) {
                      $app->redirect($app->urlFor('home'));
                  } else {
                    $nativeLocale = $user->getNativeLocale();
                    if ($nativeLocale && $nativeLocale->getLanguageCode()) {
                        $app->redirect($app->urlFor("home"));
                    } else {
                        $app->redirect($app->urlFor('user-private-profile', array('user_id' => $user->getId())));
                    }
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

        error_log("login_proz() Redirect from ProZ");

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
                gapi.signin2.render('g-signin2', {
                    scope: '$scope',
                    width: 219,
                    height: 36,
                    longtitle: true,
                    theme: 'dark',
                    onsuccess: onSignIn,
                    onfailure: onSignInFailure
                });
            }

            function onSignIn(googleUser) {
                $('#gSignInWrapper').attr('style', 'display: none');
                window.location.replace('$redirectUri?gplustoken=' + googleUser.getAuthResponse().id_token);
            }

            function onSignInFailure() {
                console.log('Google SignIn Failure');
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
        $projectDao = new DAO\projectDao();

        if (!empty($_SESSION['track_code'])) {
            $userDao->insert_tracked_registration($user_id, $_SESSION['track_code']);
            unset($_SESSION['track_code']);
        }

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
            $userPersonalInfo = $userDao->getUserPersonalInformation($user_id);
        } catch (Common\Exceptions\SolasMatchException $e) {
        }

        $languages = $langDao->getLanguages();
        $languages_array = [];
        foreach ($languages as $language) {
            $languages_array[$language->getCode()] = $language->getName();
        }
        $countries = $countryDao->getCountries();
        $language_selection = $projectDao->generate_language_selection();

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

        $langPref = $langDao->getLanguage($userPersonalInfo->getLanguagePreference());
        $langPrefSelectCode = $langPref->getCode();

        $url_list           = $userDao->getURLList($user_id);
        $capability_list    = $userDao->getCapabilityList($user_id);
        $expertise_list     = $userDao->getExpertiseList($user_id);
        $howheard_list      = $userDao->getHowheardList($user_id);
        $certification_list = $userDao->getCertificationList($user_id);

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

                if (!empty($post['nativeLanguageSelect'])) {
                    if (empty($post['nativeCountrySelect'])) $post['nativeCountrySelect'] = '--';
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
                //$userPersonalInfo->setBusinessNumber($post['businessNumber']);
                //$userPersonalInfo->setJobTitle($post['jobTitle']);
                //$userPersonalInfo->setAddress($post['address']);
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
                        list($language_code_source, $country_code_source) = $projectDao->convert_selection_to_language_country($post["language_code_source_$i"]);
                        list($language_code_target, $country_code_target) = $projectDao->convert_selection_to_language_country($post["language_code_target_$i"]);

                        $language_code_source = strtolower($language_code_source); // Just in case browser is manipulated...
                        $language_code_target = strtolower($language_code_target);
                        $country_code_source  = strtoupper($country_code_source);
                        $country_code_target  = strtoupper($country_code_target);
                        if ($country_code_source == '') $country_code_source = '--'; // Any Language
                        if ($country_code_target == '') $country_code_target = '--';

                        $found = false;
                        foreach ($userQualifiedPairs as $userQualifiedPair) {
                            if (($language_code_source == $userQualifiedPair['language_code_source']) &&
                                ($country_code_source  == $userQualifiedPair['country_code_source'])  &&
                                ($language_code_target == $userQualifiedPair['language_code_target']) &&
                                ($country_code_target  == $userQualifiedPair['country_code_target'])) {
                                $found = true;

                                if ($isSiteAdmin && ($post["qualification_level_$i"] != $userQualifiedPair['qualification_level'])) {
                                    $userDao->updateUserQualifiedPair($user_id,
                                        $language_code_source, $country_code_source,
                                        $language_code_target, $country_code_target,
                                        $post["qualification_level_$i"]);
                                }
                            }
                        }
                        if (!$found) {
                            if (!$isSiteAdmin) $post["qualification_level_$i"] = 1;

                            if (!$isSiteAdmin && empty($userQualifiedPairs)) { // First time through here for ordinary registrant
                                if ($userDao->get_tracked_registration_for_verified($user_id)) {
                                    if (!empty($post['nativeLanguageSelect']) && ($language_code_target === $post['nativeLanguageSelect'])) { // Only make verified if target matches native language 
                                        $post["qualification_level_$i"] = 2; // Verified Translator
                                    }
                                }
                            }

                            $userDao->createUserQualifiedPair($user_id,
                                $language_code_source, $country_code_source,
                                $language_code_target, $country_code_target,
                                $post["qualification_level_$i"]);
                        }
                        $i++;
                    }

                    foreach ($userQualifiedPairs as $userQualifiedPair) {
                        $i = 0;
                        $found = false;
                        while (!empty($post["language_code_source_$i"]) && !empty($post["language_code_target_$i"])) {
                            list($language_code_source, $country_code_source) = $projectDao->convert_selection_to_language_country($post["language_code_source_$i"]);
                            list($language_code_target, $country_code_target) = $projectDao->convert_selection_to_language_country($post["language_code_target_$i"]);

                            if (($language_code_source == $userQualifiedPair['language_code_source']) &&
                                ($country_code_source  == $userQualifiedPair['country_code_source'])  &&
                                ($language_code_target == $userQualifiedPair['language_code_target']) &&
                                ($country_code_target  == $userQualifiedPair['country_code_target'])) {
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

                    foreach ($url_list as $name => $url) {
                        if ($post[$name] != $url['state']) $userDao->insertUserURL($user_id, $name, $post[$name]);
                    }

                    $userDao->updateUser($user);
                    $userDao->updatePersonalInfo($user_id, $userPersonalInfo);

                    if (isset($post['interval'])) {
                        if ($post['interval'] == 0 || $post['interval'] == 10) {
                            $userDao->removeTaskStreamNotification($user_id);
                            if ($post['interval'] == 10 && $isSiteAdmin) $userDao->set_special_translator($user_id, 1);
                        } else {
                            $notifData = new Common\Protobufs\Models\UserTaskStreamNotification();
                            $notifData->setUserId($user_id);
                            $notifData->setInterval($post['interval']);
                            //if (isset($post['strictMode']) && $post['strictMode'] == 'enabled') {
                                $notifData->setStrict(true);
                            //} else {
                            //    $notifData->setStrict(false);
                            //}
                            $userDao->requestTaskStreamNotification($notifData);
                            if ($isSiteAdmin) $userDao->set_special_translator($user_id, 0);
                        }
                    }

                    foreach ($capability_list as $name => $capability) {
                        if ($capability['state'] && empty($post[$name])) {
                            $userDao->removeUserBadge($user_id, $capability['id']);
                        } elseif (!$capability['state'] && !empty($post[$name])) {
                            $userDao->addUserBadgeById($user_id, $capability['id']);
                        }
                    }

                    foreach ($expertise_list as $name => $expertise) {
                        if ($expertise['state'] && empty($post[$name])) {
                            $userDao->removeUserExpertise($user_id, $name);
                        } elseif (!$expertise['state'] && !empty($post[$name])) {
                            $userDao->addUserExpertise($user_id, $name);
                        }
                    }

                    if (!empty($post['howheard'])) $userDao->insertUserHowheard($user_id, $post['howheard']);

                    $userDao->update_terms_accepted($user_id);

                    $app->redirect($app->urlFor('user-public-profile', array('user_id' => $user_id)));
                } catch (\Exception $e) {
                    $app->flashNow('error', 'Failed to Update');
                }
            }
        }

        $notifData = $userDao->getUserTaskStreamNotification($user_id);
        if ($notifData) {
            $strict = $notifData->getStrict();

            $app->view()->appendData(array(
                'intervalId' => $notifData->getInterval(),
                'strict'     => $strict,
            ));
        }

        $extra_scripts  = "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/Parameters.js\"></script>";
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/UserPrivateProfile4.js\"></script>";

        foreach ($userQualifiedPairs as $index => $userQualifiedPair) {
            $userQualifiedPairs[$index]['language_code_source'] = $userQualifiedPair['language_code_source'] . '-' . $userQualifiedPair['country_code_source'];
            $userQualifiedPairs[$index]['language_code_target'] = $userQualifiedPair['language_code_target'] . '-' . $userQualifiedPair['country_code_target'];
            if (empty($language_selection[$userQualifiedPairs[$index]['language_code_source']])) $language_selection[$userQualifiedPairs[$index]['language_code_source']] = $languages_array[$userQualifiedPair['language_code_source']] . ($userQualifiedPair['country_code_source'] === '--' ? '' : ('-' . $userQualifiedPair['country_code_source']));
            if (empty($language_selection[$userQualifiedPairs[$index]['language_code_target']])) $language_selection[$userQualifiedPairs[$index]['language_code_target']] = $languages_array[$userQualifiedPair['language_code_target']] . ($userQualifiedPair['country_code_target'] === '--' ? '' : ('-' . $userQualifiedPair['country_code_target']));
        }
        if (empty($userQualifiedPairs)) {
            $userQualifiedPairs[] = array('language_code_source' => '', 'language_code_target' => '', 'qualification_level' => 1);
        }

        $app->view()->appendData(array(
            'siteLocation'     => Common\Lib\Settings::get('site.location'),
            'siteAPI'          => Common\Lib\Settings::get('site.api'),
            'isSiteAdmin'      => $isSiteAdmin,
            'user'             => $user,
            'user_id'          => $user_id,
            'userPersonalInfo' => $userPersonalInfo,
            'languages' => $languages,
            'countries' => $countries,
            'language_selection' => $language_selection,
            'nativeLanguageSelectCode' => $nativeLanguageSelectCode,
            'nativeCountrySelectCode'  => $nativeCountrySelectCode,
            'userQualifiedPairs'       => $userQualifiedPairs,
            'userQualifiedPairsLimit'  => $isSiteAdmin ? 120 : max(6, count($userQualifiedPairs)),
            'userQualifiedPairsCount'  => count($userQualifiedPairs),
            'langPrefSelectCode'       => $langPrefSelectCode,
            'url_list'          => $url_list,
            'capability_list'   => $capability_list,
            'capabilityCount'   => count($capability_list),
            'expertise_list'    => $expertise_list,
            'expertiseCount'    => count($expertise_list),
            'howheard_list'     => $howheard_list,
            'certification_list' => $certification_list,
            'in_kind'           => $userDao->get_special_translator($user_id),
            'profile_completed' => !empty($_SESSION['profile_completed']),
            'extra_scripts' => $extra_scripts,
            'sesskey'       => $sesskey,
        ));
       
        $app->render('user/user-private-profile.tpl');
    }

    public static function userCodeOfConduct($user_id)
    {
        $app = \Slim\Slim::getInstance();

        $userDao = new DAO\UserDao();
        $adminDao = new DAO\AdminDao();

        if (!$userDao->is_admin_or_org_member($user_id)) {
            $app->redirect($app->urlFor('user-private-profile', array('user_id' => $user_id)));
        }

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
            $userPersonalInfo = $userDao->getUserPersonalInformation($user_id);
        } catch (Common\Exceptions\SolasMatchException $e) {
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
                $userPersonalInfo->setFirstName($post['firstName']);
                $userPersonalInfo->setLastName($post['lastName']);

                try {
                    $userDao->updateUser($user);
                    $userDao->updatePersonalInfo($user_id, $userPersonalInfo);

                    $userDao->update_terms_accepted($user_id);

                    $app->redirect($app->urlFor('org-dashboard'));
                } catch (\Exception $e) {
                    $app->flashNow('error', 'Failed to Update');
                }
            }
        }

        $extra_scripts  = "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/Parameters.js\"></script>";
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/user-code-of-conduct.js\"></script>";

        $app->view()->appendData(array(
            'siteLocation'      => Common\Lib\Settings::get('site.location'),
            'siteAPI'           => Common\Lib\Settings::get('site.api'),
            'isSiteAdmin'       => $isSiteAdmin,
            'user'              => $user,
            'user_id'           => $user_id,
            'userPersonalInfo'  => $userPersonalInfo,
            'profile_completed' => !empty($_SESSION['profile_completed']),
            'extra_scripts'     => $extra_scripts,
            'sesskey'           => $sesskey,
        ));

        $app->render('user/user-code-of-conduct.tpl');
    }

    public static function userUploads($user_id, $cert_id)
    {
        $app = \Slim\Slim::getInstance();

        $userDao = new DAO\UserDao();
        $adminDao = new DAO\AdminDao();

        if (empty($_SESSION['SESSION_CSRF_KEY'])) {
            $_SESSION['SESSION_CSRF_KEY'] = UserRouteHandler::random_string(10);
        }
        $sesskey = $_SESSION['SESSION_CSRF_KEY']; // This is a check against CSRF (Posts should come back with same sesskey)

        $loggedInUserId = Common\Lib\UserSession::getCurrentUserID();
        if ($user_id != $loggedInUserId && !$adminDao->isSiteAdmin($loggedInUserId)) return;

        $user = $userDao->getUser($user_id);

        $extra_scripts = '';

        $upload_pending = 1;
        if ($post = $app->request()->post()) {
            if (empty($post['sesskey']) || $post['sesskey'] !== $sesskey || empty($post['note']) || empty($_FILES['userFile']['name']) || !empty($_FILES['userFile']['error'])
                    || (($data = file_get_contents($_FILES['userFile']['tmp_name'])) === false)) {
                $app->flashNow('error', 'Could not upload file, you must specify a file and a note');
            } else {
                $userFileName = $_FILES['userFile']['name'];
                $extensionStartIndex = strrpos($userFileName, '.');
                if ($extensionStartIndex > 0) {
                    $extension = substr($userFileName, $extensionStartIndex + 1);
                    $extension = strtolower($extension);
                    $userFileName = substr($userFileName, 0, $extensionStartIndex + 1) . $extension;
                }
                $userDao->saveUserFile($user_id, $cert_id, $post['note'], $userFileName, $data);
                $upload_pending = 0;
                $app->flashNow('success', 'Certificate uploaded sucessfully, please click <a href="javascript:window.close();">Close Window</a>');
            }
        }

        $certification_list = $userDao->getCertificationList($user_id);

        $app->view()->appendData(array(
            'user'          => $user,
            'user_id'       => $user_id,
            'cert_id'       => $cert_id,
            'desc'          => empty($certification_list[$cert_id]['desc']) ? '' : $certification_list[$cert_id]['desc'],
            'upload_pending'=> $upload_pending,
            'sesskey'       => $sesskey,
        ));

        $app->render('user/user-uploads.tpl');
    }

    public static function userDownload($id)
    {
        $userDao = new DAO\UserDao();
        $adminDao = new DAO\AdminDao();

        $certification = $userDao->getUserCertificationByID($id);

        $loggedInUserId = Common\Lib\UserSession::getCurrentUserID();
        if (empty($certification) || ($certification['user_id'] != $loggedInUserId && !$adminDao->isSiteAdmin($loggedInUserId))) return;

        $userDao->userDownload($certification);
    }

    public function users_review()
    {
        $app = \Slim\Slim::getInstance();
        $userDao = new DAO\UserDao();

        $all_users = $userDao->users_review();

        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('user/users_review.tpl');
    }

    public function users_new()
    {
        $app = \Slim\Slim::getInstance();
        $userDao = new DAO\UserDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $all_users = $userDao->users_new();

        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            Common\Lib\UserSession::checkCSRFKey($post, 'users_new');
            if (!empty($post['max_user_id'])) {
                foreach ($all_users as $user_row) {
                    if ($user_row['user_id'] <= $post['max_user_id']) { // Make sure a new one has not appeared
                        if (empty($user_row['reviewed_text'])) $userDao->updateUserHowheard($user_row['user_id'], 1);
                    }
                }
                $all_users = $userDao->users_new();
            }
        }

        $app->view()->appendData(array('all_users' => $all_users, 'sesskey' => $sesskey));
        $app->render('user/users_new.tpl');
    }

    public function download_users_new()
    {
        $this->download_users_new_unreviewed(true);
    }

    public function download_users_new_unreviewed($all = false)
    {
        $userDao = new DAO\UserDao();
        $all_users = $userDao->users_new();

        $data = "\xEF\xBB\xBF" . '"Name","Created","Native Language","Language Pairs","Biography","Certificates","Email"' . "\n";

        foreach ($all_users as $user_row) {
          if ($all || empty($user_row['reviewed_text'])) {
            $data .= '"' . str_replace('"', '""', $user_row['name']) . '","' .
                $user_row['created_time'] . '","' .
                str_replace('"', '""', $user_row['native_language']) . '","' .
                $user_row['language_pairs'] . '","' .
                str_replace(array('\r\n', '\n', '\r'), "\n", str_replace('"', '""', $user_row['bio'])) . '","' .
                $user_row['certificates'] . '","' .
                $user_row['email'] . '"' . "\n";
          }
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="users_new.csv"');
        header('Content-length: ' . strlen($data));
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');
        echo $data;
        die;
    }

    public function users_tracked()
    {
        $app = \Slim\Slim::getInstance();
        $userDao = new DAO\UserDao();
        $all_users = $userDao->users_tracked();
        $app->view()->appendData(array('all_users' => $all_users));
        $app->render('user/users_tracked.tpl');
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
        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();
        $loggedInUserId = Common\Lib\UserSession::getCurrentUserID();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        if (!is_null($loggedInUserId)) {
            $isSiteAdmin = $adminDao->isSiteAdmin($loggedInUserId);
            $app->view()->setData('isSiteAdmin', $isSiteAdmin);
        } else {
            $isSiteAdmin = 0;
            $app->view()->setData('isSiteAdmin', 0);
        }

        $private_access = 0;
        if (Common\Lib\UserSession::getCurrentUserID() == $user_id) {
            $private_access = 1;
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
        $receive_credit = 0;
        try {
            $userPersonalInfo = $userDao->getUserPersonalInformation($user_id);
            if ($userPersonalInfo->getReceiveCredit()) $receive_credit = 1;
        } catch (Common\Exceptions\SolasMatchException $e) {
            // error_log("Error getting user personal info: $e");
        }

        $testing_center_projects_by_code = [];
        $testing_center_projects = $projectDao->get_testing_center_projects($user_id, $testing_center_projects_by_code);

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

            if ($isSiteAdmin && !empty($post['admin_comment'])) {
                if (empty($post['comment']) || (int)$post['work_again'] < 1 || (int)$post['work_again'] > 5) {
                    $app->flashNow('error', 'You must enter a comment and a score between 1 and 5');
                } else {
                    $userDao->insert_admin_comment($user_id, $loggedInUserId, (int)$post['work_again'], $post['comment']);
                }
            }

            if ($isSiteAdmin && !empty($post['mark_comment_delete'])) {
                $userDao->delete_admin_comment($post['comment_id']);
            }

            if ($isSiteAdmin && !empty($post['mark_reviewed'])) {
                $userDao->updateUserHowheard($user_id, 1);
            }

            if ($isSiteAdmin && !empty($post['mark_certification_reviewed'])) {
                $userDao->updateCertification($post['certification_id'], 1);
            }

            if ($isSiteAdmin && !empty($post['mark_certification_delete'])) {
                $userDao->deleteCertification($post['certification_id']);
            }

            if (($private_access || $isSiteAdmin) && !empty($post['source_language_country']) && !empty($post['target_language_country'])) {
                // Verification System Project for this User
                $project_route_handler = new ProjectRouteHandler();

                $source_language_country = $project_route_handler->valid_language_for_matecat($post['source_language_country']);
                $language_code_source = substr($source_language_country, 0, strpos($source_language_country, '-'));
                $country_code_source  = substr($source_language_country, strpos($source_language_country, '-') + 1);

                $target_language_country = $project_route_handler->valid_language_for_matecat($post['target_language_country']);
                $language_code_target = substr($target_language_country, 0, strpos($target_language_country, '-'));
                $country_code_target  = substr($target_language_country, strpos($target_language_country, '-') + 1);

                if (!empty($source_language_country) && !empty($target_language_country) &&
                    (empty($testing_center_projects_by_code["$language_code_source-$language_code_target"]) || $isSiteAdmin)) { // Protect against browser manipulation or duplicate
                    $user_id_owner = 62927; // translators@translatorswithoutborders.org
$user_id_owner = 25016;//DEV SERVER

                    $projects_to_copy = [9149, 9151, 9152, 9153];
                    $n = count($projects_to_copy);
                    $test_number = mt_rand(0, $n - 1); // Pick a random $projects_to_copy test file
                    $i = $n;
                    while ($i--) {
                        if (empty($testing_center_projects[$projects_to_copy[$test_number]])) break; // Found test file not already used
                        $test_number = ($test_number + 1) % $n;
                    }
                    if ($i < 0) {
                        $app->flashNow('error', "Unable to create test project for $user_id, no projects");
                        error_log("Unable to create test project for $user_id, no projects");
                    } else {
                        $project_to_copy_id = $projects_to_copy[$test_number];

                        $project = new Common\Protobufs\Models\Project();
                        $project->setTitle('Test' . UserRouteHandler::random_string(4));
                        $project->setOrganisationId(643); // TWB Community&Recruitment
$project->setOrganisationId(380);//DEV SERVER
                        $project->setCreatedTime(gmdate('Y-m-d H:i:s'));
                        $project->setDeadline(gmdate('Y-m-d H:i:s', strtotime('25 days'))); // 10 days for Translation + 14 for Revision added + 1 to get to Project Deadline
                        $project->setDescription('-');
                        $project->setImpact('-');
                        $project->setReference('');
                        $project->setWordCount(1);

                        $sourceLocale = new Common\Protobufs\Models\Locale();
                        $sourceLocale->setLanguageCode($language_code_source);
                        $sourceLocale->setCountryCode($country_code_source);
                        $project->setSourceLocale($sourceLocale);

                        $project = $projectDao->createProjectDirectly($project);
                        if (empty($project)) {
                            $app->flashNow('error', "Unable to create test project for $user_id");
                            error_log("Unable to create test project for $user_id");
                        } else {
                            $project_id = $project->getId();

                            list($filename, $mime) = $projectDao->copy_project_file($project_to_copy_id, $project_id, $user_id_owner);

                            $translation_task_id = $projectDao->addProjectTask(
                                $project_to_copy_id,
                                $filename,
                                $mime,
                                $project,
                                $language_code_target,
                                $country_code_target,
                                Common\Enums\TaskTypeEnum::TRANSLATION,
                                0,
                                $user_id_owner,
                                $taskDao);
                            $proofreading_task_id = $projectDao->addProjectTask(
                                $project_to_copy_id,
                                $filename,
                                $mime,
                                $project,
                                $language_code_target,
                                $country_code_target,
                                Common\Enums\TaskTypeEnum::PROOFREADING,
                                $translation_task_id,
                                $user_id_owner,
                                $taskDao);

                            $projectDao->calculateProjectDeadlines($project_id);

                            $taskDao->insertWordCountRequestForProjects($project_id, $source_language_country, $target_language_country, 0);
                            $taskDao->insertMatecatLanguagePairs($translation_task_id,  $project_id, Common\Enums\TaskTypeEnum::TRANSLATION,  "$source_language_country|$target_language_country");
                            $taskDao->insertMatecatLanguagePairs($proofreading_task_id, $project_id, Common\Enums\TaskTypeEnum::PROOFREADING, "$source_language_country|$target_language_country");

                            $mt_engine        = '0';
                            $pretranslate_100 = '0';
                            $lexiqa           = '0';
                            $private_tm_key   = 'new';
                            $taskDao->set_project_tm_key($project_id, $mt_engine, $pretranslate_100, $lexiqa, $private_tm_key);

                            $projectDao->insert_testing_center_project($user_id, $project_id, $translation_task_id, $proofreading_task_id, $project_to_copy_id, $language_code_source, $language_code_target);

                            $userDao->queue_claim_task($user_id, $translation_task_id);

                            // Asana 4th Project
                            $re = curl_init('https://app.asana.com/api/1.0/tasks');
                            curl_setopt($re, CURLOPT_POSTFIELDS, array(
                                'name' => $user->getEmail(),
                                'notes' => ' https://' . $_SERVER['SERVER_NAME'] . "/$user_id/profile , Target: $language_code_target, Deadline: " . gmdate('Y-m-d H:i:s', strtotime('10 days')) . ' https://' . $_SERVER['SERVER_NAME'] . "/project/$project_id/view",
                                'projects' => '1127940658676844'
                                )
                            );

                            curl_setopt($re, CURLOPT_CUSTOMREQUEST, 'POST');
                            curl_setopt($re, CURLOPT_HEADER, true);
                            curl_setopt($re, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . Common\Lib\Settings::get('asana.api_key4')));
                            curl_setopt($re, CURLOPT_RETURNTRANSFER, true);
                            curl_exec($re);
                            if ($error_number = curl_errno($re)) {
                              error_log("Asana 4 API error ($error_number): " . curl_error($re));
                            }
                            curl_close($re);

                            $app->flashNow('success', '<a href="' . $app->urlFor('task-view', ['task_id' => $translation_task_id]) .
                            '">This is your Translation Test</a>, which you <strong>must</strong> translate using Kató TM. You will find the <strong>Translate using Kató TM</strong> button under the Translation Test task in your <strong>Claimed Tasks</strong> section, which you can find in the upper menu. You will need to refresh that page after a few minutes in order to see the button. Please check your email inbox in a few minutes for instructions on completing the test');
                        }
                    }
                }
            }
        }
                    
        $archivedJobs = $userDao->getUserArchivedTasks($user_id, 0, 10);
        $user_tags = $userDao->getUserTags($user_id);
        $user_orgs = $userDao->getUserOrgs($user_id);
        $badges = $userDao->getUserBadges($user_id);
        $userQualifiedPairs = $userDao->getUserQualifiedPairs($user_id);

        $orgList = array();
        if ($badges) {
            foreach ($badges as $index => $badge) {
                if ($badge->getOwnerId() != null) {
                    $org = $orgDao->getOrganisation($badge->getOwnerId());
                    $orgList[$badge->getOwnerId()] = $org;
                } else {
                    unset($badges[$index]);
                }
            }
        }
       
        $org_creation = Common\Lib\Settings::get("site.organisation_creation");
            
        $extra_scripts = "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}";
        $extra_scripts .= "resources/bootstrap/js/confirm-remove-badge.js\"></script>";
        $extra_scripts .= file_get_contents(__DIR__."/../js/profile.js");
        
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

        if ($private_access || $isSiteAdmin) {
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
            ));
        }

        $certificate = '';
        if ($private_access || $isSiteAdmin) {
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
            $encrypted = openssl_encrypt("$user_id", 'aes-256-cbc', base64_decode(Common\Lib\Settings::get('badge.key')), 0, $iv);
            $certificate = 'https://badge.translatorswb.org/index.php?volunteer_id=' . urlencode(base64_encode("$encrypted::$iv"));
        }

        $euser_id = $user_id + 999999; // Ensure we don't use identical (shared profile) key as word count badge (for a bit of extra security)
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt("$euser_id", 'aes-256-cbc', base64_decode(Common\Lib\Settings::get('badge.key')), 0, $iv);
        $key = bin2hex("$encrypted::$iv");

        $howheard = $userDao->getUserHowheards($user_id);
        if (empty($howheard)) {
            $howheard = ['reviewed' => 1, 'howheard_key' => ''];
        } else {
            $howheard = $howheard[0];
        }

        $app->view()->appendData(array(
            'certificate'            => $certificate,
            'key'                    => $key,
            'private_access'         => $private_access,
            'receive_credit'         => $receive_credit,
            'is_admin_or_org_member' => $userDao->is_admin_or_org_member($user_id),
            'howheard'               => $howheard,
            'url_list'               => $userDao->getURLList($user_id),
            'expertise_list'         => $userDao->getExpertiseList($user_id),
            'capability_list'        => $userDao->getCapabilityList($user_id),
            'supported_ngos'         => $userDao->supported_ngos($user_id),
            'quality_score'          => $userDao->quality_score($user_id),
            'admin_comments'         => $userDao->admin_comments($user_id),
            'certifications'         => $userDao->getUserCertifications($user_id),
            'tracked_registration'   => $userDao->get_tracked_registration($user_id),
            'testing_center_projects_by_code' => $testing_center_projects_by_code,
        ));

        $app->render("user/user-public-profile.tpl");
    }

    public static function profile_shared_with_key($key)
    {
        $key = hex2bin($key);
        $iv = substr($key, -16);
        $encrypted = substr($key, 0, -18);
        $user_id = (int)openssl_decrypt($encrypted, 'aes-256-cbc', base64_decode(Common\Lib\Settings::get('badge.key')), 0, $iv);
        $user_id -= 999999; // Ensure we don't use identical key to word count badge

        $app = \Slim\Slim::getInstance();
        $userDao = new DAO\UserDao();

        $user = $userDao->getUser($user_id);
        $userPersonalInfo = $userDao->getUserPersonalInformation($user_id);
        $userQualifiedPairs = $userDao->getUserQualifiedPairs($user_id);

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt("$user_id", 'aes-256-cbc', base64_decode(Common\Lib\Settings::get('badge.key')), 0, $iv);
        $certificate = 'https://badge.translatorswb.org/index.php?volunteer_id=' . urlencode(base64_encode("$encrypted::$iv"));

        $app->view()->appendData(array(
            'current_page' => 'user-profile',
            'this_user' => $user,
            'userPersonalInfo' => $userPersonalInfo,
            'userQualifiedPairs' => $userQualifiedPairs,
            'certificate' => $certificate,
            'isSiteAdmin'            => 0,
            'private_access'         => 0,
            'receive_credit'         => 1,
            'no_header'              => 1,
            'url_list'               => $userDao->getURLList($user_id),
            'expertise_list'         => $userDao->getExpertiseList($user_id),
            'capability_list'        => $userDao->getCapabilityList($user_id),
            'supported_ngos'         => $userDao->supported_ngos($user_id),
            'quality_score'          => $userDao->quality_score($user_id),
            'certifications'         => $userDao->getUserCertifications($user_id),
        ));

        $app->render('user/user-public-profile.tpl');
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
                    //if (isset($post['strictMode']) && $post['strictMode'] == 'enabled') {
                        $notifData->setStrict(true);
                    //} else {
                    //    $notifData->setStrict(false);
                    //}
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

            $strict = $notifData->getStrict();

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

    public function no_application()
    {
        $app = \Slim\Slim::getInstance();
        $app->render('user/no_application.tpl');
    }

    public function no_application_error()
    {
        $app = \Slim\Slim::getInstance();
        $app->render('user/no_application_error.tpl');
    }

    public function neonwebhook()
    {
        $userDao = new DAO\UserDao();
        $userDao->process_neonwebhook();
    }
}

$route_handler = new UserRouteHandler();
$route_handler->init();
unset ($route_handler);
