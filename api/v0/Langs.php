<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;

/**
 * Description of Languages
 *
 * @author sean
 */

require_once __DIR__.'/../lib/Languages.class.php';

class Langs
{
    public static function init()
    {
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/languages(:format)/',
            function ($format = ".json") {
                $data = Lib\Languages::getLanguageList();
                $result = null;
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getLanguages',
            null
        );
            
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/languages/getActiveLanguages(:format)/',
            function ($format = '.json') {
                API\Dispatcher::sendResponse(null, Lib\Languages::getActiveLanguages(), null, $format);
            },
            'getActiveLanguages',
            null
        );
                        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/languages/getActiveSourceLanguages(:format)/',
            function ($format = '.json') {
                API\Dispatcher::sendResponse(null, Lib\Languages::getActiveSourceLanguages(), null, $format);
            },
            'getActiveSourceLanguages',
            null
        );
          
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/languages/getActiveTargetLanguages(:format)/',
            function ($format = '.json') {
                API\Dispatcher::sendResponse(null, Lib\Languages::getActiveTargetLanguages(), null, $format);
            },
            'getActiveTargetLanguages',
            null
        );
      
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/languages/:languageId/',
            function ($languageId, $format = ".json") {
                if (!is_numeric($languageId) && strstr($languageId, '.')) {
                    $languageId = explode('.', $languageId);
                    $format = '.'.$languageId[1];
                    $languageId = $languageId[0];
                }
                $data = Lib\Languages::getLanguage($languageId, null, null);
                if (is_array($data)) {
                    $data = $data[0];
                }
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getLanguage',
            null
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/languages/getByCode/:code/',
            function ($code, $format = ".json") {
                if (!is_numeric($code) && strstr($code, '.')) {
                    $code = explode('.', $code);
                    $format = '.'.$code[1];
                    $code = $code[0];
                }
                $data = Lib\Languages::getLanguage(null, $code, null);
                if (is_array($data) && is_array($data[0])) {
                    $data = $data[0];
                }
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getLanguageByCode',
            null
        );
    }
}
Langs::init();
