<?php
namespace SolasMatch\API;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use \SolasMatch\Common as Common;


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

mb_internal_encoding('UTF-8');

require __DIR__ . '/vendor/autoload.php';

require_once __DIR__."/lib/Middleware.php";
require_once __DIR__."/OAuth2/Client.php";
require_once __DIR__."/OAuth2/Scope.php";
require_once __DIR__."/OAuth2/Session.php";
require_once __DIR__."/../Common/lib/Settings.class.php";
require_once __DIR__."/../Common/lib/ModelFactory.class.php";
require_once __DIR__."/../Common/Enums/BadgeTypes.class.php";
require_once __DIR__."/../Common/lib/APIHelper.class.php";
require_once __DIR__."/../Common/lib/UserSession.class.php";
require_once __DIR__."/../Common/Enums/HttpMethodEnum.class.php";
require_once __DIR__."/../Common/Enums/HttpStatusEnum.class.php";

$app = AppFactory::create();

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
            
    private static function initOAuth()
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
    
    public static function sendResponse($headers, $body, $code = 200, $oauthToken = null)
    {
        header('Access-Control-Allow-Origin: *');
        $response = self::getDispatcher()->response();
        $apiHelper = new Common\Lib\APIHelper('.json');
        $response['Content-Type'] = $apiHelper->getContentType();
        $body = $apiHelper->serialize($body);
        $token = $apiHelper->serialize($oauthToken);
        $response["X-Custom-Token"] = base64_encode($token);
        if ($headers != null) {
            foreach ($headers as $key => $val) {
                $response[$key] = $val;
            }
        }
        
        if ($code != null) {
            $response->status($code);
        }
        
        $response->body($body);
    }

    public static function clenseArgs($index, $default = null)
    {
        $req = self::getDispatcher()->request();
        $result = $req->get($index);
        return is_null($result) ? $default : $result;
    }
}

$app->run();
