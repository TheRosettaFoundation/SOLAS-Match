<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;
use \SolasMatch\API\DAO as DAO;

require_once __DIR__.'/../DataAccessObjects/CountryDao.class.php';

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
                    '/getByPattern/:pattern/',
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
                '/countries/',
                '\SolasMatch\API\Lib\Middleware::isloggedIn',
                '\SolasMatch\API\V0\Countries::getCountries'
            );
        });
    }

    public static function getCountryByCode($code)
    {
        $data = DAO\CountryDao::getCountry(null, $code);
        if (is_array($data) && is_array($data[0])) {
            $data = $data[0];
        }
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getCountry($countryId)
    {
        $data = DAO\CountryDao::getCountry($countryId);
        if (is_array($data) && is_array($data[0])) {
            $data = $data[0];
        }
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getCountries()
    {
        API\Dispatcher::sendResponse(null, DAO\CountryDao::getCountryList(), null);
    }
    
    public static function getCountriesByPattern($pattern)
    {
        API\Dispatcher::sendResponse(null, DAO\CountryDao::getCountriesByPattern($pattern), null);
    }
}

Countries::init();
