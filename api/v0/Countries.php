<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Languages
 *
 * @author sean
 */
require_once 'lib/Languages.class.php';
class Countries {
    public static function init(){
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/countries(:format)/', function ($format=".json"){
            $dao = new Languages();
           Dispatcher::sendResponce(null, $dao->getCountryList(), null, $format);
        },'getCountries');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/countries/:id/', function ($id,$format=".json"){
           if(!is_numeric($id)&& strstr($id, '.')){
               $id= explode('.', $id);
               $format='.'.$id[1];
               $id=$id[0];
           }
             $dao = new Languages();
           $data= $dao->getCountry($id, null, null);
           if(is_array($data)&&is_array($data[0]))$data=$data[0];
           Dispatcher::sendResponce(null, $data, null, $format);
        },'getCountry');
      
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/countries/getByCode/:code/', function ($code,$format=".json"){
           if(!is_numeric($code)&& strstr($code, '.')){
               $code= explode('.', $code);
               $format='.'.$code[1];
               $code=$code[0];
           }
             $dao = new Languages();
             $data= $dao->getCountry(null, $code, null);
             if(is_array($data)&&is_array($data[0]))$data=$data[0];
           Dispatcher::sendResponce(null, $data, null, $format);
        },'getCountryByCode');
      
        
    }
}
Countries::init();
?>
