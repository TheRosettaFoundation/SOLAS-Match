<?php
class Organisation
{
    var $_id;
    var $_name;

    public function __construct($params) {
        if(isset($params['id'])) {
            $this->setId($params['id']);
        }
        if(isset($params['name'])) {
            $this->setName($params['name']);
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
}
