<?php
require "vendor/autoload.php";

mb_internal_encoding("UTF-8");

SmartyView::$smartyDirectory = 'vendor/smarty/smarty/distribution/libs';
SmartyView::$smartyCompileDirectory = 'app/templating/templates_compiled';
SmartyView::$smartyTemplatesDirectory = 'app/templating/templates';
SmartyView::$smartyExtensions = array(
    'vendor/slim/extras/Views/Extension/Smarty'
);


//TODO remove all requires bar RoutHandlers
require_once 'app/Settings.class.php';
//require_once 'app/PDOWrapper.class.php';
//require_once 'app/BadgeDao.class.php';
//require_once 'app/OrganisationDao.class.php';
//require_once 'app/UserDao.class.php';
//require_once 'app/TaskStream.class.php';
//require_once 'app/TaskDao.class.php';
//require_once 'app/TagsDao.class.php';
//require_once 'app/TaskFile.class.php';
//require_once 'app/IO.class.php';
require_once 'app/TipSelector.class.php';
//require_once 'app/lib/Languages.class.php';
require_once 'app/lib/URL.class.php';
require_once 'app/lib/Authentication.class.php';
require_once 'app/lib/UserSession.class.php';
//require_once 'app/lib/Tags.class.php';
//require_once 'app/lib/Upload.class.php';
//require_once 'app/lib/Email.class.php';
//require_once 'app/lib/Notify.class.php';
//require_once 'app/lib/NotificationTypes.class.php';

require_once 'HTTP/Request2.php';

require_once 'ui/APIClient.class.php';
require_once 'app/Middleware.class.php';
require_once 'app/MessagingClient.class.php';

require_once 'ui/templateHelper.php';
require_once 'ui/UserRouteHandler.class.php';
require_once 'ui/OrgRouteHandler.class.php';
require_once 'ui/TaskRouteHandler.class.php';
require_once 'ui/TagRouteHandler.class.php';
require_once 'ui/BadgeRouteHandler.class.php';

require_once 'app/models/User.class.php';
require_once 'app/models/Tag.class.php';
require_once 'app/models/Task.class.php';
require_once 'app/models/Organisation.class.php';
require_once 'app/models/Badge.class.php';
require_once 'app/models/Language.class.php';
require_once 'app/models/Country.class.php';

/**
 * Start the session
 */
session_start();
// Can we get away from the app's old system?
//require('app/includes/smarty.php');

/**
 * Initiate the app
 */
$app = new Slim(array(
    'debug' => true,
    'view' => new SmartyView(),
    'mode' => 'development' // default is development. TODO get from config file, or set in environment...... $_ENV['SLIM_MODE'] = 'production';
));

$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
        'log.path' => '../logs', // Need to set this...
        'debug' => false
    ));
});

$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'log.enable' => false,
        'debug' => true
    ));
});

/*
*
*   Routing options - List all URLs here
*
*/
{
    $route_handler = new UserRouteHandler();
    $route_handler->init();

    $route_handler = new OrgRouteHandler();
    $route_handler->init();

    $route_handler = new TaskRouteHandler();
    $route_handler->init();

    $route_handler = new TagRouteHandler();
    $route_handler->init();

    $route_handler = new BadgeRouteHandler();
    $route_handler->init();
}

function isValidPost(&$app) {
    return $app->request()->isPost() && sizeof($app->request()->post()) > 2;
}

/**
 * Set up application objects
 * 
 * Given that we don't have object factories implemented, we'll initialise them directly here.
 */
$app->hook('slim.before', function () use ($app) {
//    $user_dao = new UserDao();
    $client = new APIClient();
    if (!is_null(UserSession::getCurrentUserID())&&$current_user = $client->castCall("User", APIClient::API_VERSION."/users/".UserSession::getCurrentUserID())) {
        $app->view()->appendData(array('user' => $current_user));
        if ($client->castCall("User", APIClient::API_VERSION."/users/".UserSession::getCurrentUserID(),HTTP_Request2::METHOD_GET, null, array("role"=>'organisation_member'))) {
            $app->view()->appendData(array(
                'user_is_organisation_member' => true,
                'user_organisations' => $client->castCall(Array("Organisation"), APIClient::API_VERSION."/users/".UserSession::getCurrentUserID()."orgs")
            ));
        }
    }
});

$app->run();
