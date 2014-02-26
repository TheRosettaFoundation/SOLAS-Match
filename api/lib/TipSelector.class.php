<?php

namespace SolasMatch\API\Lib;

/*
    This class is used to read humorous tips from a file
    on the local FS and select one for display
*/

class TipSelector
{
    public static function selectTip()
    {
        $tip_list = array();

        $tip_file = __DIR__.'/../../resources/tips/tips.txt';
        $handle = fopen($tip_file, 'r');

        while ($tmp = fgets($handle)) {
            if ($tmp != '') {
                $tip_list[] = $tmp;
            }
        }
        return $tip_list[rand(0, count($tip_list) - 1)];
    }
}
