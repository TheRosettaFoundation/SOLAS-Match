<?php

namespace SolasMatch\UI\Lib;

class Validator
{
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
      return (filter_var($url, FILTER_VALIDATE_URL));
    }
    
    public static function validateEmail($url)
    { 
      return (filter_var($url, FILTER_VALIDATE_EMAIL));
    }
}