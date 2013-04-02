<?php

require_once __DIR__."/../../Common/models/Tag.php";
require_once __DIR__."/../../Common/lib/PDOWrapper.class.php";

class TagsDao
{
    
    public static function find($params)
    {
        $result = self::getTag($params);
        return $result[0];
    }
        
    public static function getTag($params)
    {
        $args = "";
        $args .= ((isset($params['id']))) ? PDOWrapper::cleanseNull($params['id']) : "null";
        $args .= (isset($params['label'])) ? ",".PDOWrapper::cleanseNullOrWrapStr($params['label']) : ",null";
        $ret = null;
        $result = PDOWrapper::call("getTag", $args);
        if ($result != null) {
            $ret = array();
            foreach ($result as $r) {
                $tag_data = array();
                $tag_data['id'] = $r['id'];
                $tag_data['label'] = $r['label'];
                $ret[] = ModelFactory::buildModel("Tag", $tag_data);
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
    
    /*
        Take a string typed in by the user, try to break it up and create tags 
        from it.
        Returns an array of matching tag_ids.
        Basic format is that items are comma-delimited.

        Also responsible to special Langauge tags for content.
    */
    public static function parse($str) // Needs to be package protected
    {
        $tag_ids = false;
        $str = $this->s->io->cleanseInput($str);
        if ($tags = self::tagsToArray($str)) {
            $tag_ids = array();
            foreach ($tags as $tag) {
                // Ask the database what the ID is by searching for the tag's text label
                if ($tag_id = self::tagIDFromLabel($tag)) {
                        $tag_ids[] = $tag_id;
                } else {
                    if ($tag_id = self::insertTag($tag)) {
                        $tag_ids[] = $tag_id;
                    }
                }
            }
        }
        
        return $tag_ids;		
    }

    public static function tagIDFromLabel($label)
    {
        $result = self::getTag(array('label' => $label));
        return $result == null ? null : $result[0]->getId();
    }

    public static function label($tag_id)
    {
        $result = self::getTag(array('id'=>$tag_id));
        return $result[0]['label'];
    }
    
    /*
     * For a given tag, return its URL.
     */
    public static function url($tag_id) // Needs to be package protected
    {
        return '/tag/'.intval($tag_id).'/';		
    }

    public static function tagTargetHTML($str) // Needs to be package protected
    {
        $label = $str;
        return '<div class="tag target"><span class="label">To '.$label.'</span></div>';
    }

    public static function createAnyNewTags($labels)
    {
        $ret =  null;
        if (is_array($labels)) {
            $tags = array();
            foreach ($labels as $label) {
                if ($tag = self::find(array('label' => $label))) {
                    $tags[] = $tag;
                } else {
                    $tags[] = self::create($label);
                }
            }
            $ret = $tags;
        }
        
        return $ret;
    }

    public static function getAllTags()
    {
        $ret = false;
        foreach (self::getTag(null) as $row) {
            $ret[] = $row->getLabel();
        }
        return $ret;
    }
    
    public static function getTopTags($limit = 30)
    {
        $ret = null;
        if ($r = PDOWrapper::call("getTopTags", PDOWrapper::cleanse($limit))) {
            $ret = array();
            foreach ($r as $row) {
                $ret[] = ModelFactory::buildModel("Tag", $row);
            }
        }
        return $ret;           
     }

    public static function delete($id)
    {
        if ($r = PDOWrapper::call("deleteTag", PDOWrapper::cleanse($id))) {
            return $r[0]['result'];
        }
        return 0;
    }  
    
    public static function updateTags($project)
    {
        ProjectDao::removeAllProjectTags($project->getId());
        $tags = $project->getTagList();
        if (count($tags) > 0) {
            if ($tag_ids = self::tagsToIds($tags)) {
                ProjectDao::addProjectTags($project->getId(), $tag_ids);
                return 1;
            }
            return 0;
        }
        return 0;
    }   
    
    private static function tagsToIds($tags)
    {
        $tag_ids = array();
        foreach ($tags as $tag) {
            if ($tag_id = self::tagIDFromLabel($tag)) {
                $tag_ids[] = $tag_id;
            } else {
                $createdTag = self::create($tag);
                $tag_ids[] = $createdTag->getId();
            }
        }
        
        if (count($tag_ids) > 0) {
            return $tag_ids;
        } else {
            return null;
        }
    }
    
}
