<?php

namespace SolasMatch\UI\Lib;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\Common as Common;

require_once __DIR__.'/../../Common/lib/CacheHelper.class.php';
require_once __DIR__.'/../../Common/Enums/TimeToLiveEnum.class.php';

class Localisation
{
    public static $currentLanguage;
    private static $userLanguageDoc;
    private static $defaultLanguageDoc;
    private static $ready = false;
    
    public static function init()
    {
        self::$ready = true;
        $userLang = Common\Lib\UserSession::getUserLanguage();
        $defaultLang = Common\Lib\Settings::get('site.default_site_language_code');

        self::$defaultLanguageDoc = new \DOMDocument();
        self::$defaultLanguageDoc->loadXML(
            Common\Lib\CacheHelper::getCached(
                Common\Lib\CacheHelper::SITE_LANGUAGE,
                Common\Enums\TimeToLiveEnum::HOUR,
                __NAMESPACE__.'\Localisation::fetchTranslationFile',
                'strings_en.xml'
            )
        );

        if (!$userLang || strcasecmp(Common\Lib\Settings::get('site.default_site_language_code'), $userLang) === 0) {
            self::$userLanguageDoc = null;
        } else {
            self::$userLanguageDoc = new \DOMDocument();
            self::$userLanguageDoc->loadXML(
                Common\Lib\CacheHelper::getCached(
                    Common\Lib\CacheHelper::SITE_LANGUAGE.'_'.$userLang,
                    Common\Enums\TimeToLiveEnum::HOUR,
                    __NAMESPACE__.'\Localisation::fetchTranslationFile',
                    "strings_$userLang.xml"
                )
            );
        }
    }
    
    public static function getDefaultStrings()
    {
        if (!self::$ready) {
            self::init();
        }
        return self::$defaultLanguageDoc->saveXML(self::$defaultLanguageDoc->firstChild);
    }

    public static function getUserStrings()
    {
        if (!self::$ready) {
            self::init();
        }

        $ret = null;
        if (self::$userLanguageDoc) {
            $ret = self::$userLanguageDoc->saveXML(self::$userLanguageDoc->firstChild);
        }
        return $ret;
    }

    public static function getTranslation($stringId)
    {
        if (!self::$ready) {
            self::init();
        }

        $ret = '';
        if (self::$userLanguageDoc != null) {
            $xPath = new \DOMXPath(self::$userLanguageDoc);
            $stringElement = $xPath->query("/resources/string[@name='$stringId']");

            if ($stringElement->length !== 0) {
                $foundNode = self::$userLanguageDoc->saveXML($stringElement->item(0));
                $foundNode = substr($foundNode, strpos($foundNode, ">")+1);
                $ret = substr($foundNode, 0, strrpos($foundNode, "<"));
            }
        }
        
        if ($ret == '') {
            $xPath = new \DOMXPath(self::$defaultLanguageDoc);
            $stringElement = $xPath->query("/resources/string[@name='$stringId']");

            if ($stringElement->length !== 0) {
                $foundNode = self::$defaultLanguageDoc->saveXML($stringElement->item(0));
                $foundNode = substr($foundNode, strpos($foundNode, ">")+1);
                $ret = substr($foundNode, 0, strrpos($foundNode, "<"));
            } else {
                error_log("Could not find/load: $stringId");
                $ret = "Could not find/load: $stringId";
            }
        }

        return $ret;
    }
    
    public static function fetchTranslationFile($lang = "strings_en.xml")
    {
        return file_get_contents(__DIR__."/../localisation/$lang");
    }
}
