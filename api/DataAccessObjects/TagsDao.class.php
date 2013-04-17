<?php

require_once __DIR__."/../../Common/models/Tag.php";
require_once __DIR__."/../../Common/lib/PDOWrapper.class.php";

class TagsDao
{        
    public static function getTag($id=null, $label=null, $limit=30)
    {
        $ret = null;
        
        if($result = PDOWrapper::call("getTag", PDOWrapper::cleanseNull($id).",".PDOWrapper::cleanseNullOrWrapStr($label).",".
                                       PDOWrapper::cleanseNull($limit))) {
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
        $result = PDOWrapper::call("tagInsert", PDOWrapper::cleanseWrapStr($tag->getLabel()));
        if($result) {
            return ModelFactory::buildModel("Tag", $result[0]);
        } else {
            return null;
        }
    }
    
    public static function getTopTags($limit = 30)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getTopTags", PDOWrapper::cleanse($limit))) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Tag", $row);
            }
        }
        return $ret;           
     }

    public static function delete($id)
    {
        if ($result = PDOWrapper::call("deleteTag", PDOWrapper::cleanse($id))) {
            return $result[0]['result'];
        }
        return null;
    }  
    
    public static function updateTags($projectId, $projectTagList)
    {
        ProjectDao::removeAllProjectTags($projectId);
        if (count($projectTagList) > 0) {    
            $projectTags = array();
            foreach($projectTagList as $tag) {                
                if($tagExists = self::getTag(null, $tag->getLabel())) {
                    $projectTags[] = $tagExists[0];
                } else {
                    $newTag = self::create($tag->getLabel());
                    $projectTags[] = $newTag;
                }
            }
    
            ProjectDao::addProjectTags($projectId, $projectTags);
            return $projectTags;
        }
    }
    
//    {
//        ProjectDao::removeAllProjectTags($project->getId());
//        $tags = $project->getTagList();
//        if (count($tags) > 0) {            
//            $tagIds = array();
//            foreach($tags as $tagLabel) {
//                $tag = self::create($tagLabel); 
//                $tagIds[] = $tag->getId();
//            }
//            ProjectDao::addProjectTags($project->getId(), $tagIds);
//        }
//    }
}
