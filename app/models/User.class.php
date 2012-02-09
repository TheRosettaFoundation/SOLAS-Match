<?php
class User
{
	var $_user_id;
	var $_email;
	
	private function setUserId($user_id)
	{
		$this->_user_id = $user_id;
	}

	public static function getEmail() {
		return $this->_email;
	}

	private function setEmail($email)
	{
		$this->_email = $email;
	}

	public static function isValidEmail($email)
	{
		$ret = false;
		$ret = (strpos($email, '@')!==false && strpos($email, '.')!==false);
		return $ret;
	}
	
	public static function isValidPassword($password)
	{
		$ret = false;
		$ret = (strlen($password)>0);
		return $ret;
	}

}
