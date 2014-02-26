<?php

namespace SolasMatch\API\V0;

use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;

/**
 * Description of Languages
 *
 * @author sean
 */

require_once __DIR__.'/../lib/Languages.class.php';

class Countries
{
    public static function init()
    {
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/countries(:format)/',
            function ($format = ".json") {
                API\Dispatcher::sendResponse(null, Lib\Languages::getCountryList(), null, $format);
            },
            'getCountries'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/countries/:countryId/',
            function ($countryId, $format = ".json") {
                if (!is_numeric($countryId) && strstr($countryId, '.')) {
                    $countryId = explode('.', $countryId);
                    $format = '.'.$countryId[1];
                    $countryId = $countryId[0];
                }
                $data = Lib\Languages::getCountry($countryId, null, null);
                if (is_array($data) && is_array($data[0])) {
                    $data = $data[0];
                }
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getCountry'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/countries/getByCode/:code/',
            function ($code, $format = ".json") {
                if (!is_numeric($code) && strstr($code, '.')) {
                    $code = explode('.', $code);
                    $format = '.'.$code[1];
                    $code = $code[0];
                }
                $data = Lib\Languages::getCountry(null, $code, null);
                if (is_array($data) && is_array($data[0])) {
                    $data = $data[0];
                }
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getCountryByCode'
        );
    }
}
Countries::init();
