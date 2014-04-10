<?php

namespace SolasMatch\UI\Lib;

class Validator
{
    public static function filterSpecialChars($title)
    {
        $pattern = '/((\/)|(@)|(\\\\)|(")|(.))/';
        
        if (preg_match($pattern, $title)) {
            return false;
        } else {
            return true;
        }
    }
}