<?php

/*
	One job (such as a document) will be broken into one or more tasks.
	A task simply represents something that needs to get done in the system.
	An example of a task is to translate a segment, or an entire document.
*/
class Task {
    var $_id;
    var $_title;
    var $_impact;
    var $_referencePage;
    var $_organisationID;
    var $_sourceLanguageID;
    var $_targetID;
    var $_sourceCountryCode;
    var $_targetCountryCode;
    var $_createdTime;
    var $_wordCount;
    var $_status;
    var $_tags; // array of strings

    function __construct($params = NULL) {
            if (is_array($params)) {
                    foreach ($params as $key => $value) {
                            $this->_setParam($key, $value);
                    }	
            }
    }

    public function getTaskId() {
            return $this->_id;
    }

    public function setTaskId($task_id) {
            $this->_id = $task_id;
    }

    public function setTitle($title) {
            $this->_title = $title;
    }

    public function getTitle() {
            return $this->_title;
    }

    public function setImpact($impact) {
        $this->_impact = $impact;
    }

    public function getImpact() {
        return $this->_impact;
    }

    public function setReferencePage($url) {
        $this->_referencePage = $url;
    }

    public function getReferencePage() {
        return $this->_referencePage;
    }

    public function setOrganisationId($organisation_id) {
            $this->_organisationID = $organisation_id;
    }

    public function getOrganisationId() {
            return $this->_organisationID;
    }

    public function setSourceId($source_id)
    {
            $this->_sourceLanguageID = $source_id;
    }

    public function getSourceId() {
            return $this->_sourceLanguageID;
    }

    public function setTargetId($target_id)
    {
            $this->_targetID = $target_id;
    }

    public function getTargetId() {
            return $this->_targetID;
    }

    public function getSourceCountryCode() 
    {
        return $this->_sourceCountryCode;
    }

    public function setSourceCountryCode($CountryCode)
    {
        $this->_sourceCountryCode = $CountryCode;
    }

    public function getTargetCountryCode() 
    {
        return $this->_targetCountryCode;
    }
    
    public function setTargetCountryCode($CountryCode)
    {
        $this->_targetCountryCode = $CountryCode;
    }

    public function getSourceLanguage()
    {
        $language =  TemplateHelper::languageNameFromId($this->getSourceId());
        $region =  TemplateHelper::countryNameFromCode($this->getSourceCountryCode());
        return $language.' ('.$region.')';
    }

    public function getTargetLanguage()
    {
        $language =  TemplateHelper::languageNameFromId($this->getTargetId());
        $region =  TemplateHelper::countryNameFromCode($this->getTargetCountryCode());
        return $language.' ('.$region.')';
    }

    public function setWordCount($word_count)
    {
            $this->_wordCount = $word_count;
    }

    public function getWordCount() {
            return $this->_wordCount;
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
                'impact'            => 'setImpact',
                'reference_page'    => 'setReferencePage',
                'organisation_id' 	=> 'setOrganisationId',
                'source_id'			=> 'setSourceId',
                'target_id'			=> 'setTargetId',
                'word_count'		=> 'setWordCount',
                'created_time'		=> 'setCreatedTime',
                'tags'				=> 'setTags',
                'sourceCountry'    =>  'setSourceCountryCode',
                'targetCountry'    =>  'setTargetCountryCode'
        );

        if (isset($key_methods[$key])) {
                $this->$key_methods[$key]($value);	
        }
        else {
                throw new InvalidArgumentException('No function to set ' . $key);
        }
    }

    public function setCreatedTime($created_time) {
            $this->_createdTime = $created_time;
    }

    public function getCreatedTime() {
            return $this->_createdTime;
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
