<?php

/*
	One job (such as a document) will be broken into one or more tasks.
	A task simply represents something that needs to get done in the system.
	An example of a task is to translate a segment, or an entire document.
*/
class Task {
	var $_task_id;
	var $_title;
    var $_reference_page;
	var $_organisation_id;
	var $_source_id;
	var $_target_id;
	var $_created_time;
	var $_word_count;
    var $_status;
	var $_tags; // array of strings

	function __construct($params = NULL) {
		if (is_array($params)) {
			foreach ($params as $key => $value) {
				$this->_setParam($key, $value);
			}	
		}
	}

	function getTaskId() {
		return $this->_task_id;
	}

	function setTaskId($task_id) {
		$this->_task_id = $task_id;
	}

	public function setTitle($title) {
		$this->_title = $title;
	}

	public function getTitle() {
		return $this->_title;
	}

    public function setReferencePage($url) {
        $this->_reference_page = $url;
    }

    public function getReferencePage() {
        return $this->_reference_page;
    }

	public function setOrganisationId($organisation_id) {
		$this->_organisation_id = $organisation_id;
	}

	public function getOrganisationId() {
		return $this->_organisation_id;
	}

	public function setSourceId($source_id)
	{
		$this->_source_id = $source_id;
	}

	public function getSourceId() {
		return $this->_source_id;
	}

	public function setTargetId($target_id)
	{
		$this->_target_id = $target_id;
	}

	public function getTargetId() {
		return $this->_target_id;
	}

	public function setWordCount($word_count)
	{
		$this->_word_count = $word_count;
	}

	public function getWordCount() {
		return $this->_word_count;
	}

    public function getStatus() {
        return $this->_status;
    }

    public function setStatus($status) {
        $this->_status = $status;
    }

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	private function _setParam($key, $value)
	{
		$key_methods = array(
			'task_id' 			=> 'setTaskId',
			'title' 			=> 'setTitle',
            'reference_page'    => 'setReferencePage',
			'organisation_id' 	=> 'setOrganisationId',
			'source_id'			=> 'setSourceId',
			'target_id'			=> 'setTargetId',
			'word_count'		=> 'setWordCount',
			'created_time'		=> 'setCreatedTime',
			'tags'				=> 'setTags'
		);

		if (isset($key_methods[$key])) {
			$this->$key_methods[$key]($value);	
		}
		else {
			throw new InvalidArgumentException('No function to set ' . $key);
		}
	}

	public function setCreatedTime($created_time) {
		$this->_created_time = $created_time;
	}
	
	public function getCreatedTime() {
		return $this->_created_time;
	}
	
	public function areSourceAndTargetSet() {
		return ($this->getSourceId() && $this->getTargetId());
	}

	public function setTags($tags) {
		if (!is_null($tags) && is_array($tags)) {
			$this->_tags = $tags;
		}
	}

	public function getTags() {
		return $this->_tags;
	}
}
