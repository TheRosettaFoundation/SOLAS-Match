<?php

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
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/languages(:format)/',
            function ($format = ".json") {
                $data = Languages::getLanguageList();
                $result = null;
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'getLanguages',
            null
        );
            
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/languages/getActiveLanguages(:format)/',
            function ($format = '.json') {
                Dispatcher::sendResponce(null, Languages::getActiveLanguages(), null, $format);
            },
            'getActiveLanguages',
            null
        );
                        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/languages/getActiveSourceLanguages(:format)/',
            function ($format = '.json') {
                Dispatcher::sendResponce(null, Languages::getActiveSourceLanguages(), null, $format);
            },
            'getActiveSourceLanguages',
            null
        );
          
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/languages/getActiveTargetLanguages(:format)/',
            function ($format = '.json') {
                Dispatcher::sendResponce(null, Languages::getActiveTargetLanguages(), null, $format);
            },
            'getActiveTargetLanguages',
            null
        );
      
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/languages/:languageId/',
            function ($languageId, $format = ".json") {
                if (!is_numeric($languageId) && strstr($languageId, '.')) {
                    $languageId = explode('.', $languageId);
                    $format = '.'.$languageId[1];
                    $languageId = $languageId[0];
                }
                $data = Languages::getLanguage($languageId, null, null);
                if (is_array($data)) {
                    $data = $data[0];
                }
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'getLanguage',
            null
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/languages/getByCode/:code/',
            function ($code, $format = ".json") {
                if (!is_numeric($code) && strstr($code, '.')) {
                    $code = explode('.', $code);
                    $format = '.'.$code[1];
                    $code = $code[0];
                }
                $data = Languages::getLanguage(null, $code, null);
                if (is_array($data) && is_array($data[0])) {
                    $data = $data[0];
                }
            Dispatcher::sendResponce(null, $data, null, $format);
            },
            'getLanguageByCode',
            null
        );
    }
}
Langs::init();
