<?php
/**
 * Description of ConfAPI
 * Enables retrieving configuration settings via API
 * @author Asanka
 */
require_once __DIR__."/../../Common/Settings.class.php";

class ConfAPI
{
	public static function init()
	{
		   Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/conf/dart-conf(:format)/',
                                                        function ($format = ".json") {
            	//this API function returns dart-configuration in json format.
            
           		
            $siteLocation = Settings::get('site.location');
			$siteAPI = Settings::get('site.api');
			$task_1_colour=Settings::get('ui.task_1_colour');
			$task_2_colour=Settings::get('ui.task_2_colour');
			$task_3_colour=Settings::get('ui.task_3_colour');
			$task_4_colour=Settings::get('ui.task_4_colour');
			
			$arr=array('urls'=>array('SOLASMatch'=>$siteLocation, 'SiteLocation'=>$siteAPI),'task_colours'=>array('colour_1'=>$task_1_colour,'colour_2'=>$task_2_colour,'colour_3'=>$task_3_colour,'colour_4'=>$task_4_colour));
			if (version_compare(PHP_VERSION, '5.4.0') <0) $data=json_encode($arr); else $data=json_encode($arr, JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES); 
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'conf',null);
	}
	
} 
ConfAPI::init();
 
    
?>