<?php

require_once __DIR__."/../../Common/lib/APIHelper.class.php";

class BadgeDao
{
    private $client;
    private $siteApi;

    public function __construct()
    {
        $this->client = new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi = Settings::get("site.api");
    }

    public function getBadge($id=null,$title=null,$discription=null)
    {
       
        $request = "{$this->siteApi}v0/badges/$id";
        $response =$this->client->castCall("Badge", $request);
        return $response;
    }

    public function getBadges()
    {
        $request = "{$this->siteApi}v0/badges";
        $response =$this->client->castCall(array("Badge"), $request);
        return $response;
    }

    public function getUserWithBadge($badgeId)
    {
        
        $request = "{$this->siteApi}v0/badges/$badgeId/users";
        $response =$this->client->castCall(array("User"), $request);
        return $response;
    }

    public function createBadge($badge)
    {
        $request = "{$this->siteApi}v0/badges";
        $response =$this->client->castCall("Badge", $request,HTTP_Request2::METHOD_POST, $badge);
        return $response;
    }

    public function updateBadge($badge)
    {
        $request = "{$this->siteApi}v0/badges/{$badge->getId()}";
        $response =$this->client->castCall("Badge", $request, HTTP_Request2::METHOD_PUT, $badge);
        return $response;
    }

    public function deleteBadge($badgeId)
    {
        $request = "{$this->siteApi}v0/badges/$badgeId";
        $response =$this->client->castCall(null, $request, HTTP_Request2::METHOD_DELETE);
        return $response;
    }
}
