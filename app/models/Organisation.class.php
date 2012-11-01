<?php
class Organisation
{
    var $_id;
    var $_name;
    var $_home_page;
    var $_biography;

    public function __construct($params = array()) {
        if(isset($params['id'])) {
            $this->_id = $params['id'];
        }
        if(isset($params['name'])) {
            $this->_name = $params['name'];
        }
        if(isset($params['home_page'])) {
            $this->_home_page = $params['home_page'];
        }
        if(isset($params['biography'])) {
            $this->_biography = $params['biography'];
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
