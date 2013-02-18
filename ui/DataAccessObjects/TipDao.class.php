<?php

class TipDao
{
    public function getTip()
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tips";
        $ret = $client->call($request);
        return $ret;
    }
}
