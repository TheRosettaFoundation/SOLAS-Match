<?php

namespace SolasMatch\UI\Lib;

class Validator
{
    public static function filterSpecialChars($title)
    {
        $pattern = '/((\/)|(@)|(\\\\)|(")|(.))/';
        $result = preg_match($pattern, $title);
        if ($result === 1) {
            return false;
        } else if (result === 0) {
            return true;
        }
    }
}