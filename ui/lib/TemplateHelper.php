<?php

/**
 * Description of templateHelper
 *
 * @author sean
 */

class TemplateHelper {
    
    public static function timeSinceSqlTime($sql_string)
    {
        return self::timeSince(strtotime($sql_string));
    }

    private static function timeSince($unix_time)
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

    public static function isValidEmail($email) 
    {
        return (self::emailContainsCharacter($email, '@')
            && 
            self::emailContainsCharacter($email, '.')
        );      
    }

    public static function isValidTime($time)
    {

        if (strlen($time) > 5) {
            return false;
        } elseif (strpos($time, ":") === false) {
            return false;
        } else {
            $hour = substr($time, 0, strpos($time, ":"));
            $minute = substr($time, strpos($time, ":") + 1, strlen($time));
            if(!is_numeric($hour) || intval($hour) > 24 || !is_numeric($minute) || intval($minute) > 60) {
                return false;
            }
        }

        return true;
    }

    public static function addTimeToUnixTime($unixTime, $timeStr) 
    {
        $ret = $unixTime;

        $hour = substr($timeStr, 0, strpos($timeStr, ":"));
        $minute = substr($timeStr, strpos($timeStr, ":") + 1, strlen($timeStr));
        $ret += intval($hour) * 60 * 60;
        $ret += intval($minute) * 60;

        return $ret;
    }

    private static function emailContainsCharacter($email, $character) 
    {
        return (strpos($email, $character) !== false);
    }

    public static function isValidPassword($password) 
    {
        return (strlen($password) > 0);
    }

    public static function getTaskSourceLanguage($task)
    {
        $language =  TemplateHelper::languageNameFromCode($task->getSourceLanguageCode());
        $region =  TemplateHelper::countryNameFromCode($task->getSourceCountryCode());
        return $language.' ('.$region.')';
    }

    public static function getTaskTargetLanguage($task)
    {
        $language =  TemplateHelper::languageNameFromCode($task->getTargetLanguageCode());
        $region =  TemplateHelper::countryNameFromCode($task->getTargetCountryCode());
        return $language.' ('.$region.')';
    }

    public static function getNativeLanguage($user)
    {
        $language = TemplateHelper::languageNameFromId($user->getNativeLangId());
        $region = TemplateHelper::countryNameFromId($user->getNativeRegionId());
        return $language.' ('.$region.')';
    }

    public static function languageNameFromId($languageID)
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $result = $client->castCall("Language", "$siteApi/v0/languages/$languageID" );
        return $result->getName();
    }

    public static function languageNameFromCode($languageCode)
    {
        $ret = "";
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/languages/getByCode/$languageCode";
        $response = $client->call($request);
        if($response) {
            $lang = $client->cast("Language", $response);
            $ret = $lang->getName();
        }
        return $ret;
    }

    public static function orgNameFromId($orgID)
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $result = $client->castCall("Organisation", "$siteApi/v0/orgs/$orgID");
        return $result->getName();
    }

    public static function countryNameFromId($cID)
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $result = $client->castCall("Country", "$siteApi/v0/countries/$cID");
        return $result->getName();
    }
    
    public static function countryNameFromCode($cc) 
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $result = $client->castCall("Country", "$siteApi/v0/countries/getByCode/$cc");
        return $result->getName();
    }
     
    public static function getLanguageList() 
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $result = $client->castCall(array("Language"), "$siteApi/v0/languages");
        return $result;
    }

    public static function getCountryList()
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $result = $client->castCall(array("Country"), "$siteApi/v0/countries");
        return $result;
    }

    public static function saveLanguage($languageCode) 
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");

        $language = $client->castCall("Language", "$siteApi/v0/languages/getByCode/$languageCode");
        if (is_null(($language))) {
            throw new InvalidArgumentException('A valid language code was expected.');
        }
        return $language->getId();
    }
    
    public static function maxFileSizeBytes()
    {
        $display_max_size = self::maxUploadSizeFromPHPSettings();

        switch (substr($display_max_size, -1)) {
            case 'G':
                $display_max_size = $display_max_size * 1024;
            case 'M':
                $display_max_size = $display_max_size * 1024;
            case 'K':
                $display_max_size = $display_max_size * 1024;
        }
        return $display_max_size;
    }

    /**
     * Return an integer value of the max file size that can be uploaded to the system,
     * denominated in megabytes.
     */
    public static function maxFileSizeMB()
    {
        $bytes = self::maxFileSizeBytes();
        return round(($bytes/1024) / 1024, 1);
    }

    private static function maxUploadSizeFromPHPSettings()
    {
        return ini_get('post_max_size');
    }
        
    public static function validateFileHasBeenSuccessfullyUploaded($field_name)
    {
        if (self::isPostTooLarge()) {
            $max_file_size = ini_get('post_max_size');
            throw new Exception('Sorry, the file you tried uploading is too large.
                                The max file size is ' . $max_file_size . 
                                '. Please consider saving the file in multiple smaller parts for upload.');
        }

        if (!self::isUploadedFile($field_name)) {
            throw new Exception('You did not upload a file. Please try again.');
        }

        if (!self::isUploadedWithoutError($field_name)) {
            $error_message = self::fileUploadErrorMessage($_FILES[$form_file_field]['error']);
            throw new Exception('Sorry, we were not able to upload your file. Error: ' . $error_message);
        }
    }

    /* Thanks to http://andrewcurioso.com/2010/06/detecting-file-size-overflow-in-php/ */
    private static function isPostTooLarge()
    {
            return ( 
                    $_SERVER['REQUEST_METHOD'] == 'POST' && 
                    empty($_POST) &&
                    empty($_FILES) && 
                    $_SERVER['CONTENT_LENGTH'] > 0
            );
    }

    private static function fileUploadErrorMessage($error_code)
    {
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE :
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE :
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            case UPLOAD_ERR_PARTIAL :
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE :
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR :
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE :
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION :
                return 'File upload stopped by extension';
            default :
                return 'Unknown upload error';
        }
    }

    public static function isUploadedFile($field_name)
    {
        return is_uploaded_file($_FILES[$field_name]['tmp_name']);
    }

    public static function isUploadedWithoutError($field_name)
    {
        return $_FILES[$field_name]['error'] == UPLOAD_ERR_OK;
    }
        
    public static function separateTags($tags)
    {
        $separated_tags = null;
        if ($explosion = self::explodeTags($tags)) {
            foreach ($explosion as $tag) {
                if ($clean_tag = self::cleanTag($tag)) {
                    $separated_tags[] = $clean_tag;
                }
            }
        }
        return $separated_tags;
    }

    private static function cleanTag($tag)
    {
        $cleaned = trim($tag);
        if (strlen($cleaned) > 0) {
            return $cleaned;
        } else {
            return null;
        }
    }

    private static function explodeTags($tags)
    {
        $separator = ' ';
        return explode($separator, $tags);
    }
}
