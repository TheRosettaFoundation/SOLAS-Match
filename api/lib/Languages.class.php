<?php

require_once __DIR__."/../../Common/models/Language.php";
require_once __DIR__."/../../Common/models/Country.php";

class Languages {
    
    public static function languageIdFromName($language_name)
    {
        $result = self::getLanguage(null, null, $language_name);
        return $result->getId();
    }

    public static function languageNameFromId($language_id)
    {
        $result = self::getLanguage($language_id, null, null);
        return $result->getName();
    }

    public static function countryNameFromId($country_id)
    {
        $result = self::getCountry($country_id, null, null);
        return $result->getName();
    }
    public static function countryNameFromCode($countryCode)
    {
        $result = self::getCountry(null, $countryCode, null);
        return $result->getName();
    }
        
    public static function getLanguage($id,$code,$name)
    {
        $result = PDOWrapper::call("getLanguage", PDOWrapper::cleanseNullOrWrapStr($id)
                                            .",".PDOWrapper::cleanseNullOrWrapStr($code)
                                            .",".PDOWrapper::cleanseNullOrWrapStr($name));
        return ModelFactory::buildModel("Language", $result[0]);
    }

    public static function getCountry($id, $code, $name)
    {
        $result = PDOWrapper::call("getCountry", PDOWrapper::cleanseNUll($id)
                                            .",".PDOWrapper::cleanseNullOrWrapStr($code)
                                            .",".PDOWrapper::cleanseNullOrWrapStr($name));
        return ModelFactory::buildModel("Country", $result[0]);
    }

    public static function getLanguageList()
    {
        $languages = array();
        foreach (PDOWrapper::call("getLanguages", "") as $lcid) {
            $languages[] = ModelFactory::buildModel("Language", $lcid);
        }

        return $languages;
    }

    public static function getActiveLanguages()
    {
        $languages = null;
        if ($result = PDOWrapper::call("getActiveLanguages", "")) {
            $languages = array();
            foreach ($result as $row) {
                $languages[] = ModelFactory::buildModel("Language", $row);
            }
        }
        return $languages;
    }

    public static function getCountryList()
    {
        $countries = array();
        foreach (PDOWrapper::call("getCountries", "") as $lcid) {
            $countries[] = ModelFactory::buildModel('Country', $lcid);
        }
                
        return $countries;
    }

    public static function getlcid($lang,$country)
    {
        $lcid = PDOWrapper::call("getLCID", PDOWrapper::cleanseNullOrWrapStr($lang).",".
                                        PDOWrapper::cleanseNullOrWrapStr($country));            
        return $lcid[0]['lcid'];
    }

    public static function isValidLanguageId($language_id)
    {
        return (is_numeric($language_id) && $language_id > 0);
    }

    public static function ensureLanguageIdIsValid($language_id)
    {
        if (!self::isValidLanguageId($language_id)) {
            throw new InvalidArgumentException('A valid language id was expected.');
        }
    }

    public static function saveLanguage($language_name) 
    {
        $language_id = self::languageIdFromName($language_name);
        if (is_null(($language_id))) {
            throw new InvalidArgumentException('A valid language name was expected.');
        }
        return $language_id;
    }
}
