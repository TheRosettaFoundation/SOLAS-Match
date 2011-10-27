<?php
require('User.class.php');
/*
 * The control agent of users. The general business logic for users rests
 * here.
 */
class Users
{
	var $s;
	
	function Users(&$smarty)
	{
		$this->s = &$smarty;
	}
	
	function logOut()
	{
		User::destroySession();
	}
	
	function currentUserID()
	{
		return (intval($_SESSION['user_id']) > 0) ? intval($_SESSION['user_id']) : false;
	}
	
	function isLoggedIn()
	{
		return User::isLoggedIn();
	}
	
	public function userEmail($user_id)
	{
		return User::email($this->s, $user_id);
	}
}
