<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;
use \SolasMatch\API\DAO as DAO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__.'/../DataAccessObjects/CountryDao.class.php';

class Countries
{
    public static function init()
    {
        global $app;

        $app->get(
            '/v0/countries/getByCode/:code/',
            '\SolasMatch\API\V0\Countries:getCountryByCode')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/v0/countries/getByPattern/:pattern/',
            '\SolasMatch\API\V0\Countries:getCountriesByPattern');

        $app->get(
            '/v0/countries/:countryId/',
            '\SolasMatch\API\V0\Countries:getCountry')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/v0/countries/',
            '\SolasMatch\API\V0\Countries:getCountries')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');
    }

    public static function getCountryByCode(Request $request, Response $response, $args)
    {
        $code = $args['code'];
        $data = DAO\CountryDao::getCountry(null, $code);
        if (is_array($data) && is_array($data[0])) {
            $data = $data[0];
        }
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getCountry(Request $request, Response $response, $args)
    {
        $countryId = $args['countryId'];
        $data = DAO\CountryDao::getCountry($countryId);
        if (is_array($data) && is_array($data[0])) {
            $data = $data[0];
        }
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getCountries(Request $request, Response $response)
    {
        API\Dispatcher::sendResponse(null, DAO\CountryDao::getCountryList(), null);
    }
    
    public static function getCountriesByPattern(Request $request, Response $response, $args)
    {
        $pattern = $args['pattern'];
        API\Dispatcher::sendResponse(null, DAO\CountryDao::getCountriesByPattern($pattern), null);
    }
}

Countries::init();
