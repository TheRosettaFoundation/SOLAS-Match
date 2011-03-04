<?php

/**
 * Input/Output including parsing input, and formatting output for URLs.
 */
class IO {
	var $s;
	
	function IO(&$smarty) {
		$this->s = &$smarty;
	}

	function get($get_var_name)
	{
		return isset($_GET[$get_var_name]) ? $this->cleanseInput($_GET[$get_var_name]) : false;
	}

	function post($post_var_name)
	{
		return isset($_POST[$post_var_name]) ? $this->cleanseInput($_POST[$post_var_name]) : false;
	}

	// Cleanse input, but keep HTML tags.
	function postHTML($post_var_name)
	{
		return isset($_POST[$post_var_name]) ? $this->cleanseInputKeepHTML($_POST[$post_var_name]) : false;
	}
	
	// Cleanse input: make safe from SQL injection.
	function cleanseInput($str)
	{
		$str = $this->cleanseInputKeepHTML($str);
		//mysql_real_escape_string
		return strip_tags(trim($str));
	}

	// Allow to keep HTML tags.
	function cleanseInputKeepHTML($str)
	{
		if (get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return $str;
	}

	function formatForURL($text) {
		// Encodes the given text to be included as part of a URL
		return rawurlencode($text);
	}

	function formatFromURL($text) {
		// Decodes the given text from URL representation
		return rawurldecode($text);
	}
	
	/*
	function sendEmail($recipient, $subject, $body)
	{
		require($_SERVER['DOCUMENT_ROOT'].'/library/phpmailer/class.phpmailer.php');
		try
		{
			$mail = new PHPMailer(true); //New instance, with exceptions enabled
			$body             = preg_replace('/\\\\/','', $body); //Strip backslashes
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
			$mail->AddAddress($recipient);
			$mail->Subject    = $subject;
			$mail->Body  	  = $body;
			$mail->WordWrap   = 80; // set word wrap
			$mail->IsHTML(false); // send as HTML
			$mail->Send();
			return true;
		}
		catch (phpmailerException $e)
		{
			//echo $e->errorMessage();
			return false;
		}
	}
	*/
	
}
