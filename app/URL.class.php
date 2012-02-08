<?php
class URL 
{
	
	public static function server() {
		$url = null;
		if (self::isServerNameSet()) {
			$url = self::getHttpAccessProtocol();
			$url .= $_SERVER['SERVER_NAME'];
			if (!self::isAccessedOnPort80()) {
			 	$url .= ':' . $_SERVER['SERVER_PORT'];
			}
		}
		return $url;
	}
	
	private static function isServerNameSet() {
		return (strlen($_SERVER['SERVER_NAME']) > 0);
	}

	private static function getHttpAccessProtocol() {
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			return 'https://';
		}
		else {
			return 'http://';
		}
	}

	private static function isAccessedOnPort80() {
		return ($_SERVER['SERVER_PORT'] == '80');
	}

	function login()
	{
		return $this->server().'/login/';
	}

	function logout()
	{
		return $this->server().'/logout/';
	}

	function register()
	{
		return $this->server().'/register/';
	}

	public static function tag($tag) {
		return $this->server() . '/tag/' . $tag->getLabel() . '/';
	}
}
