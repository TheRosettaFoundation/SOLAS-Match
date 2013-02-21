<?php

require_once 'Common/lib/APIHelper.class.php';

class TipDao
{
    private $client;
    private $siteApi;
    
    public function __construct()
    {
        $this->client = new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi = Settings::get("site.api");
    }

    public function getTip()
    {
        $ret = null;
        $request = "$this->siteApi/v0/tips";
        $ret = $this->client->call($request);
        return $ret;
    }
}
