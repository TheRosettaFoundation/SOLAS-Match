<?php
namespace SolasMatch\UI;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use \SolasMatch\Common as Common;


mb_internal_encoding('UTF-8');

/**
 * We must start a native PHP session to initialize the $_SESSION superglobal.
 * However, we won't be using the native session store for persistence, so we
 * disable the session cookie and cache limiter. We also set the session
 * handler to avoid PHP's native session file locking.
 */
ini_set('session.use_cookies', 0);
session_cache_limiter(false);
session_set_save_handler('SolasMatch\UI\open', 'SolasMatch\UI\close', 'SolasMatch\UI\read', 'SolasMatch\UI\write', 'SolasMatch\UI\destroy', 'SolasMatch\UI\gc');
function open($savePath, $sessionName)
{
    return true;
}
function close()
{
    return true;
}
function read($id)
{
    return '';
}
function write($id, $data)
{
    return true;
}
function destroy($id)
{
    return true;
}
function gc($maxlifetime)
{
    return true;
}

require_once __DIR__ . '/ui/vendor/autoload.php';

define('FINANCE',           128);
define('SITE_ADMIN',         64);
define('PROJECT_OFFICER',    32);
define('COMMUNITY_OFFICER',  16);
define('NGO_ADMIN',           8);
define('NGO_PROJECT_OFFICER', 4);
define('NGO_LINGUIST',        2);
define('LINGUIST',            1);
define('ORG_EXCEPTIONS',  [773]);

require_once 'Common/lib/Settings.class.php';
require_once 'Common/lib/ModelFactory.class.php';
require_once 'Common/lib/UserSession.class.php';

require_once 'Common/Enums/BadgeTypes.class.php';
require_once 'Common/Enums/BanTypeEnum.class.php';
require_once 'Common/Enums/HttpMethodEnum.class.php';
require_once 'Common/Enums/NotificationIntervalEnum.class.php';
require_once 'Common/Enums/TaskStatusEnum.class.php';
require_once 'Common/Enums/TaskTypeEnum.class.php';

require_once 'ui/lib/Middleware.class.php';
require_once 'ui/lib/TemplateHelper.php';
require_once 'ui/lib/Localisation.php';

require_once 'Common/protobufs/models/User.php';
require_once 'Common/protobufs/models/Tag.php';
require_once 'Common/protobufs/models/Task.php';
require_once 'Common/protobufs/models/Organisation.php';
require_once 'Common/protobufs/models/Badge.php';
require_once 'Common/protobufs/models/Language.php';
require_once 'Common/protobufs/models/Country.php';
require_once 'Common/protobufs/models/TaskMetadata.php';
require_once 'Common/protobufs/models/UserTaskStreamNotification.php';
require_once 'Common/protobufs/models/TaskReview.php';

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
require_once 'ui/DataAccessObjects/SubscriptionDao.class.php';
require_once 'ui/DataAccessObjects/ProjectDao.class.php';
require_once 'ui/DataAccessObjects/TipDao.class.php';

\SolasMatch\Common\Enums\TaskTypeEnum::init();

$template_data = [];
$flash_messages = [];

$app = AppFactory::create();

$app->addRoutingMiddleware();

$app->add('\SolasMatch\UI\Lib\Middleware:Flash');
$app->add('\SolasMatch\UI\Lib\Middleware:beforeDispatch');
$app->add('\SolasMatch\UI\Lib\Middleware:SessionCookie');

$customErrorHandler = function (
    Request $request,
    \Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails,
    ?LoggerInterface $logger = null
) use ($app) {
    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write('<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >
<head><meta name="robots" content="noindex" /></head>
<body>');
    if ($exception->getMessage() === 'Not found.') {
        $response->getBody()->write('
<table width="100%">
<tr><td style="padding-bottom: 90px" /></tr>
<tr><td align="center" style="padding-bottom: 40px"><h4>Not found: ' . $request->getUri() . '<br /><a href="https://' . $_SERVER['SERVER_NAME'] . '">Click here to go to home page</a></h4></td></tr>
<tr><td align="center"><img height="60px" src="https://twbplatform.org/ui/img/TWB_logo1.PNG"></td></tr>
</table>
</body>
</html>');
        error_log('NOT FOUND: ' . $request->getUri());
        return $response->withStatus(404);
    } else {
        $response->getBody()->write('<h4>' . $request->getUri() . '<br />' . $exception->getMessage() . str_replace('#', '<br />', $exception->getTraceAsString()) . '</h4>
</body>
</html>');
        error_log('ERROR on: ' . $request->getUri() . ', ' . $exception->getMessage());
        error_log($exception->getTraceAsString());
        return $response->withStatus(500);
    }
};
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);

require_once('/repo/SOLAS-Match/ui/vendor/smarty/smarty/libs/Smarty.class.php');

require_once 'ui/RouteHandlers/AdminRouteHandler.class.php';
require_once 'ui/RouteHandlers/UserRouteHandler.class.php';
require_once 'ui/RouteHandlers/OrgRouteHandler.class.php';
require_once 'ui/RouteHandlers/TaskRouteHandler.class.php';
require_once 'ui/RouteHandlers/TagRouteHandler.class.php';
require_once 'ui/RouteHandlers/BadgeRouteHandler.class.php';
require_once 'ui/RouteHandlers/ProjectRouteHandler.class.php';
require_once 'ui/RouteHandlers/StaticRouteHandler.class.php';

$app->run();
