<?php

require_once __DIR__.'/../../Common/lib/PDOWrapper.class.php';

class ProjectTags {
    
    public static function getTags($project_id)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getProjectTags", PDOWrapper::cleanseNull($project_id))) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Tag", $row);
            }
        }
        return $ret;
    }

    public static function removeProjectTag($projectId, $tagId)
    {
        $args = PDOWrapper::cleanseNull($projectId).", ";
        $args .= PDOWrapper::cleanseNull($tagId);
        PDOWrapper::call("removeProjectTag", $args);
    }

    public static function removeAllProjectTags($projectId)
    {
        if($tags = self::getTags($projectId)) {
            foreach ($tags as $tag) {
                self::removeProjectTag($projectId, $tag->getId());
            }
        }
    }

    public static function addProjectTags($projectId, $tagIds)
    {
        foreach ($tagIds as $tagId) {
            self::addProjectTag($projectId, $tagId);
        }
    }

    public static function addProjectTag($projectId, $tagId)
    {
        $args = PDOWrapper::cleanseNull($projectId).", ";
        $args .= PDOWrapper::cleanseNull($tagId);
        PDOWrapper::call("addProjectTag", $args);
    }
}
