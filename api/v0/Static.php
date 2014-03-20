<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API as API;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Dispatcher as Dispatcher;

/**
 * Description of StaticAPI
 * Contains list of static routes, i.e. Routes without parameters, that don't belong to any major model
 */

require_once __DIR__."/../DataAccessObjects/StatDao.class.php";
require_once __DIR__."/../../Common/lib/Settings.class.php";
require_once __DIR__."/../lib/TipSelector.class.php";

class StaticAPI
{
    public static function init()
    {
        /*
         * This API function returns dart-configuration in json format.
         */
        Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/static/dart/conf(:format)/',
            function ($format = ".json") {
                $siteLocation = Common\Lib\Settings::get('site.location');
                $siteAPI = Common\Lib\Settings::get('site.api');
                $task_1_colour = Common\Lib\Settings::get('ui.task_1_colour');
                $task_2_colour = Common\Lib\Settings::get('ui.task_2_colour');
                $task_3_colour = Common\Lib\Settings::get('ui.task_3_colour');
                $task_4_colour = Common\Lib\Settings::get('ui.task_4_colour');
                $arr = array(
                    'urls' => array(
                        'SOLASMatch' => $siteAPI,
                        'SiteLocation' => $siteLocation
                    ),
                    'task_colours' => array(
                        'colour_1' => $task_1_colour,
                        'colour_2' => $task_2_colour,
                        'colour_3' => $task_3_colour,
                        'colour_4' => $task_4_colour
                    )
                );
                $data = json_encode($arr, JSON_UNESCAPED_SLASHES);
                Dispatcher::sendResponse(null, $data, null, $format);
            },
            'conf',
            null
        );

        Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/stats(:format)/',
            function ($format = ".json") {
                $data = DAO\StatDao::getStatistics('');
                Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getStatistics',
            null
        );

        Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/stats/:name/',
            function ($name, $format = ".json") {
                if (!is_numeric($name) && strstr($name, '.')) {
                    $name = explode('.', $name);
                    $format = '.'.$name[1];
                    $name = $name[0];
                }
                $data = DAO\StatDao::getStatistics($name);
                Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getStatisticByName',
            null
        );

        Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/stats/getLoginCount/:startDate/:endDate/',
            function ($startDate, $endDate, $format = '.json') {
                if (strstr($endDate, '.')) {
                    $endDate = explode('.', $endDate);
                    $format = '.'.$endDate[1];
                    $endDate = $endDate[0];
                }
                $data = DAO\StatDao::getLoginCount($startDate, $endDate);
                Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getLoginCount',
            null
        );

        Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/tips(:format)/',
            function ($format = ".json") {
                $data = API\Lib\TipSelector::selectTip();
                Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getTip'
        );

        Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/static/notFound(:format)/',
            function ($format = '.json') {
                Dispatcher::sendResponse(null, "404 Not Found", 404, $format);
            },
            'notFound',
            null
        );
    }
}

StaticAPI::init();
