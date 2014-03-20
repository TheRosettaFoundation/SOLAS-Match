<?php

namespace SolasMatch\API;

use \SolasMatch\Common as Common;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers : Content-Type');
header('Access-Control-Allow-Methods : GET, POST, PUT, DELETE');
mb_internal_encoding("UTF-8");

require __DIR__."/vendor/autoload.php";

\DrSlump\Protobuf::autoload();

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

class Dispatcher {
    
    private static $apiDispatcher = null;
    private static $oauthServer = null; 
    private static $oauthRequest = null;
            
    public static function  getDispatcher()
    {
        if (self::$apiDispatcher == null) {
            self::$apiDispatcher = new \Slim\Slim(array(
                 'debug' => true
                ,'mode' => 'development' // default is development. TODO get from config file, or set
                // in environment...... $_ENV['SLIM_MODE'] = 'production';
            ));
            $app = self::$apiDispatcher;
            self::$apiDispatcher->configureMode('production', function () use ($app) {
                $app->config(array(
                    'log.enable' => true,
                    'log.path' => '../../logs', // Need to set this...
                    'debug' => false
                ));
            });

            $app->configureMode('development', function () use ($app) {
                $app->config(array(
                    'log.enable' => false,
                    'debug' => true
                ));
            });
        }
        return self::$apiDispatcher;
    }
    

    public static function init()
    {
        $path = self::getDispatcher()->request()->getResourceUri();
        $path = explode("/", $path);
        $path = $path[1];
        $providerNames = self::readProviders("$path/");
        self::autoRequire($providerNames,"$path/");
        self::initUnitTests();
        self::initOAuth();
        self::getDispatcher()->run();  
    }

    private static function initUnitTests()
    {
        $headers = self::getDispatcher()->request()->headers;
        if ($headers->get('X-UNIT-TESTING') == '1') {
            Lib\PDOWrapper::$unitTesting = true;
        }
    }
    
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
    
    public static function sendResponse($headers, $body, $code = 200, $format = ".json",$oauthToken=null)
    {
        header('Access-Control-Allow-Origin: *');
        $response = self::getDispatcher()->response();
        $apiHelper = new Common\Lib\APIHelper($format);
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
    
    public static function register($httpMethod, $url, $function, $middleware = null)
    {        
        switch ($httpMethod) {
            case Common\Enums\HttpMethodEnum::DELETE: {
                self::getDispatcher()->delete($url,$middleware, $function);
                break;
            }
            
            case Common\Enums\HttpMethodEnum::GET: {
                self::getDispatcher()->get($url,$middleware, $function);
                break;
            }
            
            case Common\Enums\HttpMethodEnum::POST: {
                self::getDispatcher()->post($url,$middleware, $function);
                break;
            }
            
            case Common\Enums\HttpMethodEnum::PUT: {
                self::getDispatcher()->put($url,$middleware, $function);
                break;
            }
        }
    }
    
    public static function registerNamed(
        $httpMethod,
        $url,
        $function,
        $name,
        $middleware = "\SolasMatch\API\Lib\Middleware::isloggedIn"
    ) {
        switch ($httpMethod) {
            case Common\Enums\HttpMethodEnum::DELETE: {
                if($middleware!=null) {
                	self::getDispatcher()->delete($url, $middleware, $function)->name($name);	
                } else {
                    self::getDispatcher()->delete($url, $function)->name($name);
                }
                
                break;
            }
            
            case Common\Enums\HttpMethodEnum::GET: {
                if($middleware!=null) {
                	self::getDispatcher()->get($url,  $middleware, $function)->name($name);
				} else {
					self::getDispatcher()->get($url,  $function)->name($name);
				}
                break;
            }
            
            case Common\Enums\HttpMethodEnum::POST: {
                if($middleware!=null) {
                	self::getDispatcher()->post($url,  $middleware, $function)->name($name);
				} else {
					self::getDispatcher()->post($url,  $function)->name($name);
				}
                break;
            }
            
            case Common\Enums\HttpMethodEnum::PUT: {
                if($middleware!=null) {	
                	self::getDispatcher()->put($url,  $middleware, $function)->name($name);
				} else {
					self::getDispatcher()->put($url,  $function)->name($name);
				}
                break;
            }
        }
    }
    
    public static function clenseArgs($index, $httpMethod=null, $default=null)
    {
        $req = self::getDispatcher()->request();
        switch ($httpMethod){
            case Common\Enums\HttpMethodEnum::GET : {
                 $result = $req->get($index);
                 return is_null($result) ? $default : $result; 
            }
            case Common\Enums\HttpMethodEnum::POST : {
                $result = $req->post($index);
                return is_null($result) ? $default : $result; 
            }
            case Common\Enums\HttpMethodEnum::PUT : {
               $result = $req->put($index);
               return is_null($result) ? $default : $result; 
            }
            default: {
               $result = $req->params($index);
               return is_null($result) ? $default : $result; 
            }
        }
    }
    
    private static function autoRequire(array $providers, $root = "providers")
    {
        foreach ($providers as $provider) {
            require_once $root.$provider.".php";
        }
    }
    
    private static function readProviders($root)
    {
        $temp = scandir($root);
        $ret = array();
        foreach ($temp as $provider) {
            if ($provider != "." && $provider != ".." && strncmp($provider, ".", 1)) {
                $ret[] = substr($provider, 0, sizeof($provider)-5);
            }
        }
        return $ret;
    }

}
Dispatcher::init();
