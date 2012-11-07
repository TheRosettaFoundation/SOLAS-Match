<?php

class Tag {
	var $_tag_id;
	var $_label;

	function __construct($params = array()) {
		if (isset($params['tag_id'])) {
			$this->_tag_id = $params['tag_id'];
		}
		if (isset($params['label'])) {
			$this->_label = $params['label'];
		}
	}

	public function setTagId($tag_id) {
		$this->_tag_id = $tag_id;
	}

	public function getTagId() {
		return $this->_tag_id;
	}

	public function hasTagId() {
		return (!is_null($this->_tag_id));
	}

	public function setLabel($label) {
		$this->_label = $label;
	}

	public function getLabel() {
		return $this->_label;
	}

	public function hasLabel() {
		return (strlen($this->getLabel()) > 0);
	}
}
