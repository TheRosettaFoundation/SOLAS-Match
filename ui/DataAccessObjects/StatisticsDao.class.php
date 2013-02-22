<?php

require_once 'Common/lib/APIHelper.class.php';

class StatisticsDao
{
    private $client;
    private $siteApi;

    public function __construct()
    {
        $this->client = new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi = Settings::get("site.api");
    }

    public function getStats()
    {
        $ret = null;
        $request = "{$this->siteApi}/v0/stats";
        $response = $this->client->call($request);
        $ret = $this->client->cast(array("Statistic"), $response);
        return $ret;
    }
}
