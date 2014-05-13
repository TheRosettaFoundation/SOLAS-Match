<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/BaseDao.php";

class CountryDao extends BaseDao
{

    public function __construct()
    {
        $this->client = new Common\Lib\APIHelper(Common\Lib\Settings::get("ui.api_format"));
        $this->siteApi = Common\Lib\Settings::get("site.api");
    }

    public function getCountry($id)
    {
        $request = "{$this->siteApi}v0/countries/$id";
        $response = $this->client->call("\SolasMatch\Common\Protobufs\Models\Country", $request);
        return $response;
    }

    public function getCountryByCode($code)
    {
        $request = "{$this->siteApi}v0/countries/getByCode/$code";
        $response = $this->client->call("\SolasMatch\Common\Protobufs\Models\Country", $request);
        return $response;
    }

    public function getCountries()
    {
        $countries = Common\Lib\CacheHelper::getCached(
            Common\Lib\CacheHelper::COUNTRIES,
            Common\Enums\TimeToLiveEnum::MONTH,
            function ($args) {
                $request = "{$args[1]}v0/countries";
                return $args[0]->call(array("\SolasMatch\Common\Protobufs\Models\Country"), $request);
            },
            array($this->client, $this->siteApi)
        );
        return $countries;
    }
}
