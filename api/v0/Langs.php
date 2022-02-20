<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;
use \SolasMatch\API\DAO as DAO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__.'/../DataAccessObjects/LanguageDao.class.php';

class Langs
{
    public static function init()
    {
        global $app;

        $app->get(
            '/v0/languages/getActiveLanguages/',
            '\SolasMatch\API\V0\Langs:getActiveLanguages');

        $app->get(
            '/v0/languages/getActiveSourceLanguages/',
            '\SolasMatch\API\V0\Langs:getActiveSourceLanguages');

        $app->get(
            '/v0/languages/getActiveTargetLanguages/',
            '\SolasMatch\API\V0\Langs:getActiveTargetLanguages');

        $app->get(
            '/api/v0/languages/getByCode/:code/',
            '\SolasMatch\API\V0\Langs:getLanguageByCode')->setName('lang');

        $app->get(
            '/v0/languages/:languageId/',
            '\SolasMatch\API\V0\Langs:getLanguage');

        $app->get(
            '/v0/languages/',
            '\SolasMatch\API\V0\Langs:getLanguages');
    }

    public static function getActiveLanguages(Request $request, Response $response)
    {
        return API\Dispatcher::sendResponse($response, DAO\LanguageDao::getActiveLanguages(), null);
    }

    public static function getActiveSourceLanguages(Request $request, Response $response)
    {
        return API\Dispatcher::sendResponse($response, DAO\LanguageDao::getActiveSourceLanguages(), null);
    }

    public static function getActiveTargetLanguages(Request $request, Response $response)
    {
        return API\Dispatcher::sendResponse($response, DAO\LanguageDao::getActiveTargetLanguages(), null);
    }

    public static function getLanguageByCode(Request $request, Response $response, $args)
    {
        $code = $args['code'];
        $data = DAO\LanguageDao::getLanguage(null, $code);
        if (is_array($data) && is_array($data[0])) {
            $data = $data[0];
        }
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getLanguage(Request $request, Response $response, $args)
    {
        $languageId = $args['languageId'];
        $data = DAO\LanguageDao::getLanguage($languageId, null);
        if (is_array($data)) {
            $data = $data[0];
        }
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getLanguages(Request $request, Response $response)
    {
        $data = DAO\LanguageDao::getLanguageList();
        return API\Dispatcher::sendResponse($response, $data, null);
    }
}

Langs::init();
