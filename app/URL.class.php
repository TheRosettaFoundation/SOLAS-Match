<?php
class URL 
{
	/*
	 *	Return the URL for different parts of the application.
	*/

	function __construct() {
	}
	
	/*
	 * Returns the server address, without a trailing slash
	 */
	function server()
	{
		$url = false;
		if (strlen($_SERVER['SERVER_NAME'])>0)
		{
			$url = 'http';
			// Perhaps we're on https...
			if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
			{
				$url .= 's';
			}
			$url .= '://';
			// Check the port
			if ($_SERVER['SERVER_PORT'] != '80')
			{
		  		$url .= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
		 	}
		 	else
		 	{
			 	$url .= $_SERVER['SERVER_NAME'];
		 	}
		}
		return $url;
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
}
