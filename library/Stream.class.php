<?php

class Stream
{
	var $s;
	function Stream(&$smarty)
	{
		$this->s = &$smarty; 
	}
	
	/*
		Get a list of tasks to display to the user.
		
		Returns: array of Job objects, or false if none available.
		
		This list could be highly customized depending on who is calling
		it. It's where the intelligence of the system will be required
		to decide what is shown to whom.
	*/
	public function getStream($nb_items = 10)
	{
		// Simple stream, just get latest global jobs.
		return $this->getGlobalStream($nb_items);
	}
	
	public function getGlobalStream($nb_items = 10)
	{
		$ret = false;
		$q = 'SELECT id
				FROM task
				ORDER BY time_created DESC 
				LIMIT '.$this->s->db->cleanse($nb_items);
		if ($r = $this->s->db->Select($q))
		{
			$ret = array();
			foreach($r as $row)
			{
				// Add a new Job object to the array to be returned.
				$ret[] = new Task($this->s, $row['id']);
			}
		}
		return $ret;
	}
}
