<?php

class Organisations
{
	var $s;
	function Organisations(&$smarty)
	{
		$this->s = &$smarty;
	}
	
	/*
		Return the string name of the oranisation.
		Return false if organisation not found.
	*/
	function name($organisation_id)
	{
		$ret = false;
		$q = 'SELECT name
				FROM organisation
				WHERE id = '.intval($organisation_id);
		if ($r = $this->s->db->Select($q))
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
		$q = 'SELECT id
				FROM organisation';
		if ($r = $this->s->db->Select($q))
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
