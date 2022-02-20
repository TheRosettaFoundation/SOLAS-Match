<?php
namespace SolasMatch\API;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use \SolasMatch\Common as Common;


mb_internal_encoding('UTF-8');

require __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/lib/Middleware.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Exception/OAuth2Exception.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Exception/ClientException.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Exception/InvalidGrantTypeException.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Exception/InvalidAccessTokenException.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Grant/GrantTrait.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Storage/ClientInterface.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Storage/SessionInterface.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Storage/ScopeInterface.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Grant/GrantTypeInterface.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Grant/Implicit.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Grant/RefreshToken.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Grant/Password.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Grant/ClientCredentials.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Grant/AuthCode.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Authorization.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Resource.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Util/RequestInterface.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Util/SecureKey.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Util/RedirectUri.php';
require_once '/repo/SOLAS-Match/api/vendor/league/oauth2-server/src/League/OAuth2/Server/Util/Request.php';
require_once __DIR__ . '/OAuth2/Client.php';
require_once __DIR__ . '/OAuth2/Scope.php';
require_once __DIR__ . '/OAuth2/Session.php';
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

Dispatcher::initOAuth();

class Dispatcher
{
    private static $oauthServer = null;
    private static $oauthRequest = null;
            
    public static function initOAuth()
    {
        self::$oauthRequest = new \League\OAuth2\Server\Util\Request();
        self::$oauthServer = new \League\OAuth2\Server\Authorization(
            new \League\OAuth2\Server\Storage\PDO\Client(),
            new \League\OAuth2\Server\Storage\PDO\Session(),
            new \League\OAuth2\Server\Storage\PDO\Scope()
        );
        self::$oauthServer->setAccessTokenTTL(Common\Lib\Settings::get('site.oauth_timeout'));
        $passwordGrant = new \League\OAuth2\Server\Grant\Password();
        $passwordGrant->setVerifyCredentialsCallback("\SolasMatch\API\DAO\UserDao::apiLogin");
        self::$oauthServer->addGrantType($passwordGrant);
        self::$oauthServer->addGrantType(new \League\OAuth2\Server\Grant\AuthCode());
    }
    
    public static function getOauthServer()
    {
        return self::$oauthServer;
    }
    
    public static function sendResponse(Response $response, $body, $code = 200, $oauthToken = null)
    {
        $response = $response->withHeader('Access-Control-Allow-Origin',  '*');
        $response = $response->withHeader('Access-Control-Allow-Headers', 'Content-Type');
        $response = $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
        $response = $response->withHeader('Content-Type', 'application/json; charset=utf-8');

        $apiHelper = new Common\Lib\APIHelper('.json');
        $body = $apiHelper->serialize($body);

        $token = $apiHelper->serialize($oauthToken);
        $response = $response->withHeader('X-Custom-Token', base64_encode($token));
        
        if ($code != null) $response = $response->withStatus($code);
        
        return $response->getBody()->write($body);
    }

    public static function clenseArgs(Request $request, $index, $default = null)
    {
        $parms = $request->getQueryParams();
        return isset($parms[$index]) ? $parms[$index] : $default;
    }
}

$h = (string)$app->getRouteCollector()->getRouteParser()->urlFor("home");
error_log("route home: $h");
$app->run();
