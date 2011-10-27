<?php

class Tags
{
	var $s;
	function Tags(&$smarty)
	{
		$this->s = &$smarty;
	}
	
	/*
		Take a string typed in by the user, try to break it up and create tags from it.
		Returns an array of matching tag_ids.
		Basic format is that items are comma-delimited.
		
		Also responsible to special Langauge tags for content.
	*/
	function parse($str)
	{
		$tag_ids = false;
		$str = $this->s->io->cleanseInput($str);
		$str = str_replace(',', ' ', $str);
		if ($tags = explode(' ', $str)) // Space is the delimiter
		{
			$tag_ids = array();
			foreach($tags as $tag)
			{
				$tag = trim($tag);
				// Ask the database what the ID is by searching for the tag's text label
				if ($tag_id = $this->tagIDFromLabel($tag))
				{
					$tag_ids[] = $tag_id;
				}
				else
				{
					// Create this tag, and return its ID.
					$i = array();
					$i['label'] = '\''.$this->s->db->cleanse($tag).'\'';
					if ($tag_id = $this->s->db->Insert('tag', $i))
					{
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
		$q = 'SELECT id
				FROM tag
				WHERE label = \''.$this->s->db->cleanse($label).'\'
				LIMIT 1';
		if ($r = $this->s->db->Select($q))
		{
			$ret = $r[0]['id'];
		}
		return $ret;
	}
	
	function label($tag_id)
	{
		$ret = false;
		$q = 'SELECT label
				FROM tag
				WHERE id = '.intval($tag_id).'
				LIMIT 1';
		if ($r = $this->s->db->Select($q))
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
		$q = 'SELECT tag_id
				FROM task_tag
				WHERE task_id = '.intval($task_id);
		if ($r = $this->s->db->Select($q))
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
	
	/*
	 * Return the HTML to represent a tag.
	 */
	function tagHTML($tag_id)
	{
		$label = $this->label($tag_id);
		$url = $this->url($tag_id);
		return '<a class="tag" href="'.$url.'"><span class="label">'.$label.'</span></a>';
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
		$q = 'SELECT tag_id, COUNT(tag_id) AS frequency
				FROM task_tag
				GROUP BY tag_id
				ORDER BY frequency DESC
				LIMIT '.intval($limit);
		if ($r = $this->s->db->Select($q))
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
	
	/*
	 * Give the string name of a langauge, return its ID.
	 * @param $lang such as "English"
	 * @param $str_lang_code is the langauge in which the language name is written in.
	 * 			For example, is $lang contains "French", then the code of the langauge is in "en".
	 */
	function langID($lang, $str_lang_code = 'en')
	{
		return Tag::landID($this->s, $lang, $str_lang_code);
	}
}
