<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;

require_once __DIR__.'/../lib/Languages.class.php';

class Countries
{
    public static function init()
    {
        $app = \Slim\Slim::getInstance();

        $app->group('/v0', function () use ($app) {
            $app->group('/countries', function () use ($app) {

                /* Routes starting /v0/countries */
                $app->get(
                    '/getByCode/:code/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Countries::getCountryByCode'
                );
                
                $app->get(
	                '/getByPattern/:pattern(:format)/',
                    '\SolasMatch\API\V0\Countries::getCountriesByPattern'
                );

                $app->get(
                    '/:countryId/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Countries::getCountry'
                );
            });

            /* Routes starting /v0 */
            $app->get(
                '/countries(:format)/',
                '\SolasMatch\API\Lib\Middleware::isloggedIn',
                '\SolasMatch\API\V0\Countries::getCountries'
            );
        });
    }

    public static function getCountryByCode($code, $format = ".json")
    {
        if (!is_numeric($code) && strstr($code, '.')) {
            $code = explode('.', $code);
            $format = '.'.$code[1];
            $code = $code[0];
        }
        $data = Lib\Languages::getCountry(null, $code, null);
        if (is_array($data) && is_array($data[0])) {
            $data = $data[0];
        }
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getCountry($countryId, $format = ".json")
    {
        if (!is_numeric($countryId) && strstr($countryId, '.')) {
            $countryId = explode('.', $countryId);
            $format = '.'.$countryId[1];
            $countryId = $countryId[0];
        }
        $data = Lib\Languages::getCountry($countryId, null, null);
        if (is_array($data) && is_array($data[0])) {
            $data = $data[0];
        }
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getCountries($format = ".json")
    {
        API\Dispatcher::sendResponse(null, Lib\Languages::getCountryList(), null, $format);
    }
    
    public static function getCountriesByPattern($pattern, $format = ".json")
    {
        if (strstr($pattern, '.')) {
            $pattern = explode('.', $pattern);
            $format = '.'.$pattern[1];
            $pattern = $pattern[0];
        }
        API\Dispatcher::sendResponse(null, Lib\Languages::getCountriesByPattern($pattern), null, $format);
    }
}

Countries::init();
