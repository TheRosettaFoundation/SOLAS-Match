<?php
require('models/Tag.class.php');

class TagsDao {
	public function find($params) {
		$q = null;
		$db = new MySQLWrapper();
		$db->init();
		if (isset($params['tag_id'])) {
			$q = 'SELECT *
				FROM tag
				WHERE tag_id = ' . $db->cleanse($params['tag_id']) . '
				LIMIT 1';
		}
		else if (isset($params['label'])) {
			$q = 'SELECT *
				FROM tag
				WHERE label = ' . $db->cleanseWrapStr($params['label']) . '
				LIMIT 1';
		}
		else {
			throw new InvalidArgumentException('Cannot find tag, as no valid search data was provided.');
		}
		
		$ret = false;
		if ($r = $db->Select($q)) {
			$tag_data = array();
			$tag_data['tag_id'] = $r[0]['tag_id'];
			$tag_data['label'] = $r[0]['label'];
			$ret = new Tag($tag_data);
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
		$db = new MySQLWrapper();
		$db->init();
		$i = array();
		$i['label'] = $db->cleanseWrapStr($tag->getLabel());
		$db->Insert('tag', $i);
		return $this->find(array('label' => $tag->getLabel()));
	}
	
	function getTopTags($limit = 30)
	{
		$ret = false;
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT tag_id, COUNT(tag_id) AS frequency
				FROM task_tag
				GROUP BY tag_id
				ORDER BY frequency DESC
				LIMIT '.intval($limit);
		if ($r = $db->Select($q)) {
			$ret = array();
			foreach ($r as $row) {
				if ($tag = $this->find(array('tag_id' => $row['tag_id']))) {
					$ret[] = $tag;
				}
			}
		}
		return $ret;
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
		$ret = false;
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT id
				FROM tag
				WHERE label = \''.$db->cleanse($label).'\'
				LIMIT 1';
		if ($r = $db->Select($q))
		{
			$ret = $r[0]['id'];
		}
		return $ret;
	}
	
	function label($tag_id)
	{
		$ret = false;
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT label
				FROM tag
				WHERE id = '.intval($tag_id).'
				LIMIT 1';
		if ($r = $db->Select($q))
		{
			$ret = $r[0]['label'];
		}
		return $ret;
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
}