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
            '/api/v0/countries/getByPattern/{pattern}/',
            '\SolasMatch\API\V0\Countries:getCountriesByPattern');

        $app->get(
            '/api/v0/countries/{countryId}/',
            '\SolasMatch\API\V0\Countries:getCountry')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/api/v0/countries/',
            '\SolasMatch\API\V0\Countries:getCountries')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');
    }

    public static function getCountry(Request $request, Response $response, $args)
    {
        $countryId = $args['countryId'];
        $data = DAO\CountryDao::getCountry($countryId);
        if (is_array($data) && is_array($data[0])) {
            $data = $data[0];
        }
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getCountries(Request $request, Response $response)
    {
        return API\Dispatcher::sendResponse($response, DAO\CountryDao::getCountryList(), null);
    }
    
    public static function getCountriesByPattern(Request $request, Response $response, $args)
    {
        $pattern = $args['pattern'];
        return API\Dispatcher::sendResponse($response, DAO\CountryDao::getCountriesByPattern($pattern), null);
    }
}

Countries::init();
