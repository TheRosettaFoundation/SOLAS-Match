<?php

class Languages {
	public static function languageIdFromName($language_name) {
		$db 	= new PDOWrapper();
		$db->init();
		$result = self::getLanguage(null,null,$language_name);
		return $result[0];
	}
	
	public static function languageNameFromId($language_id) {
		$db 	= new PDOWrapper();
		$db->init();
		$result = self::getLanguage($language_id,null,null);
		return $result[2];
	}
        
    public static function getLanguage($id,$code,$name){
        $db = new PDOWrapper();
        $db->init();
        $result =$db->call("getLanguage", "{$db->cleanseNullOrWrapStr($id)},{$db->cleanseNullOrWrapStr($code)},{$db->cleanseNullOrWrapStr($name)}") ;
        return $result[0];
    }

        public static function getLanguageList() {
               $db = new PDOWrapper();
		$db->init();
		$languages = array();
                foreach($db->call("getLanguages", "") as $lcid) {
                    $languages[] = $lcid;
                }
                
		return $languages;
        }

        public static function getCountryList(){
              $db = new PDOWrapper();
		$db->init();
		$countries = array();
                foreach($db->call("getCountries", "") as $lcid) {
                    $countries[] = $lcid;
                }
                
		return $countries;
        }
        public static function getlcid($lang,$country){
                $db = new PDOWrapper();
		$db->init();
		$lcid = $db->call("getLCID", "{$db->cleanseNullOrWrapStr($lang)},{$db->cleanseNullOrWrapStr($country)}");            
		return $lcid[0]['lcid'];
        }

	public static function isValidLanguageId($language_id) {
		return (is_numeric($language_id) && $language_id > 0);
	}

	public static function ensureLanguageIdIsValid($language_id) {
		if (!self::isValidLanguageId($language_id)) {
			throw new InvalidArgumentException('A valid language id was expected.');
		}
	}

	public static function saveLanguage($language_name) {
		$language_id = self::languageIdFromName($language_name);
		if (is_null(($language_id))) {
			throw new InvalidArgumentException('A valid language name was expected.');
		}
		return $language_id;
	}

}
