<?php

class Tag {
	var $_tag_id;
	var $_label;

	function __construct($params) {
		$this->setTagId($params['tag_id']);
		$this->setLabel($params['label']);
	}

	public function setTagId($tag_id) {
		$this->_tag_id;
	}

	public function setLabel($label) {
		$this->_label;
	}
}