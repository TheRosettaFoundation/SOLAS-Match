<?php
require "vendor/autoload.php";

mb_internal_encoding("UTF-8");

SmartyView::$smartyDirectory = 'vendor/smarty/smarty/distribution/libs';
SmartyView::$smartyCompileDirectory = 'app/templating/templates_compiled';
SmartyView::$smartyTemplatesDirectory = 'app/templating/templates';
SmartyView::$smartyExtensions = array(
    'vendor/slim/extras/Views/Extension/Smarty'
);



require_once 'app/Settings.class.php';
require_once 'app/PDOWrapper.class.php';
require_once 'app/BadgeDao.class.php';
require_once 'app/OrganisationDao.class.php';
require_once 'app/UserDao.class.php';
require_once 'app/TaskStream.class.php';
require_once 'app/TaskDao.class.php';
require_once 'app/TagsDao.class.php';
require_once 'app/TaskFile.class.php';
require_once 'app/IO.class.php';
require_once 'app/TipSelector.class.php';
require_once 'app/lib/Languages.class.php';
require_once 'app/lib/URL.class.php';
require_once 'app/lib/Authentication.class.php';
require_once 'app/lib/UserSession.class.php';
require_once 'app/lib/Tags.class.php';
require_once 'app/lib/Upload.class.php';
require_once 'app/lib/Email.class.php';
require_once 'app/lib/Notify.class.php';
require_once 'app/lib/NotificationTypes.class.php';

require_once 'HTTP/Request2.php';

require_once 'ui/APIClient.class.php';
require_once 'app/Middleware.class.php';
require_once 'app/MessagingClient.class.php';

require_once 'ui/UserRouteHandler.class.php';
require_once 'ui/OrgRouteHandler.class.php';
require_once 'ui/TaskRouteHandler.class.php';
require_once 'ui/TagRouteHandler.class.php';
require_once 'ui/BadgeRouteHandler.class.php';

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
    $user_dao = new UserDao();
    if ($current_user = $user_dao->getCurrentUser()) {
        $app->view()->appendData(array('user' => $current_user));
        if ($user_dao->belongsToRole($current_user, 'organisation_member')) {
            $app->view()->appendData(array(
                'user_is_organisation_member' => true,
                'user_organisations' => $user_dao->findOrganisationsUserBelongsTo($current_user->getUserId())
            ));
        }
    }
});

$app->run();
