<?php

class Tasks
{
	/*
		Save to the database a new task. $tags is an array or tag_ids.
	*/
	var $s;
	
	function Tasks(&$smarty)
	{
		$this->s = &$smarty;
	}

	function create($title, $organisation_id, $tags, $source_id, $target_id, $word_count)
	{
		$ret = false;
		$task = array();
		$task['title'] = '\''.$this->s->db->cleanse($title).'\'';
		if ($source)
		{
			$task['source_id'] = '\''.$this->s->db->cleanse($source_id).'\'';	
		}
		if ($target)
		{
			$task['target_id'] = '\''.$this->s->db->cleanse($target_id).'\'';	
		}
		$task['organisation_id'] = intval($organisation_id);
		if ($word_count)
		{
			$task['word_count'] = intval($word_count);	
		}
		$task['created_time'] = 'NOW()';
		if ($task_id = $this->s->db->Insert('task', $task))
		{
			$ret = $task_id;
			// The task has been created. Now save what it was tagged with.
			if ($tag_ids = $this->s->tags->parse($tags))
			{
				// We now have an array of tag_ids related to this task. Save this information to the datbase.
				foreach ($tag_ids as $tag_id)
				{
					$task_tag = array();
					$task_tag['task_id'] = intval($task_id);
					$task_tag['tag_id'] = intval($tag_id);
					$task_tag['created_time'] = 'NOW()';
					$this->s->db->Insert('task_tag', $task_tag);
				}
			// todo, now test if it's working!
			}
		}
		return $ret;
	}

	public function getLatestTasks($nb_items = 10)
	{
		$ret = false;
		$q = 'SELECT id
				FROM task
				ORDER BY created_time DESC 
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
	
	/*
	 * Return an array of tasks that are tagged with a certain tag.
	 */
	public function getTaggedTasks($tag_id, $nb_items = 10)
	{
		$ret = false;
		$q = 'SELECT id
				FROM task
				WHERE id IN (
					SELECT task_id
					FROM task_tag
					WHERE tag_id = '.intval($tag_id).'
				) 
				ORDER BY created_time DESC 
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
	
	/*
	 * Get an array of all source languages of tasks in the database.
	 * TODO: needs to move to Tags class
	 */
	public function allSourceLangauges_OLD()
	{
		$ret = false;
		$q = 'SELECT DISTINCT(source)
				FROM task';
		if ($r = $this->s->db->Select($q))
		{
			$langs = array();
			foreach ($r as $row)
			{
				$langs[] = $row[0];
			}
			$ret = $langs;
		}
		return $ret;	
	}

	/*
	 * Get an array of all target languages of tasks in the database.
	 * TODO: needs to move to Tags class
	 */
	public function allTargetLangauges_OLD()
	{
		$ret = false;
		$q = 'SELECT DISTINCT(target)
				FROM task';
		if ($r = $this->s->db->Select($q))
		{
			$langs = array();
			foreach ($r as $row)
			{
				$langs[] = $row[0];
			}
			$ret = $langs;
		}
		return $ret;	
	}
}
