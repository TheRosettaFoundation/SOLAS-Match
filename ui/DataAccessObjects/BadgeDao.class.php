<?php

require_once 'Common/lib/APIHelper.class.php';

class BadgeDao
{
    private $client;
    private $siteApi;

    public function __construct()
    {
        $this->client = new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi = Settings::get("site.api");
    }

    public function getBadge($params)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/badges";
        
        $id = null;
        $name = null;
        if (isset($params['id'])) {
            $id = $params['id'];
            $request = "$request/$id";
        }
        
        $response = $this->client->call($request);
        if (!is_null($id)) {
            $ret = $this->client->cast("Badge", $response);
        } else {
            $ret = $this->client->cast(array("Badge"), $response);
        }
        
        return $ret;
    }

    public function find($params)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/badges/{$params['id']}";
        $response = $this->client->call($request);
        $ret = $this->client->cast("Badge", $response);
        return $ret;
    }

    public function getBadges()
    {
        $ret = null;
        $request = "{$this->siteApi}v0/badges";
        $response = $this->client->call($request);
        $ret = $this->client->cast(array("Badge"), $response);
        return $ret;
    }

    public function getUserWithBadge($badgeId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/badges/$badgeId/users";
        $response = $this->client->call($request);
        $ret = $this->client->cast(array("User"), $response);
        return $ret;
    }

    public function createBadge($badge)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/badges";
        $response = $this->client->call($request, HTTP_Request2::METHOD_POST, $badge);
        $ret = $this->client->cast("Badge", $response);
        return $ret;
    }

    public function updateBadge($badge)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/badges/{$badge->getId()}";
        $response = $this->client->call($request, HTTP_Request2::METHOD_PUT, $badge);
        $ret = $this->client->cast("Badge", $response);
        return $ret;
    }

    public function deleteBadge($badgeId)
    {
        $request = "{$this->siteApi}v0/badges/$badgeId";
        $response = $this->client->call($request, HTTP_Request2::METHOD_DELETE);
    }
}
