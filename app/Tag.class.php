<?php

class Tag
{
	/*
	 * Responsible for CRUD (cread, read, update, delete) operations
	 * related to content tags.
	 */
	
	public static function langID(&$s, $lang, $str_lang_code = 'en')
	{
		$ret = false;
		$column = $str_lang_code.'_name';
		$q = 'SELECT id
				FROM language
				WHERE '.$s->db->cleanse($column).' =  \''.$s->db->cleanse($lang).'\'';
		if ($r = $s->db->Select($q))
		{
			$ret = $r[0][0];
		}
		return $ret;
	}
	
	public static function langName(&$s, $lang_id, $str_lang_code = 'en')
	{
		$ret = false;
		$column = $str_lang_code.'_name';
		$q = 'SELECT '.$column.'
				FROM language
				WHERE id = '.$s->db->cleanse($lang_id);
		if ($r = $s->db->Select($q))
		{
			$ret = $r[0][0];
		}
		return $ret;
	}
}
