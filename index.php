<?php

mb_internal_encoding("UTF-8");

header("Content-Type:application/xhtml+xml;charset=UTF-8");

require_once __DIR__."/ui/vendor/autoload.php";

\DrSlump\Protobuf::autoload();

require_once 'Common/Settings.class.php';
require_once 'Common/HttpMethodEnum.php';
require_once 'Common/BanTypeEnum.php';
require_once 'Common/NotificationIntervalEnum.class.php';
require_once 'Common/lib/ModelFactory.class.php';
require_once 'Common/lib/BadgeTypes.class.php';
require_once 'Common/lib/UserSession.class.php';

require_once 'ui/lib/Middleware.class.php';
require_once 'ui/lib/TemplateHelper.php';
require_once 'ui/lib/GraphViewer.class.php';
require_once 'ui/lib/UIWorkflowBuilder.class.php';
require_once 'ui/lib/Localisation.php';

require_once 'Common/models/User.php';
require_once 'Common/models/Tag.php';
require_once 'Common/models/Task.php';
require_once 'Common/models/Organisation.php';
require_once 'Common/models/Badge.php';
require_once 'Common/models/Language.php';
require_once 'Common/models/Country.php';
require_once 'Common/models/TaskMetadata.php';
require_once 'Common/models/MembershipRequest.php';
require_once 'Common/models/UserTaskStreamNotification.php';
require_once 'Common/models/TaskReview.php';

require_once 'Common/protobufs/emails/EmailMessage.php';
require_once 'Common/protobufs/emails/UserFeedback.php';
require_once 'Common/protobufs/emails/OrgFeedback.php';

require_once 'ui/DataAccessObjects/AdminDao.class.php';
require_once 'ui/DataAccessObjects/BadgeDao.class.php';
require_once 'ui/DataAccessObjects/CountryDao.class.php';
require_once 'ui/DataAccessObjects/LanguageDao.class.php';
require_once 'ui/DataAccessObjects/UserDao.class.php';
require_once 'ui/DataAccessObjects/TaskDao.class.php';
require_once 'ui/DataAccessObjects/TagDao.class.php';
require_once 'ui/DataAccessObjects/OrganisationDao.class.php';
require_once 'ui/DataAccessObjects/StatisticsDao.class.php';
require_once 'ui/DataAccessObjects/ProjectDao.class.php';
require_once 'ui/DataAccessObjects/TipDao.class.php';

/**
 * Initiate the app. must be done before routes are required
 */

$app = new \Slim\Slim(array(
    'debug' => false,
    'view' => new \Slim\Views\Smarty(),
    'mode' => 'development' // default is development.
));

$view = $app->view();
$view->parserDirectory = 'ui/vendor/smarty/smarty/distribution/libs';
$view->parserCompileDirectory = 'ui/templating/templates_compiled';
$view->parserCacheDirectory = 'ui/templating/cache';
$view->parserExtensions = array( 'ui/vendor/slim/views/Slim/Views/SmartyPlugins',);
$view->setTemplatesDirectory('ui/templating/templates');

$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
        'log.path' => '../logs', // Need to set this...
        'debug' => false,
        'cookies.lifetime' => Settings::get('site.cookie_timeout'),
        'cookies.secret_key' => Settings::get('session.site_key'),
        'cookies.cipher' => MCRYPT_RIJNDAEL_256,
        'cookies.cipher_mode' => MCRYPT_MODE_CBC
    ));
});

$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'log.enable' => false,
        'debug' => false,
        'cookies.lifetime' => Settings::get('site.cookie_timeout'),
        'cookies.secret_key' => Settings::get('session.site_key'),
        'cookies.cipher' => MCRYPT_RIJNDAEL_256,
        'cookies.cipher_mode' => MCRYPT_MODE_CBC
    ));
});

$app->add(new \Slim\Middleware\SessionCookie(array(
    'expires' => Settings::get('site.cookie_timeout'),
    'path' => '/',
    'domain' => null,
    'secure' => false,
    'httponly' => false,
    'name' => 'slim_session',
    'secret' => Settings::get('session.site_key'),
    'cipher' => MCRYPT_RIJNDAEL_256,
    'cipher_mode' => MCRYPT_MODE_CBC
)));

// Register static classes so they can be used in smarty templates
\SolasMatch\UI\Lib\Localisation::registerWithSmarty();
\SolasMatch\UI\Lib\TemplateHelper::registerWithSmarty();

// Include and initialize RouteHandlers
require_once 'ui/RouteHandlers/AdminRouteHandler.class.php';
require_once 'ui/RouteHandlers/UserRouteHandler.class.php';
require_once 'ui/RouteHandlers/OrgRouteHandler.class.php';
require_once 'ui/RouteHandlers/TaskRouteHandler.class.php';
require_once 'ui/RouteHandlers/TagRouteHandler.class.php';
require_once 'ui/RouteHandlers/BadgeRouteHandler.class.php';
require_once 'ui/RouteHandlers/ProjectRouteHandler.class.php';
require_once 'ui/RouteHandlers/StaticRouteHandeler.php';

//Custom Slim Errors
$app->error(function (\Exception $e) use ($app) {
    $extra_scripts = "<script type='text/javascript' src='{$app->urlFor("home")}ui/js/slimError.showHide.js'></script>";
    $trace = str_replace('#', '<br \>', $e->getTraceAsString());
	
    $app->view()->appendData(array(
        "exception" => $e,
        "trace" => $trace,
        "extra_scripts" => $extra_scripts,
        "referrer" => $app->request()->getReferrer()
    ));
    
    $app->render('SlimError.tpl');
});

function isValidPost(&$app)
{
    return $app->request()->isPost() && sizeof($app->request()->post()) > 2;
}

$app->hook('slim.before.dispatch', function () use ($app) {
    if (!is_null($token = UserSession::getAccessToken()) && $token->getExpires() <  time()) {
        UserSession::clearCurrentUserID();
    }
    $userDao = new SolasMatch\UI\DAO\UserDao();
    if (!is_null(UserSession::getCurrentUserID())) {
        $current_user = $userDao->getUser(UserSession::getCurrentUserID());
        if (!is_null($current_user)) {
            $app->view()->appendData(array('user' => $current_user));
            $org_array = $userDao->getUserOrgs(UserSession::getCurrentUserID());
            if ($org_array && count($org_array) > 0) {
                $app->view()->appendData(array(
                    'user_is_organisation_member' => true
                ));
            }

            $tasks = $userDao->getUserTasks(UserSession::getCurrentUserID());
            if ($tasks && count($tasks) > 0) {
                $app->view()->appendData(array(
                    "user_has_active_tasks" => true
                ));
            }
            $adminDao = new SolasMatch\UI\DAO\AdminDao();
            $isAdmin = $adminDao->isSiteAdmin(UserSession::getCurrentUserID());
            if ($isAdmin) {
                $app->view()->appendData(array(
                    'site_admin' => true
                ));
            }
        } else {
            UserSession::clearCurrentUserID();
            UserSession::clearAccessToken();
        }
    }
    $app->view()->appendData(array(
        'locs' => \SolasMatch\UI\Lib\Localisation::loadTranslationFiles()
    ));
});

$app->run();
