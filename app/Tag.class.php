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
		$column = $name_in_lang.'_name';
		$q = 'SELECT id
				FROM langauge
					WHERE '.$s->db->cleanse($column).' =  \''.$s->db->cleanse($lang).'\'';
		if ($r = $s->db->Select($q))
		{
			$ret = $r[0][0];
		}
		return $ret;
	}
}
