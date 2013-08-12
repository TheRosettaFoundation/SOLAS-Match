<?php

require_once __DIR__."/../../Common/models/Tag.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";

class TagsDao
{        
    public static function getTag($id=null, $label=null, $limit=30)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($id)
                .",".PDOWrapper::cleanseNullOrWrapStr($label)
                .",".PDOWrapper::cleanseNull($limit);
        
        if($result = PDOWrapper::call("getTag", $args)) {
            $ret = array();
            foreach ($result as $tag) {
                $ret[] = ModelFactory::buildModel("Tag", $tag);
            }
        }        
        return $ret;
    }

    public static function create($label)
    {
        $tag = new Tag();
        $tag->setLabel($label);
        return self::save($tag);
    }

    public static function save($tag)
    {
        if (!$tag->hasId()) {
            return self::insert($tag);
        } else {
            echo "Error: updating existing tag functionality not implemented."; die;
        }
    }

    private static function insert($tag)
    {
        $args = PDOWrapper::cleanseWrapStr($tag->getLabel());
        
        $result = PDOWrapper::call("tagInsert", $args);
        if($result) {
            return ModelFactory::buildModel("Tag", $result[0]);
        } else {
            return null;
        }
    }
    
    public static function getTopTags($limit = 30)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($limit);
        
        if ($result = PDOWrapper::call("getTopTags", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Tag", $row);
            }
        }
        return $ret;           
     }

    public static function delete($id)
    {
        $args = PDOWrapper::cleanseNull($id);
        if ($result = PDOWrapper::call("deleteTag", $args)) {
            return $result[0]['result'];
        }
        return null;
    }  

    public static function searchForTag($name)
    {
        $ret = array();
        $args = PDOWrapper::cleanseNullOrWrapStr($name);
        if ($result = PDOWrapper::call("searchForTag", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Tag", $row);
            }
        }
        return $ret;
    }
    
    public static function updateTags($projectId, $updatedProjectTagList)
    {        
        if($updatedProjectTagList) {
            if(is_null($oldProjectTagList = ProjectDao::getTags($projectId))) $oldProjectTagList = array();
            
            $tagsToRemove = array_udiff($oldProjectTagList, $updatedProjectTagList, 'TagsDao::compareTo');

            if(!empty($tagsToRemove)) {                    
                foreach($tagsToRemove as $removedTag) {
                    ProjectDao::removeProjectTag($projectId, $removedTag->getId()); 
                }
            } 

            $tagsToAdd = array_udiff($updatedProjectTagList, $oldProjectTagList, 'TagsDao::compareTo');

            if(!empty($tagsToAdd)) {                    
                foreach($tagsToAdd as $newTag) {
                    if($tagExists = TagsDao::getTag(null, $newTag->getLabel())) {
                        ProjectDao::addProjectTag($projectId, $tagExists[0]->getId());
                    } else {
                        $tag = TagsDao::create($newTag->getLabel());
                        ProjectDao::addProjectTag($projectId, $tag->getId());
                    } 
                }
            }
        }
    }

    private static function compareTo($tag1, $tag2)
    {
        return strcasecmp($tag1->getLabel(), $tag2->getLabel());
    }
}
