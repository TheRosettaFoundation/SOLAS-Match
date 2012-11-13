<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of templateHelper
 *
 * @author sean
 */
class TemplateHelper {
    public static function timeSinceSqlTime($sql_string) {
        return self::timeSince(strtotime($sql_string));
    }

    private static function timeSince($unix_time){
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
        for ($i = 0, $j = count($chunks); $i < $j; $i++)
        {
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
    public static function languageNameFromId($languageID){
        $client = new APIClient();
        $result = $client->castCall("Language",  APIClient::API_VERSION."/languages/$languageID" );
        return $result->getEnName();
    }
    public static function orgNameFromId($orgID){
        $client = new APIClient();
        $result = $client->castCall("Organisation",  APIClient::API_VERSION."/orgs/$orgID" );
        return $result->getName();
    }
    
    public static function countryNameFromId($cID){
        $client = new APIClient();
        $result = $client->castCall("Country",  APIClient::API_VERSION."/countries/$cID" );
        return $result->getEnName();
    }
     public static function countryNameFromCode($cc) {
        $client = new APIClient();
        $result = $client->castCall("Country",  APIClient::API_VERSION."/countries/getByCode/$cc" );
        return $result->getEnName();
    }
     
     public static function getLanguageList() {
        $client = new APIClient();
        $result = $client->castCall(array("Language"),  APIClient::API_VERSION."/languages" );
        return $result;
    }

    public static function getCountryList(){
        $client = new APIClient();
        $result = $client->castCall(array("Country"),  APIClient::API_VERSION."/countries" );
        return $result;
    }

     public static function saveLanguage($language_name) {
            $client = new APIClient();
            $language = $client->castCall("Language", APIClient::API_VERSION."/langs/getByName/$language_name");
            if (is_null(($language))) {
                    throw new InvalidArgumentException('A valid language name was expected.');
            }
            return $language->getId();
    }
    
    public static function maxFileSizeBytes() {
		$display_max_size = self::maxUploadSizeFromPHPSettings();
		
		switch ( substr($display_max_size,-1) ) {
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
	public static function maxFileSizeMB() {
		$bytes = self::maxFileSizeBytes();
		return round(($bytes/1024)/1024, 1);
	}

	private static function maxUploadSizeFromPHPSettings() {
		return ini_get('post_max_size');
	}
}

?>
