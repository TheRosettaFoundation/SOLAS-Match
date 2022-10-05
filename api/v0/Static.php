<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API as API;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Dispatcher as Dispatcher;
use SolasMatch\API\Lib\Languages;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__."/../DataAccessObjects/StatDao.class.php";
require_once __DIR__."/../../Common/lib/Settings.class.php";
require_once __DIR__."/../lib/TipSelector.class.php";
require_once __DIR__."/../../Common/lib/CacheHelper.class.php";
require_once __DIR__."/../../Common/Enums/TimeToLiveEnum.class.php";

class StaticAPI
{
    public static function init()
    {
        global $app;

        $app->get(
            '/api/v0/stats/getLoginCount/{startDate}/{endDate}/',
            '\SolasMatch\API\V0\StaticAPI:getLoginCount');

        $app->get(
            '/api/v0/stats/{name}/',
            '\SolasMatch\API\V0\StaticAPI:getStatisticByName');

        $app->get(
            '/api/v0/localisation/siteLanguages/',
            '\SolasMatch\API\V0\StaticAPI:getSiteLanguagesDart')
            ->add('\SolasMatch\API\Lib\Middleware:isLoggedIn');

        $app->get(
            '/api/v0/stats/',
            '\SolasMatch\API\V0\StaticAPI:getStatistics');

        $app->get(
            '/api/v0/tips/',
            '\SolasMatch\API\V0\StaticAPI:getTip')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');
    }

    public static function getLoginCount(Request $request, Response $response, $args)
    {
        $startDate = $args['startDate'];
        $endDate = $args['endDate'];
        $data = DAO\StatDao::getLoginCount($startDate, $endDate);
        return Dispatcher::sendResponse($response, $data, null);
    }

    public static function getStatisticByName(Request $request, Response $response, $args)
    {
        $name = $args['name'];
        $data = DAO\StatDao::getStatistics($name);
        return Dispatcher::sendResponse($response, $data, null);
    }

    public static function getStatistics(Request $request, Response $response)
    {
        $data = DAO\StatDao::getStatistics('');
        return Dispatcher::sendResponse($response, $data, null);
    }

    public static function getTip(Request $request, Response $response)
    {
        $data = API\Lib\TipSelector::selectTip();
        return Dispatcher::sendResponse($response, $data, null);
    }
    
    public static function getSiteLanguagesDart(Request $request, Response $response)
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
        return API\Dispatcher::sendResponse($response, $locales, null);
    }
}

StaticAPI::init();
