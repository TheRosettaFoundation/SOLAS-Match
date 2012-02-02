<?php
class User
{
	private $user_id;
	private $email;
	
	public static function email($user_id)
	{
		// Check the database for the user.
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT email
				FROM user
				WHERE user_id = '.$db->cleanse(intval($user_id)).'
				AND email IS NOT NULL';
		return ($r = $db->Select($q)) ? $r[0]['email'] : false;
	}
	
	/*
	 * Log the user out.
	 */
	public function destroySession()
	{
		$_SESSION = array();
		session_destroy();
		return true;
	}
	
	public static function login($email, $password)	{
		$ret = false;
		// See if we can match the user with what's in the database.
		$nonce = User::nonce($email);
		$db = new MySQLWrapper();
		$db->init();
		$hashed_password = User::hashPassword($password, $nonce);
		$q = 'SELECT *
				FROM user
				WHERE email = \''.$db->cleanse($email).'\'
				AND password = \''.$db->cleanse($hashed_password).'\'';
		if ($r = $db->Select($q)) {
			$user_id = $r[0]['user_id'];
			// Successfuly found a user matching the email and password.
			User::setSession($user_id);
			$ret = true;
		} else {
			throw new AuthenticationException('test');
		}
		return $ret;
	}
	
	/*
	 * Return user ID of user created, or false.
	 */
	public static function create(&$s, $email, $password)
	{
		$ret = false;
		$nonce = self::generateNonce();
		$hashed_password = User::hashPassword($s, $password, $nonce);
		
		// The array that will contain values to be inserted to DB.
		$user = array();
		$user['email'] = '\''.$s->db->cleanse($email).'\'';
		$user['password'] = '\''.$s->db->cleanse($hashed_password).'\'';
		$user['nonce'] = $s->db->cleanse($nonce);
		$user['created_time'] = 'NOW()';
		
		if ($user_id = $s->db->Insert('user', $user))
		{
			$ret = $user_id;
		}
		return $ret;
	}
	
	public static function validEmail($email)
	{
		$ret = false;
		$ret = (strpos($email, '@')!==false && strpos($email, '.')!==false);
		return $ret;
	}
	
	public static function validPassword($password)
	{
		$ret = false;
		$ret = (strlen($password)>0);
		return $ret;
	}
	
	public static function userExists(&$s, $email)
	{
		$ret = false;
		$q = 'SELECT user_id
				FROM user
				WHERE email = \''.$s->db->cleanse($email).'\'';
		if ($r = $s->db->Select($q))
		{
			$ret = true;
		}
		return $ret;
	}
	
	private static function setSession($user_id)
	{
		$_SESSION['user_id'] = $user_id;
	}
	
	/*
	 * Return a random integer (up to the max value that MySQL will
	 * hold in an INT column).
	 */
	private static function generateNonce()
	{
		// Have to be careful not to select a number too big for MySQL to store as INT.
		$mysql_max = 4294967295;
		$algo_max = mt_getrandmax();
		$max_rand = min(array($mysql_max, $algo_max));
		return mt_rand(0, $max_rand);
	}
	
	private static function nonce($email)
	{
		$ret = false;
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT nonce
				FROM user
				WHERE email = \''.$db->cleanse($email).'\'';
		if ($r = $db->Select($q))
		{
			$ret = $r[0]['nonce'];
		}
		return $ret;
	}
	
	public static function isLoggedIn()
	{
		return (isset($_SESSION['user_id']));
	}
	
	private function setUserID($user_id)
	{
		$this->user_id = intval($user_id);
	}

	private function setEmail($email)
	{
		$this->email = $email;
	}

	private static function hashPassword($password, $nonce)
	{
		// Thanks to http://stackoverflow.com/questions/401656/secure-hash-and-salt-for-php-passwords/401684#401684
		$settings = new Settings();
		$site_key = $settings->get('user.site_key');
		return hash_hmac('sha512', $password . $nonce, $site_key);
	}
}
