<?php

namespace SolasMatch\UI\Lib;

class Validator
{
    
    private static function addhttp($url) {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }
        return $url;
    }
    
    public static function filterSpecialChars($title)
    {
        //modify common_invalid_character in strings.xml to reflect  changes here
        $pattern = '/((\/)|(@)|(\\\\)|(\")|(\;))|(\#)|(\<)|(\>)|(\|)|(\~)/';
        if (preg_match($pattern, $title)) {
            return false;
        } else {
            return true;
        }
    }
    
    public static function validateURL($url)
    { 
      return (filter_var(self::addhttp($url), FILTER_VALIDATE_URL));
    }
    
    public static function validateEmail($url)
    { 
      return (filter_var($url, FILTER_VALIDATE_EMAIL));
    }
}