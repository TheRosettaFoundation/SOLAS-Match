<?php

class Settings {

    private static $settings = array();
    private function __construct() {}

    public static function get($var)
    {
        self::load(dirname(__FILE__).'/conf/conf.ini');
        $var = explode('.', $var);
        if (isset(self::$settings[$var[1]])) {
            return self::$settings[$var[1]];
        } else {
            throw new BadMethodCallException('Could not load the requested setting ' . $var);
        }
    }

    private static function load($file)
    {
        if (file_exists($file)) {
            self::$settings = parse_ini_file($file);
            //This updates the upload path to be absolute
            self::$settings['upload_path'] = __DIR__."/../".self::$settings['upload_path'];
        } else {
            echo "<p>Could not load ini file</p>";
        }
    }
}
