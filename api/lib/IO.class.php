<?php

namespace SolasMatch\API\Lib;

/**
 * Input/Output including parsing input, and formatting output for URLs.
 */

class IO {

    public function __construct()
    {
    }

    public function get($get_var_name)
    {
        return isset($_GET[$get_var_name]) ? $this->cleanseInput($_GET[$get_var_name]) : false;
    }

    public function post($post_var_name)
    {
        return isset($_POST[$post_var_name]) ? $this->cleanseInput($_POST[$post_var_name]) : false;
    }

    // Cleanse input, but keep HTML tags.
    public function postHTML($post_var_name)
    {
        return isset($_POST[$post_var_name]) ? $this->cleanseInputKeepHTML($_POST[$post_var_name]) : false;
    }

    // Cleanse input: make safe from SQL injection.
    public function cleanseInput($str)
    {
        $str = $this->cleanseInputKeepHTML($str);
        //mysql_real_escape_string
        return strip_tags(trim($str));
    }

    // Allow to keep HTML tags.
    public function cleanseInputKeepHTML($str)
    {
        if (get_magic_quotes_gpc()) {
            $str = stripslashes($str);
        }
        return $str;
    }

    public static function formatForUrl($text)
    {
        return rawurlencode($text);
    }

    public function formatFromURL($text)
    {
        // Decodes the given text from URL representation
        return rawurldecode($text);
    }

    public static function timeSinceSqlTime($sql_string)
    {
        return self::timeSince(strtotime($sql_string));
    }

    public static function timeSince($unix_time)
    {
        // From http://www.dreamincode.net/code/snippet86.htm
        // Array of time period chunks
        $chunks = array(
            array(60 * 60 * 24 * 365 , 'year'),
            array(60 * 60 * 24 * 30 , 'month'),
            array(60 * 60 * 24 * 7, 'week'),
            array(60 * 60 * 24 , 'day'),
            array(60 * 60 , 'hour'),
            array(60 , 'minute'),
        );

        $today = time(); /* Current unix time  */
        $since = $today - $unix_time;

        // $j saves performing the count function each time around the loop
        for ($i = 0, $j = count($chunks); $i < $j; $i++) {
            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];

            // finding the biggest chunk (if the chunk fits, break)
            if (($count = floor($since / $seconds)) != 0) {
                // DEBUG print "<!-- It's $name -->\n";
                break;
            }
        }

        $print = ($count == 1) ? '1 '.$name : "$count {$name}s";

        if ($i + 1 < $j) {
            // now getting the second item
            $seconds2 = $chunks[$i + 1][0];
            $name2 = $chunks[$i + 1][1];

            // add second item if it's greater than 0
            if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
                $print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
            }
        }
        return $print;
    }

    /*
     * Pass a requested file back to the browser
     */
    public static function downloadFile($absoluteFilePath/*, $contentType*/)
    {
        if (file_exists($absoluteFilePath)) {
            return file_get_contents($absoluteFilePath);
        }
    }
    
    public static function downloadConvertedFile($absoluteFilePath, $contentType, $taskID)
    {
        if ($fd = fopen($absoluteFilePath, "r")) {
            $fsize = filesize($absoluteFilePath);
            $path_parts = pathinfo($absoluteFilePath);
            header('Content-type: '.$contentType);
            header('Content-Disposition: attachment; filename="'.$path_parts["basename"].'"');
            header("Content-length: $fsize");
            header("X-Frame-Options: ALLOWALL");
            header("Cache-control: private"); //use this to open files directly
//          header("X-Sendfile: ".realpath($absoluteFilePath));
            $file = file_get_contents($absoluteFilePath);
            echo FormatConverter::convertToXliff($file, $taskID, $path_parts["basename"]);
            die; // TODO -> this die is to get around Slim's $app->reponse() header/body response.
            // Is there a cleaner way to download files?
        }
        fclose($fd);
        return;
    }
}
