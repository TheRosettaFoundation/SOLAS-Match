<?php

/**
 * Description of Languages
 *
 * @author sean
 */

require_once __DIR__.'/../lib/Languages.class.php';

class Countries {

    public static function init()
    {
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/countries(:format)/', 
                                                        function ($format = ".json") {
            
            Dispatcher::sendResponce(null, Languages::getCountryList(), null, $format);
        }, 'getCountries');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/countries/:id/',
                                                        function ($id, $format = ".json") {
            
            if (!is_numeric($id) && strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            $data = Languages::getCountry($id, null, null);
            if (is_array($data) && is_array($data[0])) {
                $data = $data[0];
            }
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getCountry');
      
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/countries/getByCode/:code/',
                                                        function ($code, $format=".json") {

            if (!is_numeric($code) && strstr($code, '.')) {
                $code = explode('.', $code);
                $format = '.'.$code[1];
                $code = $code[0];
            }
            $data = Languages::getCountry(null, $code, null);
            if (is_array($data) && is_array($data[0])) {
                $data = $data[0];
            }
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getCountryByCode');  
    }
}
Countries::init();