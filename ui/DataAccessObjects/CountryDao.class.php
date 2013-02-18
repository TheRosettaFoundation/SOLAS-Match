<?php

class CountryDao
{
    public function getCountry($params)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/countries";
        
        $id = null;
        $code = null;
        if (isset($params['id'])) {
            $id = $params['id'];
            $request = "$request/$id";
        } elseif (isset($params['code'])) {
            $code = $params['code'];
            $request = "$request/getByCode/$code";
        }
        
        $response = $client->call($request);
        $ret = $client->cast(array("Country"), $response);
        
        if ((!is_null($id) || !is_null($code)) && is_array($ret)) {
            $ret = $ret[0];
        }
        
        return $ret;
    }
}
