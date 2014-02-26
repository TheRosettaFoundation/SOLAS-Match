<?php

namespace SolasMatch\API\DAO;

use \SolasMatch\API\Lib as Lib;

require_once __DIR__."/../../Common/models/Tag.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";

class TagsDao
{
    public static function getTag($id = null, $label = null, $limit = 30)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($id).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($label).",".
            Lib\PDOWrapper::cleanseNull($limit);
        if ($result = Lib\PDOWrapper::call("getTag", $args)) {
            $ret = array();
            foreach ($result as $tag) {
                $ret[] = \ModelFactory::buildModel("Tag", $tag);
            }
        }
        return $ret;
    }

    public static function create($label)
    {
        $tag = new \Tag();
        $tag->setLabel($label);
        return self::save($tag);
    }

    public static function save($tag)
    {
        if (!$tag->hasId()) {
            return self::insert($tag);
        } else {
            error_log("Error: updating existing tag functionality not implemented.");
            die;
        }
    }

    private static function insert($tag)
    {
        $args = Lib\PDOWrapper::cleanseWrapStr($tag->getLabel());
        $result = Lib\PDOWrapper::call("tagInsert", $args);
        if ($result) {
            return \ModelFactory::buildModel("Tag", $result[0]);
        } else {
            return null;
        }
    }
    
    public static function getTopTags($limit = 30)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($limit);
        if ($result = Lib\PDOWrapper::call("getTopTags", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = \ModelFactory::buildModel("Tag", $row);
            }
        }
        return $ret;
    }

    public static function delete($id)
    {
        $args = Lib\PDOWrapper::cleanseNull($id);
        if ($result = Lib\PDOWrapper::call("deleteTag", $args)) {
            return $result[0]['result'];
        }
        return null;
    }

    public static function searchForTag($name)
    {
        $ret = array();
        $args = Lib\PDOWrapper::cleanseNullOrWrapStr($name);
        if ($result = Lib\PDOWrapper::call("searchForTag", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = \ModelFactory::buildModel("Tag", $row);
            }
        }
        return $ret;
    }
    
    public static function updateTags($projectId, $updatedProjectTagList)
    {
        if ($updatedProjectTagList) {
            if (is_null($oldProjectTagList = \ProjectDao::getTags($projectId))) {
                $oldProjectTagList = array();
            }
            
            $tagsToRemove = array_udiff($oldProjectTagList, $updatedProjectTagList, 'TagsDao::compareTo');

            if (!empty($tagsToRemove)) {
                foreach ($tagsToRemove as $removedTag) {
                    \ProjectDao::removeProjectTag($projectId, $removedTag->getId());
                }
            }

            $tagsToAdd = array_udiff($updatedProjectTagList, $oldProjectTagList, 'TagsDao::compareTo');

            if (!empty($tagsToAdd)) {
                foreach ($tagsToAdd as $newTag) {
                    if ($tagExists = TagsDao::getTag(null, $newTag->getLabel())) {
                        \ProjectDao::addProjectTag($projectId, $tagExists[0]->getId());
                    } else {
                        $tag = TagsDao::create($newTag->getLabel());
                        \ProjectDao::addProjectTag($projectId, $tag->getId());
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
