<?php

namespace SolasMatch\API\DAO;

use \SolasMatch\Common as Common;
use \SolasMatch\API\Lib as Lib;

require_once __DIR__."/../../api/lib/PDOWrapper.class.php";
require_once __DIR__."/../../Common/protobufs/models/Language.php";
require_once __DIR__."/../../Common/protobufs/models/Country.php";

class LanguageDao
{

        
    public static function getLanguage($id, $code = null, $name = null)
    {
        $result = Lib\PDOWrapper::call(
            "getLanguage",
            Lib\PDOWrapper::cleanseNullOrWrapStr($id).", ".
            Lib\PDOWrapper::cleanseNullOrWrapStr($code).", ".
            Lib\PDOWrapper::cleanseNullOrWrapStr($name)
        );
        return Common\Lib\ModelFactory::buildModel("Language", $result[0]);
    }

    public static function getLanguageList()
    {
        $languages = array();
        $result = Lib\PDOWrapper::call("getLanguages", "");
        foreach ($result as $lang) {
            $languages[] = Common\Lib\ModelFactory::buildModel("Language", $lang);
        }

        return $languages;
    }

    public static function getActiveLanguages()
    {
        $languages = null;
        $result = Lib\PDOWrapper::call("getActiveLanguages", "");
        if ($result) {
            $languages = array();
            foreach ($result as $row) {
                $languages[] = Common\Lib\ModelFactory::buildModel("Language", $row);
            }
        }
        return $languages;
    }
    
    public static function getActiveSourceLanguages()
    {
        $languages = null;
        $result  = Lib\PDOWrapper::call("getActiveSourceLanguages", "");
        if ($result) {
            $languages = array();
            foreach ($result as $row) {
                $languages[] = Common\Lib\ModelFactory::buildModel("Language", $row);
            }
        }
        return $languages;
    }
    
    public static function getActiveTargetLanguages()
    {
        $languages = null;
        $result = Lib\PDOWrapper::call("getActiveTargetLanguages", "");
        if ($result) {
            $languages = array();
            foreach ($result as $row) {
                $languages[] = Common\Lib\ModelFactory::buildModel("Language", $row);
            }
        }
        return $languages;
    }
}
