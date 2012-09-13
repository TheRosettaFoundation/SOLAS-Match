<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Login
 *
 * @author sean
 */
class Login {
    
    public  $email;
    public  $pass;
    
    public function __construct() {
         $this->pass="";
         $this->email="";
    }
    
    public static function init(){
      
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/login(:format)/', function ($format=".json"){
            $data=new Login();
           Dispatcher::sendResponce(null, $data, null, $format);
        },'getLoginTemplate');
        
        
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/login(:format)/', function ($format=".json"){
                $data=Dispatcher::getDispatcher()->request()->getBody();
                $data= APIHelper::deserialiser($data, $format);
                $data= APIHelper::cast("Login", $data);
                $dao = new UserDao;
                Dispatcher::sendResponce(null, $dao->APIlogin($data->email, $data->pass), null, $format);
            },'login');
    }
    
    
}
Login::init();
?>
