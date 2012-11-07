<?php
class Country
{
    var $_id;
    var $_code;
    var $_en_name;
    
    public function __construct($params = array()) {
        if(isset($params['id'])) {
            $this->_id = $params['id'];
        }
        if(isset($params['code'])) {
            $this->_code = $params['code'];
        }
        if(isset($params['en_name'])) {
            $this->_en_name = $params['en_name'];
        }
    }
    
    public function setId($countryid) {
        $this->_id = $countryid;
    }
    
    public function getId() {
        return $this->_id;
    }
    
    public function setCode($code) {
        $this->_code = $code;
    }
    
    public function getCode() {
        return $this->_code;
    }
    
    public function setEnName($en_name) {
        $this->_en_name = $en_name;
    }
    
    public function getEnName() {
        return $this->_en_name;
    }
}
?>

