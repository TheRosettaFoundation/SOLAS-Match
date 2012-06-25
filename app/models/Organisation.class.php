<?php
class Organisation
{
    var $_id;
    var $_name;
    var $_home_page;
    var $_biography;

    public function __construct($params) {
        if(isset($params['id'])) {
            $this->setId($params['id']);
        }
        if(isset($params['name'])) {
            $this->setName($params['name']);
        }
        if(isset($params['home_page'])) {
            $this->setHomePage($params['home_page']);
        }
        if(isset($params['biography'])) {
            $this->setBiography($params['biography']);
        }
    }

    public function getId() {
        return $this->_id;
    }

    public function setId($id) {
        $this->_id = $id;
    }

    public function getName() {
        return $this->_name;
    }

    public function setName($name) {
        $this->_name = $name;
    }

    public function getHomePage() {
        return $this->_home_page;
    }

    public function setHomePage($home_page) {
        $this->_home_page = $home_page;
    }

    public function getBiography() {
        return $this->_biography;
    }

    public function setBiography($bio) {
        $this->_biography = $bio;
    }
}
