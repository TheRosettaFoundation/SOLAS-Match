<?php
// http://www.phpit.net/article/create-settings-class-php/
// Loads the file /conf/conf.php.

Class Settings {
    var $_settings = array();

	function Settings() {
		$file = $_SERVER['DOCUMENT_ROOT'].'/../includes/conf.php';
		$this->load($file);
	}

    function get($var) {
        $var = explode('.', $var);

        $result = $this->_settings;
        foreach ($var as $key) {
            if (!isset($result[$key])) { return false; }

            $result = $result[$key];
        }

        return $result;
    }

	function load ($file) {
        //if (file_exists($file) == false) { return false; }

        // Include file
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

