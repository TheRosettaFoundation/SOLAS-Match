<?php
require($_SERVER['DOCUMENT_ROOT'].'/../library/MySQLHandler.class.php');
require($_SERVER['DOCUMENT_ROOT'].'/../library/Stream.class.php');
require($_SERVER['DOCUMENT_ROOT'].'/../library/Task.class.php');

class RosettaSmarty extends Smarty {
	var $set;
	var $db;
	var $sream;
	
	function initRosettaSmarty()
	{
		$this->set = new Settings();
		$this->stream = new Stream($this);
		$this->db = new MySQLHandler();
		$this->db->init();
	}
	
	/*
	 * Extends Smarty's display() method.
	 * Custom function to get the text of all required interface texts.
	 */
	public function display($template, $cache_id = null, $compile_id = null, $parent = null)
	{
		if (isset($this->db) && $this->db->ANALYSE_QUERIES)
		{
			echo '.'.$this->db->RECORDED_EXPLAINS.'.';
		}
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
