<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/BaseDao.php";

class AdminDao extends BaseDao
{
    public function __construct()
    {
        $this->client = new Common\Lib\APIHelper(Common\Lib\Settings::get("ui.api_format"));
        $this->siteApi = Common\Lib\Settings::get("site.api");
    }

    public function getSiteAdmins()
    {
        $request = "{$this->siteApi}v0/admins";
        $response = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\User"), $request);
        return $response;
    }
    
    public function getOrgAdmins($orgId)
    {
        $request = "{$this->siteApi}v0/admins/getOrgAdmins/$orgId";
        $response = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\User"), $request);
        return $response;
    }

    public function createSiteAdmin($userId)
    {
        $request = "{$this->siteApi}v0/admins/$userId";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
    }
    
    public function removeSiteAdmin($userId)
    {
        $request = "{$this->siteApi}v0/admins/$userId";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
    }
    
    public function createOrgAdmin($userId, $orgId)
    {
        $request = "{$this->siteApi}v0/admins/createOrgAdmin/$orgId/$userId";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
    }
    
    public function removeOrgAdmin($userId, $orgId)
    {
        $request = "{$this->siteApi}v0/admins/removeOrgAdmin/$orgId/$userId";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
    }
    
    public function isSiteAdmin($userId)
    {
        $request = "{$this->siteApi}v0/admins/isSiteAdmin/$userId";
        $response = $this->client->call(null, $request);
        return $response;
    }
    
    public function isOrgAdmin($orgId, $userId)
    {
        $request = "{$this->siteApi}v0/admins/isOrgAdmin/$orgId/$userId";
        $response = $this->client->call(null, $request);
        return $response;
    }
    
    public function getBannedUsers()
    {
        $request = "{$this->siteApi}v0/admins/getBannedUsers";
        $response = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\BannedUser"), $request);
        return $response;
    }
    
    public function getBannedUser($userId)
    {
        $request = "{$this->siteApi}v0/admins/getBannedUser/$userId";
        $response = $this->client->call("\SolasMatch\Common\Protobufs\Models\BannedUser", $request);
        return $response;
    }
    
    public function getBannedOrgs()
    {
        $request = "{$this->siteApi}v0/admins/getBannedOrgs";
        $response = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\BannedOrganisation"), $request);
        return $response;
    }
    
    public function getBannedOrg($orgId)
    {
        $request = "{$this->siteApi}v0/admins/getBannedOrg/$orgId";
        $response = $this->client->call("\SolasMatch\Common\Protobufs\Models\BannedOrganisation", $request);
        return $response;
    }
    
    public function banUser($bannedUser)
    {
        $request = "{$this->siteApi}v0/admins/banUser";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::POST, $bannedUser);
    }
    
    public function banOrg($bannedOrg)
    {
        $request = "{$this->siteApi}v0/admins/banOrg";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::POST, $bannedOrg);
    }
    
    public function unBanUser($userId)
    {
        $request = "{$this->siteApi}v0/admins/unBanUser/$userId";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
    }
    
    public function unBanOrg($orgId)
    {
        $request = "{$this->siteApi}v0/admins/unBanOrg/$orgId";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
    }
    
    public function isUserBanned($userId)
    {
        $request = "{$this->siteApi}v0/admins/isUserBanned/$userId";
        $response = $this->client->call(null, $request);
        return $response;
    }
    
    public function isOrgBanned($orgId)
    {
        $request = "{$this->siteApi}v0/admins/isOrgBanned/$orgId";
        $response = $this->client->call(null, $request);
        return $response;
    }
}
