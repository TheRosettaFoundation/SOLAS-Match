<?php

class UserSession {
    
    public static function setSession($user_id) 
    {
        $_SESSION['user_id'] = $user_id;
    }

    public static function destroySession() 
    {
        $_SESSION = array();
        session_destroy();
    }

    public static function getCurrentUserID() 
    {
        if (isset($_SESSION['user_id']) && UserSession::isValidUserId($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        } else {
            return null;
        }
    }
    
    public static function clearCurrentUserID()
    {
         if (isset($_SESSION['user_id'])) unset($_SESSION['user_id']);
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
    
    public static function setReferer($ref){
        $_SESSION['ref'] = $ref;
    }
    public static function getReferer(){
        if (isset($_SESSION['ref'])) {
            return $_SESSION['ref'];
        } else {
            return null;
        }        
    }
    public static function clearReferer(){
        if (isset($_SESSION['ref']))unset($_SESSION['ref']);
    }
    
    public static function setHash($hash) {
        $_SESSION['hash'] = $hash;
    }
    
    public static function getHash(){
        if (isset($_SESSION['hash'])) {
            return $_SESSION['hash'];
        } else {
            return null;
        }        
    }
}