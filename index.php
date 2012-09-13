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
*   Middleware - Used to authenticat users when entering restricted pages
*
*/
function authUserIsLoggedIn()
{
    $app = Slim::getInstance();

    $user_dao = new UserDao();
    if(!is_object($user_dao->getCurrentUser())) {
        $app->flash('error', "Login required to access page");
        $app->redirect($app->urlFor('login'));
    }

    return true;
}

function authenticateUserForTask($request, $response, $route) {
    $app = Slim::getInstance();
    $params = $route->getParams();
    if($params !== NULL) {
        $task_id = $params['task_id'];
        $task_dao = new TaskDao();
        if($task_dao->taskIsClaimed($task_id)) {
            $user_dao = new UserDao();
            $current_user = $user_dao->getCurrentUser();
            if(!is_object($current_user)) {
                $app->flash('error', 'Login required to access page');
                $app->redirect($app->urlFor('login'));
            }
            if(!$task_dao->hasUserClaimedTask($current_user->getUserId(), $task_id)) {
                $app->flash('error', 'This task has been claimed by another user');
                $app->redirect($app->urlFor('home'));
            }
        }
        return true;
    } else {
        $app->flash('error', 'Unable to find task');
        $app->redirect($app->urlFor('home'));
    }
}


function authUserForOrg($request, $response, $route) {
    $params = $route->getParams();
    if($params !== NULL) {
        $org_id = $params['org_id'];
        $user_dao = new UserDao();
        $user = $user_dao->getCurrentUser();
        if(is_object($user)) {
            $user_orgs = $user_dao->findOrganisationsUserBelongsTo($user->getUserId());
            if(!is_null($user_orgs)) {
                if(in_array($org_id, $user_orgs)) {
                    return true;
                }
            }
        }
    }

    $app = Slim::getInstance();
    $org_name = 'this organisation';
    if(isset($org_id)) {
        $org_dao = new OrganisationDao();
        $org = $org_dao->find(array('id' => $org_id));
        $org_name = "<a href=\"".$app->urlFor('org-public-profile', array('org_id' => $org_id))."\">".$org->getName()."</a>";
    }
    $app->flash('error', "You are not authorised to view this profile. Only members of ".$org_name." may view this page.");
    $app->redirect($app->urlFor('home'));
}

/*
 *  Middleware for ensuring the current user belongs to the Org that uploaded the associated Task
 *  Used for altering task details
 */
function authUserForOrgTask($request, $response, $route) {
    $params= $route->getParams();
    if($params != NULL) {
        $task_id = $params['task_id'];
        $task_dao = new TaskDao();
        $task = $task_dao->find(array('task_id' => $task_id));

        $org_id = $task->getOrganisationId();
        $user_dao = new UserDao();
        $user = $user_dao->getCurrentUser();
        if(is_object($user)) {
            $user_orgs = $user_dao->findOrganisationsUserBelongsTo($user->getUserId());
            if(!is_null($user_orgs) && in_array($org_id, $user_orgs)) {
                return true;
            }
        }
    }

    $app = Slim::getInstance();
    $org_name = 'this organisation';
    if(isset($org_id)) {
        $org_dao = new OrganisationDao();
        $org = $org_dao->find(array('id' => $org_id));
        $org_name = "<a href=\"".$app->urlFor('org-public-profile', array('org_id' => $org_id))."\">".$org->getName()."</a>";
    }
    $app->flash('error', "You are not authorised to view this page. Only members of ".$org_name." may view this page.");
    $app->redirect($app->urlFor('home'));
}

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
