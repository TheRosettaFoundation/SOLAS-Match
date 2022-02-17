<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API as API;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Dispatcher as Dispatcher;
use SolasMatch\API\Lib\Languages;

require_once __DIR__."/../DataAccessObjects/StatDao.class.php";
require_once __DIR__."/../../Common/lib/Settings.class.php";
require_once __DIR__."/../lib/TipSelector.class.php";
require_once __DIR__."/../../Common/lib/CacheHelper.class.php";
require_once __DIR__."/../../Common/Enums/TimeToLiveEnum.class.php";

class StaticAPI
{
    public static function init()
    {
        $app = \Slim\Slim::getInstance();

        $app->group('/v0', function () use ($app) {
            $app->group('/stats', function () use ($app) {

                /* Routes starting /v0/stats */
                $app->get(
                    '/getLoginCount/:startDate/:endDate/',
                    '\SolasMatch\API\V0\StaticAPI::getLoginCount'
                );

                $app->get(
                    '/:name/',
                    '\SolasMatch\API\V0\StaticAPI::getStatisticByName'
                );
            });

            $app->group('/static', function () use ($app) {

                /* Routes starting /v0/static */
                $app->get(
                    '/dart/conf/',
                    '\SolasMatch\API\V0\StaticAPI::getDartConf'
                );

                $app->get(
                    '/notFound/',
                    '\SolasMatch\API\V0\StaticAPI::notFound'
                );
            });

            /* Routes starting /v0 */
            $app->get(
                '/localisation/siteLanguages/',
                '\SolasMatch\API\Lib\Middleware::isLoggedIn',
                '\SolasMatch\API\V0\StaticAPI::getSiteLanguagesDart'
            );
            
            $app->get(
                '/stats/',
                '\SolasMatch\API\V0\StaticAPI::getStatistics'
            );

            $app->get(
                '/tips/',
                '\SolasMatch\API\Lib\Middleware::isloggedIn',
                '\SolasMatch\API\V0\StaticAPI::getTip'
            );
        });
    }

    public static function getLoginCount($startDate, $endDate)
    {
        $data = DAO\StatDao::getLoginCount($startDate, $endDate);
        Dispatcher::sendResponse(null, $data, null);
    }

    public static function getStatisticByName($name)
    {
        $data = DAO\StatDao::getStatistics($name);
        Dispatcher::sendResponse(null, $data, null);
    }

    public static function getDartConf()
    {
        $siteLocation = Common\Lib\Settings::get('site.location');
        $siteAPI = Common\Lib\Settings::get('site.api');
        $task_1_colour = Common\Lib\Settings::get('ui.task_1_colour');
        $task_2_colour = Common\Lib\Settings::get('ui.task_2_colour');
        $task_3_colour = Common\Lib\Settings::get('ui.task_3_colour');
        $task_4_colour = Common\Lib\Settings::get('ui.task_4_colour');
        $projImageSize = Common\Lib\Settings::get('projectImages.max_image_size');
        $projImageWidth = Common\Lib\Settings::get('projectImages.max_width');
        $projImageHeight = Common\Lib\Settings::get('projectImages.max_height');
        $projImageFormats = Common\Lib\Settings::get('projectImages.supported_formats');
        
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
            ),
            'project_images' => array (
            	'max_size' => $projImageSize,
                'max_width' => $projImageWidth,
                'max_height' => $projImageHeight,
                'supported_formats' => $projImageFormats
            )
        );
        $data = json_encode($arr, JSON_UNESCAPED_SLASHES);
        Dispatcher::sendResponse(null, $data, null);
    }

    public static function notFound()
    {
        Dispatcher::sendResponse(null, "404 Not Found", 404);
    }

    public static function getStatistics()
    {
        $data = DAO\StatDao::getStatistics('');
        Dispatcher::sendResponse(null, $data, null);
    }

    public static function getTip()
    {
        $data = API\Lib\TipSelector::selectTip();
        Dispatcher::sendResponse(null, $data, null);
    }
    
    public static function getSiteLanguagesDart()
    {
        $matches = array();
        $locales = array();
        
        $filePaths = glob(__DIR__."/../../ui/localisation/strings_*.xml");
        foreach ($filePaths as $filePath) {
            preg_match('/_(.*)\.xml/', realpath($filePath), $matches);
            $lang = Common\Lib\CacheHelper::getCached(
                Common\Lib\CacheHelper::LOADED_LANGUAGES."_$matches[1]",
                Common\Enums\TimeToLiveEnum::QUARTER_HOUR,
                '\SolasMatch\API\Lib\Languages:getLanguage',
                array(null, $matches[1], null)
            );
            $locales[] = $lang;
        }
        API\Dispatcher::sendResponse(null, $locales, null);
    }
}

StaticAPI::init();
