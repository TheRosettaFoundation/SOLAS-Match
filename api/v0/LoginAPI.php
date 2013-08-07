<?php

/**
 * Description of Login
 *
 * @author sean
 */

require_once __DIR__.'/../DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../Common/models/OAuthResponce.php';

class LoginAPI {
    
    public  $email;
    public  $pass;
    
    public function __construct()
    {
         $this->pass="";
         $this->email="";
    }
    
    public static function init()
    {
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/login(:format)/', function ($format = ".json") {
            $data = new Login();
            $data->setEmail("manuel@test.com");
            $data->setPassword("test");
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getLoginTemplate',null);
        
        
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/login(:format)/', function ($format = ".json") {
            $data = Dispatcher::getDispatcher()->request()->getBody();
            $client = new APIHelper($format);
            $data = $client->deserialize($data,"Login");
            $params= array();
            try {             
                $data = UserDao::apiLogin($data->getEmail(), $data->getPassword());
                if(!is_null($data)){
                $server = Dispatcher::getOauthServer();                
                $responce = $server->getGrantType('password')->completeFlow(array("client_id"=>$data->getId(),"client_secret"=>$data->getPassword()));
               
                $oAuthResponce = new OAuthResponce();
                $oAuthResponce->setToken($responce['access_token']);
                $oAuthResponce->setTokenType($responce['token_type']);
                $oAuthResponce->setExpires($responce['expires']);
                $oAuthResponce->setExpiresIn($responce['expires_in']);
                
                
                    $data->setPassword(null);
                    $data->setNonce(null);
//                    UserSession::setSession($data->getId());
//                    UserSession::setHash(md5("{$data->getEmail()}:{$data->getDisplayName()}"));
                    


                }
                Dispatcher::sendResponce(null, $data, null, $format, $oAuthResponce);
            } catch(Exception $e) {
                Dispatcher::sendResponce(null, null, $e->getMessage(), $format);
            }
            
         }, 'login',null);
    }
}
LoginAPI::init();
