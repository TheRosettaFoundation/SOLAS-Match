<?php

/*
 * Responsible for CRUD (cread, read, update, delete) operations
 * related to content tags.
 */	
class Tag
{
	public static function langID($lang, $str_lang_code = 'en')
	{
		$ret = false;
		$db = new MySQLWrapper();
		$db->init();
		$column = $str_lang_code.'_name';
		$q = 'SELECT id
				FROM language
				WHERE '.$db->cleanse($column).' =  \''.$db->cleanse($lang).'\'';
		if ($r = $db->Select($q))
		{
			$ret = $r[0][0];
		}
		return $ret;
	}
	
	public static function langName($lang_id, $str_lang_code = 'en')
	{
		$ret = false;
		$db = new MySQLWrapper();
		$db->init();
		$column = $str_lang_code.'_name';
		$q = 'SELECT '.$column.'
				FROM language
				WHERE id = '.$db->cleanse($lang_id);
		if ($r = $db->Select($q))
		{
			$ret = $r[0][0];
		}
		return $ret;
	}
}
