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
    public static function getStream($nb_items = 10)
    {
        // Simple stream, just get latest global jobs.
        $task_dao = new TaskDao();
        return $task_dao->getLatestAvailableTasks($nb_items);
    }

    /*
        Same as above except take the user's preferences into account
    */
    public static function getUserStream($user_id, $nb_items = 0)
    {
        $task_dao = new TaskDao();
        return $task_dao->getUserTopTasks($user_id, $nb_items);
    }
    
    /*
     * Return the list of (open) tasks that are tagged with a specific tag.
     */
    public static function getTaggedStream($tag, $nb_items)
    {
        $task_dao = new TaskDao();
        return $task_dao->getTaggedTasks($tag, $nb_items);
    }
}
