<?php

class Tags
{

	/*
		Take a string typed in by the user, try to break it up and create tags from it.
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

	/**
	 * Convert a string of inputted tags into an array of strings
	 *
	 * @return array of strings
	 * @author 
	 **/
	private function tagsToArray($tags)
	{
		$str = str_replace(',', ' ', $tags);
		$arr = explode(' ', $str);
		$trimmed = array();
		foreach ($arr as $tag) {
			if (strlen(trim($tag)) > 0) {
				$trimmed[] = trim($tag);
			}
		}
		return (count($trimmed) > 0) ? $trimmed : null;
	}

	/**
	 * Insert new tag into database
	 *
	 * @return tag_id
	 * @author 
	 **/
	private function insertTag($tag)
	{
		$i = array();
		$i['label'] = '\''.$db->cleanse($tag).'\'';
		$db = new MySQLWrapper();
		$db->init();
		return $db->Insert('tag', $i);
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
	 * Return an array of tag_ids related to a certain task.
	 * Return false if nothing found.
	 */
	function taskTagIDs($task_id)
	{
		$ret = false;
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT tag_id
				FROM task_tag
				WHERE task_id = '.intval($task_id);
		if ($r = $db->Select($q))
		{
			$ret = array();
			foreach($r as $row)
			{
				$ret[] = $row['tag_id'];
			}
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
	
	/*
	 * Return an array of (label, frequency) of tags, ordered by most frequent at top.
	*/
	function topTags($limit = 30)
	{
		$ret = false;
		$db = new MySQLWrapper();
		$db->init();
		$q = 'SELECT tag_id, COUNT(tag_id) AS frequency
				FROM task_tag
				GROUP BY tag_id
				ORDER BY frequency DESC
				LIMIT '.intval($limit);
		if ($r = $db->Select($q))
		{
			$ret = array();
			foreach ($r as $row)
			{
				$ret[] = array('tag_id' => $row['tag_id'],
								'frequency' => $row['frequency']);			
			}
		}
		return $ret;
	}
}
