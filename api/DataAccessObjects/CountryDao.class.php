<?php
namespace SolasMatch\API\DAO;

use \SolasMatch\Common as Common;
use \SolasMatch\API\Lib as Lib;

require_once __DIR__."/../../api/lib/PDOWrapper.class.php";
require_once __DIR__."/../../Common/protobufs/models/Country.php";

class CountryDao 
{
    public static function getCountry($id, $code = null, $name = null)
    {
        $result = Lib\PDOWrapper::call(
                "getCountry",
                Lib\PDOWrapper::cleanseNUll($id).", ".
                Lib\PDOWrapper::cleanseNullOrWrapStr($code).", ".
                Lib\PDOWrapper::cleanseNullOrWrapStr($name)
        );
        return Common\Lib\ModelFactory::buildModel("Country", $result[0]);
    }
    
    public static function getCountryList()
    {
        $countries = array();
        foreach (Lib\PDOWrapper::call("getCountries", "") as $lcid) {
            $countries[] = Common\Lib\ModelFactory::buildModel('Country', $lcid);
        }
        return $countries;
    }
    
    public static function getCountriesByPattern($pattern)
    {
        $countries = array();
    
        $args = Lib\PDOWrapper::cleanseNullOrWrapStr($pattern);
        $result = Lib\PDOWrapper::call("getCountriesByPattern", $args);
        foreach ($result as $country) {
            $countries[] = Common\Lib\ModelFactory::buildModel('Country', $country);
        }
        return $countries;
    }
}