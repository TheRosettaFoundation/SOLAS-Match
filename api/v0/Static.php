<?php
/**
 * Description of StaticAPI
 * Contains list of static routes, i.e. Routes without parameters, that don't belong to any major model
 */
require_once __DIR__."/../../Common/Settings.class.php";

class StaticAPI
{
	public static function init()
	{
        /*
         * This API function returns dart-configuration in json format.
         */
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/static/dart/conf(:format)/',
            function ($format = ".json") 
            {
                $siteLocation = Settings::get('site.location');
                $siteAPI = Settings::get('site.api');
                $task_1_colour = Settings::get('ui.task_1_colour');
                $task_2_colour = Settings::get('ui.task_2_colour');
                $task_3_colour = Settings::get('ui.task_3_colour');
                $task_4_colour = Settings::get('ui.task_4_colour');

                $arr = array(
                    'urls' => array(
                        'SOLASMatch' => $siteAPI,
                        'SiteLocation' => $siteLocation
                    ),
                    'task_colours' => array(
                        'colour_1' => $task_1_colour,
                        'colour_2' => $task_2_colour,
                        'colour_3' => $task_3_colour,
                        'colour_4'=>$task_4_colour
                    )
                );

                $data = json_encode($arr, JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES); 
                Dispatcher::sendResponce(null, $data, null, $format);
            }, 'conf',null);
	}
} 

StaticAPI::init();
