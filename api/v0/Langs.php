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

        $app->group('/v0', function () use ($app) {
            $app->group('/languages', function () use ($app) {

                /* Routes starting /v0/languages */
                $app->get(
                    '/getActiveLanguages/',
                    '\SolasMatch\API\V0\Langs::getActiveLanguages'
                );

                $app->get(
                    '/getActiveSourceLanguages/',
                    '\SolasMatch\API\V0\Langs::getActiveSourceLanguages'
                );

                $app->get(
                    '/getActiveTargetLanguages/',
                    '\SolasMatch\API\V0\Langs::getActiveTargetLanguages'
                );

                $app->get(
                    '/getByCode/:code/',
                    '\SolasMatch\API\V0\Langs::getLanguageByCode'
                );

                $app->get(
                    '/:languageId/',
                    '\SolasMatch\API\V0\Langs::getLanguage'
                );
            });

            /* Routes starting /v0 */
            $app->get(
                '/languages/',
                '\SolasMatch\API\V0\Langs::getLanguages'
            );
        });
    }

    public static function getActiveLanguages()
    {
        API\Dispatcher::sendResponse(null, DAO\LanguageDao::getActiveLanguages(), null);
    }

    public static function getActiveSourceLanguages()
    {
        API\Dispatcher::sendResponse(null, DAO\LanguageDao::getActiveSourceLanguages(), null);
    }

    public static function getActiveTargetLanguages()
    {
        API\Dispatcher::sendResponse(null, DAO\LanguageDao::getActiveTargetLanguages(), null);
    }

    public static function getLanguageByCode($code)
    {
        $data = DAO\LanguageDao::getLanguage(null, $code);
        if (is_array($data) && is_array($data[0])) {
            $data = $data[0];
        }
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getLanguage($languageId)
    {
        $data = DAO\LanguageDao::getLanguage($languageId, null);
        if (is_array($data)) {
            $data = $data[0];
        }
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getLanguages()
    {
        $data = DAO\LanguageDao::getLanguageList();
        $result = null;
        API\Dispatcher::sendResponse(null, $data, null);
    }
}

Langs::init();
