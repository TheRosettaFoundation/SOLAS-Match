<?php
require('models/Tag.class.php');

class TagsDao {
	public function find($params) {
            $result = self::getTag($params);
            return $result[0];
//		$args = "";
//		$db = new PDOWrapper();
//		$db->init();
//                if(!(isset($params['label'])||isset($params['tag_id']))) 
//                    throw new InvalidArgumentException('Cannot find tag, as no valid search data was provided.');
//                
//                $args.=((isset($params['tag_id'])))?"{$db->cleanseNull($params['tag_id'])}":"null";
//                $args.=(isset($params['label']))?",{$db->cleanseNullOrWrapStr($params['label'])}":",null";
//                
////                
////		if (isset($params['tag_id'])) {
////			$q = 'SELECT *
////				FROM tag
////				WHERE tag_id = ' . $db->cleanse($params['tag_id']) . '
////				LIMIT 1';
////		}
////		else if (isset($params['label'])) {
////			$q = 'SELECT *
////				FROM tag
////				WHERE label = ' . $db->cleanseWrapStr($params['label']) . '
////				LIMIT 1';
////		}
////		else {
////			throw new InvalidArgumentException('Cannot find tag, as no valid search data was provided.');
////		}
//		
//		$ret = false;
//		if ( $db->call("getTag", $args)) {
//			$tag_data = array();
//			$tag_data['tag_id'] = $r[0]['tag_id'];
//			$tag_data['label'] = $r[0]['label'];
//			$ret = new Tag($tag_data);
//		}
//		return $ret;
	}
        
        public function getTag($params){
            $args = "";
		$db = new PDOWrapper();
		$db->init();
//                if(!(isset($params['label'])||isset($params['tag_id']))) 
//                    throw new InvalidArgumentException('Cannot find tag, as no valid search data was provided.');
                
                $args.=((isset($params['tag_id'])))?"{$db->cleanseNull($params['tag_id'])}":"null";
                $args.=(isset($params['label']))?",{$db->cleanseNullOrWrapStr($params['label'])}":",null";
                $ret = array();
		foreach ( $db->call("getTag", $args) as $r) {
			$tag_data = array();
			$tag_data['tag_id'] = $r['tag_id'];
			$tag_data['label'] = $r['label'];
			$ret []= new Tag($tag_data);
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
                return $db->call("tagInsert", $db->cleanseWrapStr($tag->getLabel()));
//		$i = array();
//		$i['label'] = $db->cleanseWrapStr($tag->getLabel());
//		$db->Insert('tag', $i);
//		return $this->find(array('label' => $tag->getLabel()));
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
			$db = new MySQLWrapper();
			$db->init();
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

	function tagIDFromLabel($label)
	{
		$result=self::getTag(array('label'=>$label));
                return $result[0]['tag_id'];
	}
	
	function label($tag_id)
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
}
