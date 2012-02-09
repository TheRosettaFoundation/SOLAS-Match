<?php

require('models/User.class.php');

class UserDao {
	public function find($params) {
		$query = null;
		$db = new MySQLWrapper();
		$db->init();
		if (isset($params['user_id'])) {
			$query = 'SELECT *
						FROM user
						WHERE user_id = ' . $db->cleanse($params['user_id']);
		}
		else if (isset($params['email'])) {
			$query = 'SELECT *
						FROM user
						WHERE email = ' . $db->cleanseWrapStr($params['email']);
		}
		else {
			throw new InvalidArgumentException('Cannot search for user, as no valid parameters were given.');
		}

		$ret = null;
		if ($r = $db->Select($q)) {
			$user_data = array(
				'user_id' => $r[0]['user_id'],
				'email' => $r[0]['email'],
				'nonce' => $r[0]['nonce']
			);
			$ret = new User($user_data);
		}
		return $ret;
	}

	public function create($email, $clear_password) {
		if (!User::isValidEmail($email)) {
			throw new InvalidArgumentException('Please check the email provided, and try again. It was not found to be valid.');
		}
		else if (!User::isValidPassword($clear_password)) {
			throw new InvalidArgumentException('Please check the password provided, and try again. It was not found to be valid.');
		}
		else if (is_object($this->find(array('email' => $email)))) {
			throw new InvalidArgumentException('Oops, you already have an account here with that email address. Please log in instread.');
		}

		$nonce = Authentication::generateNonce();
		$password = Authentication::hashPassword($clear_password, $user_nonce);
		
		$user_data = array(
			'email' => $email,
			'nonce' => $nonce,
			'password' => $password
		);
		$user = new User($user_data);
		return $this->save($user);
	}

	public function save($user) {
		if (is_null($user->getUserId())) {
			return $this->_insert($user);
		}
		else {
			return $this->_update($user);
		}
	}

	private static function _update($user) {
		echo "oops, still have to create _update";
	}

	private static function _insert($user_data) {
		// The array that will contain values to be inserted to DB.
		$db = new MySQLWrapper();
		$db->init();
		$insert = array();
		$insert['email'] = $db->cleanseWrapStr($user->getEmail());
		$insert['nonce'] = $db->cleanse($user->getNonce());
		$insert['password'] = $db->cleanseWrapStr($user->getPassword());
		$insert['created_time'] = 'NOW()';
		
		if ($user_id = $s->db->Insert('user', $insert)) {
			return $this->find(array('user_id' => $user_id))
		}
		else {
			return null;
		}
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
	
	public static function logOut() {
		User::destroySession();
	}
	
	function currentUserID() {
		return (isset($_SESSION['user_id']) && intval($_SESSION['user_id']) > 0) ? intval($_SESSION['user_id']) : false;
	}
	
	public static function isLoggedIn()
	{
		return (isset($_SESSION['user_id']));
	}

	public static function destroySession() {
		$_SESSION = array();
		session_destroy();
		return true;
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
}