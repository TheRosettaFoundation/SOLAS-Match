<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Register
 *
 * @author sean
 */
class Register {
   public  $email;
   public  $pass;
    
     public function __construct($e="",$p="") {         
         $this->email=$e;
         $this->pass=$p;
    }
    
    public static function init(){
      
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/register(:format)/', function ($format=".json"){
            $data=new Register();
           Dispatcher::sendResponce(null, $data, null, $format);
        },'getRegisterTemplate');
        
        
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/register(:format)/', function ($format=".json"){
                $data=Dispatcher::getDispatcher()->request()->getBody();
                $data= APIHelper::deserialiser($data, $format);
                $data= APIHelper::cast("Register", $data);
                $dao = new UserDao;
                $data= $dao->APIRegister($data->email, $data->pass);
                if(is_array($data)&&isset ($data[0]))$data=$data[0];
                Dispatcher::sendResponce(null,$data, null, $format);
        },'register');
    }
    
}
Register::init();
?>