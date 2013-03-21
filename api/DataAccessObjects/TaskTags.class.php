<?php

require_once __DIR__.'/../../Common/lib/PDOWrapper.class.php';

class TaskTags {
    
    public static function getTags($task_id)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getTaskTags", PDOWrapper::cleanseNull($task_id))) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Tag", $row);
            }
        }
        return $ret;
    }

    public static function deleteTaskTags($task)
    {
        PDOWrapper::call("unlinkStoredTags", PDOWrapper::cleanse($task->getId()));
    }

    public static function setTaskTags($task, $tag_ids)
    {
        foreach ($tag_ids as $tag_id) {
            self::setTaskTag($task->getId(), $tag_id);
        }
    }

    public static function setTaskTag($task_id, $tag_id)
    {
        PDOWrapper::call("storeTagLinks", PDOWrapper::cleanse($task_id).",".PDOWrapper::cleanse($tag_id));
    }
}
