<?php

require_once 'Common/lib/APIHelper.class.php';

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
        if (!is_null($id) || !is_null($code)) {
            $ret = $this->client->cast("Language", $response);
        } else {
            $ret = $this->client->cast(array("Language"), $response);
        }
        
        return $ret;
    }
}
