<?php
require('User.class.php');
/*
 * The control agent of users. The general business logic for users rests
 * here.
 */
class Users {
	function __construct() {
	}
	
	function logOut()
	{
		User::destroySession();
	}
	
	function currentUserID()
	{
		return (isset($_SESSION['user_id']) && intval($_SESSION['user_id']) > 0) ? intval($_SESSION['user_id']) : false;
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
