<?php
require 'Task.class.php';

/*
	Save to the database a new task. $tags is an array or tag_ids.
*/	
class Tasks
{
	
	function __construct() {
	}

	function create($title, $organisation_id, $tags, $source_id, $target_id, $word_count)
	{
		$db = new MySQLWrapper();
		$db->init();
		$ret = false;
		$task = array();
		$task['title'] = '\''.$db->cleanse($title).'\'';
		if ($source_id)
		{
			$task['source_id'] = '\''.$db->cleanse($source_id).'\'';	
		}
		if ($target_id)
		{
			$task['target_id'] = '\''.$db->cleanse($target_id).'\'';	
		}
		$task['organisation_id'] = intval($organisation_id);
		if ($word_count)
		{
			$task['word_count'] = intval($word_count);	
		}
		$task['created_time'] = 'NOW()';
		if ($task_id = $db->Insert('task', $task))
		{
			$ret = $task_id;
			// The task has been created. Now save what it was tagged with.
			$tags = new Tags();
			if ($tag_ids = $tags->parse($tags))
			{
				// We now have an array of tag_ids related to this task. Save this information to the datbase.
				foreach ($tag_ids as $tag_id)
				{
					$task_tag = array();
					$task_tag['task_id'] = intval($task_id);
					$task_tag['tag_id'] = intval($tag_id);
					$task_tag['created_time'] = 'NOW()';
					$db->Insert('task_tag', $task_tag);
				}
			// todo, now test if it's working!
			}
		}
		return $ret;
	}

	public function getLatestTasks($nb_items = 10)
	{
		$db = new MySQLWrapper();
		$db->init();
		$ret = false;
		$q = 'SELECT id
				FROM task
				ORDER BY created_time DESC 
				LIMIT '.$db->cleanse($nb_items);
		if ($r = $db->Select($q))
		{
			$ret = array();
			foreach($r as $row)
			{
				// Add a new Job object to the array to be returned.
				$ret[] = new Task($row['id']);
			}
		}
		return $ret;
	}
	
	/*
	 * Return an array of tasks that are tagged with a certain tag.
	 */
	public function getTaggedTasks($tag_id, $nb_items = 10)
	{
		$db = new MySQLWrapper();
		$db->init();
		$ret = false;
		$q = 'SELECT id
				FROM task
				WHERE id IN (
					SELECT task_id
					FROM task_tag
					WHERE tag_id = '.intval($tag_id).'
				) 
				ORDER BY created_time DESC 
				LIMIT '.$db->cleanse($nb_items);
		if ($r = $db->Select($q))
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
