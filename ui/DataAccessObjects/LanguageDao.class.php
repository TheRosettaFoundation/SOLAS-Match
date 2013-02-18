<?php

class LanguageDao
{
    private $client;
    private $siteApi;
    
    public function __construct()
    {
        $this->client = new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi = Settings::get("site.api");
    }

    public function getLanguage($params)
    {
        $ret = null;
        $request = "$this->siteApi/v0/languages";
        $id = null;
        $code = null;
        if (isset($params['id'])) {
            $id = $params['id'];
            $request = "$request/$id";
        } elseif (isset($params['code'])) {
            $code = $params['code'];
            $request = "$request/getByCode/$code";
        }
        
        $response = $this->client->call($request);
        $ret = $this->client->cast(array("Language"), $response);
        
        if ((!is_null($id) || !is_null($code)) && is_array($ret)) {
            $ret = $ret[0];
        }
        
        return $ret;
    }
}
