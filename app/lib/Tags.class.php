<?php

class Tags {
	public static function separateLabels($labels)
	{
		$str = str_replace(',', ' ', $labels);
		$arr = explode(' ', $str);
		$trimmed = array();
		foreach ($arr as $tag) {
			if (strlen(trim($tag)) > 0) {
				$trimmed[] = trim($tag);
			}
		}
		return (count($trimmed) > 0) ? $trimmed : null;
	}
}