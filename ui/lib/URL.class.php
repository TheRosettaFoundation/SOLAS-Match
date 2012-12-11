<?php

class URL 
{	
    public static function server()
    {
        $url = null;
        if (self::isServerNameSet()) {
            $url = self::getHttpAccessProtocol();
            $url .= $_SERVER['SERVER_NAME'];
            if (!self::isAccessedOnPort80()) {
                $url .= ':' . $_SERVER['SERVER_PORT'];
            }
        }
        return $url;
    }

    private static function isServerNameSet()
    {
        return (strlen($_SERVER['SERVER_NAME']) > 0);
    }

    private static function getHttpAccessProtocol()
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            return 'https://';
        } else {
            return 'http://';
        }
    }

    private static function isAccessedOnPort80()
    {
        return ($_SERVER['SERVER_PORT'] == '80');
    }

    public function login()
    {
        return self::server().'/login/';
    }

    public function logout()
    {
        return self::server().'/logout/';
    }

    public function register()
    {
        return self::server().'/register/';
    }

    public static function tag($tag) 
    {
        if (is_object($tag)) {
                throw new InvalidArgumentException('Cannot generate a URL for a tag,
                                                    as the tag should be a string and not an object.');
        }
        return self::server() . '/tag/' . IO::formatForUrl($tag) . '/';
    }
}