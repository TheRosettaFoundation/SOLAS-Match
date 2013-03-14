<?php

class Settings {

    public static function get($var)
    {
        $settings = self::load(dirname(__FILE__).'/conf/conf.ini');
        $var = explode('.', $var);
        if (isset($settings[$var[0]][$var[1]])) {
            return $settings[$var[0]][$var[1]];
        } else {
            throw new BadMethodCallException('Could not load the requested setting ' . $var[0] . ".". $var[1]);
        }
    }

    private static function load($file)
    {
        $settings = array();
        if (file_exists($file)) {
            $settings = parse_ini_file($file, true);
            //This updates the upload path to be absolute
            $settings['files']['upload_path'] = __DIR__."/../".$settings['files']['upload_path'];
        } else {
            echo "<p>Could not load ini file</p>";
        }
        return $settings;
    }
}
