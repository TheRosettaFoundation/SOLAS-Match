<?php
require('MySQLWrapper.class.php');
require('IO.class.php');
require('Organisations.class.php');
require('Stream.class.php');
require('Tags.class.php');
require('Task.class.php');
require('TaskFile.class.php');
require('Tasks.class.php');
require('URL.class.php');
require('Users.class.php');

class RosettaSmarty extends Smarty {
	var $set;
	var $db;
	var $io;
	var $orgs;
	var $stream;
	var $tags;
	var $tasks;
	var $url;
	var $users;
	
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
		$this->users = new Users($this);
		
		// Start session management allowing for logging in.
		if (!isset($_SESSION))
		{
			session_start();
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
}
