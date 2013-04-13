<?php

require_once __DIR__."/../../Common/lib/APIHelper.class.php";

class CountryDao
{

    private $client;
    private $siteApi;

    public function __construct()
    {
        $this->client=new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi=Settings::get("site.api");
    }

    public function getCountry($id)
    {
        $request="{$this->siteApi}v0/countries/$id";
        $response=$this->client->call("Country", $request);
        return $response;
    }

    public function getCountryByCode($code)
    {
        $request="{$this->siteApi}v0/countries/getByCode/$code";
        $response=$this->client->call("Country", $request);
        return $response;
    }

    public function getCountries()
    {
        $request="{$this->siteApi}v0/countries";
        $response=$this->client->call(array("Country"), $request);
        return $response;
    }

}
