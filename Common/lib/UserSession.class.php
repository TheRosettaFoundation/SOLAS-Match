<?php

namespace SolasMatch\Common\Lib;

class UserSession
{
    public static function setSession($user_id)
    {
        $_SESSION['user_id'] = $user_id;
    }

    public static function destroySession()
    {
        unset($_SESSION['user_id']);
    }

    public static function getCurrentUserID()
    {
        if (isset($_SESSION['user_id']) && self::isValidUserId($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        } else {
            return null;
        }
    }
    
    public static function clearCurrentUserID()
    {
        if (isset($_SESSION['user_id'])) {
            unset($_SESSION['user_id']);
        }
    }

    public static function isValidUserId($user_id)
    {
        return (is_numeric($user_id) && $user_id > 0);
    }
    
    public static function setUserLanguage($lang)
    {
        $_SESSION['siteLanguage'] = $lang;
    }
    
    public static function getUserLanguage()
    {
        if (isset($_SESSION['siteLanguage'])) {
            return $_SESSION['siteLanguage'];
        } else {
            return null;
        }
    }
    
    public static function setReferer($ref)
    {
        $_SESSION['ref'] = $ref;
    }

    public static function getReferer()
    {
        if (isset($_SESSION['ref'])) {
            return $_SESSION['ref'];
        } else {
            return null;
        }
    }

    public static function clearReferer()
    {
        if (isset($_SESSION['ref'])) {
            unset($_SESSION['ref']);
        }
    }

    public static function setAccessToken($token)
    {
        $_SESSION['AccessToken'] = $token;
    }
    
    public static function getAccessToken()
    {
        if (isset($_SESSION['AccessToken'])) {
            return $_SESSION['AccessToken'];
        } else {
            return null;
        }
    }

    public static function clearAccessToken()
    {
        if (isset($_SESSION['AccessToken'])) {
            unset($_SESSION['AccessToken']);
        }
    }

    public static function registerWithSmarty()
    {
        \Slim\Slim::getInstance()->view()->getInstance()->registerClass('UserSession', __NAMESPACE__.'\UserSession');
    }
}
