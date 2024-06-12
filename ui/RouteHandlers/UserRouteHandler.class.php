<?php

namespace SolasMatch\UI\RouteHandlers;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\Common as Common;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/../DataAccessObjects/UserDao.class.php";
require_once __DIR__ . "/../../Common/protobufs/models/Register.php";
require_once __DIR__ . "/../../Common/protobufs/models/Login.php";
require_once __DIR__ . "/../../Common/protobufs/models/Locale.php";

class UserRouteHandler
{
    public function init()
    {
        global $app;

        $app->map(['GET', 'POST'],
            '[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:home')
            ->setName('home');

        $app->map(['GET', 'POST'],
            '/paged/{page_no}/tt/{tt}/sl/{sl}/tl/{tl}[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:homeIndex')
            ->setName('home-paged');

        $app->map(['GET', 'POST'],
            '/register[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:register')
            ->setName('register');

        $app->map(['GET', 'POST'],
            '/register_track/{track_code}[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:register')
            ->setName('register_track');

        $app->map(['GET', 'POST'],
            '/special_registration/{reg_data}[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:register')
            ->setName('special_registration');

        $app->map(['GET', 'POST'],
            '/{user_id}/change_email[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:changeEmail')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('change-email');

        $app->map(['GET', 'POST'],
            '/user/{uuid}/verification[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:emailVerification')
            ->setName('email-verification');

        $app->map(['GET', 'POST'],
            '/{uuid}/password/reset[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:passwordReset')
            ->setName('password-reset');

        $app->map(['GET', 'POST'],
            '/password/reset[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:request_password_reset')
            ->setName('password-reset-request');

        $app->get(
            '/logout[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:logout')
            ->setName('logout');

        $app->map(['GET', 'POST'],
            '/login[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:login')
            ->setName('login');

        $app->map(['GET', 'POST'],
            '/{user_id}/profile[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:userPublicProfile')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->setName('user-public-profile');

        $app->get(
            '/{key}/key[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:profile_shared_with_key')
            ->setName('shared_with_key');

        $app->get(
            '/{key}/bkey[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:badge_shared_with_key')
            ->setName('badge_shared_with_key');

        $app->map(['GET', 'POST'],
            '/set_paid_eligible_pair/{user_id}/sl/{sl}/sc/{sc}/tl/{tl}/tc/{tc}/eligible/{eligible}[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:set_paid_eligible_pair')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_or_COMMUNITY')
            ->setName('set_paid_eligible_pair');

        $app->map(['GET', 'POST'],
            '/{user_id}/privateProfile[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:userPrivateProfile')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedInNoProfile')
            ->setName('user-private-profile');

        $app->map(['GET', 'POST'],
            '/{user_id}/user_rate_pairs[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:user_rate_pairs')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('user_rate_pairs');

        $app->map(['GET', 'POST'],
            '/{user_id}/googleregister[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:googleregister')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedInNoProfile')
            ->setName('googleregister');

        $app->map(['GET', 'POST'],
            '/{user_id}/user-admin-profile[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:userCodeOfConduct')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->setName('user-code-of-conduct');

        $app->map(['GET', 'POST'],
        '/invoice/{invoice_number}[/]',
        '\SolasMatch\UI\RouteHandlers\UserRouteHandler:getInvoice')
        ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
        ->setName('get-invoice');

        $app->map(['GET', 'POST'],
            '/{user_id}/user-uploads/{cert_id}[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:userUploads')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedInNoProfile')
            ->setName('user-uploads');

        $app->get(
            '/{id}/user-download[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:userDownload')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->setName('user-download');
        

        $app->get(
            '/users_review[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:users_review')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('users_review');

        $app->map(['GET', 'POST'],
            '/users_new[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:users_new')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('users_new');

        $app->get(
            '/users_tracked[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:users_tracked')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('users_tracked');

        $app->map(['GET', 'POST'],
            '/add_tracking_code[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:add_tracking_code')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('add_tracking_code');

        $app->get(
            '/download_users_tracked[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:download_users_tracked')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('download_users_tracked');

        $app->get(
            '/download_users_new[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:download_users_new')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('download_users_new');

        $app->get(
            '/download_users_new_unreviewed[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:download_users_new_unreviewed')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('download_users_new_unreviewed');

        $app->map(['GET', 'POST'],
            '/{user_id}/notification/stream[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:editTaskStreamNotification')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->setName('stream-notification-edit');

        $app->map(['GET', 'POST'],
            '/user/task/{task_id}/reviews[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:userTaskReviews')
            ->add('\SolasMatch\UI\Lib\Middleware:authenticateUserForTask')
            ->setName('user-task-reviews');

        $app->get(
            '/native_languages/{term}/search[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:native_languages')
            ->setName('native_languages');

        $app->map(['GET', 'POST'],
            '/{user_id}/{request_type}/printrequest[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:userPrintRequest')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('user-print-request');

        $app->map(['GET', 'POST'],
            '/{valid_key}/generatevolunteercertificate[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:generatevolunteercertificate')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('user-print-certificate');

            $app->map(['GET', 'POST'],
            '/{valid_key}/downloadletter[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:downloadletter')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('downloadletter');

            $app->map(['GET', 'POST'],
            '/{filename}/download[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:download')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin_any')
            ->setName('download');

            $app->map(['GET', 'POST'],
            '/{org_id}/invite_admins[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:invite_admins')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrg_incl_community_officer')
            ->setName('invite_admins');

            $app->map(['GET', 'POST'],
            '/invite_site_admins[/]',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:invite_site_admins')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('invite_site_admins');

            $app->map(['GET', 'POST'],
            '/docusign_redirect_uri',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:docusign_redirect_uri')
            ->setName('docusign_redirect_uri');

            $app->map(['GET', 'POST'],
            '/docusign_hook',
            '\SolasMatch\UI\RouteHandlers\UserRouteHandler:docusign_hook')
            ->setName('docusign_hook');
    }

    public function homeIndex(Request $request, Response $response, $args)
    {
        $currentScrollPage          = !empty($args['page_no']) ? $args['page_no'] : 1;
        $selectedTaskType           = !empty($args['tt'])      ? $args['tt'] : NULL;
        $selectedSourceLanguageCode = !empty($args['sl'])      ? $args['sl'] : NULL;
        $selectedTargetLanguageCode = !empty($args['tl'])      ? $args['tl'] : NULL;

        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();
        $projectDao = new DAO\ProjectDao();

        $siteLocation = Common\Lib\Settings::get('site.location');
        $itemsPerScrollPage = 6;
        $offset = ($currentScrollPage - 1) * $itemsPerScrollPage;
        $topTasks = [];

        $selectedTaskType = (int)$selectedTaskType;
        $filter_source = NULL;
        $filter_target = NULL;
        if (!is_null($selectedSourceLanguageCode)) {
         
            $filter_source = $selectedSourceLanguageCode;           
        }

        if (!is_null($selectedTargetLanguageCode)) {        
           
            $filter_target = $selectedTargetLanguageCode;
        }

        try {
            if ($user_id) {
                $topTasks = $userDao->getUserPageTasks($user_id, $itemsPerScrollPage, $offset, $selectedTaskType, $filter_source, $filter_target);
            }
        } catch (\Exception $e) {
            $topTasks = [];
        }

        $projectAndOrgs = [];
        $taskImages = [];
        $tasksIds = [];
        foreach ($topTasks as $topTask) {
            $topTask->setTitle(Lib\TemplateHelper::uiCleanseHTMLNewlineAndTabs($topTask->getTitle()));
            $topTask->getSourceLocale()->setLanguageName(Lib\TemplateHelper::getLanguageAndCountryNoCodes($topTask->getSourceLocale()));
            $topTask->getTargetLocale()->setLanguageName(Lib\TemplateHelper::getLanguageAndCountryNoCodes($topTask->getTargetLocale()));
            $taskId = $topTask->getId();
            array_push($tasksIds,$taskId);
            $project = $projectDao->getProject($topTask->getProjectId());
            $org_id = $project->getOrganisationId();
            $org = $orgDao->getOrganisation($org_id);
            $projectUri = "{$siteLocation}project/{$project->getId()}/view";
            $projectName = $project->getTitle();
            $orgUri = "{$siteLocation}org/{$org_id}/profile";
            $orgName = $org->getName();

            $projectAndOrgs[$taskId] = sprintf(
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
        $chunks =  $userDao->getUserTaskChunks(...$tasksIds);
 
        $results = json_encode(['tasks'=> $topTasks , 'images' => $taskImages, 'projects'=> $projectAndOrgs, 'chunks'=> $chunks]);
        $response->getBody()->write($results);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function home(Request $request, Response $response)
    {
        global $app, $template_data;
        $selectedTaskType           = 0;
        $selectedSourceLanguageCode = 0;

        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();
        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();
        $adminDao = new DAO\AdminDao();

        $viewData = array();
        $viewData['current_page'] = 'home';

        $tagDao = new DAO\TagDao();
        $top_tags = $tagDao->getTopTags(10);
        $viewData['top_tags'] = $top_tags;

        if ($user_id != null) {
            $user_tags = $userDao->getUserTags($user_id);
            $viewData['user_tags'] = $user_tags;
        }

        $template_data = array_merge($template_data, $viewData);

        $siteLocation = Common\Lib\Settings::get('site.location');
        $itemsPerScrollPage = 6;
        $offset = 0;
        $topTasksCount = 0;
        $topTasks = [];

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();

            if (isset($post['taskTypes'])) {
                $selectedTaskType = $post['taskTypes'];
            }
            if (isset($post['sourceLanguage'])) {
                $selectedSourceLanguageCode = $post['sourceLanguage'];
            }
        }

         // Post or route handler may return '0', need an explicit zero
         $selectedTaskType = (int)$selectedTaskType;
         if ($selectedSourceLanguageCode === '0') $selectedSourceLanguageCode = 0;

         $filter_type   = NULL;
         $filter_source = NULL;
         $filter_target = NULL;
         // Identity tests (also in template) because a language code string evaluates to zero; (we use '0' because URLs look better that way)
         if ($selectedTaskType           !== 0) $filter_type = $selectedTaskType;
         if ($selectedSourceLanguageCode !== 0) {
             $codes = explode('_', $selectedSourceLanguageCode);
             $filter_source = $codes[0];
             $filter_target = $codes[1];
         }
 
         try {
             if ($user_id) {
                 $topTasks      = $userDao->getUserPageTasks($user_id, $itemsPerScrollPage, $offset, $filter_type, $filter_source, $filter_target);
                 $topTasksCount = intval($userDao->getUserPageTasksCount($user_id, $filter_type, $filter_source, $filter_target));
             }
         } catch (\Exception $e) {
             $topTasks = [];
             $topTasksCount = 0;
         }

        $deadline_timestamps = array();
        $projectAndOrgs = array();
        $taskImages = array();
        $tasksIds = array();
       
        $pages = ceil($topTasksCount/6);
            foreach ($topTasks as $topTask) {
                $taskId = $topTask->getId();
                array_push($tasksIds,$taskId);
                $project = $projectDao->getProject($topTask->getProjectId());
                $org_id = $project->getOrganisationId();
                $org = $orgDao->getOrganisation($org_id);
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
                $projectAndOrgs[$taskId] = sprintf(
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
         

        $extra_scripts  = "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/Parameters.js\"></script>";
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/Home.js\" async></script>";
        $extra_scripts .= "<script type=\"text/javascript\"  src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/pagination.js\" defer ></script>";
 

        $org_admin = false;
        if (empty($topTasks) && !empty($user_id)) {
            $org_admin = $adminDao->isSiteAdmin_any_or_org_admin_any_for_any_org($user_id);
        }

        $chunks = [];
        if (!empty($user_id)) {
            $chunks = $userDao->getUserTaskChunks(...$tasksIds);
        }

        $template_data = array_merge($template_data, array(
            'siteLocation' => $siteLocation,
            'active_languages' => !empty($user_id) ? $taskDao->get_active_languages($user_id) : [],
            'selectedTaskType' => $selectedTaskType,
            'selectedSourceLanguageCode' => $selectedSourceLanguageCode,
            'selectedTargetLanguageCode' => 0,
            'topTasks' => $topTasks,
            'chunks' => $chunks,
            'deadline_timestamps' => $deadline_timestamps,
            'projectAndOrgs' => $projectAndOrgs,
            'taskImages' => $taskImages,        
            'itemsPerScrollPage' => min($itemsPerScrollPage, $topTasksCount),
            'extra_scripts' => $extra_scripts,
            'user_id' => $user_id,
            'org_admin' => $org_admin,
            'page_count' => $pages,
            'roles' => !empty($user_id) ? $adminDao->get_roles($user_id) : 0,
        ));
        return UserRouteHandler::render('index-home.tpl', $response);
    }

    public function register(Request $request, Response $response, $args)
    {
        global $app, $template_data;

        $userDao = new DAO\UserDao();
        $langDao = new DAO\LanguageDao();
        $adminDao = new DAO\AdminDao();

        if (!empty($args['track_code'])) $_SESSION['track_code'] = $args['track_code'];
        $email = '';
        $error = null;
        if (!empty($args['reg_data'])) {
            $_SESSION['reg_data'] = $args['reg_data'];
            [$email, $error] = $adminDao->get_special_registration();
            if ($error) {
                UserRouteHandler::flashNow('error', $error);
                $template_data = array_merge($template_data, ['disabled' => 1]);
            }
        }
        $google_site_key = Common\Lib\Settings::get('google.captcha_site_key');
        $google_secret_key = Common\Lib\Settings::get('google.captcha_secret_key');

        $extra_scripts  = self::createGooglePlusJavaScript();
        $extra_scripts .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/js/bootstrap.min.js" type="text/javascript"></script>';
        $extra_scripts .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js" type="text/javascript"></script>';
        $extra_scripts .= '<script src="https://www.google.com/recaptcha/api.js?render=' . $google_site_key . '" type="text/javascript"></script>';
        $extra_scripts .= '<script type="text/javascript">
        $().ready(function() {
            $("#registerform").validate({
                rules: {
                    first_name: "required",
                    last_name: "required",
                    password: {
                        required: true,
                        minlength: 5
                    },
                    confirm_password: {
                        required: true,
                        minlength: 5,
                        equalTo: "#password"
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    age_consent: "required",
                    conduct_consent: "required",
                   
                },
                messages: {
                    first_name: "Please enter your First name",
                    last_name: "Please enter your Last name",
                    password: {
                        required: "Please provide a password",
                        minlength: "Your password must be at least 5 characters long"
                    },
                    confirm_password: {
                        required: "Please provide a password",
                        minlength: "Your password must be at least 5 characters long",
                        equalTo: "Please enter the same password as above"
                    },
                    email: "Please enter a valid email address",
                    age_consent: "Please ensure you are above 18 years of age",
                    conduct_consent: "You need to agree to this to proceed",
                }
            });
            $("#tool").tooltip();
        });
        </script>';
        $extra_scripts .= '<script type="text/javascript">
        grecaptcha.ready(function () {
            grecaptcha.execute("' . $google_site_key . '", { action: "kp_registration"}).then(function (token) {
                document.getElementById("g_response").value = token;
            });
        });
        </script>';
        $template_data = array_merge($template_data, array('extra_scripts' => $extra_scripts));

        if ($request->getMethod() === 'POST' && sizeof($request->getParsedBody()) > 2 && !$error) {
            $post = $request->getParsedBody();
            $ip = $_SERVER['REMOTE_ADDR'];
            // post request to Google recaptcha server
            $data = array('secret' => $google_secret_key, 'response' => $post['g-recaptcha-response']);
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context = stream_context_create($options);
            $google_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
            if (!empty($google_response)) {
                $response_keys = json_decode($google_response, true);
                if($response_keys['success'] != 1) {
                    $error = 'Oops! something went wrong, please try again.';
                    // Get exact response message why it has been flagged as spam
                    $g_response = $response_keys['error-codes'][0];
                    error_log("$error: $ip Google_response: $g_response");
                }
            } else {
                error_log("Spam response from Google empty ip: $ip");
            }

            $temp = md5($post['email'] . substr(Common\Lib\Settings::get("session.site_key"), 0, 20));
            Common\Lib\UserSession::clearCurrentUserID();
            if (!Lib\Validator::validateEmail($post['email']) || ($email && $post['email'] != $email)) {
                $error = Lib\Localisation::getTranslation('register_1');
            } elseif (!Lib\TemplateHelper::isValidPassword($post['password'])) {
                $error = Lib\Localisation::getTranslation('register_2');
            } elseif ($user = $userDao->getUserByEmail($post['email'], $temp)) {
                if ($userDao->isUserVerified($user->getId())) {
                    $error = sprintf(Lib\Localisation::getTranslation('register_3'), $app->getRouteCollector()->getRouteParser()->urlFor("login"));
                } else {
                    $error = "User is not verified";
                    // notify user that they are not yet verified an resent verification email
                }
            } elseif (empty($post['first_name'])) {
                $error = 'You did not enter First name';
            } elseif (empty($post['last_name'])) {
                $error = 'You did not enter Last name';
            }

            if (is_null($error)) {
                array_key_exists('newsletter_consent', $post) ? $communications_consent = 1 : $communications_consent = 0;
                if ($userDao->register($post['email'], $post['password'], $post['first_name'], $post['last_name'], $communications_consent)) {
                    UserRouteHandler::flashNow(
                        "success",
                        sprintf(Lib\Localisation::getTranslation('register_4'), $app->getRouteCollector()->getRouteParser()->urlFor("login"))
                    );
                } else {
                    UserRouteHandler::flashNow(
                        'error',
                        'Failed to register'
                    );
                }
            } else {
                if ($error === 'Oops! something went wrong, please try again.') {
                    $template_data = array_merge($template_data, ['first_name' => $post['first_name'], 'last_name' => $post['last_name'], 'email' => $post['email']]);
                    UserRouteHandler::flashNow('error', $error);
                }
            }
        } else {
            if ($email) $template_data = array_merge($template_data, ['email' => $email]);
        }
        if ($error) {
            $template_data = array_merge($template_data, array("error" => $error));
        }
        return UserRouteHandler::render("user/register.tpl", $response);
    }

    public function changeEmail(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $user_id = $args['user_id'];

        $userDao = new DAO\UserDao();
        $adminDao = new DAO\AdminDao();
        $loggedInUserId = Common\Lib\UserSession::getCurrentUserID();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $error = '';
        if ($request->getMethod() === 'POST' && sizeof($request->getParsedBody()) > 1) {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'changeEmail')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            if (!Lib\Validator::validateEmail($post['email'])) {
                $error = Lib\Localisation::getTranslation('register_1');
            } elseif ($userDao->getUserByEmail($post['email'])) {
                $error = Lib\Localisation::getTranslation('common_new_email_already_used');
            }

            if (!$error && ($adminDao->get_roles($loggedInUserId) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER))) {
                $user = $userDao->getUser($user_id);
                if (!($error = $userDao->changeEmail($user_id, $post['email'], $user->getEmail()))) {
                    UserRouteHandler::flashNow('success', '');
                } else {
                    UserRouteHandler::flashNow('error', $error);
                }
            }
        }
        if ($error) $template_data = array_merge($template_data, ['error' => $error]);
        $template_data = array_merge($template_data, ['user_id' => $user_id, 'sesskey' => $sesskey]);
        return UserRouteHandler::render('user/change-email.tpl', $response);
    }

    public function emailVerification(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $uuid = $args['uuid'];

        $userDao = new DAO\UserDao();

        $user = $userDao->getRegisteredUser($uuid);

        if (is_null($user)) {
            UserRouteHandler::flash("error", Lib\Localisation::getTranslation('email_verification_7'));
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("home"));
        }

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if (isset($post['verify'])) {
                if ($userDao->finishRegistration($uuid)) {
                    UserRouteHandler::flash('success', Lib\Localisation::getTranslation('email_verification_8'));
                } else {
                    UserRouteHandler::flash('error', 'Failed to finish registration');  // TODO: remove inline text
                }
                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('login'));
            }
        }

        $template_data = array_merge($template_data, array('uuid' => $uuid));

        return UserRouteHandler::render("user/email.verification.tpl", $response);
    }
    
    public function invite_admins(Request $request, Response $response, $args)
    {
        global $app, $template_data;

        $adminDao = new DAO\AdminDao();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();      
        $org_id = $args['org_id'];
        $org = $orgDao->getOrganisation($org_id);
        $admin_id = Common\Lib\UserSession::getCurrentUserID();
        $roles = $adminDao->get_roles($admin_id, $org_id);

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            $email = $post['email'];
            if ((($roles&(SITE_ADMIN + PROJECT_OFFICER + COMMUNITY_OFFICER + NGO_ADMIN)) || $post['role'] != NGO_ADMIN) && Lib\Validator::validateEmail($email)) {
                $userExist = $userDao->getUserByEmail($email, null);
                if ($userExist) {
                    if ($userDao->isUserVerified($userExist->getId())) {
                        $adminDao->adjust_org_admin($userExist->getId(), $org_id, 0, $post['role']&~LINGUIST);
                        UserRouteHandler::flashNow('success', 'A user with this email already exists and they have now been given the requested role.');
                    } else {
                        UserRouteHandler::flashNow('error', 'This user is not verified, please verify them first, if you trust them.');
                    }
                } else {
                    $adminDao->insert_special_registration($post['role'], $email, $org_id, $admin_id);
                    UserRouteHandler::flashNow('success', 'This user has been sent an email to invite them to register.');
                }
            } else UserRouteHandler::flashNow('error', 'That is not a valid email.');
        }

        $template_data = array_merge($template_data, [
            'sent' => $adminDao->get_special_registration_records($org_id),
            'orgName' => $org->name,
            'org_id' => $org_id,
            'roles' => $roles,
            ]);
        return UserRouteHandler::render('user/invite-admin.tpl', $response);
    }

    public function invite_site_admins(Request $request, Response $response)
    {
        global $app, $template_data;

        $adminDao = new DAO\AdminDao();
        $userDao = new DAO\UserDao();
        $admin_id = Common\Lib\UserSession::getCurrentUserID();

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            $email = $post['email'];
            if ($post['role'] !== SITE_ADMIN && Lib\Validator::validateEmail($email)) {
                $userExist = $userDao->getUserByEmail($email, null);
                if ($userExist) {
                    if ($userDao->isUserVerified($userExist->getId())) {
                        $adminDao->adjust_org_admin($userExist->getId(), 0, 0, $post['role']);
                        UserRouteHandler::flashNow('success', 'A user with this email already exists and they have now been given the requested role.');
                    } else {
                        UserRouteHandler::flashNow('error', 'This user is not verified, please verify them first, if you trust them.');
                    }
                } else {
                    $adminDao->insert_special_registration($post['role'], $email, 0, $admin_id);
                    UserRouteHandler::flashNow('success', 'This user has been sent an email to invite them to register.');
                }
            } else UserRouteHandler::flashNow('error', 'That is not a valid email.');
        }

        $template_data = array_merge($template_data, [
            'sent' => $adminDao->get_special_registration_records(0),
            ]);
        return UserRouteHandler::render('user/invite-site-admin.tpl', $response);
    }

    public function passwordReset(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $uuid = $args['uuid'];

        $userDao = new DAO\UserDao();

        if (!$userDao->get_password_reset_request_by_uuid($uuid)) {
            UserRouteHandler::flash("error", Lib\Localisation::getTranslation('password_reset_1'));
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("home"));
        }

        $template_data = array_merge($template_data, ['uuid' => $uuid]);
        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();

            if (isset($post['new_password']) && Lib\TemplateHelper::isValidPassword($post['new_password'])) {
                if (
                    isset($post['confirmation_password']) &&
                    $post['confirmation_password'] == $post['new_password']
                ) {
                    $response_dao = $userDao->resetPassword($post['new_password'], $uuid);
                    if ($response_dao) {
                        UserRouteHandler::flash("success", Lib\Localisation::getTranslation('password_reset_2'));
                        return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("home"));
                    } else {
                        UserRouteHandler::flashNow("error", Lib\Localisation::getTranslation('password_reset_1'));
                    }
                } else {
                    UserRouteHandler::flashNow("error", Lib\Localisation::getTranslation('password_reset_1'));
                }
            } else {
                UserRouteHandler::flashNow("error", Lib\Localisation::getTranslation('password_reset_1'));
            }
        }
        return UserRouteHandler::render("user/password-reset.tpl", $response);
    }

    public function request_password_reset(Request $request, Response $response)
    {
        global $app, $template_data;
        $userDao = new DAO\UserDao();

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if (isset($post['password_reset'])) {
                if (isset($post['email_address']) && $post['email_address'] != '') {
                        $success = $userDao->request_password_reset($post['email_address']);
                        if ($success == 1) {
                            UserRouteHandler::flash("success", Lib\Localisation::getTranslation('user_reset_password_2'));
                            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("home"));
                        } elseif ($success == -1) {
                            UserRouteHandler::flashNow('error', 'This email has requested too many password resets in 24 hours, please check the emails that were previously sent to you.');
                        } else {
                            UserRouteHandler::flashNow(
                                "error",
                                "Failed to request password reset, are you sure you entered your email " .
                                    "address correctly?"
                            );
                        }
                } else {
                    UserRouteHandler::flashNow("error", Lib\Localisation::getTranslation('user_reset_password_4'));
                }
            }
        }
        return UserRouteHandler::render("user/user.reset-password.tpl", $response);
    }

    public function logout(Request $request, Response $response)
    {
        global $app;

        Common\Lib\UserSession::destroySession();
        return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("home"));
    }

    public function login(Request $request, Response $response)
    {
        global $app, $template_data;

        $userDao = new DAO\UserDao();
        $langDao = new DAO\LanguageDao();
        $adminDao = new DAO\AdminDao();

        $error = null;
        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();

            if (isset($post['login'])) {
                $user = null;
                try {
                    $user = $userDao->login($post['email'], $post['password']);
                } catch (Common\Exceptions\SolasMatchException $e) {
                    $error = sprintf(
                        Lib\Localisation::getTranslation('login_1'),
                        $app->getRouteCollector()->getRouteParser()->urlFor("login"),
                        $app->getRouteCollector()->getRouteParser()->urlFor("register"),
                        $e->getMessage()
                    );
                    UserRouteHandler::flashNow('error', $error);
                }
                if (!is_null($user)) {
                    error_log("Password, Login: {$post['email']}");
                    Common\Lib\UserSession::setSession($user->getId());
                    $request_url = Common\Lib\UserSession::getReferer();
                    Common\Lib\UserSession::clearReferer();

                    // Check have we previously been redirected from SAML to do login, if so get return address so we can redirect to it below
                    if (!$request_url) {
                        if (!empty($_SESSION['return_to_SAML_url'])) {
                            $request_url = $_SESSION['return_to_SAML_url'];
                        }
                    }
                    unset($_SESSION['return_to_SAML_url']);

                    $userInfo = $userDao->getUserPersonalInformation($user->getId());
                    $langPrefId = $userInfo->getLanguagePreference();
                    $preferredLang = $langDao->getLanguage($langPrefId);
                    // Set site language to user's preferred language if it is not already
                    $user_language = Common\Lib\UserSession::getUserLanguage();
                    if (empty($user_language)) {
                        Common\Lib\UserSession::setUserLanguage($preferredLang->getCode());
                    } else {
                        $currentSiteLang = $langDao->getLanguageByCode($user_language);
                        if ($currentSiteLang != $preferredLang) {
                            Common\Lib\UserSession::setUserLanguage($preferredLang->getCode());
                        }
                    }

                    $userDao->setRequiredProfileCompletedinSESSION($user->getId());

                    // Redirect to homepage, or the page the page user was previously on e.g. if their session timed out and they are logging in again.
                    if ($request_url) {
                        return $response->withStatus(302)->withHeader('Location', $request_url);
                    } else {
                        $terms_accepted = $userDao->terms_accepted($user->getId());
                        if ($terms_accepted < 2) {
                            $message = $adminDao->copy_roles_from_special_registration($user->getId(), $user->getEmail());
                            if ($message) UserRouteHandler::flash('error', $message);
                        }
                        if ($adminDao->isSiteAdmin_any_or_org_admin_any_for_any_org($user->getId())) {
                            // Next line should not happen in this path?
                            if ($terms_accepted == 1) return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('googleregister', array('user_id' => $user->getId())));
                            if ($terms_accepted  < 3) $userDao->update_terms_accepted($user->getId(), 3);
                            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('org-dashboard'));
                        } else {
                            $nativeLocale = $user->getNativeLocale();
                            if ($nativeLocale && $nativeLocale->getLanguageCode()) {
                                if ($message = $userDao->get_post_login_message($user->getId())) {
                                    UserRouteHandler::flash('error', $message);
                                    return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('user-private-profile', array('user_id' => $user->getId())));
                                }
                                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("home"));
                            } else {
                                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('user-private-profile', array('user_id' => $user->getId())));
                            }
                        }
                    }
                }
            } elseif (isset($post['password_reset'])) {
                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("password-reset-request"));
            } elseif (isset($post['credential'])) { // Google Sign-In
                if (empty($post['g_csrf_token']))    $error = 'No CSRF token in post body.';
                if (empty($_COOKIE['g_csrf_token'])) $error = 'No CSRF token in Cookie.';
                if (!$error && $_COOKIE['g_csrf_token'] != $post['g_csrf_token']) {
                    $error = 'Failed to verify double submit cookie.';
                } else {
                    // https://github.com/googleapis/google-api-php-client
                    require_once 'ui/google-api-php-client/vendor/autoload.php';
                    $client = new \Google_Client(['client_id' => Common\Lib\Settings::get('googlePlus.client_id')]);
                    $payload = $client->verifyIdToken($post['credential']);
                    if ($payload) {
                        if (empty($payload['email'])) $error = 'email empty.';
                        if (!$error) {
                            $email = $payload['email'];
                            if (!empty($payload['given_name']) && !empty($payload['family_name'])) $userDao->set_google_user_details($email, $payload['given_name'], $payload['family_name']);
                            error_log("Google Sign-In, Login: $email");
                            return $response->withStatus(302)->withHeader('Location', $userDao->requestAuthCode($email));
                        }
                    } else {
                        $error = 'Invalid ID token';
                    }
                }

                $error = sprintf(Lib\Localisation::getTranslation('gplus_error'), $app->getRouteCollector()->getRouteParser()->urlFor('login'), $app->getRouteCollector()->getRouteParser()->urlFor('register'), "[$error]");
                UserRouteHandler::flash('error', $error);
                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
            }
        } else {
            $parms = $request->getQueryParams();
            $authCode = !empty($parms['code']) ? $parms['code'] : null;
            if (!is_null($authCode)) {
                // Exchange auth code for access token
                $user = null;
                try {
                    $user = $userDao->loginWithAuthCode($authCode);
                } catch (\Exception $e) {
                    $error = sprintf(
                        Lib\Localisation::getTranslation('login_1'),
                        $app->getRouteCollector()->getRouteParser()->urlFor("login"),
                        $app->getRouteCollector()->getRouteParser()->urlFor("register"),
                        $e->getMessage()
                    );
                    UserRouteHandler::flash('error', $error);
                    return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('login'));
                }
                error_log('OAuth, Login: ' . $user->getEmail());
                Common\Lib\UserSession::setSession($user->getId());
                $request_url = Common\Lib\UserSession::getReferer();
                Common\Lib\UserSession::clearReferer();

                // Check have we previously been redirected from SAML to do login, if so get return address so we can redirect to it below
                if (!$request_url) {
                    if (!empty($_SESSION['return_to_SAML_url'])) {
                        $request_url = $_SESSION['return_to_SAML_url'];
                    }
                }
                unset($_SESSION['return_to_SAML_url']);

                $userInfo = $userDao->getUserPersonalInformation($user->getId());
                $langPrefId = $userInfo->getLanguagePreference();
                $preferredLang = $langDao->getLanguage($langPrefId);
                // Set site language to user's preferred language if it is not already
                $user_language = Common\Lib\UserSession::getUserLanguage();
                if (empty($user_language)) {
                    Common\Lib\UserSession::setUserLanguage($preferredLang->getCode());
                } else {
                    $currentSiteLang = $langDao->getLanguageByCode($user_language);
                    if ($currentSiteLang != $preferredLang) {
                        Common\Lib\UserSession::setUserLanguage($preferredLang->getCode());
                    }
                }

                $userDao->setRequiredProfileCompletedinSESSION($user->getId());

                if ($request_url) {
                    return $response->withStatus(302)->withHeader('Location', $request_url);
                } else {
                    $terms_accepted = $userDao->terms_accepted($user->getId());
                    if ($terms_accepted < 2) {
                        $message = $adminDao->copy_roles_from_special_registration($user->getId(), $user->getEmail());
                        if ($message) UserRouteHandler::flash('error', $message);
                    }
                    if ($adminDao->isSiteAdmin_any_or_org_admin_any_for_any_org($user->getId())) {
                        if ($terms_accepted == 1) return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('googleregister', array('user_id' => $user->getId())));
                        if ($terms_accepted  < 3) $userDao->update_terms_accepted($user->getId(), 3);
                        return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('org-dashboard'));
                    } else {
                        $nativeLocale = $user->getNativeLocale();
                        if ($nativeLocale && $nativeLocale->getLanguageCode()) {
                            if ($message = $userDao->get_post_login_message($user->getId())) {
                                UserRouteHandler::flash('error', $message);
                                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('user-private-profile', array('user_id' => $user->getId())));
                            }
                            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("home"));
                        } else {
                            if ($terms_accepted == 1) {
                                // Since they are logged in (via Google)...
                                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('googleregister', array('user_id' => $user->getId())));
                            }
                            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('user-private-profile', array('user_id' => $user->getId())));
                        }
                    }
                }
            }

            $parms = $request->getQueryParams();
            $return_to_SAML_url = !empty($parms['ReturnTo']) ? $parms['ReturnTo'] : null;
            if (!empty($return_to_SAML_url)) {
                $_SESSION['return_to_SAML_url'] = $return_to_SAML_url;
            }

            $error = !empty($parms['error']) ? $parms['error'] : null;
            if (!is_null($error)) {
                UserRouteHandler::flashNow('error', !empty($parms['error_message']) ? $parms['error_message'] : '');
            }
        }

        $template_data = array_merge($template_data, array(
            'extra_scripts' => self::createGooglePlusJavaScript(),
        ));

        return UserRouteHandler::render("user/login.tpl", $response);
    }

    private static function createGooglePlusJavaScript()
    {
        return '<script src="https://accounts.google.com/gsi/client" async defer></script>';
    }

    public function googleregister(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $user_id = $args['user_id'];

        $userDao = new DAO\UserDao();
        $adminDao = new DAO\AdminDao();

        if ($user_id != Common\Lib\UserSession::getCurrentUserID()) {
            UserRouteHandler::flash('error', Lib\Localisation::getTranslation('common_login_required_to_access_page'));
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('login'));
        }

        $user_info = $userDao->getUser($user_id);
        $user_personal_info = $userDao->getUserPersonalInformation($user_id);
        $firstName = $user_personal_info->firstName;
        $lastName = $user_personal_info->lastName;

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'googleregister')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            $user_personal_info->setFirstName($post['first_name']);
            $user_personal_info->setLastName($post['last_name']);
            $userDao->updatePersonalInfo($user_id, $user_personal_info);
            array_key_exists('newsletter_consent', $post) ? $userDao->insert_communications_consent($user_id, 1) : $userDao->insert_communications_consent($user_id, 0);
            if ($userDao->terms_accepted($user_id) < 2) $userDao->update_terms_accepted($user_id, 2);

            if ($adminDao->isSiteAdmin_any_or_org_admin_any_for_any_org($user_id)) {
                $userDao->update_terms_accepted($user_id, 3);
                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('org-dashboard'));
            }
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('user-private-profile', array('user_id' => $user_id)));
        } else {
            $extra_scripts  = '<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/js/bootstrap.min.js" type="text/javascript"></script> ';
            $extra_scripts .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js" type="text/javascript"></script> ';
            $extra_scripts .= '<script type="text/javascript">
            $().ready(function() {
            $("#gregisterform").validate({
                rules: {
                    first_name: "required",
                    last_name: "required",
                    age_consent: "required",
                    conduct_consent: "required"
                },
                messages: {
                    first_name: "Please enter your First name",
                    last_name: "Please enter your Last name",
                    age_consent: "Please ensure you are above 18 years of age",
                    conduct_consent: "You need to agree to this to proceed",
                }
            });
            $("#tool").tooltip();
            $(".profile").hide();
            $(".logout").hide();
        });
            </script>';
            $template_data = array_merge($template_data, array("extra_scripts" => $extra_scripts));
            $template_data = array_merge($template_data, array('firstname' => $firstName, 'lastname' => $lastName, 'user_id' => $user_id, 'sesskey' => $sesskey));
            return UserRouteHandler::render('user/googleregister.tpl', $response);
        }
    }

    public function set_paid_eligible_pair(Request $request, Response $response, $args)
    {
        $taskDao = new DAO\TaskDao();

        $result = 1;
        if (Common\Lib\UserSession::checkCSRFKey($request->getParsedBody(), 'set_paid_eligible_pair')) $result = 0;
        if ($result) {
            if ($args['eligible'] > 0) $taskDao->create_user_paid_eligible_pair($args['user_id'], $args['sl'], $args['sc'], $args['tl'], $args['tc'], $args['eligible']);
            else                       $taskDao->remove_user_paid_eligible_pair($args['user_id'], $args['sl'], $args['sc'], $args['tl'], $args['tc']);
        }
        $results = json_encode(['result'=> $result]);
        $response->getBody()->write($results);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function userPrivateProfile(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $user_id = $args['user_id'];

        $userDao = new DAO\UserDao();
        $adminDao = new DAO\AdminDao();
        $langDao = new DAO\LanguageDao();
        $countryDao = new DAO\CountryDao();
        $projectDao = new DAO\projectDao();
        $taskDao = new DAO\TaskDao();

        $loggedInUserId = Common\Lib\UserSession::getCurrentUserID();
        $roles = $adminDao->get_roles($loggedInUserId);
        if (!($user_id == $loggedInUserId || ($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)))) {
            UserRouteHandler::flash('error', 'You do not have rights to edit this user');
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }

        if (!empty($_SESSION['track_code'])) {
            $userDao->insert_tracked_registration($user_id, $_SESSION['track_code']);
            unset($_SESSION['track_code']);
        }

        if (empty($_SESSION['SESSION_CSRF_KEY'])) {
            $_SESSION['SESSION_CSRF_KEY'] = UserRouteHandler::random_string(10);
        }
        $sesskey = $_SESSION['SESSION_CSRF_KEY']; // This is a check against CSRF (Posts should come back with same sesskey)

        $user = $userDao->getUser($user_id);
        Common\Lib\CacheHelper::unCache(Common\Lib\CacheHelper::GET_USER . $user_id);

        if (!is_object($user)) {
            UserRouteHandler::flash("error", Lib\Localisation::getTranslation('common_login_required_to_access_page'));
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("login"));
        }

        $userPersonalInfo = null;
        try {
            $userPersonalInfo = $userDao->getUserPersonalInformation($user_id);
        } catch (Common\Exceptions\SolasMatchException $e) {
        }

        $user_task_limitation_current_user = $taskDao->get_user_task_limitation($loggedInUserId);

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
            $nativeLanguageSelectName = $nativeLocale->getLanguageName();
            $nativeCountrySelectCode = $nativeLocale->getCountryCode();
        } else {
            $nativeLanguageSelectCode = '999999999';
            $nativeLanguageSelectName = '999999999';
            $nativeCountrySelectCode = '999999999';
        }

        $userQualifiedPairs = $userDao->getUserQualifiedPairs($user_id);
        $url_list           = $userDao->getURLList($user_id);
        $capability_list    = $userDao->getCapabilityList($user_id);
        $expertise_list     = $userDao->getExpertiseList($user_id);
        $howheard_list      = $userDao->getHowheardList($user_id);
        $certification_list = $userDao->getCertificationList($user_id);

        if ($post = $request->getParsedBody()) {
            if (empty($post['sesskey']) || $post['sesskey'] !== $sesskey || empty($post['displayName'])) {
                UserRouteHandler::flashNow('error', Lib\Localisation::getTranslation('user_private_profile_2'));
            } else {
                // error_log("POST" . print_r($post, true));
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

                $userPersonalInfo->setFirstName($post['firstName']);
                $userPersonalInfo->setLastName($post['lastName']);
                $userPersonalInfo->setCity($post['city']);
                $userPersonalInfo->setCountry($post['country']);

                if (!empty($post['receiveCredit'])) {
                    $userPersonalInfo->setReceiveCredit(true);
                } else {
                    $userPersonalInfo->setReceiveCredit(false);
                }

                try {
                    for ($i = 0; $i < 121; $i++) {
                      if (!empty($post["language_code_source_$i"]) && !empty($post["language_code_target_$i"])) {
                        list($language_code_source, $country_code_source) = $projectDao->convert_selection_to_language_country($post["language_code_source_$i"]);
                        list($language_code_target, $country_code_target) = $projectDao->convert_selection_to_language_country($post["language_code_target_$i"]);
                        if (empty($post["qualification_level_$i"])) $post["qualification_level_$i"] = 1;

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
                                ($country_code_target  == $userQualifiedPair['country_code_target'])
                            ) {
                                $found = true;

                                if (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) && ($post["qualification_level_$i"] != $userQualifiedPair['qualification_level'])) {
                                    $userDao->updateUserQualifiedPair(
                                        $user_id,
                                        $language_code_source,
                                        $country_code_source,
                                        $language_code_target,
                                        $country_code_target,
                                        $post["qualification_level_$i"]
                                    );
                                }
                            }
                        }
                        if (!$found) {
                            if (!($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER))) $post["qualification_level_$i"] = 1;

                            if (!($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) && empty($userQualifiedPairs)) { // First time through here for ordinary registrant
                                if ($userDao->get_tracked_registration_for_verified($user_id)) {
                                    if (!empty($post['nativeLanguageSelect']) && ($language_code_target === $post['nativeLanguageSelect'])) { // Only make verified if target matches native language 
                                        $post["qualification_level_$i"] = 2; // Verified Translator
                                    }
                                }
                            }

                            $userDao->createUserQualifiedPair(
                                $user_id,
                                $language_code_source,
                                $country_code_source,
                                $language_code_target,
                                $country_code_target,
                                $post["qualification_level_$i"]
                            );
                        }
                      }
                    }

                    foreach ($userQualifiedPairs as $userQualifiedPair) {
                        $found = false;
                        for ($i = 0; $i < 121; $i++) {
                          if (!empty($post["language_code_source_$i"]) && !empty($post["language_code_target_$i"])) {
                            list($language_code_source, $country_code_source) = $projectDao->convert_selection_to_language_country($post["language_code_source_$i"]);
                            list($language_code_target, $country_code_target) = $projectDao->convert_selection_to_language_country($post["language_code_target_$i"]);

                            if (($language_code_source == $userQualifiedPair['language_code_source']) &&
                                ($country_code_source  == $userQualifiedPair['country_code_source'])  &&
                                ($language_code_target == $userQualifiedPair['language_code_target']) &&
                                ($country_code_target  == $userQualifiedPair['country_code_target'])
                            ) {
                                $found = true;
                            }
                          }
                        }
                        if (!$found && !$user_task_limitation_current_user['limit_profile_changes']) {
                            $userDao->removeUserQualifiedPair(
                                $user_id,
                                $userQualifiedPair['language_code_source'],
                                $userQualifiedPair['country_code_source'],
                                $userQualifiedPair['language_code_target'],
                                $userQualifiedPair['country_code_target']
                            );
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
                            if ($post['interval'] == 10 && ($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER))) $userDao->set_special_translator($user_id, 1);
                        } else {
                            $notifData = new Common\Protobufs\Models\UserTaskStreamNotification();
                            $notifData->setUserId($user_id);
                            $notifData->setInterval($post['interval']);
                            $notifData->setStrict(true);
                            $userDao->requestTaskStreamNotification($notifData);
                            if ($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) $userDao->set_special_translator($user_id, 0);
                        }
                    }

                    foreach ($capability_list as $name => $capability) {
                        if ($capability['state'] && empty($post[$name])) {
                            $userDao->remove_user_service($user_id, $capability['id']);
                        } elseif (!$capability['state'] && !empty($post[$name])) {
                            $userDao->add_user_service($user_id, $capability['id']);
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
                    else                           $userDao->insertUserHowheard($user_id, 'Other');

                    if (!empty($post['communications_consent'])) $userDao->insert_communications_consent($user_id, 1);
                    else                                         $userDao->insert_communications_consent($user_id, 0);

                    $notify = $userDao->terms_accepted($user_id) < 3;
                    $userDao->update_terms_accepted($user_id, 3);
                    if ($notify) $userDao->NotifyRegistered($user_id);

                    $userDao->update_post_login_message($user_id);

                    return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('user-public-profile', array('user_id' => $user_id)));
                } catch (\Exception $e) {
                    UserRouteHandler::flashNow('error', 'Failed to Update');
                }
            }
        }

        $notifData = $userDao->getUserTaskStreamNotification($user_id);
        if ($notifData) {
            $template_data = array_merge($template_data, array(
                'intervalId' => $notifData->getInterval(),
            ));
        }

        foreach ($userQualifiedPairs as $index => $userQualifiedPair) {
            $userQualifiedPairs[$index]['language_code_source'] = $userQualifiedPair['language_code_source'] . '-' . $userQualifiedPair['country_code_source'];
            $userQualifiedPairs[$index]['language_code_target'] = $userQualifiedPair['language_code_target'] . '-' . $userQualifiedPair['country_code_target'];
            if (empty($language_selection[$userQualifiedPairs[$index]['language_code_source']])) $language_selection[$userQualifiedPairs[$index]['language_code_source']] = $languages_array[$userQualifiedPair['language_code_source']] . ($userQualifiedPair['country_code_source'] === '--' ? '' : ('-' . $userQualifiedPair['country_code_source']));
            if (empty($language_selection[$userQualifiedPairs[$index]['language_code_target']])) $language_selection[$userQualifiedPairs[$index]['language_code_target']] = $languages_array[$userQualifiedPair['language_code_target']] . ($userQualifiedPair['country_code_target'] === '--' ? '' : ('-' . $userQualifiedPair['country_code_target']));
        }
        if (empty($userQualifiedPairs)) {
            $userQualifiedPairs[] = array('language_code_source' => '', 'language_code_target' => '', 'qualification_level' => 1);
        }

        $source_lang = '';
        $target_lang = '';
        foreach ($language_selection as $key => $language) {
            $source_lang .= "<option value=$key>$language</option>";
            $target_lang .= "<option value=$key>$language</option>";
        }
        $qualification_levels = [
            1 => 'TWB Translator',
            2 => 'TWB Verified Translator',
            3 => 'TWB Senior Translator'
        ];
        $qualification_level = '';
        foreach ($qualification_levels as $key => $qualification) {
            $qualification_level .= "<option value=$key>$qualification</option>";
        }

        $extra_scripts  = '<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/js/bootstrap.min.js" type="text/javascript"></script> ';
        $extra_scripts .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js" type="text/javascript"></script> ';
        $extra_scripts .= '<script src="' . $app->getRouteCollector()->getRouteParser()->urlFor('home') . 'ui/js/additional-methods.min.js"></script>';
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/Parameters.js\"></script>";
        $extra_scripts .= '<link href="'  . $app->getRouteCollector()->getRouteParser()->urlFor('home') . 'resources/css/select2.min.css" rel="stylesheet" />';
        $extra_scripts .= '<script src="' . $app->getRouteCollector()->getRouteParser()->urlFor('home') . 'ui/js/select2.min.js"></script>';
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/UserPrivateProfile6.js\"></script>";
        $extra_scripts .= '<script type="text/javascript">
        $(document).ready(function() {
            $(".countclick").hide();

            //Admin
            var admin = "' . (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) ? 1 : 0) . '";
            $.validator.addMethod( "notEqualTo", function( value, element, param ) {
                return this.optional( element ) || !$.validator.methods.equalTo.call( this, value, element, param );
            }, "Please enter a different value, values must not be the same." );

            var validator = $("#userprofile").validate({
                rules: {
                    firstName: "required",
                    lastName: "required",
                    nativeLanguageSelect: "required",
                    displayName: {
                        required: true,
                        minlength: 2
                    },
                    nativeLanguageSelect: "required",
                    nativeCountrySelect: "required",
                },
                success: function(label,element) {
                    label.parent().removeClass("error");
                    label.remove(); 
                	
                },
                messages: {
                    firstName: "Please enter your First name",
                    lastName: "Please enter your Last name",
                    nativeLanguageSelect: "Please select your Native language",
                    nativeCountrySelect:"Please select your variant",
                    displayName: {
                        required: "Please enter a username",
                        minlength: "Your username must consist of at least 2 characters"
                    },
                }
            });
           

            $(".nexttab").click(function() {
                var valid = true;
                var i = 0;
                var $inputs = $(this).closest("div").find("input");
                var $select = $(this).closest("div").find("select");

                $inputs.each(function() {
                    if (!validator.element(this) && valid) {
                        valid = false;
                    }
                });

                $select.each(function() {
                    if (!validator.element(this) && valid) {
                        valid = false;
                    }
                });

                if (valid) {
                    if ($(this).attr("href") == "#profile1") {
                        $(".tabcounter").text("2/3");
                        jQuery("#myTab li:eq(1) a").tab("show");

                        localStorage.setItem("selected_native_lang", $("#nativeLanguageSelect").val());
                    } else if ($(this).attr("href") == "#verifications") {
                        $(".tabcounter").text("3/3");
                        jQuery("#myTab li:eq(2) a").tab("show");
                    }
                } else {
                    if ($(this).attr("href") == "#profile") {
                        $(".tabcounter").text("1/3");
                        jQuery("#myTab li:eq(0) a").tab("show");
                        //$("#myTab li#prof").addClass("not-active");
                    } else if ($(this).attr("href") == "#verifications") {
                        $(".tabcounter").text("2/3");
                        jQuery("#myTab li:eq(1) a").tab("show");
                    }
                }
            });

            $(".nexttab1").click(function() {
                var valid = true;
                var i = 0;
                var $inputs = $(this).closest("div").find("input");
                var $select = $(this).closest("div").find("select");

                if ($(".capabilities:checked").length > 0) {
                    // at least one checkbox was checked
                } else {
                    // no checkbox was checked
                    $("#ch1").text("Please check at least one");
                    valid = false;
                }
                if ($(".expertise:checked").length > 0) {
                    // at least one checkbox was checked
                } else {
                    // no checkbox was checked
                    $("#ch").text("Please check at least one");
                    valid = false;
                }

                $select.each(function() {
                    if (!validator.element(this) && valid) {
                        valid = false;
                    }
                });

                if (valid) {
                    if ($(this).attr("href") == "#verifications") {
                        $(".tabcounter").text("3/3");
                        jQuery("#myTab li:eq(2) a").tab("show");

                        if (localStorage.getItem("selected_native_lang") != null) {
                            $("#deleteBtn").show();
                        } else {
                            $("#deleteBtn").hide();
                        }
                    }
                } else {
                    if ($(this).attr("href") == "#verifications") {
                        $(".tabcounter").text("2/3");
                        jQuery("#myTab li:eq(1) a").tab("show");
                    }
                }
            });

            $("#tool").tooltip();
            $("#tool1").tooltip();
            $("#tool2").tooltip();
            $("#tool3").tooltip();
            $("#tool4").tooltip();
            $("#tool5").tooltip();
            $("#tool6").tooltip();

            function formatCountry (country) {
                if (!country.id) { return country.text; }
                var $country = $(
                "<span class=\"flag-icon flag-icon-"+ country.id.toLowerCase() +" flag-icon-squared\"></span>" +
                "<span class=\"flag-text\">"+ country.text+"</span>"
                );
                return $country;
            };

            $(".country").select2({
                placeholder: "Select a country",
                templateResult: formatCountry
            });
            $(".nativeLanguageSelect").select2({
                ajax: {
                    url: function (params) {
                        return getSetting("siteLocation") + "native_languages/" + params.term + "/search/";
                    },
                    dataType: "json",
                },
                placeholder: "Select a native language",
                minimumInputLength: 2,
            });
            $(".variant").select2({
                placeholder: "Select a variant",
            });

            if (jQuery("#myTab li:eq(0) a").tab("show")) {
                $(".tabcounter").text("1/3");
            }
            else if (jQuery("#myTab li:eq(1) a").tab("show")) {
                $(".tabcounter").text("2/3");
            } else {
                $(".tabcounter").text("3/3");
            }

            $(document).on("click", ".next11", function(e) {
                e.preventDefault();
                $(".tabcounter").text("2/3");
                jQuery("#myTab li:eq(1) a").tab("show");
            });

            $(document).on("click", ".next111a", function(e) {
                e.preventDefault();
                if ($(this).attr("href") == "#home"){
                    $(".tabcounter").text("2/3");
                    jQuery("#myTab li:eq(1) a").tab("show");
                }
                else if ($(this).attr("href") == "#profile") {
                    $(".tabcounter").text("3/3");
                    jQuery("#myTab li:eq(2) a").tab("show");
                }
            });

            if (!' . $user_task_limitation_current_user['limit_profile_changes'] . ') {
            var userQualifiedPairsCount = parseInt(getSetting("userQualifiedPairsCount"));
            for (select_count = 0; select_count < userQualifiedPairsCount; select_count++) {
                Count();

                if($("#btnclick").text() >= parseInt(getSetting("userQualifiedPairsLimit"))) {
                  $("#add").hide();
                } else {
                  $("#add").show();
                }

                var fieldWrapper = $("<div class=\"row-fluid\" id=\"field" + select_count + "\"/>");
                fieldWrapper.data("idx", select_count);
                var fName = $("<div class=\"span5\"><select name=\"language_code_source_" + select_count + "\" id=\"language_code_source_" + select_count + "\" class=\"fieldtype\"><option value>--Select a language--</option>' . $source_lang . '</select></div>");
                var fType = $("<div class=\"span4\"><select name=\"language_code_target_" + select_count + "\" id=\"language_code_target_" + select_count + "\" class=\"fieldtype\"><option value>--Select a language--</option>' . $target_lang . '</select></div>");
                var fTypee = $("<div class=\"span2\"><select name=\"qualification_level_" + select_count + "\" id=\"qualification_level_" + select_count + "\" style=\"width: 75%\" class=\"fieldtype1\"><option value>--Select--</option>' . $qualification_level . '</select></div>");

                fieldWrapper.append(fName);
                fieldWrapper.append(fType);

                if (admin == "1") {
                    fieldWrapper.append(fTypee);
                }             

                if (select_count == 0) {
                    var tool6tip = "<i class=\"icon-question-sign\" id=\"tool6\" data-toggle=\"tooltip\" title=\"Please choose your native language.\"></i>";
                    var addButton = $("<div class=\"span1\" style=\"\"><input type=\"button\" class=\"add\" id=\"add\" value=\"+\" title=\"Add another translation pair.\" /><div>");
                    fieldWrapper.append(addButton);
                } else {
                    var removeButton = $("<div class=\"span1\" style=\"\"><input type=\"button\" class=\"remove\" value=\"-\"  /><div>");
                    removeButton.click(function() {
                        Count1();
                        if ($("#btnclick").text() <= parseInt(getSetting("userQualifiedPairsLimit"))) {
                            $("#add").show();
                        } else {
                            $("#add").hide();
                        }
                        $(this).parent().remove();
                    });
                    fieldWrapper.append(removeButton);
                }
                $("#buildyourform").append(fieldWrapper);
                $(".fieldtype").select2({
                    placeholder: "--Select a language--",
                });
                if (getSetting("userQualifiedPairLanguageCodeSource_" + select_count) != "") {
                    $("#language_code_source_" + select_count).select2().val(getSetting("userQualifiedPairLanguageCodeSource_" + select_count)).trigger("change");
                    $("#language_code_target_" + select_count).select2().val(getSetting("userQualifiedPairLanguageCodeTarget_" + select_count)).trigger("change");
                    $("#qualification_level_"  + select_count).select2().val(getSetting("userQualifiedPairQualificationLevel_" + select_count)).trigger("change");
                }
            }
            $("#language_code_source_0").rules("add", { required: true, notEqualTo:"#language_code_target_0"});
            $("#language_code_target_0").rules("add", { required: true, notEqualTo:"#language_code_source_0" });
            }
        });

        $(document).on("click", "#btnTrigger", function(e) {
            e.preventDefault();
            if ($(this).attr("href") == "#home") {
                $(".tabcounter").text("1/3");
                jQuery("#myTab li:eq(0) a").tab("show");
            }
            else if ($(this).attr("href") == "#profile") {
            }  else if ($(this).attr("href") == "#verifications") {
                $(".tabcounter").text("3/3");
                jQuery("#myTab li:eq(2) a").tab("show");
            }
        });

        $(document).on("click", "#btnTrigger1999", function(e) {
            e.preventDefault();
            if ($(this).attr("href") == "#home") {
                $(".tabcounter").text("1/3");
                jQuery("#myTab li:eq(0) a").tab("show");
            }
            else if ($(this).attr("href") == "#profile1") {
                var valid = true;
                var i = 0;
                var $inputs = $(this).closest("div").find("input");

                $inputs.each(function() {
                    if (!validator.element(this) && valid) {
                        valid = false;
                    }
                });

                if (valid) {
                  $(".tabcounter").text("2/3");
                  jQuery("#myTab li:eq(1) a").tab("show");
                } else {
                  $(".tabcounter").text("1/3");
                  jQuery("#myTab li:eq(0) a").tab("show");
                }
            } else if ($(this).attr("href") == "#verifications") {
                $(".tabcounter").text("3/3");
                jQuery("#myTab li:eq(2) a").tab("show");
            }
        });

        $(document).on("click", "#btnTrigger11", function(e) {
            e.preventDefault();
            if ($(this).attr("href") == "#home") {
                $(".tabcounter1").text("1/3");
                jQuery("#myTab li:eq(0) a").tab("show");
            }
            else if ($(this).attr("href") == "#profile1") {
                $(".tabcounter1").text("2/3");
                jQuery("#myTab li:eq(1) a").tab("show");
            } else if ($(this).attr("href") == "#verifications") {
                $(".tabcounter1").text("3/3");
                jQuery("#myTab li:eq(2) a").tab("show");
            }
        });

        $(".btn").click(function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        });

        var count = 0;
        function Count() {
            count++;
            $("#btnclick").text(count);
            return false;
        }

        function Count1() {
            count--;
            $("#btnclick").text(count);
            return false;
        }

        // Build language input fields
        if (!' . $user_task_limitation_current_user['limit_profile_changes'] . ') $(document).on("click", "#add", function(e) {
            var select_count = $("#btnclick").text();
            Count();

            if ($("#btnclick").text() == parseInt(getSetting("userQualifiedPairsLimit"))) {
              $("#add").hide();
            } else {
              $("#add").show();
            }
            e.preventDefault();
            var lastField = $("#buildyourform div:last");

            var intId = (lastField && lastField.length && lastField.data("idx") + 1) || 1;

            var fieldWrapper = $("<div class=\"row-fluid\" id=\"field" + intId + "\"/>");
            fieldWrapper.data("idx", intId);

            var fName = $("<div class=\"span5\"><select name=\"language_code_source_" + select_count + "\" id=\"language_code_source_" + select_count + "\" class=\"fieldtype\" required=\"required\"><option value>--Select a language--</option>' . $source_lang . '</select></div>");
            var fType = $("<div class=\"span4\"><select name=\"language_code_target_" + select_count + "\" id=\"language_code_target_" + select_count + "\" class=\"fieldtype\" required=\"required\"><option value>--Select a language--</option>' . $target_lang . '</select></div>");
            var fTypee = $("<div class=\"span2\"><select name=\"qualification_level_" + select_count + "\" id=\"qualification_level_" + select_count + "\" style=\"width: 75%\" class=\"fieldtype1\"><option value>--Select--</option>' . $qualification_level . '</select></div>");
            var removeButton = $("<div class=\"span1\" style=\"\"><input type=\"button\" class=\"remove\" value=\"-\"  /><div>");

            removeButton.click(function() {
                Count1();
                if ($("#btnclick").text() <= parseInt(getSetting("userQualifiedPairsLimit"))) {
                    $("#add").show();
                } else {
                    $("#add").hide();
                }
                // console.log($(this));
                $(this).parent().remove();
            });

            fieldWrapper.append(fName);
            fieldWrapper.append(fType);
            var admin = "' . (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) ? 1 : 0) . '";

            if (admin == "1") {
                fieldWrapper.append(fTypee);
            }
            fieldWrapper.append(removeButton);
         
            $("#language_code_source_"+ select_count).rules("add", { required: true, notEqualTo: "#language_code_target_"+ select_count });
            $("#language_code_target_"+ select_count).rules("add", { required: true, notEqualTo: "#language_code_source_"+ select_count  });

            $("#buildyourform").append(fieldWrapper);
            $(".fieldtype").select2({
                placeholder: "--Select a language--",
            });

            $(".fieldtype1").select2({
                placeholder: "--Select--",
                width: "resolve"
            });
        });

        $(document).ready(function() {
        $(".capabilities").change(function() {
            if($(this).is(":checked")) {
                $("#ch1").hide();
            }
            else {
                $("#ch1").show(); 
            }
        });

        $(".capabilities").on("change", function() {
            var capabilities_no = $("input.capabilities:checked").length;
            if(capabilities_no == 0){
                $("#ch1").show();
            }else{
                $("#ch1").hide();
            }
        });

        $(".expertise").on("change", function() {
            var expertise_no = $("input.expertise:checked").length;
            if(expertise_no == 0){
                $("#ch").show();
            }else{
                $("#ch").hide();
            }
        });

        $("#nativeLanguageSelect").change(function(){
            $(this).valid();
        });
        $("#nativeCountrySelect").change(function(){
            $(this).valid();
        });
        $("#nativeCountrySelect").change(function(){
            $(this).valid();
        });

        if (!' . $user_task_limitation_current_user['limit_profile_changes'] . ') {
        $("#language_code_source_0").change(function(){
            $(this).valid();
        });
        $(document).on("change", "#language_code_source_1", function(){
            $("#language_code_source_1").rules("add", { notEqualTo: "#language_code_target_1" });
            $(this).valid();
            
        });
        $(document).on("change", "#language_code_source_2", function(){
            $("#language_code_source_2").rules("add", { notEqualTo: "#language_code_target_2" });
            $(this).valid();
        });
        $(document).on("change", "#language_code_source_3", function(){
            $("#language_code_source_3").rules("add", { notEqualTo: "#language_code_target_3" });
            $(this).valid();
        });
        $(document).on("change", "#language_code_source_4", function(){
            $("#language_code_source_4").rules("add", { notEqualTo: "#language_code_target_4" });
            $(this).valid();
        });
        $(document).on("change", "#language_code_source_5", function(){
            $("#language_code_source_5").rules("add", { notEqualTo: "#language_code_target_5" });
            $(this).valid();
        });

        $("#language_code_target_0").change(function(){
            $(this).valid();
        });
        $(document).on("change", "#language_code_target_1", function(){
            $("#language_code_target_1").rules("add", { notEqualTo: "#language_code_source_1" });
            $(this).valid();
            
        });
        $(document).on("change", "#language_code_target_2", function(){
            $("#language_code_target_2").rules("add", { notEqualTo: "#language_code_source_2" });
            $(this).valid();
        });
        $(document).on("change", "#language_code_target_3", function(){
            $("#language_code_target_3").rules("add", { notEqualTo: "#language_code_source_3" });
            $(this).valid();
        });
        $(document).on("change", "#language_code_target_4", function(){
            $("#language_code_target_4").rules("add", { notEqualTo: "#language_code_source_4" });
            $(this).valid();
        });
        $(document).on("change", "#language_code_target_5", function(){
            $("#language_code_target_5").rules("add", { notEqualTo: "#language_code_source_5" });
            $(this).valid();
        });
        }
    });        
        </script>';

        $template_data = array_merge($template_data, array(
            'siteLocation'     => Common\Lib\Settings::get('site.location'),
            'siteAPI'          => Common\Lib\Settings::get('site.api'),
            'roles'            => $roles,
            'user'             => $user,
            'user_id'          => $user_id,
            'userPersonalInfo' => $userPersonalInfo,
            'countries' => $countries,
            'language_selection' => $language_selection,
            'nativeLanguageSelectCode' => $nativeLanguageSelectCode,
            'nativeLanguageSelectName' => $nativeLanguageSelectName,
            'nativeCountrySelectCode'  => $nativeCountrySelectCode,
            'userQualifiedPairs'       => $userQualifiedPairs,
            'userQualifiedPairsLimit'  => ($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) ? 120 : max(6, count($userQualifiedPairs)),
            'userQualifiedPairsCount'  => count($userQualifiedPairs),
            'url_list'          => $url_list,
            'capability_list'   => $capability_list,
            'capabilityCount'   => count($capability_list),
            'expertise_list'    => $expertise_list,
            'expertiseCount'    => count($expertise_list),
            'howheard_list'     => $howheard_list,
            'certification_list' => $certification_list,
            'in_kind'           => $userDao->get_special_translator($user_id),
            'communications_consent' => $userDao->get_communications_consent($user_id),
            'user_task_limitation_current_user' => $user_task_limitation_current_user,
            'extra_scripts' => $extra_scripts,
            'sesskey'       => $sesskey,
        ));

        return UserRouteHandler::render('user/user-private-profile.tpl', $response);
    }

    public static function user_rate_pairs(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $user_id = $args['user_id'];

        $userDao = new DAO\UserDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $user = $userDao->getUser($user_id);
        if (!is_object($user)) {
            UserRouteHandler::flash("error", Lib\Localisation::getTranslation('common_login_required_to_access_page'));
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("login"));
        }

        list($source_options, $target_options) = $userDao->generate_user_rate_pair_selections();
        $user_rate_pairs = $userDao->get_user_rate_pairs($user_id);

        if ($post = $request->getParsedBody()) {
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'user_rate_pairs')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);
            for ($i = 0; $i < 121; $i++) {
                if (!empty($post["language_id_source_$i"]) && !empty($post["language_country_id_target_$i"]) && !empty($post["task_type_$i"]) && !empty($post["unit_rate_$i"]) && is_numeric($post["unit_rate_$i"])) {
                    $target = explode('-', $post["language_country_id_target_$i"]);
                    $post["unit_rate_$i"] = (float)$post["unit_rate_$i"];
                    $found = false;
                    foreach ($user_rate_pairs as $user_rate_pair) {
                        if (($post["language_id_source_$i"] == $user_rate_pair['language_id_source']) &&
                            ($target[0] == $user_rate_pair['language_id_target']) &&
                            ($target[1] == $user_rate_pair['country_id_target']) &&
                            ($post["task_type_$i"] == $user_rate_pair['task_type'])
                        ) {
                            $found = true;
                            if ($post["unit_rate_$i"] != $user_rate_pair['unit_rate']) {
                                $userDao->update_user_rate_pair(
                                    $user_id,
                                    $post["task_type_$i"],
                                    $post["language_id_source_$i"],
                                    $target[0],
                                    $target[1],
                                    $post["unit_rate_$i"]
                                );
                            }
                        }
                    }
                    if (!$found) {
                        $userDao->create_user_rate_pair(
                            $user_id,
                            $post["task_type_$i"],
                            $post["language_id_source_$i"],
                            $target[0],
                            $target[1],
                            $post["unit_rate_$i"]
                        );
                    }
                }
            }
            foreach ($user_rate_pairs as $user_rate_pair) {
                $found = false;
                for ($i = 0; $i < 121; $i++) {
                    if (!empty($post["language_id_source_$i"]) && !empty($post["language_country_id_target_$i"]) && !empty($post["task_type_$i"]) && !empty($post["unit_rate_$i"]) && is_numeric($post["unit_rate_$i"])) {
                        $target = explode('-', $post["language_country_id_target_$i"]);
                        if (($post["language_id_source_$i"] == $user_rate_pair['language_id_source']) &&
                            ($target[0] == $user_rate_pair['language_id_target']) &&
                            ($target[1] == $user_rate_pair['country_id_target']) &&
                            ($post["task_type_$i"] == $user_rate_pair['task_type'])
                        ) {
                            $found = true;
                        }
                    }
                }
                if (!$found) {
                     $userDao->remove_user_rate_pair(
                        $user_id,
                        $user_rate_pair['task_type'],
                        $user_rate_pair['language_id_source'],
                        $user_rate_pair['language_id_target'],
                        $user_rate_pair['country_id_target']
                    );
                }
            }
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('user-public-profile', array('user_id' => $user_id)));
        }
        if (empty($user_rate_pairs)) {
            $user_rate_pairs = [['task_type' => '', 'language_id_source' => '', 'language_country_id_target' => '', 'unit_rate' => '']];
        }

        $task_type = '';
        $source_lang = '';
        $target_lang = '';
        foreach (Common\Enums\TaskTypeEnum::$enum_to_UI as $key => $ui) {
            if ($ui['enabled']) $task_type .= "<option value=$key>{$ui['type_text']}</option>";
        }
        foreach ($source_options as $key => $language) {
            $source_lang .= "<option value=$key>$language</option>";
        }
        foreach ($target_options as $key => $language) {
            $target_lang .= "<option value=$key>$language</option>";
        }

        $extra_scripts  = '<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/js/bootstrap.min.js" type="text/javascript"></script> ';
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/Parameters.js\"></script>";
        $extra_scripts .= '<link href="'  . $app->getRouteCollector()->getRouteParser()->urlFor('home') . 'resources/css/select2.min.css" rel="stylesheet" />';
        $extra_scripts .= '<script src="' . $app->getRouteCollector()->getRouteParser()->urlFor('home') . 'ui/js/select2.min.js"></script>';
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/UserPrivateProfile6.js\"></script>";
        $extra_scripts .= '<script type="text/javascript">
        var row_count = 0;

        $(document).ready(function() {
            $("#tool_type").tooltip();
            $("#tool_source").tooltip();
            $("#tool_target").tooltip();
            $("#tool_rate").tooltip();

            row_count = parseInt(getSetting("user_rate_pairs_count"));
            for (let row = 0 ; row < row_count; row++) {
                add_row(row);
                if (getSetting("user_rate_pair_language_id_source_" + row) != "") {
                    $("#task_type_"                  + row).select2().val(getSetting("user_rate_pair_task_type_"                  + row)).trigger("change");
                    $("#language_id_source_"         + row).select2().val(getSetting("user_rate_pair_language_id_source_"         + row)).trigger("change");
                    $("#language_country_id_target_" + row).select2().val(getSetting("user_rate_pair_language_country_id_target_" + row)).trigger("change");
                    $("#unit_rate_"                  + row).val(getSetting("user_rate_pair_unit_rate_" + row));
                }
            }
        });

        function add_row(row) {
            var fieldWrapper = $("<div class=\"row-fluid\" id=\"field" + row + "\"/>");
            var f_task_type  = $("<div class=\"span3\"><select name=\"task_type_"                  + row + "\" id=\"task_type_"                  + row + "\" class=\"field_select_type\"><option value>--Select a task type--</option>' . $task_type   . '</select></div>");
            f_task_type.change(function () {
                var type = $("#task_type_" + row).select2().val();
                $("#unit_rate_" + row).val(        $("#default_unit_rate_"                       + type).html());
                $("#unit_text_" + row).html("$/" + $("#pricing_and_recognition_unit_text_hours_" + type).html());
            });
            var f_source     = $("<div class=\"span3\"><select name=\"language_id_source_"         + row + "\" id=\"language_id_source_"         + row + "\" class=\"field_select_lang\"><option value>--Select a language--</option>'  . $source_lang . '</select></div>");
            var f_target     = $("<div class=\"span3\"><select name=\"language_country_id_target_" + row + "\" id=\"language_country_id_target_" + row + "\" class=\"field_select_lang\"><option value>--Select a language--</option>'  . $target_lang . '</select></div>");
            var f_unit_rate  = $("<div class=\"span2\"><input  name=\"unit_rate_"                  + row + "\" id=\"unit_rate_"                  + row + "\" class=\"field_unit_rate\" type=\"text\" value=\"\" style=\"width: 50%\" /><br /><div id=\"unit_text_" + row +  "\"></div></div>");
            fieldWrapper.append(f_task_type);
            fieldWrapper.append(f_source);
            fieldWrapper.append(f_target);
            fieldWrapper.append(f_unit_rate);

            if (row == 0) {
                var addButton = $("<div class=\"span1\" style=\"\"><input type=\"button\" class=\"add\" id=\"add\" value=\"+\" title=\"Add another rate pair.\" /><div>");
                fieldWrapper.append(addButton);
            } else {
                var removeButton = $("<div class=\"span1\" style=\"\"><input type=\"button\" class=\"remove\" value=\"-\" /><div>");
                removeButton.click(function() {
                    row_count--;
                    $(this).parent().remove();
                });
                fieldWrapper.append(removeButton);
            }
            $("#buildyourform").append(fieldWrapper);
            $(".field_select_lang").select2({
                placeholder: "--Select a language--",
            });
            $(".field_select_type").select2({
                placeholder: "--Select a task type--",
            });
        }

        $(document).on("click", "#add", function(e) {
            e.preventDefault();
            add_row(row_count++);
        });
        </script>';

        $template_data = array_merge($template_data, array(
            'siteLocation'     => Common\Lib\Settings::get('site.location'),
            'siteAPI'          => Common\Lib\Settings::get('site.api'),
            'user'             => $user,
            'user_id'          => $user_id,
            'user_rate_pairs'       => $user_rate_pairs,
            'user_rate_pairs_count' => count($user_rate_pairs),
            'extra_scripts' => $extra_scripts,
            'sesskey'       => $sesskey,
        ));

        return UserRouteHandler::render('user/user_rate_pairs.tpl', $response);
    }

    public function native_languages(Request $request, Response $response, $args)
    {
        $term = $args['term'];

        $langDao = new DAO\LanguageDao();
        $languages = $langDao->getLanguages();

        $results = [];
        foreach ($languages as $language) {
            $name = $language->getName();
            if (mb_stripos($name, $term) !== false) $results[] = ['id' => $language->getCode(), 'text' => $name];
        }
        header('Content-Type: application/json');
        echo json_encode(['results' => $results]);
        die;
    }

    public static function userCodeOfConduct(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $user_id = $args['user_id'];

        $userDao = new DAO\UserDao();
        $adminDao = new DAO\AdminDao();

        $loggedInUserId = Common\Lib\UserSession::getCurrentUserID();
        $roles = $adminDao->get_roles($loggedInUserId);
        if (!($user_id == $loggedInUserId || ($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)))) {
            UserRouteHandler::flash('error', 'You do not have rights to edit this user');
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }

        if (!$adminDao->isSiteAdmin_any_or_org_admin_any_for_any_org($user_id)) {
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('user-private-profile', ['user_id' => $user_id]));
        }

        if (empty($_SESSION['SESSION_CSRF_KEY'])) {
            $_SESSION['SESSION_CSRF_KEY'] = UserRouteHandler::random_string(10);
        }
        $sesskey = $_SESSION['SESSION_CSRF_KEY']; // This is a check against CSRF (Posts should come back with same sesskey)

        $user = $userDao->getUser($user_id);
        Common\Lib\CacheHelper::unCache(Common\Lib\CacheHelper::GET_USER . $user_id);

        if (!is_object($user)) {
            UserRouteHandler::flash("error", Lib\Localisation::getTranslation('common_login_required_to_access_page'));
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('login'));
        }

        $userPersonalInfo = null;
        try {
            $userPersonalInfo = $userDao->getUserPersonalInformation($user_id);
        } catch (Common\Exceptions\SolasMatchException $e) {
        }

        if ($post = $request->getParsedBody()) {
            if (empty($post['sesskey']) || $post['sesskey'] !== $sesskey || empty($post['displayName'])) {
                UserRouteHandler::flashNow('error', Lib\Localisation::getTranslation('user_private_profile_2'));
            } else {
                $user->setDisplayName($post['displayName']);
                $userPersonalInfo->setFirstName($post['firstName']);
                $userPersonalInfo->setLastName($post['lastName']);

                try {
                    $userDao->updateUser($user);
                    $userDao->updatePersonalInfo($user_id, $userPersonalInfo);

                    if (!empty($post['communications_consent'])) $userDao->insert_communications_consent($user_id, 1);
                    else                                         $userDao->insert_communications_consent($user_id, 0);

                    return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('user-public-profile', ['user_id' => $user_id]));
                } catch (\Exception $e) {
                    UserRouteHandler::flashNow('error', 'Failed to Update');
                }
            }
        }

        $extra_scripts  = "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/Parameters.js\"></script>";
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/user-code-of-conduct.js\"></script>";

        $template_data = array_merge($template_data, array(
            'siteLocation'      => Common\Lib\Settings::get('site.location'),
            'siteAPI'           => Common\Lib\Settings::get('site.api'),
            'user'              => $user,
            'user_id'           => $user_id,
            'userPersonalInfo'  => $userPersonalInfo,
            'communications_consent' => $userDao->get_communications_consent($user_id),
            'extra_scripts'     => $extra_scripts,
            'sesskey'           => $sesskey,
        ));

        return UserRouteHandler::render('user/user-code-of-conduct.tpl', $response);
    }

    public static function userUploads(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $user_id = $args['user_id'];
        $cert_id = $args['cert_id'];

        $userDao = new DAO\UserDao();
        $adminDao = new DAO\AdminDao();

        if (empty($_SESSION['SESSION_CSRF_KEY'])) {
            $_SESSION['SESSION_CSRF_KEY'] = UserRouteHandler::random_string(10);
        }
        $sesskey = $_SESSION['SESSION_CSRF_KEY']; // This is a check against CSRF (Posts should come back with same sesskey)

        $loggedInUserId = Common\Lib\UserSession::getCurrentUserID();
        if ($user_id != $loggedInUserId && !($adminDao->get_roles($loggedInUserId) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER))) return $response;

        $user = $userDao->getUser($user_id);

        $extra_scripts = '';

        $upload_pending = 1;
        if ($post = $request->getParsedBody()) {
            if (
                empty($post['sesskey']) || $post['sesskey'] !== $sesskey || empty($post['note']) || empty($_FILES['userFile']['name']) || !empty($_FILES['userFile']['error'])
                || (($data = file_get_contents($_FILES['userFile']['tmp_name'])) === false)
            ) {
                UserRouteHandler::flashNow('error', 'Could not upload file, you must specify a file and a note');
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
                // UserRouteHandler::flashNow('success', 'Certificate uploaded sucessfully, please click <a href="javascript:window.close();">Close Window</a>');
                UserRouteHandler::flashNow('success', 'Certificate uploaded sucessfully, please close this window to get back to your profile page');
            }
        }

        $certification_list = $userDao->getCertificationList($user_id);

        $template_data = array_merge($template_data, array(
            'user'          => $user,
            'user_id'       => $user_id,
            'cert_id'       => $cert_id,
            'desc'          => empty($certification_list[$cert_id]['desc']) ? '' : $certification_list[$cert_id]['desc'],
            'upload_pending' => $upload_pending,
            'sesskey'       => $sesskey,
        ));

        return UserRouteHandler::render('user/user-uploads.tpl', $response);
    }

    public static function userDownload(Request $request, Response $response, $args)
    {
        $id = $args['id'];

        $userDao = new DAO\UserDao();
        $adminDao = new DAO\AdminDao();

        $certification = $userDao->getUserCertificationByID($id);

        $loggedInUserId = Common\Lib\UserSession::getCurrentUserID();
        if (empty($certification) || ($certification['user_id'] != $loggedInUserId && !($adminDao->get_roles($loggedInUserId) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)))) return $response;

        $destination = Common\Lib\Settings::get('files.upload_path') . "certs/{$certification['user_id']}/{$certification['certification_key']}/{$certification['vid']}/{$certification['filename']}";

        header('Content-type: ' . $certification['mimetype']);
        header("Content-Disposition: attachment; filename=\"" . trim($certification['filename'], '"') . "\"");
        header('Content-length: ' . filesize($destination));
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');
        header('X-Sendfile: ' . realpath($destination));
        die;
    }


    public function users_review(Request $request, Response $response)
    {
        global $app, $template_data;
        $userDao = new DAO\UserDao();

        $all_users = $userDao->users_review();

        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('user/users_review.tpl', $response);
    }

    public function users_new(Request $request, Response $response)
    {
        global $app, $template_data;
        $userDao = new DAO\UserDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $all_users = $userDao->users_new();

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'users_new')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);
            if (!empty($post['max_user_id'])) {
                foreach ($all_users as $user_row) {
                    if ($user_row['user_id'] <= $post['max_user_id']) { // Make sure a new one has not appeared
                        if (empty($user_row['reviewed_text'])) $userDao->updateUserHowheard($user_row['user_id'], 1);
                    }
                }
                $all_users = $userDao->users_new();
            }
        }

        $template_data = array_merge($template_data, array('all_users' => $all_users, 'sesskey' => $sesskey));
        return UserRouteHandler::render('user/users_new.tpl', $response);
    }

    public function download_users_new(Request $request, Response $response, $args)
    {
        $this->download_users_new_unreviewed($request, $response, $args, true);
    }

    public function download_users_new_unreviewed(Request $request, Response $response, $args, $all = false)
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

    public function users_tracked(Request $request, Response $response)
    {
        global $app, $template_data;
        $userDao = new DAO\UserDao();
        $all_users = $userDao->users_tracked();
        $template_data = array_merge($template_data, array('all_users' => $all_users));
        return UserRouteHandler::render('user/users_tracked.tpl', $response);
    }

    public function add_tracking_code(Request $request, Response $response)
    {
        global $app, $template_data;
        $userDao = new DAO\UserDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'add_tracking_code')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            if (!empty($post['tracking_code'])) {
                $url = $userDao->record_referer($post['tracking_code']);
                UserRouteHandler::flashNow('success', "Added Tracking Code (if not already present), URL: $url");
            }
        }
        $referers = $userDao->get_referers();
        foreach ($referers as $i => $referer) {
            $referers[$i] = ['referer' => $referer, 'url' => $userDao->get_referer_link($referer)];
        }
        $template_data = array_merge($template_data, array(
            'sesskey'  => $sesskey,
            'referers' => $referers,
        ));
        return UserRouteHandler::render('user/add_tracking_code.tpl', $response);
    }

    public function download_users_tracked(Request $request, Response $response)
    {
        $userDao = new DAO\UserDao();
        $all_users = $userDao->users_tracked();

        $data = "\xEF\xBB\xBF" . '"Tracked","Name","Created","Native Language","Language Pairs","Biography","Certificates","Email"' . "\n";

        foreach ($all_users as $user_row) {
            $data .= '"' . str_replace('"', '""', $user_row['referer']) . '","' .
                str_replace('"', '""', $user_row['name']) . '","' .
                $user_row['created_time'] . '","' .
                str_replace('"', '""', $user_row['native_language']) . '","' .
                $user_row['language_pairs'] . '","' .
                str_replace(array('\r\n', '\n', '\r'), "\n", str_replace('"', '""', $user_row['bio'])) . '","' .
                $user_row['certificates'] . '","' .
                $user_row['email'] . '"' . "\n";
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="users_tracked.csv"');
        header('Content-length: ' . strlen($data));
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');
        echo $data;
        die;
    }

    /**
     * Generate and return a random string of the specified length.
     *
     * @param int $length The length of the string to be created.
     * @return string
     */
    private static function random_string($length = 15)
    {
        $pool  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pool .= 'abcdefghijklmnopqrstuvwxyz';
        $pool .= '0123456789';
        $poollen = strlen($pool);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($pool, (mt_rand() % ($poollen)), 1);
        }
        return $string;
    }

    public static function userPublicProfile(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $user_id = $args['user_id'];

        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();
        $adminDao = new DAO\AdminDao();
        $langDao = new DAO\LanguageDao();
        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();
        $countryDao = new DAO\CountryDao();
        $loggedInUserId = Common\Lib\UserSession::getCurrentUserID();
        $roles = $adminDao->get_roles($loggedInUserId);

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $private_access = 0;
        if ($loggedInUserId == $user_id) $private_access = 1;

        $user = null;
        try {
            Common\Lib\CacheHelper::unCache(Common\Lib\CacheHelper::GET_USER . $user_id);
            $user = $userDao->getUser($user_id);
        } catch (Common\Exceptions\SolasMatchException $e) {
            UserRouteHandler::flash('error', Lib\Localisation::getTranslation('common_login_required_to_access_page'));
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('login'));
        }
        $userPersonalInfo = null;
        $receive_credit = 0;
        try {
            $userPersonalInfo = $userDao->getUserPersonalInformation($user_id);
            if ($userPersonalInfo && $userPersonalInfo->getReceiveCredit()) $receive_credit = 1;
        } catch (Common\Exceptions\SolasMatchException $e) {
        }

        $testing_center_projects_by_code = [];
        $testing_center_projects = $projectDao->get_testing_center_projects($user_id, $testing_center_projects_by_code);
        $supported_ngos_paid = $userDao->supported_ngos_paid($user_id);

        $show_create_memsource_user = ($roles & SITE_ADMIN) && !$userDao->get_memsource_user($user_id) && ($adminDao->get_roles($user_id) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER));

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'userPublicProfile')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            if (isset($post['revokeBadge']) && isset($post['badge_id']) && $post['badge_id'] != "") {
                $badge_id = $post['badge_id'];
                $userDao->removeUserBadge($user_id, $badge_id);
            }

            if (isset($post['revoke'])) {
                $org_id = $post['org_id'];
                $adminDao->adjust_org_admin($user_id, $org_id, NGO_ADMIN | NGO_PROJECT_OFFICER | NGO_LINGUIST, 0);
                $adminDao->adjust_org_admin($user_id, 0, 0, LINGUIST);
                error_log("$user_id Leave Organisation $org_id (by $loggedInUserId)");
            }

            if (isset($post['referenceRequest'])) {
                $userDao->requestReferenceEmail($user_id);
                $template_data = array_merge($template_data, array("requestSuccess" => true));
            }

            if (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) && isset($post['requestDocuments'])) {
                $ch = curl_init('https://app.asana.com/api/1.0/tasks');
                $pm = $userDao->getUser($loggedInUserId);
                $pm_info = $userDao->getUserPersonalInformation($loggedInUserId);
                $full_name = !empty($userPersonalInfo) ? $userPersonalInfo->getFirstName() . ' ' . $userPersonalInfo->getLastName() : '';
                $paid_work = "\n";
                foreach ($supported_ngos_paid as $ngo) {
                    $paid_work .= $ngo['org_name'] . "\n";
                }
                $objDateTime = new \DateTime();
                $objDateTime->add(\DateInterval::createFromDateString('3 day'));
                $data = ['data' => [
                    'name' => "Documentation for $full_name",
                    'projects' => ['1201514646699532'],
                    'due_at' => $objDateTime->format('c'),
                    'notes' =>
                        'PM: ' . $pm_info->getFirstName() . ' ' . $pm_info->getLastName() . ' - ' . $pm->getEmail() . "\n" .
                        "Paid work: $paid_work" .
                        "Linguist: $full_name - " . $user->getEmail() . " - https://twbplatform.org/$user_id/profile/"
                ]];
                $payload = json_encode($data);
error_log("payload: $payload");//(**)
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . Common\Lib\Settings::get('asana.api_key6')]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);
error_log("result: $result");//(**)
                UserRouteHandler::flashNow('success', 'Posted to Asana');
            }

            if (isset($post['PrintRequest']) || isset($post['PrintRequestLetter'])) {
                $request_type = 0;
                if (isset($post['PrintRequestLetter'])) $request_type = 1;
                $userDao->insert_print_request($user_id, $request_type, $loggedInUserId);
                UserRouteHandler::flashNow('success', 'Print request made for user');
            }

            if (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) && !empty($post['admin_comment'])) {
                if (empty($post['comment']) || (int)$post['work_again'] < 1 || (int)$post['work_again'] > 5) {
                    UserRouteHandler::flashNow('error', 'You must enter a comment and a score between 1 and 5');
                } else {
                    $userDao->insert_admin_comment($user_id, $loggedInUserId, (int)$post['work_again'], $post['comment']);
                }
            }

            if (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) && !empty($post['mark_comment_delete'])) {
                $userDao->delete_admin_comment($post['comment_id']);
            }

            if (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) && !empty($post['mark_adjust_points'])) {
                if (empty($post['comment']) || !is_numeric($post['points'])) {
                    UserRouteHandler::flashNow('error', 'You must enter a comment and integer points');
                } else {
                    $userDao->insert_adjust_points($user_id, $loggedInUserId, (int)$post['points'], $post['comment']);
                }
            }

            if (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) && !empty($post['mark_points_delete'])) {
                $userDao->delete_adjust_points($post['comment_id']);
            }

            if (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) && !empty($post['mark_adjust_points_strategic'])) {
                if (empty($post['comment']) || !is_numeric($post['points'])) {
                    UserRouteHandler::flashNow('error', 'You must enter a comment and integer points');
                } else {
                    $userDao->insert_adjust_points_strategic($user_id, $loggedInUserId, (int)$post['points'], $post['comment']);
                }
            }

            if (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) && !empty($post['mark_points_delete_strategic'])) {
                $userDao->delete_adjust_points_strategic($post['comment_id']);
            }

            if (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) && !empty($post['mark_reviewed'])) {
                $userDao->updateUserHowheard($user_id, 1);
            }

            if ($show_create_memsource_user && !empty($post['mark_create_memsource_user'])) {
                if ($memsource_user_uid = $userDao->create_memsource_user($user_id)) {
                    UserRouteHandler::flashNow('success', "Memsource user $memsource_user_uid created");
                    $show_create_memsource_user = 0;
                } else UserRouteHandler::flashNow('error', "Unable to create Memsource user for $user_id");
            }

            if (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) && !empty($post['mark_certification_reviewed'])) {
                $userDao->updateCertification($post['certification_id'], 1);
            }

            if (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) && !empty($post['mark_certification_delete'])) {
                $userDao->deleteCertification($post['certification_id']);
            }

            if (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) && !empty($post['mark_user_task_limitation'])) {
                if (!preg_match('/^[0-9]*$/', $post['max_not_comlete_tasks']) || !strlen($post['max_not_comlete_tasks']) ||
                    !preg_match('/^[,0-9]*$/', $post['allowed_types']) || $post['allowed_types'] === '0' ||
                    !preg_match('/^[,0-9]*$/', $post['excluded_orgs']) ||
                    !preg_match('/^[0-9]*$/', $post['limit_profile_changes']) || !strlen($post['limit_profile_changes'])
                ) UserRouteHandler::flashNow('error', 'You must enter the correct format for values');
                else {
                    $taskDao->insert_update_user_task_limitation($user_id, $loggedInUserId, $post['max_not_comlete_tasks'], $post['allowed_types'], $post['excluded_orgs'], $post['limit_profile_changes']);
                    UserRouteHandler::flashNow('success', 'Success');
                }
            }

            if (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) && !empty($post['mark_linguist_payment_information'])) {
                if (empty($post['country_id'])) UserRouteHandler::flashNow('error', 'You must enter a valid Country');
                elseif (empty($post['linguist_name'])) UserRouteHandler::flashNow('error', 'You must enter an Official Name');
                elseif (substr($post['google_drive_link'], 0, 25) != 'https://drive.google.com/') UserRouteHandler::flashNow('error', 'You must enter a valid Google Drive Folder Link');
                else {
                    $taskDao->insert_update_linguist_payment_information($user_id, $loggedInUserId, $post['country_id'], $post['google_drive_link'], $post['linguist_name']);
                    UserRouteHandler::flashNow('success', 'Success');
                }
            }

            if (($roles & (SITE_ADMIN | COMMUNITY_OFFICER)) && !empty($post['send_contract'])) {
                if ($userDao->insert_sent_contract($user, $userPersonalInfo, $loggedInUserId)) UserRouteHandler::flash('error', 'Connection to Docusign Failed');
                else                                                                           UserRouteHandler::flash('success', 'Success');
                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('user-public-profile', ['user_id' => $user_id]));
            }
        }

        $archivedJobs = $userDao->getUserArchivedTasks($user_id, 0, 10);
        $user_tags = $userDao->getUserTags($user_id);
        $user_orgs = $userDao->find_all_orgs_for_user($user_id);
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

        $extra_scripts = "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}";
        $extra_scripts .= "resources/bootstrap/js/confirm-remove-badge.js\"></script>";
        $extra_scripts .= "<script type=\"text/javascript\"  src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/eligible.js\" defer ></script>";
        $extra_scripts .= file_get_contents(__DIR__ . "/../js/profile.js");
        $extra_scripts .= "<script type=\"text/javascript\" src=\"https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js\"></script>";
        $extra_scripts .= '<script type="text/javascript">
        $(document).ready(function() {
            $("#printrequest").DataTable({
                "ajax": {
                    "url": "/'.$user_id.'/0/printrequest",
                    "dataSrc": "",
                    "type": "GET",
                    "datatype": "json"
                },
                columns: [
                    { data: "date_of_request" },
                    { data: "request_by" },
                    { data: "word_count" },
                    { data: "hours_donated_for_cert" },
                    { data: "valid_key",
                        "render": function ( data, type, row, meta ) {
                            return data;
                        }
                    },
                ],
                order: [[0, "desc"]],
            });

            $("#printrequestletter").DataTable({
                "ajax": {
                    "url": "/'.$user_id.'/1/printrequest",
                    "dataSrc": "",
                    "type": "GET",
                    "datatype": "json"
                },
                columns: [
                    { data: "date_of_request" },
                    { data: "request_by" },
                    { data: "word_count" },
                    { data: "hours_donated_for_cert" },
                    { data: "valid_key",
                        "render": function ( data, type, row, meta ) {
                            return data;
                        }
                    },
                ],
                order: [[0, "desc"]],
            });

            $("body").on("click", ".download-cert", function(e) {
                e.preventDefault();
                var valid_key = $(this).data("id");
                $.ajax({
                    type: "POST",
                    url: "/"+valid_key+"/generatevolunteercertificate",
                    data: {
                        valid_key: valid_key
                    },
                    dataType: "json"
                }).done(function(response) {
                    setTimeout(function() {
                        window.open("/"+response.file_name+"/generatevolunteercertificate", "_blank");
                    }, 2000);
                });
            });
        });
        </script>';
        $extra_styles = "<link rel=\"stylesheet\" href=\"https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css\"/>";

        if (isset($userPersonalInfo)) {
            $langPref = $langDao->getLanguage($userPersonalInfo->getLanguagePreference());
            $langPrefName = $langPref->getName();
        } else {
            $langPrefName = '';
        }

        $template_data = array_merge($template_data, array(
            'sesskey' => $sesskey,
            "badges" => $badges,
            "orgList" => $orgList,
            "user_orgs" => $user_orgs,
            "current_page" => "user-profile",
            "archivedJobs" => $archivedJobs,
            "user_tags" => $user_tags,
            "this_user" => $user,
            "extra_scripts" => $extra_scripts,
            'extra_styles' => $extra_styles,
            "userPersonalInfo" => $userPersonalInfo,
            "langPrefName" => $langPrefName,
            "userQualifiedPairs" => $userQualifiedPairs,
            'user_rate_pairs'    => ($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER)) ? $userDao->get_user_rate_pairs($user_id) : 0,
        ));

        if ($private_access || ($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER))) {
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
            $template_data = array_merge($template_data, array(
                "interval"       => $interval,
                "lastSent"       => $lastSent,
                "strict"         => $strict,
            ));
        }

        $euser_id = $user_id + 999999; // Ensure we don't use identical (shared profile) key as word count badge (for a bit of extra security)
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt("$euser_id", 'aes-256-cbc', base64_decode(Common\Lib\Settings::get('badge.key')), 0, $iv);
        $key = bin2hex("$encrypted::$iv");
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt("$user_id", 'aes-256-cbc', base64_decode(Common\Lib\Settings::get('badge.key')), 0, $iv);
        $bkey = bin2hex("$encrypted::$iv");
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt("-$user_id", 'aes-256-cbc', base64_decode(Common\Lib\Settings::get('badge.key')), 0, $iv);
        $hourkey = bin2hex("$encrypted::$iv");

        $howheard = $userDao->getUserHowheards($user_id);
        if (empty($howheard)) {
            $howheard = ['reviewed' => 1, 'howheard_key' => ''];
        } else {
            $howheard = $howheard[0];
        }

        $uuid = 0;
        if (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER))) $uuid = $userDao->get_password_reset_request_uuid($user_id);

        $valid_key_certificate = $userDao->get_print_request_valid_key_for_user($user_id, 0);
        $valid_key_reference_letter = $userDao->get_print_request_valid_key_for_user($user_id, 1);

        $template_data = array_merge($template_data, array(
            'user_has_strategic_languages' => $userDao->user_has_strategic_languages($user_id),
            'roles'                  => $roles,
            'user_badges'            => $userDao->get_points_for_badges($user_id),
            'user_badge_name'        => !empty($userPersonalInfo) ? wordwrap($userPersonalInfo->getFirstName() . ' ' . $userPersonalInfo->getLastName(), 20, '\n') : '',
            'key'                    => $key,
            'bkey'                   => $bkey,
            'hourkey'                => $hourkey,
            'private_access'         => $private_access,
            'receive_credit'         => $receive_credit,
            'howheard'               => $howheard,
            'url_list'               => $userDao->getURLList($user_id),
            'expertise_list'         => $userDao->getExpertiseList($user_id),
            'capability_list'        => $userDao->getCapabilityList($user_id),
            'supported_ngos'         => $userDao->supported_ngos($user_id),
            'supported_ngos_paid'    => $supported_ngos_paid,
            'quality_score'          => $userDao->quality_score($user_id),
            'admin_comments'         => $userDao->admin_comments($user_id),
            'admin_comments_average' => $userDao->admin_comments_average($user_id),
            'adjust_points'          => $userDao->adjust_points($user_id),
            'adjust_points_strategic'=> $userDao->adjust_points_strategic($user_id),
            'certifications'         => $userDao->getUserCertifications($user_id),
            'tracked_registration'   => $userDao->get_tracked_registration($user_id),
            'testing_center_projects_by_code' => $testing_center_projects_by_code,
            'show_create_memsource_user'      => $show_create_memsource_user,
            'uuid' => $uuid,
            'valid_key_certificate' => $valid_key_certificate,
            'valid_key_reference_letter' => $valid_key_reference_letter,
            'admin_role' => $adminDao->isSiteAdmin_any_or_org_admin_any_or_linguist_for_any_org($user_id),
            'user_task_limitation' => $taskDao->get_user_task_limitation($user_id),
            'linguist_payment_information' => $taskDao->get_linguist_payment_information($user_id),
            'countries' => $countryDao->getCountries(),
            'user_task_limitation_current_user' => $taskDao->get_user_task_limitation($loggedInUserId),
            'sent_contracts' => $userDao->get_sent_contracts($user_id),
            'user_invoices'  => $userDao->getUserInvoices($user_id),
        ));
        return UserRouteHandler::render("user/user-public-profile.tpl", $response);
    }

    public static function userPrintRequest(Request $request, Response $response, $args)
    {
        $user_id = $args['user_id'];
        $request_type = $args['request_type'];
        $userDao = new DAO\UserDao();
        $print_data = $userDao->get_print_request_by_user($user_id, $request_type);
        $print_data_val = [];
        foreach ($print_data as $key => $value) {
            $user_personal_info = $userDao->getUserPersonalInformation($print_data[$key]['request_by']);
            $firstName = $user_personal_info->firstName;
            array_push($print_data_val, [
                'date_of_request' => $print_data[$key]['date_of_request'],
                'request_by' => '<a href="/' . $print_data[$key]['request_by'] . '/profile" target="_blank">' . $firstName . '</a>',
                'word_count' => $print_data[$key]['word_count'],
                'hours_donated_for_cert' => $print_data[$key]['hours_donated_for_cert'],
                'valid_key' =>  $print_data[$key]['valid_key']
            ]);
        }
        echo json_encode($print_data_val);
        die();
    }

    public static function generatevolunteercertificate(Request $request, Response $response, $args)
    {
        require_once 'resources/TCPDF-main/examples/tcpdf_include.php';
        $valid_key = $args['valid_key'];
        $userDao = new DAO\UserDao();
        $print_data_by_key = $userDao->get_print_request_by_valid_key($valid_key); 
        $user_id = $print_data_by_key[0]['user_id'];
        $user = $userDao->getUser($user_id);
        $userinfo = $userDao->getUserPersonalInformation($user_id);
        $name = $userinfo->firstName . ' ' . $userinfo->lastName;
        $firstName = $userinfo->firstName;
        $lastName = $userinfo->lastName;
        $words_total = $print_data_by_key[0]['word_count'];
        $words_donated = $print_data_by_key[0]['words_donated'];
        $words_paid = $words_total - $words_donated;
        $hours_donated = $print_data_by_key[0]['hours_donated'];
        $hours_paid = $print_data_by_key[0]['hours_paid'];
        $word_words = [];
        if ($words_donated || $hours_donated) {
            $donated = [];
            if ($words_donated) $donated[] = "$words_donated words";
            if ($hours_donated) $donated[] = "$hours_donated hours";
            $word_words[] = implode(' and ', $donated) . ' in volunteer tasks';
        }
        if ($words_paid || $hours_paid) {
            $paid = [];
            if ($words_paid) $paid[] = "$words_paid words";
            if ($hours_paid) $paid[] = "$hours_paid hours";
            $word_words[] = implode(' and ', $paid) . ' in paid tasks';
        }
        $word_words = implode(' and ', $word_words);

        $hours_words = '';
        $hours_donated_for_cert = $print_data_by_key[0]['hours_donated_for_cert'];
        if ($hours_donated_for_cert) $hours_words = "In total, $firstName contributed $hours_donated_for_cert hours to TWB / CLEAR Global.";
        $user_tasks = $userDao->get_user_tasks($user_id, 1000000, 0);

        $languages = [];
        foreach ($user_tasks as $item) {
            $languages[$item['sourceLanguageName'] . ' to ' . $item['targetLanguageName']] = $item['sourceLanguageName'] . ' to ' . $item['targetLanguageName'] . ', ';
        }
        asort($languages);
        $languages = UserRouteHandler::join_with_and($languages, ',', ', and');

        $createdtime = $user->getCreatedTime();
        $datetime = new \DateTime($createdtime);
        $since = $datetime->format('F') . ' ' . $datetime->format('Y');

        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('TWB Platform');
        $pdf->SetTitle("Volunteer Certificate - $name");
        $pdf->SetSubject('Generate Certificate');
        $pdf->SetKeywords('TWB Platform,Volunteer Certificate');
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, [0, 64, 255], [0, 64, 128]);
        $pdf->setFooterData([0, 64, 0], [0, 64, 128]);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $pdf->setFontSubsetting(true);
        $pdf->SetFont('dejavusans', '', 9, '', false);
        $pdf->AddPage('L');
        $pdf->SetLineStyle(['width' => 5, 'color' => [232, 153, 28]]);
        $pdf->Line(0, 0, $pdf->getPageWidth(), 0);
        $pdf->Line($pdf->getPageWidth(), 0, $pdf->getPageWidth(), $pdf->getPageHeight());
        $pdf->Line(0, $pdf->getPageHeight(), $pdf->getPageWidth(), $pdf->getPageHeight());
        $pdf->Line(0, 0, 0, $pdf->getPageHeight());

$html = <<<EOF
        <style>
        div.test {
            color: #000000;
            font-size: 13pt;
            border-style: solid solid solid solid;
            border-width: 8px 8px 8px 8px;
            border-color: #FFFFFF;
            text-align: center;
            margin: 50px auto;
        }
        .uppercase {
            text-transform: uppercase;
            font-weight:bold;
        }
        .footer {
            text-align: center;
            font-size: 11pt;
        }
        .footer-main {
            text-align:center;
        }
        </style>
        <table width="100%" cellspacing="0" cellpadding="55%">
        <tr valign="bottom">
              <td class="header1" rowspan="2" align="left" valign="middle"
                    width="33%"><br/><img width="240"  style="text-align:left;" alt="TWB logo"  class="clearlogo" src="/ui/img/cropped-TWB_Logo_horizontal_primary_RGB-1-1.png"></td>
              <td width="35%"></td>  
              <td class="header1" rowspan="2" align="right" valign="middle"
                    width="25%"><br/><br/><img width="140"  style="text-align:right;" alt="CLEAR Global logo" data-src="/ui/img/CG_Logo_horizontal_primary_RGB.svg" class="clearlogo" src="/ui/img/CG_Logo_horizontal_primary_RGB.svg"></td>
        </tr></table>
        <div class="test">
        <br /><br />This is to certify that
        <br /><br /><br /><span class="uppercase">$name</span>
        <br /><br />is a volunteer with Translators without Borders (TWB) / CLEAR Global since $since.
        <br />$firstName has contributed $word_words providing language services for: $languages.
        <br />$hours_words
        <br /><br />Translators without Borders is part of CLEAR Global, a nonprofit helping people get vital information and be
        <br/>heard, whatever language they speak. We do this through language support, training, data, and technology.
        </div>
        <div class="footer-main">
        <img  src="/ui/img/aimee_sign.png" />
        </div>
        <hr style="height: 1px; border: 0px solid #D6D6D6; border-top-width: 1px;" />
        <div class="footer-main">
        <span>Aimee Ansari, CEO, CLEAR Global / TWB</span>
        </div>
EOF;
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Cell(20, 10, "Issued on " . date("d F Y"), 0, false, 'L', 0, '', 0, false, 'T', 'M');
    $pdf->Cell(0, 9, "Ref: $valid_key", 0, false, 'R', 0, '', 0, false, 'T', 'M' );
    $pdf->lastPage();

    $file_name = 'Certificate_' . $firstName . '_' .$lastName . '_' . date('Y-m-d') . '.pdf';
    $pdf->Output($file_name, 'I');
    exit;
    }

public static function downloadletter(Request $request, Response $response, $args)
{
        require_once 'resources/TCPDF-main/examples/tcpdf_custom.php';
        
        $valid_key = $args['valid_key'];
        $userDao = new DAO\UserDao();
        $projectDao = new DAO\ProjectDao();
        $print_data_by_key = $userDao->get_print_request_by_valid_key($valid_key); 
        $user_id = $print_data_by_key[0]['user_id'];
        $user = $userDao->getUser($user_id);
        $userinfo = $userDao->getUserPersonalInformation($user_id);
        $name = $userinfo->firstName . ' ' . $userinfo->lastName;
        $firstName = $userinfo->firstName;
        $firstName = $userinfo->firstName;
        $lastName = $userinfo->lastName;
        $words_total = $print_data_by_key[0]['word_count'];
        $words_donated = $print_data_by_key[0]['words_donated'];
        $words_paid = $words_total - $words_donated;
        $hours_donated = $print_data_by_key[0]['hours_donated'];
        $hours_paid = $print_data_by_key[0]['hours_paid'];
        $word_words = [];
        if ($words_donated || $hours_donated) {
            $donated = [];
            if ($words_donated) $donated[] = "$words_donated words";
            if ($hours_donated) $donated[] = "$hours_donated hours";
            $word_words[] = implode(' and ', $donated) . ' in volunteer tasks';
        }
        if ($words_paid || $hours_paid) {
            $paid = [];
            if ($words_paid) $paid[] = "$words_paid words";
            if ($hours_paid) $paid[] = "$hours_paid hours";
            $word_words[] = implode(' and ', $paid) . ' in paid tasks';
        }
        $word_words = implode(' and ', $word_words);

        $hours_words = '';
        $hours_donated_for_cert = $print_data_by_key[0]['hours_donated_for_cert'];
        if ($hours_donated_for_cert) $hours_words = "In total, $firstName contributed $hours_donated_for_cert hours to TWB / CLEAR Global. ";
        $user_tasks = $userDao->get_user_tasks($user_id, 1000000, 0);

        $languages = [];
        $types = [];
        foreach ($user_tasks as $item) {
            $languages[$item['sourceLanguageName'] . ' to ' . $item['targetLanguageName']] = '<li>' . $item['sourceLanguageName'] . ' to ' . $item['targetLanguageName'] . ',</li>';
            $types[$item['taskType']] = Common\Enums\TaskTypeEnum::$enum_to_UI[$item['taskType']]['type_text'] . ', ';
        }
        asort($languages);
        ksort($types);
        $languages = UserRouteHandler::join_with_and($languages, '</li>', ' and</li>');
        $types = UserRouteHandler::join_with_and($types, ',', ', and');

        $orgs = [];
        foreach ($user_tasks as $item) {
            $org_name = $projectDao->get_project_org_name($item['projectId']);
            $orgs[$org_name] = "<li>$org_name,</li>";
        }
        asort($orgs);
        $orgs = UserRouteHandler::join_with_and($orgs, '</li>', ' and</li>');

        $createdtime = $user->getCreatedTime();
        $datetime = new \DateTime($createdtime);
        $since = $datetime->format('F') . ' ' . $datetime->format('Y');
        $today = date('d F Y');
        $pageDimension = ['500,300'];

        $pdf = new \MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('TWB Platform');
        $pdf->SetTitle("Volunteer Letter - $name");
        $pdf->SetSubject('Generate Certificate');
        $pdf->SetKeywords('TWB Platform,Volunteer Certificate');
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, [0, 64, 255], [0, 64, 128]);
        $pdf->setFooterData([0, 64, 0], [0, 64, 128]);
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(60);
        $pdf->CustomKey = $valid_key;
        $pdf->SetAutoPageBreak(TRUE, 50);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $pdf->setFontSubsetting(true);
        $pdf->SetFont('dejavusans', '', 8, '', false);
        $pdf->AddPage();
$html = <<<EOF
<style>
div.test {
    color: #000000;
    
    font-size: 12pt;
    border-style: solid solid solid solid;
    border-width: 8px 8px 8px 8px;
    border-color: #FFFFFF;
    text-align: center;
    
}
.uppercase {
    text-transform: uppercase;
    font-weight:bold;
}
.footer {
    text-align: right;
    font-size: 10pt;
}
.footer-clear{
    border: 0px solid #f89406;
    font-size:10pt; 
}
.footer-address{
    font-size:10pt; 
}
</style>

<div class="test">
<br/><br/><span style="text-align:left">$today</span>
<br/><br/><span style="text-align:left">This letter is to confirm that $name is a volunteer with Translators without Borders (TWB) / CLEAR Global. </span>
<br/><br/><span style="text-align:left">Since $firstName joined in $since, $firstName has contributed $word_words by completing $types tasks. $hours_words$firstName has delivered work in the following language combination[s]:
<ul>$languages</ul>
Thereby, $firstName has provided linguistic support to the following nonprofit partners:
<ul>
$orgs
</ul>
</div>
EOF;

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->lastPage();
        $file_name = 'Reference_' . $firstName . '_' .$lastName . '_'. date('Y-m-d') . '.pdf';
        $pdf->Output($file_name, 'I');
        exit;	
    }

    public static function join_with_and($array, $sub, $with) {
        $n = count($array);
        if ($n > 1) $array[array_keys($array)[$n - 2]] = str_replace(       $sub,    $with, $array[array_keys($array)[$n - 2]]);
        if ($n)     $array[array_keys($array)[$n - 1]] = str_replace([', ', ','], ['', ''], $array[array_keys($array)[$n - 1]]);
        return implode('', $array);
    }

    public static function profile_shared_with_key(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $key = $args['key'];

        $key = hex2bin($key);
        $iv = substr($key, -16);
        $encrypted = substr($key, 0, -18);
        $user_id = (int)openssl_decrypt($encrypted, 'aes-256-cbc', base64_decode(Common\Lib\Settings::get('badge.key')), 0, $iv);
        $user_id -= 999999; // Ensure we don't use identical key to word count badge

        $userDao = new DAO\UserDao();

        $user = $userDao->getUser($user_id);
        $userPersonalInfo = $userDao->getUserPersonalInformation($user_id);
        $userQualifiedPairs = $userDao->getUserQualifiedPairs($user_id);

        $template_data = array_merge($template_data, array(
            'current_page' => 'user-profile',
            'this_user' => $user,
            'userPersonalInfo' => $userPersonalInfo,
            'userQualifiedPairs' => $userQualifiedPairs,
            'user_has_strategic_languages' => 0,
            'user_badges'            => $userDao->get_points_for_badges($user_id),
            'user_badge_name'        => wordwrap($userPersonalInfo->getFirstName() . ' ' . $userPersonalInfo->getLastName(), 20, '\n'),
            'roles'                  => 0,
            'private_access'         => 0,
            'receive_credit'         => 1,
            'no_header'              => 1,
            'url_list'               => $userDao->getURLList($user_id),
            'expertise_list'         => $userDao->getExpertiseList($user_id),
            'capability_list'        => $userDao->getCapabilityList($user_id),
            'supported_ngos'         => $userDao->supported_ngos($user_id),
            'supported_ngos_paid'    => [],
            'quality_score'          => $userDao->quality_score($user_id),
            'certifications'         => $userDao->getUserCertifications($user_id),
            'show_create_memsource_user' => 0,
        ));

        return UserRouteHandler::render('user/user-public-profile.tpl', $response);
    }

    public static function badge_shared_with_key(Request $request, Response $response, $args)
    {
        $key = $args['key'];

        $key = hex2bin($key);
        $iv = substr($key, -16);
        $encrypted = substr($key, 0, -18);
        $user_id = (int)openssl_decrypt($encrypted, 'aes-256-cbc', base64_decode(Common\Lib\Settings::get('badge.key')), 0, $iv);
        if ($user_id > 0) {
            $badge_title = 'WORDS DONATED';
        } else {
            $badge_title = 'HOURS DONATED';
            $user_id = -$user_id;
        }
        $userDao = new DAO\UserDao();
        $user_badges = $userDao->get_points_for_badges($user_id);
        if ($badge_title == 'WORDS DONATED') {
            $badge_quantity = $user_badges['words_donated'];
        } else {
            $badge_quantity = $user_badges['hours_donated'];
        }

        header('Content-type: image/png');
        header('X-Frame-Options: ALLOWALL');
        header('Pragma: no-cache');
        header('Cache-control: no-cache, must-revalidate, no-transform');

        $logo = imagecreatefrompng('/repo/SOLAS-Match/ui/img/badge.png');
        $size = 60;
        $angle = 0;
        $left = 50;
        $top = 740;
        $color = imagecolorallocate($logo, 232, 153, 28);
        $font_path = '/repo/SOLAS-Match/ui/img/font.ttf';
        imagettftext($logo, $size, $angle, $left, $top, $color, $font_path, $badge_quantity);
        $left = 50;
        $top = 840;
        $color = imagecolorallocate($logo, 0, 0, 0);
        imagettftext($logo, $size, $angle, $left, $top, $color, $font_path, $badge_title);
        $size = 70;
        $left = 50;
        $top = 522;
        $color = imagecolorallocate($logo, 0, 0, 0);
        imagettftext($logo, $size, $angle, $left, $top, $color, $font_path, mb_strtoupper(wordwrap($user_badges['name'], 20, "\n")));

        imagepng($logo);
        imagedestroy($logo);
        die;


        // $logo = imagecreatefrompng('/repo/SOLAS-Match/ui/img/TWB_Community_members_badge_BG-01.png');
        // $size = 60;
        // $angle = 0;
        // $left = 950;
        // $top = 740;
        // $color = imagecolorallocate($logo, 232, 153, 28);
        // $font_path = '/repo/SOLAS-Match/ui/img/font.ttf';
        // imagettftext($logo, $size, $angle, $left, $top, $color, $font_path, $badge_quantity);
        // $left = 750;
        // $top = 840;
        // $color = imagecolorallocate($logo, 87, 110, 130);
        // imagettftext($logo, $size, $angle, $left, $top, $color, $font_path, $badge_title);
        // $size = 70;
        // $left = 595;
        // $top = 522;
        // $color = imagecolorallocate($logo, 0, 0, 0);
        // imagettftext($logo, $size, $angle, $left, $top, $color, $font_path, mb_strtoupper(wordwrap($user_badges['name'], 20, "\n")));

        // imagepng($logo);
        // imagedestroy($logo);
        // die;
    }

    public function editTaskStreamNotification(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $userId = $args['user_id'];

        $userDao = new DAO\UserDao();
        $taskDao = new DAO\TaskDao();
        if ($taskDao->get_user_task_limitation(Common\Lib\UserSession::getCurrentUserID())['limit_profile_changes']) return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("user-public-profile", array("user_id" => $userId)));

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $user = $userDao->getUser($userId);

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'editTaskStreamNotification')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            if (isset($post['interval'])) {
                $success = false;
                if ($post['interval'] == 0) {
                    $success = $userDao->removeTaskStreamNotification($userId);
                } else {
                    $notifData = new Common\Protobufs\Models\UserTaskStreamNotification();
                    $notifData->setUserId($userId);
                    $notifData->setInterval($post['interval']);
                    $notifData->setStrict(true);
                    $success = $userDao->requestTaskStreamNotification($notifData);
                }

                UserRouteHandler::flash("success", Lib\Localisation::getTranslation('user_public_profile_17'));
                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("user-public-profile", array("user_id" => $userId)));
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

            $template_data = array_merge($template_data, array(
                "interval"  => $interval,
                "intervalId" => $notifData->getInterval(),
                "lastSent"  => $lastSent,
                'strict'    => $strict
            ));
        }

        $template_data = array_merge($template_data, array(
            'sesskey' => $sesskey,
            "user" => $user
        ));

        return UserRouteHandler::render("user/user.task-stream-notification-edit.tpl", $response);
    }

    public function userTaskReviews(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $taskId = $args['task_id'];

        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $adminDao = new DAO\AdminDao();

        $loggedInUserId = Common\Lib\UserSession::getCurrentUserID();
        $task = $taskDao->getTask($taskId);
        $project = $projectDao->getProject($task->getProjectId());
        $roles = $adminDao->get_roles($loggedInUserId, $project->getOrganisationId());

        $sesskey = Common\Lib\UserSession::getCSRFKey();
        if (($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER)) && $request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'userTaskReviews')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);
            if (!empty($post['user_id'])) $taskDao->delete_review($taskId, $post['user_id']);
        }

        $task = $taskDao->getTask($taskId);
        $reviews = $taskDao->getTaskReviews($taskId);

        $extra_scripts = "";
        $extra_scripts .= "<link rel=\"stylesheet\" href=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/RateIt/src/rateit.css\"/>";
        $extra_scripts .= "<script>" . file_get_contents(__DIR__ . "/../js/RateIt/src/jquery.rateit.min.js") . "</script>";

        $template_data = array_merge($template_data, array(
            'task'          => $task,
            'reviews'       => $reviews,
            'roles'         => $roles,
            'sesskey'       => $sesskey,
            'extra_scripts' => $extra_scripts
        ));

        return UserRouteHandler::render("user/user.task-reviews.tpl", $response);
    }

    public function docusign_redirect_uri()
    {
        error_log('docusign_redirect_uri:' . print_r($_GET, 1));
        die;
    }

    public function docusign_hook(Request $request)
    {
        $userDao = new DAO\UserDao();

        $body = (string)$request->getBody();
        $docusign_hook = json_decode($body, 1);
        error_log('docusign_hook:' . print_r($docusign_hook, 1));
        if (!empty($docusign_hook['data']['envelopeId']) && !empty($docusign_hook['event']) && ($docusign_hook['event'] == 'envelope-completed' || (!empty($docusign_hook['data']['recipientId']) && $docusign_hook['data']['recipientId'] == 1)))
            $userDao->update_sent_contract($docusign_hook['event'], $docusign_hook['data']['envelopeId']);
        die;
    }

    public function getInvoice(Request $request, Response $response, $args)
    {
        require_once 'resources/TCPDF-main/examples/tcpdf_include.php';
       
        $userDao = new DAO\UserDao();

        $invoice_number = $args['invoice_number'];
        $rows = $userDao->getInvoice($invoice_number);
        $invoice = $rows[0];

        $TWB = 'TWB-';
        if ($invoice['status']&1) $TWB = 'DRAFT-';
        $invoice_number = $TWB . str_pad($invoice_number, 4, '0', STR_PAD_LEFT);

        $name = $invoice['linguist_name'];
        $email = $invoice['email'];
        $country = $invoice['country'];
        $date = date("Y-m-d" , strtotime($invoice['invoice_date']));
        $amount = '$' . round($invoice['amount'], 2);

        foreach ($rows as $row) {
            $purchase_order = $row['purchase_order'];
            $description = $row['title'];
            $type = $row['type_text'];
            $language = $row['language_pair_name'];
            $project = $row['project_title'];
            $row_amount = '$' . round($row['row_amount'], 2);
            $unit = $row['pricing_and_recognition_unit_text_hours'];
            $unit_rate = '$' . $row['unit_rate'];
            $quantity = round($row['quantity'], 2);
        }

         // column titles
        $header = array('S/N', 'Description', 'PO', 'Quantity', 'Unit Price','Amount');

        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('TWB Platform');
        $pdf->SetTitle("Invoice");
        $pdf->SetSubject('Generate Linguist Invoice');
        $pdf->SetKeywords('TWB Platform,Linguist Invoice');
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, [0, 64, 255], [0, 64, 128]);
        $pdf->setFooterData([0, 64, 0], [0, 64, 128]);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('dejavusans', '', 9, '', false);
        $pdf->AddPage('L');
        $pdf->SetLineStyle(['width' => 5, 'color' => [232, 153, 28]]);
        $pdf->Line(0, 0, $pdf->getPageWidth(), 0);
        $pdf->Line($pdf->getPageWidth(), 0, $pdf->getPageWidth(), $pdf->getPageHeight());
        $pdf->Line(0, $pdf->getPageHeight(), $pdf->getPageWidth(), $pdf->getPageHeight());
        $pdf->Line(0, 0, 0, $pdf->getPageHeight());
$html = <<<EOF
        <style>
        d-flex {
            display:flex;
            justify-content:space-between;
        }
        div.test {
            color: #000000;
            font-size: 13pt;
            border-style: solid solid solid solid;
            border-width: 8px 8px 8px 8px;
            border-color: #FFFFFF;
            text-align: center;
            margin: 50px auto;
        }
        .uppercase {
            text-transform: uppercase;
            font-weight:bold;
        }
        .footer {
            text-align: center;
            font-size: 11pt;
        }
        .footer-main {
            text-align:center;
        }
        </style>
       <divstyle="display:flex;justify-content:between;"> 
        <img width="140"  style="margin-bottom:14px;" alt="CLEAR Global logo" data-src="/ui/img/CG_Logo_horizontal_primary_RGB.svg" class="clearlogo" src="/ui/img/CG_Logo_horizontal_primary_RGB.svg">
        <div style="font-weight:bold; float:left ;">INVOICE</div>
        </div>

       <br/>
       <br/>
       
       <table width="100%" cellspacing="0" cellpadding="55%">
        <tr valign="bottom">
              <td class="header1" rowspan="2" align="left" valign="middle"
                    width="33%"><br/>
                    <div style="font-weight:bold;">From</div>
                    <div>$name</div>
                    <div>$email</div>
                    <div>$country</div>
                    </td>
              <td width="35%"></td>  
              <td class="header1" rowspan="2" align="left" valign="middle"
                    width="25%">
                    <div>$invoice_number</div>
                    <div>$date</div>
                    <br/><br/>
                    </td>
        </tr></table>
       <div style="margin-top:20px;">
       <br/>
       <br/>
        <div style="font-weight:bold;">To</div>
        <div style="font-weight:bold;">CLEAR Global inc.</div>
        <div>9169 W State St#83714</div>
        <div>(203) 794-6698</div>
       </div> 
       <br/>
EOF;

$tbl = <<<EOD
<table border="1" cellpadding="2" cellspacing="2">
<thead>
 <tr style="background-color:#FAFAFA;color:black;">
  <td width="30" align="center"><b>S/N</b></td>
  <td width="300" align="center"><b>Description</b></td>
  <td width="140" align="center"><b>PO</b></td>
  <td width="200" align="center"> <b>Quantity</b></td>
  <td width="100" align="center"><b>Unit Price</b></td>
  <td width="100" align="center"><b>Amount</b></td>
 </tr>
</thead>
 <tr>
  <td width="30" align="center"><b>1</b></td>
  <td width="300">$description<br /> $project <br /> Language: $language<br />$type<br /></td>
  <td width="140" align="center">$purchase_order</td>
  <td width="200" align="center">$quantity $unit</td>
  <td width="100" align="center">$unit_rate</td>
  <td align="center" width="100">$row_amount</td>
 </tr>
 <tr>
 <td colspan="5" style="font-weight:bold;">Total</td>
 <td width="100" align="center">$amount</td>
</tr>
</table>
EOD;
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->writeHTML($tbl, true, false, false, false, '');
    $pdf->Cell(20, 10, "Issued on " . date("d F Y"), 0, false, 'L', 0, '', 0, false, 'T', 'M');
    $pdf->lastPage();

    $pdf->Output($invoice['filename'], 'I');
    exit;
    }

    public static function flash($key, $value)
    {
        global $flash_messages;
        $flash_messages['next'][$key] = $value;
    }

    public static function flashNow($key, $value)
    {
        global $flash_messages;
        $flash_messages['now'][$key] = $value;
    }

    public static function flashKeep()
    {
        global $flash_messages;
        foreach ($flash_messages['prev'] as $key => $val) {
            $flash_messages['next'][$key] = $val;
        }
    }

    public static function render($template, Response $response) {
        global $template_data, $flash_messages;

        $smarty = new \Smarty();
        $smarty->setTemplateDir('/repo/SOLAS-Match/ui/templating/templates');
        $smarty->setCompileDir('/repo/SOLAS-Match/ui/templating/templates_compiled');
        $smarty->setCacheDir('/repo/SOLAS-Match/ui/templating/cache');
        $smarty->registerClass('Settings',                 '\SolasMatch\Common\Lib\Settings');
        $smarty->registerClass('UserSession',              '\SolasMatch\Common\Lib\UserSession');
        $smarty->registerClass('TemplateHelper',           '\SolasMatch\UI\Lib\TemplateHelper');
        $smarty->registerClass('Localisation',             '\SolasMatch\UI\Lib\Localisation');
        $smarty->registerClass('TaskTypeEnum',             '\SolasMatch\Common\Enums\TaskTypeEnum');
        $smarty->registerClass('TaskStatusEnum',           '\SolasMatch\Common\Enums\TaskStatusEnum');
        $smarty->registerClass('NotificationIntervalEnum', '\SolasMatch\Common\Enums\NotificationIntervalEnum');
        $smarty->registerClass('BanTypeEnum',              '\SolasMatch\Common\Enums\BanTypeEnum');
        $smarty->registerPlugin('function', 'urlFor', 'SolasMatch\UI\RouteHandlers\smarty_function_urlFor');

        foreach ($template_data as $key => $item) $smarty->assign($key, $item);
        $smarty->assign('SITE_ADMIN',         64);
        $smarty->assign('PROJECT_OFFICER',    32);
        $smarty->assign('COMMUNITY_OFFICER',  16);
        $smarty->assign('NGO_ADMIN',           8);
        $smarty->assign('NGO_PROJECT_OFFICER', 4);
        $smarty->assign('NGO_LINGUIST',        2);
        $smarty->assign('LINGUIST',            1);
        $smarty->assign('ORG_EXCEPTIONS', ORG_EXCEPTIONS);

        $smarty->assign('flash', array_merge($flash_messages['prev'], $flash_messages['now']));

        $response->getBody()->write($smarty->fetch($template));
        return $response->withHeader('Content-Type', 'text/html;charset=UTF-8');
    }
}

$route_handler = new UserRouteHandler();
$route_handler->init();
unset($route_handler);

function smarty_function_urlFor($params, $template)
{
    global $app;

    $name = isset($params['name']) ? $params['name'] : '';

    if (isset($params['options'])) {
        $options = explode('|', $params['options']);
        $options_array = [];
        foreach ($options as $option) {
            list($key, $value) = explode('.', $option);
            $options_array[$key] = $value;
        }
        $url = $app->getRouteCollector()->getRouteParser()->urlFor($name, $options_array);
    } else {
        $url = $app->getRouteCollector()->getRouteParser()->urlFor($name);
    }
    return $url;
}
