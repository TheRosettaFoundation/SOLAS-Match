<?php

/**
 * Description of PasswordReset
 *
 * @author sean
 */

require_once '../Common/models/PasswordResetRequest.php';
require_once '../Common/models/PasswordReset.php';

class PasswordResetAPI {
    
    public  $pass;
    public  $key;
    
    public function __construct()
    {
         $this->pass = "";
         $this->key = "";
    }
    
    public static function init()
    {   
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/password_reset(:format)/',
                                                        function ($format = ".json") {
            
            $data = ModelFactory::buildModel("PasswordReset", array());
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getResetTemplate');

        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/password_reset/:key/',
                                                        function ($key, $format = ".json") {
            
            if (!is_numeric($key) && strstr($key, '.')) {
                $key = explode('.', $key);
                $format = '.'.$key[1];
                $key = $key[0];
            }
            $dao = new UserDao();           
            $data = $dao->getPasswordResetRequests(array('uid' => $key));
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getResetRequest');
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/password_reset(:format)/',
                                                        function ($format = ".json") {
            
            $data = Dispatcher::getDispatcher()->request()->getBody();
            $client = new APIHelper($format);
            $data = $client->deserialize($data);
            $data = $client->cast('PasswordReset', $data);
            $dao = new UserDao;
            $result = $dao->passwordReset($data->getPassword(), $data->getKey());
            Dispatcher::sendResponce(null, $result, null, $format);
         }, 'resetPassword');         
    }
}

PasswordResetAPI::init();
