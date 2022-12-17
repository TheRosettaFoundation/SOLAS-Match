<?php

namespace SolasMatch\Common\Lib;

class Settings
{
    public static function get($var)
    {
        $settings = parse_ini_file('/repo/SOLAS-Match/Common/conf/conf.ini', true);
        $var = explode('.', $var);
        if (isset($settings[$var[0]][$var[1]])) {
            return $settings[$var[0]][$var[1]];
        } else {
            throw new \BadMethodCallException('Could not load the requested setting ' . $var[0] . '.' . $var[1]);
        }
    }
}
