<?php

class Languages {
	public static function languageIdFromName($language_name) {
		$db 	= new MySQLWrapper();
		$db->init();
		$q 		= 'SELECT id
					FROM language
					WHERE en_name =  ' . $db->cleanseAndQuoteString($language_name);

		$ret = null;
		if ($r = $db->Select($q)) {
			$ret = $r[0][0];
		}
		return $ret;
	}
	
	public static function languageNameFromId($language_id) {
		$db 	= new MySQLWrapper();
		$db->init();
		$column = $str_lang_code . '_name';
		$q 		= 'SELECT en_name
					FROM language
					WHERE id = ' . $db->cleanse($language_id);

		$ret = null;
		if ($r = $db->Select($q)) {
			$ret = $r[0][0];
		}
		return $ret;
	}
}