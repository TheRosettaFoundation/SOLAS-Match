<?php

class UserSession {
	public static function setSession($user_id) {
		$_SESSION['user_id'] = $user_id;
	}

	public static function destroySession() {
		$_SESSION = array();
		session_destroy();
	}

	public static function getCurrentUserID() {
		if (isset($_SESSION['user_id']) && User::isValidUserId($_SESSION['user_id'])) {
			return $_SESSION['user_id'];
		}
		else {
			return null;
		}
	}
}