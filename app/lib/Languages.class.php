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