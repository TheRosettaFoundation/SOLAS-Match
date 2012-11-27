<?php
require('../Common/models/Tag.class.php');
require_once ('../Common/lib/PDOWrapper.class.php');

class TagsDao {
	public function find($params) {
            $result = self::getTag($params);
            return $result[0];
	}
        
    public function getTag($params){
        $args = "";
		$db = new PDOWrapper();
		$db->init();
        $args.=((isset($params['tag_id'])))?"{$db->cleanseNull($params['tag_id'])}":"null";
        $args.=(isset($params['label']))?",{$db->cleanseNullOrWrapStr($params['label'])}":",null";
        $ret = array();
        $result=$db->call("getTag", $args);
        if($result !=null){
            foreach ( $result as $r) {
                $tag_data = array();
                $tag_data['tag_id'] = $r['tag_id'];
                $tag_data['label'] = $r['label'];
                $ret []= new Tag($tag_data);
            }
        }
		return $ret;
    }

    public function create($label) {
		$tag = new Tag(array('label' => $label));
		return $this->save($tag);
	}

	public function save($tag) {
		if (!$tag->hasTagId()) {
			return $this->_insert($tag);
		}
		else {
			echo "Error: updating existing tag functionality not implemented."; die;
		}
	}

	private function _insert($tag)
	{
		$db = new PDOWrapper();
		$db->init();
                $id =$db->call("tagInsert", $db->cleanseWrapStr($tag->getLabel()));
                return $id[0]['tag_id'];
	}
	
	/*
		Take a string typed in by the user, try to break it up and create tags 
		from it.
		Returns an array of matching tag_ids.
		Basic format is that items are comma-delimited.
		
		Also responsible to special Langauge tags for content.
	*/
	function parse($str) {
		$tag_ids = false;
		$str = $this->s->io->cleanseInput($str);
		if ($tags = $this->tagsToArray($str)) {
			$tag_ids = array();
			foreach($tags as $tag) {
				// Ask the database what the ID is by searching for the tag's text label
				if ($tag_id = $this->tagIDFromLabel($tag)) {
					$tag_ids[] = $tag_id;
				}
				else {
					if ($tag_id = $this->insertTag($tag)) {
						$tag_ids[] = $tag_id;
					}
				}
			}
		}
		return $tag_ids;		
	}

	public function tagIDFromLabel($label)
	{
		$result=self::getTag(array('label'=>$label));
                return $result==null?null:$result[0]->getTagId();
	}
	
	public function label($tag_id)
	{
		$result=self::getTag(array('tag_id'=>$tag_id));
                return $result[0]['label'];
	}
	
	/*
	 * For a given tag, return its URL.
	 */
	function url($tag_id)
	{
		return '/tag/'.intval($tag_id).'/';		
	}

	function tagTargetHTML($str)
	{
		$label = $str;
		return '<div class="tag target"><span class="label">To '.$label.'</span></div>';
	}

	public function createAnyNewTags($labels) {
		$ret =  null;
		if (is_array($labels)) {
			$tags = array();
			foreach($labels as $label) {
				if ($tag = $this->find(array('label' => $label))) {
					$tags[] = $tag;
				}
				else {
					$tags[] = $this->create($label);
				}
			}
			$ret = $tags;
		}
		return $ret;
	}

    public function getAllTags()
    {
        $ret = false;
        foreach(self::getTag(null) as $row) {
            $ret[] = $row->getLabel();
        }
        return $ret;
    }
    
    public static function getTopTags ($limit = 30) {
		$ret = false;
		$db = new PDOWrapper();
		$db->init();
		if ($r = $db->call("getTopTags", "{$db->cleanse($limit)}")) {
			$ret = array();
			foreach ($r as $row) {
				$ret[] = new Tag($row);
			}
		}
		return $ret;           
     }
     
     public function delete($id) {
         $db = new PDOWrapper();
         $db->init();
         if ($r = $db->call("deleteTag", "{$db->cleanse($id)}")) {
                 return $r[0]['result'];
         }
         return 0;
     }
     
     
        
}
