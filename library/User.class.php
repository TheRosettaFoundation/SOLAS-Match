<?php
// This Rosetta Platform class uses an adapted version of Access_user Class, whose copyright notice is required to be left in. See notice below.
/************************************************************************
Access_user Class ver. 1.97
A complete PHP suite to protect pages and maintain members

Copyright (c) 2004 - 2007, Olaf Lederer
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
    * Neither the name of the finalwebsites.com nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
_________________________________________________________________________
available at http://www.finalwebsites.com/portal
Comments & suggestions: http://www.webdigity.com/index.php/board,74.0.html,ref.olaf
*************************************************************************/

//header('Cache-control: private'); // //IE 6 Fix
// error_reporting (E_ALL); // I use this only for testing

//require($_SERVER['DOCUMENT_ROOT'].'/library/user/db_config.php');

if (USE_MYSQL_SESSIONS) // TODO how to get this from site settings?
{
	include_once($_SERVER['DOCUMENT_ROOT'].'/../library/user/session_handler.php');
}
else
{
	session_start();
}

class User
{
	var $s;
	var $table_name;
	var $user;
	var $id;
	var $password;
	var $user_info;
	var $user_email;
	var $save_login = 'yes';
	var $cookie_name = COOKIE_NAME;
	var $cookie_path = COOKIE_PATH;
	var $is_cookie;
	var $count_visit;
	var $language =  'en'; // updated in construction method, so default setting here doesn't mean much.
	var $the_msg;
	var $auto_activation; // use this variable in your login script
	var $send_copy = true; // send a mail copy (after activation) to the administrator
	var $webmaster_mail;
	var $webmaster_name;
	var $admin_mail;
	var $admin_name;
	var $login_page = LOGIN_PAGE;
	var $main_page = START_PAGE;
	var $password_page = ACTIVE_PASS_PAGE;
	var $deny_access_page = DENY_ACCESS_PAGE;
	var $admin_page = ADMIN_PAGE;

	function User(&$smarty, $redirect = true)
	{
		$this->s = &$smarty;
		$this->table_name = $this->s->setting('users.table');
		$this->login_page = $this->s->setting('users.table');
		$this->main_page = START_PAGE;
		$this->password_page = ACTIVE_PASS_PAGE;
		$this->deny_access_page = DENY_ACCESS_PAGE;
		$this->admin_page = ADMIN_PAGE;
		
		$this->webmaster_mail = $this->s->setting('site.email');
		$this->webmaster_name = $this->s->setting('site.name');
		$this->admin_mail = $this->s->setting('site.email');
		$this->admin_name = $this->s->setting('site.name');
		//$this->connect_db();
		$this->id = false;
		
		if (empty($_SESSION['logged_in']))
		{
			$this->login_reader();
			if ($this->is_cookie)
			{
				$this->set_user($redirect);
			}
		}
		if (isset($_SESSION['id'], $_SESSION['pw']))
		{
			$this->id = $_SESSION['id'];
			$this->password = $_SESSION['pw'];
			if (!$this->check_user_db())
			{
				// Security measure.
				$this->id = null;
				$this->password = null;
			}
		}
	}
	
	/* Check if the activated user exists
	 * in the DB that matches this object.
	 * Returns: user id, or false;
	 */
	function check_user_db()
	{
		$ret = false;
		if (!empty($this->id) && !empty($this->password))
		{
			$sql = 'SELECT id
					FROM '.$this->s->db->cleanseSQL($this->table_name).'
					WHERE id = '.$this->s->db->cleanseSQL($this->id).'
					AND pw = \''.$this->s->db->cleanseSQL($this->password).'\'
					AND active = \'y\'';
			$result = $this->s->db->Select($sql);
			if (count($result) == 1 && intval($result[0]['id'])>0)
			{
				$ret = intval($result[0]['id']);
			}
		}
		return $ret;
	}
	
	// $password must already be MD5
	function check_login_credentials($email, $password)
	{
		$ret = false;
		if (!empty($email) && !empty($password))
		{
			$sql = 'SELECT id
					FROM '.$this->s->db->cleanseSQL($this->table_name).'
					WHERE email = \''.$this->s->db->cleanseSQL($email).'\'
					AND pw = \''.$this->s->db->cleanseSQL($password).'\'
					AND active = \'y\'';
			$result = $this->s->db->Select($sql);
			if (count($result) == 1 && intval($result[0]['id'])>0)
			{
				$ret = intval($result[0]['id']);
			}
		}
		return $ret;
	}
	
	/*
	 * Check if for a given email an active account exists.
	 * Returns: user_id, or false;
	 */
	function check_active_email($email)
	{
		$ret = false;
		if ($this->check_email($email))
		{
			$sql = 'SELECT id
					FROM '.$this->s->db->cleanseSQL($this->table_name).'
					WHERE email = \''.$this->s->db->cleanseSQL($email).'\'
					AND active = \'y\'';
			$result = $this->s->db->Select($sql);
			if ((count($result) == 1) && ($result[0]['id'] > 0))
			{
				$ret = intval($result[0]['id']);
			}
		}
		return $ret;
	}
	
	/*
	 * Check if for a given email an account exists.
	 * Returns: user_id, or false;
	 */
	function check_email_exists($email)
	{
		$ret = false;
		if ($this->check_email($email))
		{
			$sql = 'SELECT id
					FROM '.$this->s->db->cleanseSQL($this->table_name).'
					WHERE email = \''.$this->s->db->cleanseSQL($email).'\'';
			$result = $this->s->db->Select($sql);
			if ((count($result) == 1) && ($result[0]['id'] > 0))
			{
				$ret = intval($result[0]['id']);
			}
		}
		return $ret;
	}

	/*
	 * Check if for a given email an inactive account exists.
	 * Returns: user_id, or false;
	 */
	function check_inactive_email($email)
	{
		$ret = false;
		if ($this->check_email($email))
		{
			$sql = 'SELECT id
					FROM '.$this->s->db->cleanseSQL($this->table_name).'
					WHERE email = \''.$this->s->db->cleanseSQL($email).'\'
					AND active = \'n\'';
			$result = $this->s->db->Select($sql);
			if ((count($result) == 1) && ($result[0]['id'] > 0))
			{
				$ret = intval($result[0]['id']);
			}
		}
		return $ret;
	}
	
	function check_inactive_account($id)
	{
		$ret = false;
		if (intval($id) > 0)
		{
			$sql = 'SELECT id
					FROM '.$this->s->db->cleanseSQL($this->table_name).'
					WHERE id = \''.$this->s->db->cleanseSQL($id).'\'
					AND active = \'n\'';
			$result = $this->s->db->Select($sql);
			if ((count($result) == 1) && ($result[0]['id'] > 0))
			{
				$ret = intval($result[0]['id']);
			}
		}
		return $ret;
	}
	
	function check_account_exists($id)
	{
		$ret = false;
		if (intval($id) > 0)
		{
			$sql = 'SELECT id
					FROM '.$this->s->db->cleanseSQL($this->table_name).'
					WHERE id = \''.$this->s->db->cleanseSQL($id).'\'';
			$result = $this->s->db->Select($sql);
			if ((count($result) == 1) && ($result[0]['id'] > 0))
			{
				$ret = intval($result[0]['id']);
			}
		}
		return $ret;
	}
	
	/*
	 * Purpose: find out if for the given email that it has a 
	 * temporary email address assigned to it in the database.
	 * If it does, then we can proceed to validate that new address.
	 */
	function check_email_to_validate()
	{
		$str = 'SELECT id FROM '.$this->table_name.' WHERE id='.$this->s->db->cleanseSQL($this->id).' AND tmp_mail <> \'\'';
		return $this->s->db->Select($str);
	}
	
	/*
	 * Purpose: part of the request a password functionality. This
	 * method is used to validate the URL that someone clicked on
	 * their email requesting a new password.
	 */
	function check_new_password_db()
	{
		$str = 'SELECT id FROM '.$this->table_name.' WHERE id='.$this->s->db->cleanseSQL($this->id).' AND MD5(pw) = \''.$this->s->db->cleanseSQL($this->password).'\'';
		return $this->s->db->Select($str);
	}
	
	// removed check for encoded var $this->password
	// replaced in default case var $password with $this->password
	// added MD5 to sql statement for "new_pass"
	function check_user($pass) {
		$sql = '';
		if ($pass == 'active')
		{
			$sql = sprintf("SELECT COUNT(*) AS test FROM %s WHERE id = %d AND active = 'n'", $this->table_name, $this->id);
		}
		else if ($pass == 'email_exists')
		{
			$sql = sprintf("SELECT COUNT(*) AS test FROM %s WHERE email = %s", $this->table_name, $this->ins_string($this->user_email));
		}
		$ret = false;
		if (!empty($sql))
		{
			$result = mysql_query($sql) or die(mysql_error());
			if (mysql_result($result, 0, 'test') == 1)
			{
				$ret = true;
			}
		}
		return $ret;
	}

	// New methods to handle the access level
	function get_access_level() 
	{
		if (!$this->isLoggedIn())
		{
			return false;
		}
		$sql = sprintf('SELECT access_level FROM %s WHERE id = %s AND active = \'y\'', $this->table_name, $this->ins_string($this->id));
		if (!$result = mysql_query($sql)) 
		{
		   $this->the_msg = $this->s->i->getTextForDisplay('user_error');
		}
		else 
		{
			return mysql_result($result, 0, 'access_level');
		}
	}
	
	// Log in user through a session. Cookie is set elsewhere.
	function openSession()
	{
		$_SESSION['id'] = $this->id;
		$_SESSION['pw'] = $this->password;
		$_SESSION['logged_in'] =  time(); // to offer a time limited access (later)
	}
	
	function set_user($goto_page) {
		$this->openSession();
		$next_page = '';
		if (strlen($this->s->io->post('redirect'))>0)
		{
			$next_page = $this->s->io->post('redirect');
		}
		else
		{
			if ($this->isAuthAdmin())
			{
				// Forward on to admin dashboard.
				$next_page = $this->s->url->dashboard();
			}
			else
			{
				// Forward on to lessons.
				$next_page = $this->main_page;
			}
		}
		if ($goto_page) {
			header('Location: '.$next_page);
			exit;
		}
	}

	/*		
	function connect_db() {
		$conn_str = mysql_connect(BITEY_DB_SERVER, BITEY_DB_USER, BITEY_DB_PASSWORD);
		mysql_select_db(BITEY_DB_NAME); // if there are problems with the tablenames inside the config file use this row
	}
	*/
	
	// added md5 to var $password
	// changed argument for req_visit to $this->password
	function login_user($email, $password) 
	{
		if ($email == '' || $password == '') 
		{
			$this->the_msg = 'Email and/or password is empty.';
		}
		else
		{
			$user_id = $this->check_login_credentials($email, md5($password));
			if ($user_id) 
			{
				$this->id = intval($user_id);
				$this->user_email = $email;
				$this->password = md5($password);
				$this->login_saver();
				//if ($this->count_visit) 
				//{
				$this->reg_visit($this->id);
				//}
				$this->set_user(true);
			}
			else if ($this->check_inactive_email($email)) 
			{
				// Account exists but is not active. Provide a link to page to resend activation email.
				$this->the_msg = $this->s->i->getTextForDisplay('user_login_not_active').'  <a href="'.RESEND_ACTIVATION_PAGE.'">'.$this->s->i->getTextForDisplay('user_request_activation').'</a>';
			}
			else if ($this->check_email_exists($email))
			{
				// Account exists, but by now we know the password is incorrect.
				$forgot_pw_url = $this->s->url->forgotPasswordEmail($email);
				$this->the_msg = 'Incorrect password. By the way, it’s case sensitive. No joy? Then, <a href="'.$forgot_pw_url.'">set a new password</a>.';
			}
			else 
			{
				// Account does not exist.
				$site_email = $this->s->set->get('site.email').
				$this->the_msg = 'That email does not exist in our database. By the way, it’s case sensitive. You can email us at <a href="'.$site_email.'">'.$site_email.'</a>, and we’ll check it out for you.';
			}
		}
	}
	
	function isLoggedIn() {
		return intval($this->id) > 0;
	}
	
	/* This is the only method that sets the login cookie. */
	function login_saver()
	{
		$now = time();
		if ($this->save_login == 'no')
		{
			if (isset($_COOKIE[$this->cookie_name]))
			{
				// Expire the cookie.
				$expire = $now-3600;
			}
			else
			{
				return;
			}
		}
		else
		{
			$expire = $now+2592000; // 30 days
		}
		$cookie_saved_secret = $this->getCookieSharedSecret();
		// Construct cookie value.
		$cookie_str = $this->id.chr(31).$cookie_saved_secret;
		// Save to cookie.
		setcookie($this->cookie_name, $cookie_str, $expire, $this->cookie_path);
	}
	
	/* Create a string that will be stored in the database.
	Whent he person returns and the cookie is still set,
	we'll check the value in their cookie against the value
	of this string to verify it's them. */
	private function getCookieSharedSecret()
	{
		// First check in the database for existing shared secret.
		// If that doesn't exist, create one.
		$cookie_shared_secret = '';
		$str = 'SELECT cookie_shared_secret FROM user WHERE id='.$this->s->db->cleanseSQL($this->id).' LIMIT 1';
		$result = $this->s->db->Select($str);
		if ($result)
		{
			$cookie_shared_secret = $result[0]['cookie_shared_secret'];
		}
		if (strlen($cookie_shared_secret) == 0)
		{
			// Make a new shared secret and save it in the database.
			$cookie_shared_secret = md5(rand().$this->user_email.COOKIE_SECRET_WORD);
			$this->saveCookieSharedSecret($cookie_shared_secret);
		}
		return $cookie_shared_secret;
	}
	
	/* Along with setting a cookie, save the shared secret in the database
	to verify them later. */
	private function saveCookieSharedSecret($shared_secret)
	{
		$str = 'UPDATE user SET cookie_shared_secret=\''.$this->s->db->cleanseSQL($shared_secret).'\' WHERE id ='.$this->s->db->cleanseSQL($this->id).' LIMIT 1';
		return $this->s->db->Update($str);
	}
	
	private function getUserPassword()
	{
		$str = 'SELECT pw FROM user WHERE id='.$this->s->db->cleanseSQL($this->id).' LIMIT 1';
		$result = $this->s->db->Select($str);
		return $result[0]['pw'];
	}
	
	/*
	 * When logging in, read cookie, and if it exists, check that the id and
	 * password match the user.
	 */
	function login_reader() {
		if (isset($_COOKIE[$this->cookie_name]))
		{
			$cookie_parts = explode(chr(31), $_COOKIE[$this->cookie_name]);
			$this->id = intval($cookie_parts[0]);
			$cookie_shared_secret = $this->s->io->cleanseInput($cookie_parts[1]);
			if ($this->validate_cookie($cookie_shared_secret))
			{
				// Cookie passed validation, user can log in.
				$this->is_cookie = true;
				$this->password = $this->getUserPassword();
			}
			else
			{
				$this->id = false;
				$this->password = false;
			}
		}
	}
	
	/* Check against the database whether the id+shared secret
	are correct. */
	private function validate_cookie($cookie_shared_secret)
	{
		$str = 'SELECT id FROM user WHERE id='.$this->s->db->cleanseSQL($this->id).' AND cookie_shared_secret IS NOT NULL AND cookie_shared_secret=\''.$this->s->db->cleanseSQL($cookie_shared_secret).'\'';
		return (count($this->s->db->Select($str)) > 0);
	}
	
	// Alias function.
	function loginLink()
	{
		return $this->login_link();
	}
	function login_link()
	{
		// allow the page to create a link to the login page. This ensures that we don't rely on HTTP_REFERER which cannot be trusted, when redirecting the user to the page they came from before logging in.
		$redirect = $_SERVER['REQUEST_URI']; // where we need to redirect the user after logging in.
		$link = LOGIN_PAGE.'?redirect='.$this->s->io->formatForURL($redirect);
		return $link;
	}
	function registerLink()
	{
		$redirect = $_SERVER['REQUEST_URI']; // where we need to redirect the user after logging in.
		$link = REGISTER_PAGE.'?redirect='.$this->s->io->formatForURL($redirect);
		return $link;
	}

	// removed the md5 from var $pass
	function reg_visit($id)
	{
		$i = array();
		$i['user_id'] = $this->s->db->cleanse($id);
		$i['time'] = 'NOW()';
		$this->s->db->Insert('user_checkin', $i);
	}
	
	function log_out()
	{
		unset($_SESSION['id']);
		unset($_SESSION['pw']);
		unset($_SESSION['logged_in']);
		session_destroy(); // new in version 1.92
		header('Location: '.LOGOUT_PAGE);
		exit;
	}
	
	function logoutLink()
	{
		return LOGOUT_PAGE.'?redirect='.$this->s->io->formatForURL($_SERVER['REQUEST_URI']);
	}
	
	function logoutRedirect()
	{
		$redirect = $this->s->io->formatFromURL($_GET['redirect']);
		if (strlen($redirect) == 0)
		{
			// No redirect URL given. Try to send back to referring URL.
			$redirect = '/'; //$_SERVER['HTTP_REFERER'];
		}
		$this->logout();
		header('Location: '.$redirect);
		exit;
	}
	
	function logout()
	{
		unset($_SESSION['id']);
		unset($_SESSION['pw']);
		unset($_SESSION['logged_in']);
		unset($_SESSION['SID']);
		session_destroy(); // new in version 1.92
	}
	
	// $qs = query string
	function access_page($refer = "", $qs = "", $level = DEFAULT_ACCESS_LEVEL)
	{
		$refer_qs = $refer;
		$refer_qs .= ($qs != "") ? "?".$qs : "";
		if (!$this->check_user_db())
		{
			$_SESSION['referer'] = $refer_qs;
			header('Location: '.$this->login_page);
			exit;
		}
		if ($this->get_access_level() < $level)
		{
			header('Location: '.$this->deny_access_page);
			exit;
		}
	}
	
	function access_page_levels($refer = "", $qs = "", $arrlevel = array("DEFAULT_ACCESS_LEVEL"))
	{
		$refer_qs = $refer;
		$refer_qs .= ($qs != "") ? "?".$qs : "";
		if (!$this->check_user_db())
		{
			$_SESSION['referer'] = $refer_qs;
			header('Location: '.$this->login_page);
			exit;
		}
		if (!in_array($this->get_access_level(), $arrlevel))
		{
			header('Location: '.$this->deny_access_page);
			exit;
		}
	}

	/* 	Test: Does this user's authority match one of those listed in the array?
		Use this to test if a user is allowed to do a certain action.
	*/
	function is_auth($auths)
	{
		return in_array($this->get_access_level(), $auths);
	}
	
	// Alias function.
	function getUserID()
	{
		return $this->get_user_id();
	}
	function get_user_id()
	{
		$ret = -1;
		if (intval($this->id) > 0)
		{
			$ret = intval($this->id);
		}
		return $ret;
	}
	
	// Alias function. Sets and returns user's email.
	function email()
	{
		$ret = false;
		$str = 'SELECT email FROM '.$this->table_name.' WHERE id = '.$this->s->db->cleanseSQL($this->id);
		$result = $this->s->db->Select($str);
		if ($result)
		{
			$ret = $this->user_email = $result[0]['email'];
		}
		return $ret;
	}
	
	function userEmail($user_id)
	{
		$ret = false;
		$str = 'SELECT email FROM '.$this->table_name.' WHERE id = '.$this->s->db->cleanseSQL($user_id);
		$result = $this->s->db->Select($str);
		if ($result)
		{
			$ret = $this->user_email = $result[0]['email'];
		}
		return $ret;
	}
	
	function get_user_info()
	{
		$sql_info = sprintf("SELECT real_name, extra_info, login, email FROM %s WHERE id = %s AND pw = %s", $this->table_name, $this->ins_string($this->id), $this->ins_string($this->password));
		$res_info = mysql_query($sql_info);
		$this->user_full_name = mysql_result($res_info, 0, 'real_name');
		$this->user_info = mysql_result($res_info, 0, 'extra_info');
		$this->user = mysql_result($res_info, 0, 'login');
		$this->user_email = mysql_result($res_info, 0, 'email');
	}
	
	function update_user($new_login, $new_password, $new_confirm, $new_mail)
	{
		$continue = true;
		// Update display name
		if (trim($new_login) <> $this->user)
		{
			if ($this->check_username($new_login))
			{
				// Update the username.
				$old_username = $this->user;
				$str = 'UPDATE  '.$this->table_name.' SET login=\''.$this->s->db->cleanseSQL($new_login).'\' WHERE id='.$this->s->db->cleanseSQL($this->id);
				$this->s->db->Update($str);
				Forum::changeUsername($this->smarty, $new_login, $old_username);
			}
		}
		// Update password
		if (!empty($new_password))
		{
			// User hopefully wants to change their password.
			if ($this->check_new_password($new_password, $new_confirm))
			{
				// Ok, update password straight away.
				$ins_password = md5($new_password);
				$str = 'UPDATE '.$this->table_name.' SET pw=\''.$this->s->db->cleanseSQL($ins_password).'\' WHERE id='.$this->s->db->cleanseSQL($this->id);
				$this->s->db->Update($str);
				// Update session information.
				$_SESSION['pw'] = $this->password = $ins_password;
				
				// Update the forum account.
				Forum::changePassword($this->smarty, $ins_password);
			}
		}
		// Update email address. Complicated, as this requires re-activation.
		if (trim($new_mail) <> $this->user_email)
		{
			if  (!$this->check_email($new_mail))
			{
				// Format of new email address invalid.
				$this->the_msg = $this->s->i->getTextForDisplay('user_email_notvalid');
			}
			else
			{			
				if ($this->check_email_exists($new_mail))
				{
					// Email already exists in database, can't use that for
					// the account's new address.
					$this->the_msg = $this->s->i->getTextForDisplay('user_email_exists');
				}
				else
				{	
					// Go ahead with updating the email address.
					$str = 'UPDATE '.$this->table_name.' SET tmp_mail = \''.$this->s->db->cleanseSQL($new_mail).'\' WHERE id='.$this->s->db->cleanseSQL($this->id);
					$this->s->db->Update($str);
					Forum::changeEmail($this->smarty, $new_mail);
					// Send the confirmation email that they must
					// activate the new address.
					if ($this->send_mail($new_mail, 33))
					{
						$this->the_msg = $this->s->i->getTextForDisplay('user_check_email_mods');
					}
				}
			} 
		}
	}
	
	/*
	 * Confirms that the password is of enough length
	 * and that the confirmation password matches.
	 */
	function check_new_password($pass, $pw_conform)
	{
		if ($pass == $pw_conform)
		{
			if (strlen($pass) >= PW_LENGTH)
			{
				return true;
			}
			else
			{
				$this->the_msg = $this->messages(32);
				return false;
			}
		}
		else
		{
			$this->the_msg = 'The confirmation password does not match the password. Please try again.';
			return false;
		}
	}
	
	/*
	 * Simply check formatting of email address.
	 */
	function check_email($mail_address)
	{
		return preg_match("/^[0-9a-z]+(([\.\-_\+])[0-9a-z]+)*@[0-9a-z]+(([\.\-])[0-9a-z-]+)*\.[a-z]{2,4}$/i", $mail_address);
	}

	function ins_string($value) {
		if (preg_match("/^(.*)(##)(int|date|eu_date)$/", $value, $parts)) {
			$value = $parts[1];
			$type = $parts[3];
		} else {
			$type = "";
		}
		$value = (!get_magic_quotes_gpc()) ? addslashes($value) : $value;
		switch ($type) {
			case "int":
			$value = ($value != "") ? intval($value) : NULL;
			break;
			case "eu_date":
			$date_parts = preg_split ("/[\-\/\.]/", $value);
			$time = mktime(0, 0, 0, $date_parts[1], $date_parts[0], $date_parts[2]);
			$value = strftime("'%Y-%m-%d'", $time);
			break;
			case "date":
			$value = "'".preg_replace("/[\-\/\.]/", "-", $value)."'";
			break;
			default:
			$value = ($value != "") ? "'" . $value . "'" : "''";
		}
		return $value;
	}
	
	function getPassword()
	{
		return (strlen($this->password)>0) ? $this->password : false;
	}
	
	/*
	 * Check that the username complies with rules.
	 * Returns true or false. Also sets message in this->the_msg if
	 * rejected.
	 */
	function check_username($username)
	{
		$successful = true;
		if (strlen($username) < LOGIN_LENGTH) 
		{
			// Display name too short
			$this->the_msg = vsprintf(
								'Please specify a public display name of at least %1$s characters.',
								array(
									LOGIN_LENGTH
								)
							);
			 //$this->s->i->getTextVars('user_login_req', array(LOGIN_LENGTH));
			$successful = false;
		}
		else if (strlen($username) > LOGIN_MAX_LENGTH) 
		{
			// Display name too long
			$this->the_msg = vsprintf(
								'Please specify a public display name not longer than %1$s characters.',
								array(
									LOGIN_MAX_LENGTH
								)
							);
			$successful = false;
		}
		else if ((strpos($username, '[') !== false || strpos($username, ']') !== false) && strpos($username, '"') !== false)
		{
			// Reserved characters.
			$this->the_msg = 'Please remove the characters <em>[</em>, <em>]</em>, or <em>&quot;</em> from your public display name.';
			$successful = false;
		}
		else if (preg_match('/(?:\[\/?(?:b|u|i|h|colou?r|quote|code|img|url|email|list)\]|\[(?:code|quote|list)=)/i', $username))
		{
			// Contains BBCode.
			$this->the_msg = 'Please remove the formatting tag from your public display name.';
			$successful = false;
		}
		return $successful;
	}
	
	function check_registration_info($display_name, $password, $confirm_password, $email)
	{
		$successful = false;
		if (!$this->check_email($email)) 
		{
			$this->the_msg = vsprintf(
								'Please enter a valid email address. (If you have entered a valid email address, but it\'s not being accepted here, please email us at <a href="mailto:%1$s">%1$s</a> to let us know.)',
								array(
									$this->s->set->get('site.email')
								));
		}
		else if ($this->check_email_exists($email))
		{
			// Account with that email already exists.
			$this->the_msg = vsprintf('<p>We already have an account whose login is <em>%1$s</em>.</p>
									   <p>Please either continue below using a different email address for this newer order, or just <a href="%2$s">log in</a> to the existing account instead.</p>
									   <p>If you have mistakenly placed a double-order, email us at <a href="mailto:%3$s">%3$s</a> requesting one to be cancelled. Then you may <a href="%2$s">log in</a> to the original account.</p>', 
										array(
											$email, 
											$this->s->url->loginEmail($email),
											$this->s->set->get('site.email')
										));
		}
		else if ($this->check_new_password($password, $confirm_password))
		{
			// Passwords ok at this stage.
			if ($this->check_username($display_name))
			{	
				$successful = true;
			}
		}
		return $successful;
	}
	
	function register_user($display_name, $password, $confirm_password, $email, $access_level, $sale_id = -1) 
	{
		if ($this->check_registration_info($display_name, $password, $confirm_password, $email))
		{
			// Insert user into database
			$this->user = $display_name;
			$this->user_email = $email; // preparing for next check.
			$result = $this->insert_user($display_name, $password, $this->user_email, $access_level, true);
			if (intval($result) > 0)
			{
				// Successfully created the account.
				$this->id = intval($result);
				$this->password = md5($password);
				if ($sale_id > 0)
				{
					$this->s->sale->setAccountCreated($sale_id, $this->id);
				}
				// Log them in directly and show confirmation.
				$this->openSession();
				$successful = true;
			}
			else
			{
				$this->the_msg = vsprintf(
									'Ouch, our system messed up and wasn\'t able to create your account. Please email us at <a href="mailto:%1$s">%1$s</a>, describing how you came to see this error message.',
									array(
										$this->s->set->get('site.email')
									));
			}
		}
		return $successful;
	}
	
	// Email the user once a direct return comes in.
	function emailNewFreeUser()
	{
		$subject = 'This subject needs to be set';
		$this->s->assign('site_name', $this->s->set->get('site.name'));
		$this->s->assign('site_url', $this->s->set->get('site.url'));
		$this->s->assign('site_email', $this->s->set->get('site.email'));
		$this->s->assign('email', $this->user_email);
		$body = $this->s->fetch('email.welcome.tpl');
		$this->s->io->sendEmail($this->user_email, $subject, $body);
	}

	function adminAddUser($auth, $display_name, $password, $email) 
	{
		$successful = false;
		if (!$this->check_email($email)) 
		{
			$this->the_msg = "Email address not valid.";
		}
		else if ($this->check_email_exists($email))
		{
			// Account with that email already exists.
			$this->the_msg = "Email address already exists.";
		}
		else if ($this->check_new_password($password, $password))
		{
			// Passwords ok at this stage.
			if ($this->check_username($display_name))
			{
				// Insert user into database
				$this->user = $display_name;
				$this->user_email = $email; // preparing for next check.
				$result = $this->insert_user($display_name, $password, $this->user_email, $auth, true);
				if (intval($result) > 0)
				{
					// Successfully created the account.
					$this->id = intval($result);
					$this->password = md5($password);
					// Send activation email.
					$this->the_msg = "User was created. If it was a complimentary member, they have also been emailed login information and added to the members' mailing list.. <br /><br />Login: ".$this->user_email."<br />Password: ".$password;
					$successful = true;					
				}
				else
				{
					$this->the_msg = "Couldn't insert user into database.";
				}					
			}
		}
		return $successful;
	}

	function insert_user($display_name, $password, $email, $access_level, $active)
	{
		$q = array(
			'login' => '\''.$this->s->db->cleanse($display_name).'\'',
			'pw' => '\''.$this->s->db->cleanse(md5($password)).'\'',
			'email' => '\''.$this->s->db->cleanse($email).'\'',
			'created_time' => 'NOW()',
			'access_level' => $this->s->db->cleanse($access_level),
			'orig_access_level' => $this->s->db->cleanse($access_level),
			'active' => '\''.($active ? 'y' : 'n').'\'');
		return $this->s->db->Insert($this->table_name, $q); // Test for intval()>0
	}
	
	function resend_activation($resend_email)
	{
		if (!$this->check_email($resend_email))
		{
			// Address was not properly formatted
			$this->the_msg = $this->s->i->getTextForDisplay('user_email_notvalid');
		}
		else
		{
			if ($this->check_inactive_email($resend_email)) 
			{
				// User does exist, and user is inactive. Send the email again.
				$this->user_email = $resend_email;
				$resend_sql = sprintf("SELECT id, pw FROM %s WHERE email = %s", $this->table_name, $this->ins_string($this->user_email));
				if ($resend_result = mysql_query($resend_sql)) {
					$this->id = mysql_result($resend_result, 0, 'id');
					$this->password = mysql_result($resend_result, 0, 'pw');
					// Send activation email.
					if ($this->send_mail($this->user_email, 29, 28)) {
						$this->the_msg = $this->s->i->getTextVars('user_check_mail', array($this->s->i->getText('url_email_admin')));
					} else {
						$this->the_msg = $this->s->i->getTextForDisplay('user_error');
					}
				}
			}
			else if ($this->check_active_email($resend_email))
			{
				// User does exist but already has an active account.
				$this->the_msg = $this->s->i->getTextForDisplay('user_activate_already');

			}
			else
			{
				// The account does not exist.
				$this->the_msg = $this->s->i->getTextForDisplay('user_no_email_match');

			}
		}
	}
	
	function validate_email($validation_key, $key_id)
	{
		if ($validation_key != '' && strlen($validation_key) == 32 && intval($key_id) > 0)
		{
			$this->id = intval($key_id);
			if ($this->check_email_to_validate())
			{
				$str = 'UPDATE '.$this->table_name.' SET email = tmp_mail, tmp_mail = \'\' WHERE id = '.$this->s->db->cleanseSQL($key_id).' AND MD5(pw) = \''.$this->s->db->cleanseSQL($validation_key).'\''; 
				//$upd_sql = sprintf("UPDATE %s SET email = tmp_mail, tmp_mail = '' WHERE id = %d AND MD5(pw) = %s", $this->table_name, $key_id, $this->ins_string($validation_key));
				if ($this->s->db->Update($str))
				{
					$this->the_msg = $this->messages(18);
				}
				else
				{
					$this->the_msg = $this->messages(19);
				}
			}
			else
			{
				$this->the_msg = $this->messages(34);
			}
		}
		else
		{
			$this->the_msg = $this->s->i->getTextVars('user_invalid_key', array(FORGOT_PASSWORD_PAGE));
		}
	}
	
	// upd. version 1.97 only activate status active = 'n', update the database table:
	// ALTER TABLE `users` CHANGE `active` `active` ENUM( 'y', 'n', 'b' ) DEFAULT 'n' NOT NULL
	function activate_account($activate_key, $key_id)
	{
		$successful = false;
		if ($activate_key != "" && strlen($activate_key) == 32 && $key_id > 0)
		{
			if ($this->check_inactive_account($key_id))
			{
				// There is an inactive account that needs to be updated.
				$this->id = $key_id;
				if ($this->auto_activation)
				{
					$str = 'UPDATE '.$this->table_name.' SET active=\'y\' WHERE id='.$this->s->db->cleanseSQL($key_id).' AND MD5(pw)=\''.$this->s->db->cleanseSQL($activate_key).'\'';
					if ($this->s->db->Update($str))
					{
						if ($this->send_confirmation($key_id))
						{
							$successful = true;
							$this->the_msg = $this->messages(18);
							// Account activated!
							Users::recordAction($this->smarty, ACTION_ACCOUNT_ACTIVATED, $this->id, VALUE_UNKNOWN);
							Forum::createUser($this->smarty, $this->id);
							$this->s->rep->actionUserValidated($this->id); // Assign rep.
						}
						else
						{
							// Cannot activate account.
							$this->the_msg = $this->s->i->getTextForDisplay('user_error');
						}
					}
					else
					{
						$this->the_msg = $this->messages(19);
					}
				}
				else
				{
					if ($this->send_mail($this->admin_mail, 40, 39))
					{
						$this->the_msg = $this->messages(36);
					} else {
						$this->the_msg = $this->s->i->getTextForDisplay('user_error');
					}
				}
			}
			else if(!$this->check_account_exists($key_id))
			{
				// Account of this ID doesn't exist.
				$this->the_msg = $this->messages(20);
			}
		}
		else
		{
			$this->the_msg = $this->s->i->getTextVars('user_invalid_key', array(FORGOT_PASSWORD_PAGE));
		}
		return $successful;
	}
	
	function forgot_password($forgot_email)
	{
		if (!$this->check_email($forgot_email)) 
		{
			$this->the_msg = 'Oops, please check that the email address has a valid format.';
		}
		else
		{
			if (!$this->check_email_exists($forgot_email))
			{
				// Couldn't find a matching account.
				$this->the_msg = 'We don\'t have an account on record with that email address.';
			}
			else 
			{
				$this->user_email = $forgot_email;
				$forgot_sql = sprintf("SELECT id, pw FROM %s WHERE email = %s", $this->table_name, $this->ins_string($this->user_email));
				if ($forgot_result = mysql_query($forgot_sql)) {
					$this->id = mysql_result($forgot_result, 0, "id");
					$this->password = mysql_result($forgot_result, 0, "pw");
					
					// Send the email to reset the password.
					$subject = 'Password reset';
					$this->s->assign('site_name', $this->s->set->get('site.name'));
					$this->s->assign('url_password', $this->s->url->server().$this->password_page."?id=".$this->id."&activate=".md5($this->password));
					$body = $this->s->fetch('email.forgot-password.tpl');
					$this->s->io->sendEmail($this->s->io->post('email'), $subject, $body);
					
					$this->the_msg = 'Please check your e-mail to get your new password.';
				} else {
					$this->the_msg = 'We encountered an error in sending your password. Please contact us describing how you got to this point.';
				}
			}
		}
	}
	
	function check_activation_password($controle_str, $id)
	{
		$ret = false;
		if ($controle_str != "" && strlen($controle_str) == 32 && intval($id) > 0)
		{
			$this->password = $controle_str;
			$this->id = intval($id);
			if ($this->check_new_password_db())
			{
				// The information from the email link was correct.
				$ret = true;
			}
			else
			{
				$this->the_msg = 'Invalid activation key. Please contact us, describing how you reached this point.'; //$this->s->i->getTextVars('user_invalid_key', array(FORGOT_PASSWORD_PAGE));
			}
		}
		else
		{
			$this->the_msg = 'Invalid activation key. Please contact us, describing how you reached this point.'; //$this->s->i->getTextVars('user_invalid_key', array(FORGOT_PASSWORD_PAGE));
		}
		return $ret;
	}
	
	function set_user_id($user_id)
	{
		$ret = false;
		if (intval($user_id) > 0)
		{
			$this->id = intval($user_id);
			$ret = $this->id;
		}
		return $ret;
	}
	
	function activate_new_password($new_pass, $new_confirm, $activation, $user_id)
	{
		$ret = false;
		if ($this->check_new_password($new_pass, $new_confirm) && $this->set_user_id($user_id))
		{
			// Passwords match and conform to requirements.
			// Go ahead updating this account's password.
			$str = 'UPDATE '.$this->table_name.' SET pw = \''.$this->s->db->cleanseSQL(md5($new_pass)).'\' WHERE id = '.$this->s->db->cleanseSQL($user_id).' AND MD5(pw) = \''.$this->s->db->cleanseSQL($activation).'\' LIMIT 1';
			$this->s->db->Update($str);
			$this->email();
			
			$url_login = $this->login_page.'?email='.$this->s->io->formatForURL($this->user_email);
			$this->the_msg = vsprintf(
								'Your new password has been set! You can no <a href="%1$s">log in with your new password</a>.',
								array($url_login)
							 );
			
			$ret = true;
		}
		return $ret;
	}
	
	function send_confirmation($id)
	{
		$sql = sprintf("SELECT login, email FROM %s WHERE id = %d", $this->table_name, $id);
		$res = mysql_query($sql);
		$user_email = mysql_result($res, 0, 'email');
		$this->user = mysql_result($res, 0, 'login');
		//$this->user_full_name = mysql_result($res, 0, "real_name");
		//if ($this->user_full_name == "") $this->user_full_name = "User"; // change "User" to whatever you want, it's just a default name
		if ($this->send_mail($user_email, 37, 24, $this->send_copy)) 
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	function send_mail($mail_address, $msg = 29, $subj = 28, $send_admin = false)
	{
		require($_SERVER['DOCUMENT_ROOT'].'/library/phpmailer/class.phpmailer.php');
		try {
			$mail = new PHPMailer(true); //New instance, with exceptions enabled
			$body             = $msg;
			//$body             = preg_replace('/\\\\/','', $body); //Strip backslashes
			$mail->IsSMTP();                           // tell the class to use SMTP
			$mail->SMTPAuth   = true;                  // enable SMTP authentication
			$mail->Port       = $this->s->set->get('email.port');  // set the SMTP server port
			$mail->Host       = $this->s->set->get('email.host'); 	// SMTP server
			$mail->Username   = $this->s->set->get('email.username'); // SMTP server username
			$mail->Password   = $this->s->set->get('email.password'); // SMTP server password
			//$mail->IsSendmail();  // tell the class to use Sendmail
			$mail->AddReplyTo($this->s->set->get('site.email'),$this->s->set->get('site.name'));
			$mail->From       = $this->s->set->get('site.email');
			$mail->FromName   = $this->s->set->get('site.name');
			$to = $mail_address;
			$mail->AddAddress($to);
			$mail->Subject  = $subj;
			$mail->Body  = $body;
			//$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
			$mail->WordWrap   = 80; // set word wrap
			//$mail->MsgHTML($body);
			$mail->IsHTML(false); // send as HTML
			$mail->Send();
			return true;
		} catch (phpmailerException $e) {
			//echo $e->errorMessage();
			return false;
		}
		/*
		// Pre-SMTP
		$header = "From: \"".$this->webmaster_name."\" <".$this->webmaster_mail.">\r\n";
		if ($send_admin) $header .= "Bcc: ".ADMIN_MAIL."\r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: text/plain; charset=\"iso-8859-1\"\r\n";
		$header .= "Content-Transfer-Encoding: 7bit\r\n";
		$subject = $this->messages($subj);
		$body = $this->messages($msg);
		if (mail($mail_address, $subject, $body, $header)) {
			
		} else {
			return false;
		}
		*/
	}
	
	function messages($num) {
		$host = $this->s->url->server(); //$host = "http://".$_SERVER['HTTP_HOST'];
		//$this->s->i->setLang("en"); //for debugging
		//echo $num;
		//$msg[18] = "Your account has been activated! Please login below.";
		//$msg[19] = 'Sorry, cannot activate your account.';
		$msg[20] = 'We don\'t seem to have a matching account for the email you provided...';
		$msg[23] = 'Please check your e-mail to get your new password.';
		$msg[25] = 'Sorry, cannot activate your password.';
		$msg[32] = vsprintf(
						'Please enter a password (minimum %s characters).',
						array(PW_LENGTH)
					);
		//$msg[38] = $this->s->i->getTextForDisplay("user_pass_not_match");
		$msg[34] = 'There is no e-mail address for validation.';

		// For email display - do not format for HTML characters
		//$msg[24] = 'Your account is now active';
		$msg[26] = 'Your forgotten password...';
		//$msg[28] = 'Activate your account';
		//$msg[39] = 'A new user has registered';
		//$msg[41] = $this->s->i->getText("user_mailsubj_validate_mail"); // subject in e-mail
		/*
		$msg[36] = 'Your request has been processed and is pending validation by the admin.

You will get an e-mail when it has been validated. You will not be able to log in until then.';
		*/

		$rootLink = $this->s->url->server();

		//$activationLink = $host.$this->login_page.'?ident='.$this->id.'&activate='.md5($this->password).'&email='.$this->s->io->formatForURL($this->user_email); //."&language=".$this->language;
		//$msg[29] = $this->s->i->getTextVars('user_mail_activate', array($activationLink, $this->admin_name, $rootLink));
		/*
		 * Hello,

			to activate your account please click the following link:
			<%1$s>

			Kind regards,
			%2$s
			%3$s
		*/

		//$validationLink = $host.$this->login_page."?id=".$this->id."&validate=".md5($this->password); //."&language=".$this->language;
		//$msg[33] = $this->s->i->getTextVars("user_mail_activate_address", array($validationLink, $this->admin_name, $rootLink));

		$emailLink = $host.$this->password_page."?id=".$this->id."&activate=".md5($this->password); //."&language=".$this->language;
		$msg[35] =  vsprintf( 'Hello,

Follow this link to choose a new password:
<%1$s>

Kind regards,
%2$s
%3$s',
						array($emailLink, $this->admin_name, $rootLink)
					);
		
		$msg[37] = vsprintf('Hello %1$s,

Your account has now been activated and you can now log in:
<%2$s>',
						array($this->user, $host.$this->login_page)
					);
				
		//$adminLink = $host.$this->admin_page."?login_id=".$this->id;
		//$msg[40] = $this->s->i->getTextVars("user_mail_admin_newuser", array(date("Y-m-d"), $host.$this->admin_page, $adminLink));
		/* There was a new user registration on %1$s:

Access the admin page:
<%2$s>*/

		return $msg[$num];
	}

	// Set permissions here... (not very good way to do this)
	function is_auth_edit_users()
	{
		$arrAuth = array(AUTH_ADMIN);
		return in_array($this->get_access_level(), $arrAuth);
	}
	function is_auth_add()
	{
		// Allow to add headwords/word entries/translations.
		return $this->isLoggedIn();
	}
	/** Alias function **/
	function isAuthEdit()
	{
		return $this->is_auth_edit();
	}
	function is_auth_edit()
	{
		// Allow to edit word entries.
		$arrAuth = array(AUTH_ADMIN, AUTH_EDITOR);
		return in_array($this->get_access_level(), $arrAuth);
	}
	/** Alias function **/
	function isAuthAdmin()
	{
		return $this->is_auth_admin();
	}
	function is_auth_admin()
	{
		$arrAuth = array(AUTH_ADMIN);
		return in_array($this->get_access_level(), $arrAuth);
	}	
	function getNbActivatedAccounts()
	{
		// Used for statistics. Written for display on the home page.
		$sql_get_user = sprintf('SELECT COUNT(*) FROM %s WHERE active = \'y\'', $this->table_name);
		$get_user = mysql_query($sql_get_user);
		$row = mysql_fetch_row($get_user);
		return $row[0];
	}
	function neverEmail()
	{
		/*
		 * Return true if the user has chosen never to received
		 * email notifications.
		 */
		$ret = false;
		$q = 'SELECT never_email
				FROM user
				WHERE id='.$this->s->db->cleanse($this->getUserID()).'
				AND never_email IS NOT NULL';
		if ($r = $this->s->db->Select($q))
		{
			$ret = true; // They have chosen not to be emailed.
		}
		return $ret;
	}
}
