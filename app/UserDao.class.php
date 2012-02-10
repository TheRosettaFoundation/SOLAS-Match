<?php

require('models/User.class.php');

class UserDao {
	public function find($params) {
		$query = null;
		$db = new MySQLWrapper();
		$db->init();
		if (isset($params['user_id']) && isset($param['password'])) {
			$query = 'SELECT *
					FROM user
					WHERE user_id = ' . $db->cleanse($params['user_id']) . '
					AND password = ' . $db->cleanseWrapStr($params['password']);
		}
		else if (isset($params['user_id'])) {
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
		if ($r = $db->Select($query)) {
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
		$password = Authentication::hashPassword($clear_password, $nonce);
		
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

	private function _update($user) {
		echo "oops, still have to create _update";
	}

	private function _insert($user) {
		// The array that will contain values to be inserted to DB.
		$db = new MySQLWrapper();
		$db->init();
		$insert = array();
		$insert['email'] = $db->cleanseWrapStr($user->getEmail());
		$insert['nonce'] = $db->cleanse($user->getNonce());
		$insert['password'] = $db->cleanseWrapStr($user->getPassword());
		$insert['created_time'] = 'NOW()';
		
		if ($user_id = $db->Insert('user', $insert)) {
			return $this->find(array('user_id' => $user_id));
		}
		else {
			return null;
		}
	}
	
	private function clearPasswordMatchesUsersPassword($user, $clear_password) {
		$hashed_input_password = Authentication::hashPassword($clear_password, $user->getNonce());

		return is_object(
				$this->find(array(
					'user_id' => $user->getUserId(),
					'password' => $hashed_input_password
				))
		);
	}

	public function login($email, $clear_password) {
		$user = $this->find(array('email' => $email));

		if (!is_object($user)) {
			throw new InvalidArgumentException('Sorry, we could not find an account for that email address. Please check the provided address, or register for an account.');
		}

		if (!$this->clearPasswordMatchesUsersPassword($user, $clear_password)) {
			throw new InvalidArgumentException('Sorry, that password is incorrect. Please try again.');
		}

		UserSession::setSession($user->getUserId());

		return true;
	}
	
	public function logout() {
		UserSession::destroySession();
	}
	
	public function getCurrentUser() {
		$ret = null;
		if ($user_id = UserSession::getCurrentUserId()) {
			$ret = $this->find(array('user_id' => $user_id));
		}
		return $ret;
	}

	public static function isLoggedIn()
	{
		return (!is_null(UserSession::getCurrentUserId()));
	}

}