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
        foreach (Lib\PDOWrapper::call("getLanguages", "") as $lcid) {
            $languages[] = Common\Lib\ModelFactory::buildModel("Language", $lcid);
        }

        return $languages;
    }

    public static function getActiveLanguages()
    {
        $languages = null;
        if ($result = Lib\PDOWrapper::call("getActiveLanguages", "")) {
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
        if ($result = Lib\PDOWrapper::call("getActiveSourceLanguages", "")) {
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
        if ($result = Lib\PDOWrapper::call("getActiveTargetLanguages", "")) {
            $languages = array();
            foreach ($result as $row) {
                $languages[] = Common\Lib\ModelFactory::buildModel("Language", $row);
            }
        }
        return $languages;
    }
}
