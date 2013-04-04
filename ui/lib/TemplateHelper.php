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
            array(60 * 60 * 24 * 365 , "year"),
            array(60 * 60 * 24 * 30 , "month"),
            array(60 * 60 * 24 * 7, "week"),
            array(60 * 60 * 24 , "day"),
            array(60 * 60 , "hour"),
            array(60 , "minute"),
        );

        $today = time(); /* Current unix time  */
        $since = $today - $unix_time;

        // $j saves performing the count function each time around the loop
        for ($i = 0, $j = count($chunks); $i < $j; $i++) {
            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];

            // finding the biggest chunk (if the chunk fits, break)
            if (($count = floor($since / $seconds)) != 0) {
                // DEBUG print "<!-- It"s $name -->\n";
                break;
            }
        }        
       
        $print = ($count == 1) ? "1 $name" : "$count {$name}s";

        if ($i + 1 < $j) {
            // now getting the second item
            $seconds2 = $chunks[$i + 1][0];
            $name2 = $chunks[$i + 1][1];

            // add second item if it"s greater than 0
            if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
                $print .= ($count2 == 1) ? ", 1 $name2" : ", $count2 {$name2}s";
            }
        }
        return $print;
    }
    
    public static function getTaskTypeFromId($taskTypeId)
    {
        switch ($taskTypeId) {
            case TaskTypeEnum::POSTEDITING:
                return "Postediting";
            case TaskTypeEnum::TRANSLATION:
                return "Translation";
            case TaskTypeEnum::PROOFREADING:
                return "Proofreading";
            case TaskTypeEnum::CHUNKING:
                return "Chunking";
            default:
                return "Unknown Task Type";
        }
    }

    public static function isValidEmail($email) 
    {   
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    public static function isValidDateTime($dateTime)
    {   
        return (false !== strtotime(trim(str_replace(" - ", " ", $dateTime))));
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
        $use_language_codes = Settings::get("ui.language_codes"); 
        
        if($use_language_codes == "y") {
            return $task->getSourceLanguageCode()."-".$task->getSourceCountryCode();
        } else if($use_language_codes == "n") {
            $language = TemplateHelper::languageNameFromCode($task->getSourceLanguageCode());
            $region = TemplateHelper::countryNameFromCode($task->getSourceCountryCode());
            return $language." - ".$region;
        } else if($use_language_codes == "h") {
            return TemplateHelper::languageNameFromCode($task->getSourceLanguageCode())." - "
                .TemplateHelper::countryNameFromCode($task->getSourceCountryCode())
                ." (".$task->getSourceLanguageCode()."-".$task->getSourceCountryCode().")";
        }
    }

    public static function getTaskTargetLanguage($task)
    {
        $use_language_codes = Settings::get("ui.language_codes"); 
        
        if($use_language_codes == "y") {
            return $task->getTargetLanguageCode()."-".$task->getTargetCountryCode();
        } else if($use_language_codes == "n") {
            $language = TemplateHelper::languageNameFromCode($task->getTargetLanguageCode());
            $region = TemplateHelper::countryNameFromCode($task->getTargetCountryCode());
            return $language." - ".$region;
        } else if($use_language_codes == "h") {
            return TemplateHelper::languageNameFromCode($task->getTargetLanguageCode())
                ." - ".TemplateHelper::countryNameFromCode($task->getTargetCountryCode())
                ." (".$task->getTargetLanguageCode()."-".$task->getTargetCountryCode().")";
        }
    }
    
    public static function getProjectSourceLanguage($project)
    {
        $use_language_codes = Settings::get("ui.language_codes"); 
        
        if($use_language_codes == "y") {
            return $project->getSourceLanguageCode()."-".$project->getSourceCountryCode();
        } else if($use_language_codes == "n") {
            $language = TemplateHelper::languageNameFromCode($project->getSourceLanguageCode());
            $region = TemplateHelper::countryNameFromCode($project->getSourceCountryCode());
            return $language." - ".$region;
        } else if($use_language_codes == "h") {
            return TemplateHelper::languageNameFromCode($project->getSourceLanguageCode())." - "
                .TemplateHelper::countryNameFromCode($project->getSourceCountryCode())
                ." (".$project->getSourceLanguageCode()."-".$project->getSourceCountryCode().")";
        }
    }

    public static function getNativeLanguage($user)
    {
        $use_language_codes = Settings::get("ui.language_codes"); 
        
        $language = TemplateHelper::languageNameFromId($user->getNativeLangId());
        $region = TemplateHelper::countryNameFromId($user->getNativeRegionId());
        
        if($use_language_codes == "y") {
            return $user->getNativeLangId()."-".$user->getNativeRegionId();
        } else if($use_language_codes == "n") {
            return TemplateHelper::languageNameFromCode($user->getNativeLangId())." - ".TemplateHelper::countryNameFromCode($user->getNativeRegionId());
        } else if($use_language_codes == "h") {
            return TemplateHelper::languageNameFromCode($user->getNativeLangId())." - ".TemplateHelper::countryNameFromCode($user->getNativeRegionId())
                    ." (".$user->getNativeLangId()."-".$user->getNativeRegionId().")";
        }
        
        return $language." - ".$region;
    }
    
    public static function getLanguageAndCountryFromCode($codes)
    {
        $splitCodes = explode(",", $codes);
        $languageCode = $splitCodes[0];
        $countryCode = $splitCodes[1];
        
        $use_language_codes = Settings::get("ui.language_codes");
        
        if($use_language_codes == "y") {
            return $languageCode."-".$countryCode;
        } else if($use_language_codes == "n") {
            $language = TemplateHelper::languageNameFromCode($languageCode);
            $region = TemplateHelper::countryNameFromCode($countryCode);
            return $language." - ".$region;
        } else if($use_language_codes == "h") {
            return TemplateHelper::languageNameFromCode($languageCode)." - "
                .TemplateHelper::countryNameFromCode($countryCode)
                ." (".$languageCode."-".$countryCode.")";
        }
        
        return $language." - ".$region;
    }

    public static function languageNameFromId($languageID)
    {
        $languageDao = new LanguageDao();
        $result = $languageDao->getLanguage($languageID);
        return self::cleanse($result->getName());
    }

    public static function languageNameFromCode($languageCode)
    {
        $ret = "";
        $langDao = new LanguageDao();
        $lang = $langDao->getLanguageByCode($languageCode);
        if($lang) {
            $ret = self::cleanse($lang->getName());
        }
        return $ret;
    }

    public static function orgNameFromId($orgID)
    {
        $orgDao = new OrganisationDao();
        $result = $orgDao->getOrganisation(array("id" => $orgID));
        return self::cleanse($result->getName());
    }

    public static function countryNameFromId($cID)
    {
        $countryDao = new CountryDao();
        $result = $countryDao->getCountry($cID);
        return self::cleanse($result->getName());
    }
    
    public static function countryNameFromCode($cc) 
    {
        $countryDao = new CountryDao();
        $result = $countryDao->getCountryByCode($cc);
        return self::cleanse($result->getName());
    }
     
    public static function getLanguageList() 
    {
        $use_language_codes = Settings::get("ui.language_codes");
        $langDao = new LanguageDao();
        $result = $langDao->getLanguages();        
        foreach($result as $lang)
        {
            if($use_language_codes == "y") {
                $lang->setName(self::cleanse($lang->getCode()));
            } else if($use_language_codes == "n") {
                $lang->setName(self::cleanse($lang->getName()));                
            } else if($use_language_codes == "h") {
                $lang->setName(self::cleanse($lang->getName())." (".self::cleanse($lang->getCode()).")"); 
            }
        }
        return $result;
    }

    public static function getCountryList()
    {
        $use_language_codes = Settings::get("ui.language_codes");     
        $countryDao = new CountryDao();
        $result = $countryDao->getCountries();
        foreach($result as $country)
        {
            if($use_language_codes == "y") {
                $country->setName(self::cleanse($country->getCode()));
            } else if($use_language_codes == "n") {
                $country->setName(self::cleanse($country->getName()));                
            } else if($use_language_codes == "h") {
                $country->setName(self::cleanse($country->getName())." (".self::cleanse($country->getCode()).")"); 
            }
            
        }
        return $result;
    }

    public static function saveLanguage($languageCode) 
    {
        $langDao = new LanguageDao();
        $language = $langDao->getLanguageByCode($languageCode);
        if (is_null(($language))) {
            throw new InvalidArgumentException("A valid language code was expected.");
        }
        return $language->getId();
    }
    
    public static function maxFileSizeBytes()
    {
        $display_max_size = self::maxUploadSizeFromPHPSettings();

        switch (substr($display_max_size, -1)) {
            case "G":
                $display_max_size = $display_max_size * 1024;
            case "M":
                $display_max_size = $display_max_size * 1024;
            case "K":
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
        return ini_get("post_max_size");
    }
        
    public static function validateFileHasBeenSuccessfullyUploaded($field_name)
    {
        if (self::isPostTooLarge()) {
            $max_file_size = ini_get("post_max_size");
            throw new Exception("Sorry, the file you tried uploading is too large.
                                The max file size is $max_file_size.
                                 Please consider saving the file in multiple smaller parts for upload.");
        }

        if (!self::isUploadedFile($field_name)) {
            throw new Exception("You did not upload a file. Please try again.");
        }

        if (!self::isUploadedWithoutError($field_name)) {
            $error_message = self::fileUploadErrorMessage($_FILES[$form_file_field]["error"]);
            throw new Exception("Sorry, we were not able to upload your file. Error: $error_message");
        }
    }

    /* Thanks to http://andrewcurioso.com/2010/06/detecting-file-size-overflow-in-php/ */
    private static function isPostTooLarge()
    {
            return ( 
                    $_SERVER["REQUEST_METHOD"] == "POST" && 
                    empty($_POST) &&
                    empty($_FILES) && 
                    $_SERVER["CONTENT_LENGTH"] > 0
            );
    }

    private static function fileUploadErrorMessage($error_code)
    {
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE :
                return "The uploaded file exceeds the upload_max_filesize directive in php.ini";
            case UPLOAD_ERR_FORM_SIZE :
                return "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
            case UPLOAD_ERR_PARTIAL :
                return "The uploaded file was only partially uploaded";
            case UPLOAD_ERR_NO_FILE :
                return "No file was uploaded";
            case UPLOAD_ERR_NO_TMP_DIR :
                return "Missing a temporary folder";
            case UPLOAD_ERR_CANT_WRITE :
                return "Failed to write file to disk";
            case UPLOAD_ERR_EXTENSION :
                return "File upload stopped by extension";
            default :
                return "Unknown upload error";
        }
    }

    public static function isUploadedFile($field_name)
    {
        return is_uploaded_file($_FILES[$field_name]["tmp_name"]);
    }

    public static function isUploadedWithoutError($field_name)
    {
        return $_FILES[$field_name]["error"] == UPLOAD_ERR_OK;
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
        $separator = " ";
        return explode($separator, $tags);
    }
    
    private static function cleanse($string)
    {
        return str_replace("_", " ", $string);
    }
    
}
