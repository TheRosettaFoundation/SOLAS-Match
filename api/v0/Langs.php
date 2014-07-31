<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;
use \SolasMatch\API\DAO as DAO;

require_once __DIR__.'/../DataAccessObjects/LanguageDao.class.php';

class Langs
{
    public static function init()
    {
        $app = \Slim\Slim::getInstance();

        $app->group('/v0', function () use ($app) {
            $app->group('/languages', function () use ($app) {

                /* Routes starting /v0/languages */
                $app->get(
                    '/getActiveLanguages(:format)/',
                    '\SolasMatch\API\V0\Langs::getActiveLanguages'
                );

                $app->get(
                    '/getActiveSourceLanguages(:format)/',
                    '\SolasMatch\API\V0\Langs::getActiveSourceLanguages'
                );

                $app->get(
                    '/getActiveTargetLanguages(:format)/',
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
                '/languages(:format)/',
                '\SolasMatch\API\V0\Langs::getLanguages'
            );
        });
    }

    public static function getActiveLanguages($format = '.json')
    {
        API\Dispatcher::sendResponse(null, DAO\LanguageDao::getActiveLanguages(), null, $format);
    }

    public static function getActiveSourceLanguages($format = '.json')
    {
        API\Dispatcher::sendResponse(null, DAO\LanguageDao::getActiveSourceLanguages(), null, $format);
    }

    public static function getActiveTargetLanguages($format = '.json')
    {
        API\Dispatcher::sendResponse(null, DAO\LanguageDao::getActiveTargetLanguages(), null, $format);
    }

    public static function getLanguageByCode($code, $format = ".json")
    {
        if (!is_numeric($code) && strstr($code, '.')) {
            $code = explode('.', $code);
            $format = '.'.$code[1];
            $code = $code[0];
        }
        $data = DAO\LanguageDao::getLanguage(null, $code);
        if (is_array($data) && is_array($data[0])) {
            $data = $data[0];
        }
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getLanguage($languageId, $format = ".json")
    {
        if (!is_numeric($languageId) && strstr($languageId, '.')) {
            $languageId = explode('.', $languageId);
            $format = '.'.$languageId[1];
            $languageId = $languageId[0];
        }
        $data = DAO\LanguageDao::getLanguage($languageId, null);
        if (is_array($data)) {
            $data = $data[0];
        }
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getLanguages($format = ".json")
    {
        $data = DAO\LanguageDao::getLanguageList();
        $result = null;
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }
}

Langs::init();
