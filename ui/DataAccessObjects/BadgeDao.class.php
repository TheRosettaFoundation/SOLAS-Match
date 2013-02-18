<?php

class BadgeDao
{
    public function getBadge($params)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/badges";
        
        $id = null;
        if (isset($params['id'])) {
            $id = $params['id'];
            $request = "$request/$id";
        }
       
        $response = $client->call($request);
        $ret = $client->cast(array("Badge"), $response);
       
        if (!is_null($id) && is_array($ret)) {
            $ret = $ret[0];
        }
       
        return $ret;
    }

    public function getUserWithBadge($badgeId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/badges/$badgeId/users";
        $response = $client->call($request);
        $ret = $client->cast(array("User"), $response);
        return $ret;
    }

    public function createBadge($badge);
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/badges";
        $response = $client->call($request, HTTP_Request2::METHOD_POST, $badge);
        $ret = $client->cast("Badge", $response);
        return $ret;
    }

    public function updateBadge($badge)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/badges/{$badge->getId()}";
        $response = $client->call($request, HTTP_Request2::METHOD_PUT, $badge);
        $ret = $client->cast("Badge", $response);
        return $ret;
    }

    public function deleteBadge($badgeId)
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/badges/$badgeId";
        $response = $client->call($request, HTTP_Request2::METHOD_DELETE, $badge);
    }
}
