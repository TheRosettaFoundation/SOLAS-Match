<?php
class Users
{
	var $s;
	function Users(&$smarty)
	{
		$this->s = &$smarty;
	}
	
	function userIDEmail($email)
	{
		$ret = false;
		$q = 'SELECT id 
				FROM user
				WHERE email LIKE \''.$this->s->db->cleanse($email).'\'';
		if ($r = $this->s->db->Select($q))
		{
			$ret = $r[0][0];
		}
		return $ret;
	}
	
	function gravatarSrc($user_id, $width = 14)
	{
		$email = $this->email($user_id);
		$hash = md5(strtolower(trim($email)));
		return 'http://www.gravatar.com/avatar/'.$hash.'?s='.intval($width).'&d=identicon';
	}
	
	function displayName($user_id)
	{
		$ret = false;
		$q = 'SELECT login
				FROM user
				WHERE id = '.$this->s->db->cleanse($user_id);
		if ($r = $this->s->db->Select($q))
		{
			$ret = $r[0]['login'];
		}
		return $ret;
	}
	
	function email($user_id)
	{
		$ret = false;
		$q = 'SELECT email
				FROM user
				WHERE id = '.$this->s->db->cleanse($user_id);
		if ($r = $this->s->db->Select($q))
		{
			$ret = $r[0]['email'];
		}
		return $ret;
	}
	
	function accessLevel($user_id)
	{
		$ret = false;
		$q = 'SELECT access_level
				FROM user
				WHERE id = '.$this->s->db->cleanse($user_id);
		if ($r = $this->s->db->Select($q))
		{
			$ret = $r[0]['access_level'];
		}
		return $ret;	
	}
	
	// Return the user_ids of the most active users recently.
	function recentlyActive($days = 30, $users = 50)
	{
		$ret = false;
		$q = 'SELECT DISTINCT(user_id) 
				FROM user_checkin
				WHERE time >= DATE_SUB(CURDATE(), INTERVAL '.intval($days).' DAY)
				ORDER BY RAND()
				LIMIT '.intval($users);
		if ($r = $this->s->db->Select($q))
		{
			$arr = array();
			foreach($r as $row)
			{
				$arr[] = $row[0];
			}
			if (count($arr)>0)
			{
				$ret = $arr;
			}
		}
		return $ret;
	}
}
