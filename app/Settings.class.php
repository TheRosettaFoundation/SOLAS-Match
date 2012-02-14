<?php
// http://www.phpit.net/article/create-settings-class-php/
// Loads the file /conf/conf.php.

Class Settings {
    var $_settings = array();

    function __construct() {
        $file = dirname(__FILE__).'/includes/conf.php';
        $this->load($file);
    }

    public function get($var) {
        $result = $this->_retrieveValue($var);
        if (is_null($result)) {
            throw new BadMethodCallException('Could not load the requested setting ' . $var);
        }
        return $result;
    }

    private function _retrieveValue($var) {
        $var = explode('.', $var);
        if (isset($this->_settings[$var[0]][$var[1]])) {
            return $this->_settings[$var[0]][$var[1]];
        }
        else {
            return null;
        }
    }

    public function isSettingStored($var) {
        $result = $this->_retrieveValue($var);
        return (!empty($result));
    }

	function load($file) {
        require ($file);
        unset($file);

        // Get declared variables
        $vars = get_defined_vars();

        // Add to settings array
        foreach ($vars as $key => $val) {
            if ($key == 'this') continue;
            $this->_settings[$key] = $val;
        }
    }
}
