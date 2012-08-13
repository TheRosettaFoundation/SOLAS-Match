<?php

class Languages {
	public static function languageIdFromName($language_name) {
		$db 	= new MySQLWrapper();
		$db->init();
		$q 		= 'SELECT id
					FROM language
					WHERE en_name =  ' . $db->cleanseWrapStr($language_name);

		$ret = null;
		if ($r = $db->Select($q)) {
			$ret = $r[0][0];
		}
		return $ret;
	}
	
	public static function languageNameFromId($language_id) {
		self::ensureLanguageIdIsValid($language_id);

		$db 	= new MySQLWrapper();
		$db->init();
		$q 		= 'SELECT en_name
					FROM language
					WHERE id = ' . $db->cleanse($language_id);

		$ret = null;
		if ($r = $db->Select($q)) {
			$ret = $r[0][0];
		}
		return $ret;
	}

        public static function getLanguageList() {
               $db = new PDOWrapper();
		$db->init();
		$languages = array();
                foreach($db->call("getLanguages", "") as $lcid) {
                    $languages[] = $lcid[0];
                }
                
		return $languages;
        }

        public static function getCountryList(){
              $db = new PDOWrapper();
		$db->init();
		$countries = array();
                foreach($db->call("getCountries", "") as $lcid) {
                    $countries[] = $lcid[0];
                }
                
		return $countries;
        }
        public static function getlcid(&$lang,$country){
            $settings = new Settings();
            $result= "";
             $language_file = $settings->get("files.languages");
            if(file_exists($language_file)) {
                $language_list = parse_ini_file($language_file);
                foreach($language_list as $language => $code) {
                    if($language==$lang)$result.=$code;
                }
            }
            $country_file = $settings->get("files.countries");
            if(file_exists($country_file)) {
                $country_file = parse_ini_file($country_file);
                foreach($country_file as $current => $code) {
                   if($country==$current)$result="{$result}-{$code}";
                }
            }
            return $result;
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
			$language_id = self::_insert($language_name);
		}
		return $language_id;
	}

	private static function _insert($language_name) {
		$db = new MySQLWrapper;
		$db->init();
		$ins = array();
		$ins['en_name'] = $db->cleanseWrapStr($language_name);
		$language_id = $db->Insert('language', $ins);
		return $language_id;
	}
}
