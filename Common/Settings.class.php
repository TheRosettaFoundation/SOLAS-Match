<?php

class Settings {

    private $_settings = array();

    public function __construct()
    {
        $file = dirname(__FILE__).'/includes/conf.ini';
        $this->load($file);
    }

    public function get($var)
    {
        $result = $this->retrieveValue($var);
        if (is_null($result)) {
            throw new BadMethodCallException('Could not load the requested setting ' . $var);
        }
        return $result;
    }

    private function retrieveValue($var)
    {
        $var = explode('.', $var);
        if (isset($this->_settings[$var[1]])) {
            return $this->_settings[$var[1]];
        } else {
            return null;
        }
    }

    public function load($file)
    {
        if (file_exists($file)) {
            $this->_settings = parse_ini_file($file);
            //This updates the upload path to be absolute
            $this->_settings['upload_path'] = __DIR__."/../".$this->_settings['upload_path'];
        } else {
            echo "<p>Could not load ini file</p>";
        }
    }
}