<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/BaseDao.php";

class BadgeDao extends BaseDao
{
    public function __construct()
    {
        $this->client = new Common\Lib\APIHelper(Common\Lib\Settings::get("ui.api_format"));
        $this->siteApi = Common\Lib\Settings::get("site.api");
    }

    public function getBadge($id = null, $title = null, $discription = null)
    {
        $request = "{$this->siteApi}v0/badges/$id";
        $response =$this->client->call("Badge", $request);
        return $response;
    }

    public function getBadges()
    {
        $request = "{$this->siteApi}v0/badges";
        $response =$this->client->call(array("Badge"), $request);
        return $response;
    }

    public function getUserWithBadge($badgeId)
    {
        
        $request = "{$this->siteApi}v0/badges/$badgeId/users";
        $response =$this->client->call(array("User"), $request);
        return $response;
    }

    public function createBadge($badge)
    {
        $request = "{$this->siteApi}v0/badges";
        $response = $this->client->call("Badge", $request, Common\Enums\HttpMethodEnum::POST, $badge);
        return $response;
    }

    public function updateBadge($badge)
    {
        $request = "{$this->siteApi}v0/badges/{$badge->getId()}";
        $response =$this->client->call("Badge", $request, Common\Enums\HttpMethodEnum::PUT, $badge);
        return $response;
    }

    public function deleteBadge($badgeId)
    {
        $request = "{$this->siteApi}v0/badges/$badgeId";
        $response =$this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
        return $response;
    }
}
