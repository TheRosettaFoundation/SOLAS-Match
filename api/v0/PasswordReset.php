<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PasswordReset
 *
 * @author sean
 */
require_once '../Common/models/PasswordResetRequest.class.php';
require_once '../Common/models/PasswordReset.class.php';
class PasswordResetAPI {
    public  $pass;
    public  $key;
    
    public function __construct() {
         $this->pass="";
         $this->key="";
    }
    public static function init(){

        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/password_reset(:format)/', function ($format=".json"){
            $data=new PasswordReset();
           Dispatcher::sendResponce(null, $data, null, $format);
        },'getResetTemplate');

         Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/password_reset/:key/', function ($key,$format=".json"){
           if(!is_numeric($key)&& strstr($key, '.')){
               $key= explode('.', $key);
               $format='.'.$key[1];
               $key=$key[0];
           }
           $dao = new UserDao();
           
           $data = $dao->getPasswordResetRequests(array('uid'=>$key));
           $data = new PasswordResetRequest($data['user_id'],$data['uid']);
           Dispatcher::sendResponce(null, $data, null, $format);
        },'getResetRequest');
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/password_reset(:format)/', function ($format=".json"){
                $data=Dispatcher::getDispatcher()->request()->getBody();
                $data= APIHelper::deserialiser($data, $format);
                $data= APIHelper::cast("PasswordReset", $data);
                $dao = new UserDao;
                $result= $dao->passwordReset($data->pass,$data->key);
                Dispatcher::sendResponce(null, $result, null, $format);
         },'resetPassword');
         
    }
    
   
}
PasswordResetAPI::init();
?>