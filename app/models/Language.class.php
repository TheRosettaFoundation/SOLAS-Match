<?php
class Language
{
    var $id;
    var $code;
    var $name;
    
    public function __construct($params = array()) {
        if(isset($params['id'])) {
            $this->id = $params['id'];
        }
        if(isset($params['code'])) {
            $this->code = $params['code'];
        }
        if(isset($params['language'])) {
            $this->name = $params['language'];
        }
    }
    
    public function setId($languageid) {
        $this->id = $languageid;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setCode($code) {
        $this->code = $code;
    }
    
    public function getCode() {
        return $this->code;
    }
    
    public function setEnName($en_name) {
        $this->name = $en_name;
    }
    
    public function getEnName() {
        return $this->name;
    }
}
?>
