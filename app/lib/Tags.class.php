<?php

class Tags {
	public static function separateTags($tags) {
		$separated_tags = null;
		if ($explosion = self::explodeTags($tags)) {
			foreach ($explosion as $tag) {
				if ($clean_tag = self::cleanTag($tag)) {
					$separated_tags[] = $clean_tag;
				}
			}
		}

		return $separated_tags;
	}

	private static function cleanTag($tag) {
		$cleaned = trim($tag);
		if (strlen($cleaned) > 0) {
			return $cleaned;
		}
		else {
			return null;
		}
	}

	private static function explodeTags($tags) {
		$separator = ' ';
		return explode($separator, $tags);
	}
}