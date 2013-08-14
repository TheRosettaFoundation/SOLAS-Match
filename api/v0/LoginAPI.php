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
                }
                Dispatcher::sendResponce(null, $data, null, $format, $oAuthResponce);
            } catch(Exception $e) {
                Dispatcher::sendResponce(null, $e->getMessage(), HttpStatusEnum::UNAUTHORIZED, $format);
            }
            
         }, 'login',null);
         
          Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/login/openidLogin/:email/',
                                                        function ($email, $format = ".json") {
            
           
            
             if(isset($_SERVER['HTTP_X_CUSTOM_AUTHORIZATION'])){
                $headerHash = $_SERVER['HTTP_X_CUSTOM_AUTHORIZATION'];
                 if (!is_numeric($email) && strstr($email, '.')) {
                    $temp = array();
                    $temp = explode('.', $email);
                    $lastIndex = sizeof($temp)-1;
                    if ($lastIndex > 1) {
                        $format='.'.$temp[$lastIndex];
                        $email = $temp[0];
                        for ($i = 1; $i < $lastIndex; $i++) {
                            $email = "{$email}.{$temp[$i]}";
                        }
                    }
                }
                $openidHash = md5($email.substr(Settings::get("session.site_key"),0,20));
                if ($headerHash!=$openidHash) {
                    Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, "The Autherization header does not match the current user or the user does not have permission to acess the current resource");
                } 
            }
            
            
            $data = UserDao::getUser(null, $email);
            if (is_array($data)) {
                $data = $data[0];
            }
            
            $oAuthResponce = null;
            if(!is_null($data)) {
                $server = Dispatcher::getOauthServer();       
                $responce = $server->getGrantType('password')->completeFlow(array("client_id"=>$data->getId(),"client_secret"=>$data->getPassword()));
                $oAuthResponce = new OAuthResponce();
                $oAuthResponce->setToken($responce['access_token']);
                $oAuthResponce->setTokenType($responce['token_type']);
                $oAuthResponce->setExpires($responce['expires']);
                $oAuthResponce->setExpiresIn($responce['expires_in']);
            }
            
            Dispatcher::sendResponce(null, $data, null, $format, $oAuthResponce);
        }, 'openidLogin',null);
    }
}
LoginAPI::init();
