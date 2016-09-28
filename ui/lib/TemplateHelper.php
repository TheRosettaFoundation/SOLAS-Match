<?php

namespace SolasMatch\UI\Lib;

use \SolasMatch\Common as Common;
use \SolasMatch\UI\DAO as DAO;

class TemplateHelper
{
    public static function utcStringToTime($sql_string)
    {
        return strtotime($sql_string . ' UTC');
    }

    public static function timeSinceSqlTime($sql_string)
    {
        return self::timeSince(strtotime($sql_string . ' UTC'));
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
            case Common\Enums\TaskTypeEnum::DESEGMENTATION:
                return Localisation::getTranslation('common_desegmentation_task');
            case Common\Enums\TaskTypeEnum::TRANSLATION:
                return Localisation::getTranslation('common_translation_task');
            case Common\Enums\TaskTypeEnum::PROOFREADING:
                return Localisation::getTranslation('common_proofreading_task');
            case Common\Enums\TaskTypeEnum::SEGMENTATION:
                return Localisation::getTranslation('common_segmentation_task');
            default:
                return Localisation::getTranslation('common_error_unknown_task_type');
        }
    }

    public static function isValidDateTime($dateTime)
    {
        //Does not support daylight saving time - Use UTC
        if ($validTime = strtotime(trim(str_replace(" - ", " ", $dateTime)))) {
            return $validTime;
        } else {
            return false;
        }
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

    public static function isValidPassword($password)
    {
        $password = trim($password);
        return (strlen($password) > 0);
    }

    public static function getTaskSourceLanguage($task)
    {
        $use_language_codes = Common\Lib\Settings::get("ui.language_codes");

        if ($use_language_codes == "y") {
            return $task->getSourceLocale()->getLanguageCode()."-".$task->getSourceLocale()->getCountryCode();
        } elseif ($use_language_codes == "n") {
            $language = $task->getSourceLocale()->getLanguageName();
            $region = $task->getSourceLocale()->getCountryName();
            return $language." - ".$region;
        } elseif ($use_language_codes == "h") {
            return $task->getSourceLocale()->getLanguageName()." - "
                .$task->getSourceLocale()->getCountryName()
                ." (".$task->getSourceLocale()->getLanguageCode()."-".$task->getSourceLocale()->getCountryCode().")";
        }
    }

    public static function getTaskTargetLanguage($task)
    {
        $use_language_codes = Common\Lib\Settings::get("ui.language_codes");

        if ($use_language_codes == "y") {
            return $task->getTargetLocale()->getLanguageCode()."-".$task->getTargetLocale()->getCountryCode();
        } elseif ($use_language_codes == "n") {
            $language = $task->getTargetLocale()->getLanguageName();
            $region = $task->getTargetLocale()->getCountryName();
            return $language." - ".$region;
        } elseif ($use_language_codes == "h") {
            return $task->getTargetLocale()->getLanguageName()." - "
                .$task->getTargetLocale()->getCountryName()
                ." (".$task->getTargetLocale()->getLanguageCode()."-".$task->getTargetLocale()->getCountryCode().")";
        }
    }

    public static function getProjectSourceLanguage($project)
    {
        $use_language_codes = Common\Lib\Settings::get("ui.language_codes");

        if ($use_language_codes == "y") {
            return $project->getSourceLanguageCode()."-".$project->getSourceCountryCode();
        } elseif ($use_language_codes == "n") {
            $language = TemplateHelper::languageNameFromCode($project->getSourceLanguageCode());
            $region = TemplateHelper::countryNameFromCode($project->getSourceCountryCode());
            return $language." - ".$region;
        } elseif ($use_language_codes == "h") {
            return TemplateHelper::languageNameFromCode($project->getSourceLanguageCode())." - "
                .TemplateHelper::countryNameFromCode($project->getSourceCountryCode())
                ." (".$project->getSourceLanguageCode()."-".$project->getSourceCountryCode().")";
        }
    }

    public static function getLanguage($locale)
    {
        $use_language_codes = Common\Lib\Settings::get("ui.language_codes");

        $languageName = $locale->getLanguageName();
        $languageCode = $locale->getLanguageCode();

        if ($use_language_codes == "y") {
            return $languageCode;
        } elseif ($use_language_codes == "n") {
            return $languageName;
        } elseif ($use_language_codes == "h") {
            return $languageName." (".$languageCode.")";
        }

        return $languageName;
    }

    public static function getCountry($locale)
    {
        $use_language_codes = Common\Lib\Settings::get("ui.language_codes");

        $countryName = $locale->getCountryName();
        $countryCode = $locale->getCountryCode();

        if ($use_language_codes == "y") {
            return $countryCode;
        } elseif ($use_language_codes == "n") {
            return $countryName;
        } elseif ($use_language_codes == "h") {
            return $countryName." (".$countryCode.")";
        }

        return $countryName;
    }

    public static function getLanguageAndCountry($locale)
    {
        $use_language_codes = Common\Lib\Settings::get("ui.language_codes");

        $languageName = $locale->getLanguageName();
        $languageCode = $locale->getLanguageCode();
        $countryName = $locale->getCountryName();
        $countryCode = $locale->getCountryCode();

        if ($use_language_codes == "y") {
            return $languageCode." - ".$countryCode;
        } elseif ($use_language_codes == "n") {
            return $languageName." - ".$countryName;
        } elseif ($use_language_codes == "h") {
            return $languageName." - ".$countryName
                    ." (".$languageCode." - ".$countryCode.")";
        }

        return $languageName." - ".$countryName;
    }

    public static function getLanguageAndCountryNoCodes($locale)
    {
        $languageName = $locale->getLanguageName();
        $countryName = $locale->getCountryName();
        return $languageName." - ".$countryName;
    }

    public static function getLanguageAndCountryFromCode($codes)
    {
        $splitCodes = explode(",", $codes);
        $languageCode = $splitCodes[0];
        $countryCode = $splitCodes[1];

        $use_language_codes = Common\Lib\Settings::get("ui.language_codes");

        if ($use_language_codes == "y") {
            return $languageCode." - ".$countryCode;
        } elseif ($use_language_codes == "n") {
            $language = TemplateHelper::languageNameFromCode($languageCode);
            $region = TemplateHelper::countryNameFromCode($countryCode);
            return $language." - ".$region;
        } elseif ($use_language_codes == "h") {
            return TemplateHelper::languageNameFromCode($languageCode)." - "
                .TemplateHelper::countryNameFromCode($countryCode)
                ." (".$languageCode." - ".$countryCode.")";
        }

        return $language." - ".$region;
    }

    public static function languageNameFromId($languageID)
    {
        $languageDao = new DAO\LanguageDao();
        $result = $languageDao->getLanguage($languageID);
        return self::cleanse($result->getName());
    }

    public static function languageNameFromCode($languageCode)
    {
        $ret = "";
        $langDao = new DAO\LanguageDao();
        $lang = $langDao->getLanguageByCode($languageCode);
        if ($lang) {
            $ret = self::cleanse($lang->getName());
        }
        return $ret;
    }

    public static function orgNameFromId($orgID)
    {
        $orgDao = new DAO\OrganisationDao();
        $result = $orgDao->getOrganisation($orgID);
        return self::cleanse($result->getName());
    }

    public static function countryNameFromId($cID)
    {
        $countryDao = new DAO\CountryDao();
        $result = $countryDao->getCountry($cID);
        return self::cleanse($result->getName());
    }

    public static function countryNameFromCode($cc)
    {
        $countryDao = new DAO\CountryDao();
        $result = $countryDao->getCountryByCode($cc);
        return self::cleanse($result->getName());
    }

    public static function getLanguageList()
    {
        $use_language_codes = Common\Lib\Settings::get("ui.language_codes");
        $langDao = new DAO\LanguageDao();
        $languages = $langDao->getLanguages();

        foreach ($languages as $lang) {
            if ($use_language_codes == "y") {
                $lang->setName(self::cleanse($lang->getCode()));
            } elseif ($use_language_codes == "n") {
                $lang->setName(self::cleanse($lang->getName()));
            } elseif ($use_language_codes == "h") {
                $lang->setName(self::cleanse($lang->getName())." (".self::cleanse($lang->getCode()).")");
            }
        }
        return $languages;
    }

    public static function getCountryList()
    {
        $use_language_codes = Common\Lib\Settings::get("ui.language_codes");
        $countryDao = new DAO\CountryDao();
        $countries = $countryDao->getCountries();

        foreach ($countries as $country) {
            if ($use_language_codes == "y") {
                $country->setName(self::cleanse($country->getCode()));
            } elseif ($use_language_codes == "n") {
                $country->setName(self::cleanse($country->getName()));
            } elseif ($use_language_codes == "h") {
                $country->setName(
                    self::cleanse($country->getName())." (".
                    self::cleanse($country->getCode()).")"
                );
            }

        }
        return $countries;
    }

    public static function maxFileSizeBytes()
    {
        $display_max_size = self::maxUploadSizeFromPHPSettings();

        switch (substr($display_max_size, -1)) {
            case "G":   // no break
                $display_max_size = $display_max_size * 1024;
                // no break
            case "M":
                $display_max_size = $display_max_size * 1024;
                // no break
            case "K":
                $display_max_size = $display_max_size * 1024;
                break;
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
        return ini_get("upload_max_filesize");
    }

    public static function validateFileHasBeenSuccessfullyUploaded($field_name)
    {
        if (!self::isUploadedWithoutError($field_name)) {
            $error_message = self::fileUploadErrorMessage($_FILES[$field_name]["error"]);
            throw new \Exception(
                sprintf(
                    Localisation::getTranslation('common_error_unable_to_upload'),
                    $error_message
                )
            );
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
        $max_file_size = self::maxFileSizeMB();
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
                return sprintf(Localisation::getTranslation('common_error_file_too_large'), $max_file_size);
            case UPLOAD_ERR_FORM_SIZE:
                return Localisation::getTranslation('common_error_file_too_large');
            case UPLOAD_ERR_PARTIAL:
                return Localisation::getTranslation('common_error_partial_upload');
            case UPLOAD_ERR_NO_FILE:
                return Localisation::getTranslation('common_error_no_file_selected');
            case UPLOAD_ERR_NO_TMP_DIR:
                return Localisation::getTranslation('common_error_server_missing_temp');
            case UPLOAD_ERR_CANT_WRITE:
                return Localisation::getTranslation('common_error_server_failed_write_disk');
            case UPLOAD_ERR_EXTENSION:
                return Localisation::getTranslation('common_error_file_stopped_by_extension');
            default:
                return Localisation::getTranslation('common_error_file_invalid_empty');
        }
    }

    public static function isUploadedFile($field_name)
    {
        return is_uploaded_file($_FILES[$field_name]["tmp_name"]);
    }

    public static function isUploadedWithoutError($field_name)
    {
        return $_FILES[$field_name]["error"] == UPLOAD_ERR_OK && filesize($_FILES[$field_name]['tmp_name']) != 0;
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

    public static function uiCleanseNewlineAndTabs($string)
    {
        return str_replace('\t', '&nbsp;&nbsp;&nbsp;&nbsp;', str_replace(array('\r\n', '\n', '\r'), "<br/>", $string)) ;
    }

    public static function uiCleanseHTMLNewlineAndTabs($string)
    {
        return str_replace('\t', '&nbsp;&nbsp;&nbsp;&nbsp;', str_replace(array('\r\n', '\n', '\r'), "<br/>", htmlspecialchars($string, ENT_COMPAT, 'UTF-8')));
    }

    public static function uiCleanseHTMLReinsertNewlineAndTabs($string)
    {
        return str_replace('\t', '&nbsp;&nbsp;&nbsp;&nbsp;', str_replace(array('\r\n', '\n', '\r'), "\r\n", htmlspecialchars($string, ENT_COMPAT, 'UTF-8')));
    }

    public static function uiCleanseHTML($string)
    {
        return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
    }

    public static function uiCleanseHTMLKeepMarkup($string)
    {
        $escaped = preg_replace(
            '# target="_blank">#',
            '>target=_blank',
            $string);
        $escaped = preg_replace(
            '#<a href="([^"<>]*)">([^<>]*)</a>#U',
            '@BRAxyz@a href=@QUOTExyz@$1@QUOTExyz@@KETxyz@$2@BRAxyz@/a@KETxyz@',
            $escaped);
        $escaped = preg_replace(
            '#<strong>([^<>]*)</strong>#U',
            '@BRAxyz@strong@KETxyz@$1@BRAxyz@/strong@KETxyz@',
            $escaped);
        $escaped = preg_replace(
            '#<b>([^<>]*)</b>#U',
            '@BRAxyz@b@KETxyz@$1@BRAxyz@/b@KETxyz@',
            $escaped);
        $escaped = preg_replace(
            '#<p>([^<>]*)</p>#U',
            '@BRAxyz@p@KETxyz@$1@BRAxyz@/p@KETxyz@',
            $escaped);
        $escaped = htmlspecialchars($escaped, ENT_COMPAT, 'UTF-8');
        $escaped = preg_replace(
            array('#@BRAxyz@#', '#@KETxyz@#', '#@QUOTExyz@#'),
            array('<',          '>',          '"'),
            $escaped);
        $escaped = preg_replace(
            '#>target=_blank#',
            ' target="_blank">',
            $escaped);
        return $escaped;
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

    public static function expandSelectedOptions($options)
    {
        $string = '';
        foreach ($options as $option) {
            if ($option['selected']) {
                if ($string === '') {
                    $string .= $option['value'];
                } else {
                    $string .= ', ' . $option['value'];
                }
            }
        }
        return $string;
    }

    public static function expandSelectedOptionsSemicolon($options)
    {
        $string = '';
        foreach ($options as $option) {
            if ($option['selected']) {
                if ($string === '') {
                    $string .= $option['value'];
                } else {
                    $string .= '; ' . $option['value'];
                }
            }
        }
        return $string;
    }

    public static function registerWithSmarty()
    {
        \Slim\Slim::getInstance()->view()->getInstance()->registerClass('TemplateHelper', __NAMESPACE__.'\TemplateHelper');
    }
}
