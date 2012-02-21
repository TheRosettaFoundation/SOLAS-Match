<?php

class TaskStream
{	
	/*
		Get a list of tasks to display to the user.
		
		Returns: array of Job objects, or false if none available.
		
		This list could be highly customized depending on who is calling
		it. It's where the intelligence of the system will be required
		to decide what is shown to whom.
	*/
	public static function getStream($nb_items = 10) {
		// Simple stream, just get latest global jobs.
		return self::getGlobalStream($nb_items);
	}
	
	public static function getGlobalStream($nb_items = 10)	{
		$task_dao = new TaskDao();
		return $task_dao->getLatestTasks($nb_items);
	}
	
	/*
	 * Return the list of (open) tasks that are tagged with a specific tag.
	 */
	public static function getTaggedStream($tag, $nb_items)	{
		$task_dao = new TaskDao();
		return $task_dao->getTaggedTasks($tag, $nb_items);
	}
}
