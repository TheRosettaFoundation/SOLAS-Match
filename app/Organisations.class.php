<?php

class Organisations
{
	function __construct() {
	}	
	
	/*
		Return the string name of the oranisation.
		Return false if organisation not found.
	*/
	public static function nameFromId($organisation_id)
	{
		$ret = false;
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT name
				FROM organisation
				WHERE id = '.intval($organisation_id);
		if ($r = $db->Select($q))
		{
			$ret = $r[0]['name'];
		}
		return $ret;
	}
	
	/* 
		Return an array of all organisation ids.
	*/
	function organisationIDs()
	{
		$ret = false;
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT id
				FROM organisation';
		if ($r = $db->Select($q))
		{
			$ret = array();
			foreach ($r as $row)
			{
				$ret[] = $row['id'];
			}
		}
		return $ret;
	}
}
