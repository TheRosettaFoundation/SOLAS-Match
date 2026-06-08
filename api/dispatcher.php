<?php
namespace SolasMatch\API;
//error_log('REQUEST_URI: ' . $_SERVER['REQUEST_URI']);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use \SolasMatch\Common as Common;


mb_internal_encoding('UTF-8');

require __DIR__ . '/vendor/autoload.php';

define("SITE_ADMIN",         64);
define("PROJECT_OFFICER",    32);
define("COMMUNITY_OFFICER",  16);
define("NGO_ADMIN",           8);
define("NGO_PROJECT_OFFICER", 4);
define("NGO_LINGUIST",        2);
define("LINGUIST",            1);

require_once __DIR__ . '/lib/Middleware.php';
require_once __DIR__ . '/../Common/lib/Settings.class.php';
require_once __DIR__ . '/../Common/lib/ModelFactory.class.php';
require_once __DIR__ . '/../Common/Enums/BadgeTypes.class.php';
require_once __DIR__ . '/../Common/lib/APIHelper.class.php';
require_once __DIR__ . '/../Common/lib/UserSession.class.php';
require_once __DIR__ . '/../Common/Enums/HttpMethodEnum.class.php';
require_once __DIR__ . '/../Common/Enums/HttpStatusEnum.class.php';

$app = AppFactory::create();

$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

define("PROJECTQUEUE",                     "3");

define("EmailVerification",               "13");
define("PasswordResetEmail",               "5");
define("UserBadgeAwardedEmail",           "22");
define("BannedLogin",                     "14");
define("UserReferenceEmail",              "21");
define("OrgCreatedNotificationRequest",  "100");
define("OrgMembershipAccepted",            "3");
define("OrgMembershipRefused",             "4");
define("ProjectImageUploadedEmail",       "29");
define("ProjectImageApprovedEmail",       "31");
define("ProjectImageDisapprovedEmail",    "32");
define("ProjectImageRemovedEmail",        "30");
define("TaskArchived",                     "6");
define("OrgFeedback",                     "18");
define("UserTaskClaim",                    "2");
define("TaskClaimed",                      "7");
define("TaskUploadNotificationRequest",  "101");
define("TaskRevokedNotification",        "102");
define("UserFeedback",                    "11");
define("UserTaskCancelled",               "36");

require_once 'v0/Admins.php';
require_once 'v0/Badges.php';
require_once 'v0/Countries.php';
require_once 'v0/IO.php';
require_once 'v0/Langs.php';
require_once 'v0/Orgs.php';
require_once 'v0/Projects.php';
require_once 'v0/Static.php';
require_once 'v0/Tags.php';
require_once 'v0/Tasks.php';
require_once 'v0/Users.php';

class Dispatcher
{
    public static function sendResponse(Response $response, $body, $code = 200)
    {
        $response = $response->withHeader('Access-Control-Allow-Origin',  '*');
        $response = $response->withHeader('Access-Control-Allow-Headers', 'Content-Type');
        $response = $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
        $response = $response->withHeader('Content-Type', 'application/json; charset=utf-8');

        $apiHelper = new Common\Lib\APIHelper('.json');
        $body = $apiHelper->serialize($body);

        if ($code != null) $response = $response->withStatus($code);
        
        if ($body != null) $response->getBody()->write((string)$body);
        return $response;
    }

    public static function clenseArgs(Request $request, $index, $default = null)
    {
        $parms = $request->getQueryParams();
        return isset($parms[$index]) ? $parms[$index] : $default;
    }
}

$app->run();
