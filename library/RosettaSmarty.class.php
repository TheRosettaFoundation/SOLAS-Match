<?php
require($_SERVER['DOCUMENT_ROOT'].'/../library/MySQLWrapper.class.php');
require($_SERVER['DOCUMENT_ROOT'].'/../library/IO.class.php');
require($_SERVER['DOCUMENT_ROOT'].'/../library/Organisations.class.php');
require($_SERVER['DOCUMENT_ROOT'].'/../library/Stream.class.php');
require($_SERVER['DOCUMENT_ROOT'].'/../library/Tags.class.php');
require($_SERVER['DOCUMENT_ROOT'].'/../library/Task.class.php');
require($_SERVER['DOCUMENT_ROOT'].'/../library/TaskFile.class.php');
require($_SERVER['DOCUMENT_ROOT'].'/../library/Tasks.class.php');
require($_SERVER['DOCUMENT_ROOT'].'/../library/URL.class.php');
require($_SERVER['DOCUMENT_ROOT'].'/../library/User.class.php');

class RosettaSmarty extends Smarty {
	var $set;
	var $db;
	var $io;
	var $orgs;
	var $stream;
	var $tags;
	var $tasks;
	var $url;
	var $user;
	
	function initRosettaSmarty()
	{
		$this->set = new Settings();
		$this->stream = new Stream($this);
		$this->db = new MySQLWrapper();
		$this->db->init();
		$this->io = new IO($this);
		$this->orgs = new Organisations($this);
		$this->tags = new Tags($this);
		$this->tasks = new Tasks($this);
		$this->url = new URL($this);
		
		// Start session management allowing for logging in.
		if (!isset($_SESSION))
		{
			session_start();
		}
		
		if (User::isLoggedIn())
		{
			$user_id = User::sessionUserID(); 
			$this->user = new User($this, $user_id);
		}
	}
	
	/*
	 * Extends Smarty's display() method.
	 * Custom function to get the text of all required interface texts.
	 */
	public function display($template, $cache_id = null, $compile_id = null, $parent = null)
	{
		parent::display($template, $cache_id, $compile_id, $parent);
	}
	
	function setting($set)
	{
		/*
		 * Return setting value. Old way was $s->set->get('setting')
		 * but this function allows simpler $s->setting('setting');
		 */
		return $this->set->get($set);
	}
	
	function isLoggedIn()
	{
		return User::isLoggedIn();
	}
}
