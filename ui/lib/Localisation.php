<?php

require_once __DIR__.'/../../Common/lib/CacheHelper.class.php';
require_once __DIR__.'/../../Common/TimeToLiveEnum.php';
require_once __DIR__.'/../localisation/Strings.php';

class Localisation {
    
    public static $currentLanguage;
    private static $doc;
    private static $xPath;
    private static $ready = false;
    
    public static function init()
    {   
        self::$ready = true;      
        $userLang = UserSession::getUserLanguage();
        if(!$userLang || strcasecmp("en", $userLang) === 0) {
            self::$currentLanguage = null;
        } else {
            self::$currentLanguage = $userLang;
        }
        self::$doc = new DOMDocument();
        
        if(is_null(self::$currentLanguage)) {
            self::$doc->loadXML(CacheHelper::getCached(CacheHelper::SITE_LANGUAGE, TimeToLiveEnum::HOUR, 'Localisation::fetchTranslationFile', 'strings.xml'));
        } else {
            self::$doc->loadXML(CacheHelper::getCached(CacheHelper::SITE_LANGUAGE."_".self::$currentLanguage, TimeToLiveEnum::HOUR, 'Localisation::fetchTranslationFile', "strings_".self::$currentLanguage.".xml"));
        }
    }

    public static function getTranslation($stringId)
    {
        //apc_clear_cache();
        if(!self::$ready) self::init();
        self::$xPath = new DOMXPath(self::$doc);
        $stringElement = self::$xPath->query("/resources/string[@name='$stringId']");
//        if($stringElement->length == 0) {
//            return "Could not find/load: $stringId";
//        }
        return $stringElement->item(0)->nodeValue;
    }
    
    public static function fetchTranslationFile($lang = "strings.xml")
    {   
        return file_get_contents(__DIR__."/../localisation/$lang");
    }
}
