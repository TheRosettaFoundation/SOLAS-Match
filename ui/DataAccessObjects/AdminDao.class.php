<?php

require_once __DIR__."/../../Common/lib/APIHelper.class.php";

class AdminDao
{
    private $client;
    private $siteApi;

    public function __construct()
    {
        $this->client = new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi = Settings::get("site.api");
    }

    public function getSiteAdmins()
    {       
        $request = "{$this->siteApi}v0/admins";
        $response = $this->client->call(array("User"), $request);
        return $response;
    }
    
    public function getOrgAdmins($orgId)
    {       
        $request = "{$this->siteApi}v0/admins/getOrgAdmins/$orgId";
        $response = $this->client->call(array("User"), $request);
        return $response;
    }

    public function createSiteAdmin($userId)
    {
        $request = "{$this->siteApi}v0/admins/$userId";
        $this->client->call(null, $request, HttpMethodEnum::PUT);
    }
    
    public function removeSiteAdmin($userId)
    {
        $request = "{$this->siteApi}v0/admins/$userId";
        $this->client->call(null, $request, HttpMethodEnum::DELETE);
    }
    
    public function createOrgAdmin($userId, $orgId)
    {
        $request = "{$this->siteApi}v0/admins/createOrgAdmin/$orgId/$userId";
        $this->client->call(null, $request, HttpMethodEnum::PUT);
    }
    
    public function removeOrgAdmin($userId, $orgId)
    {
        $request = "{$this->siteApi}v0/admins/removeOrgAdmin/$orgId/$userId";
        $this->client->call(null, $request, HttpMethodEnum::DELETE);
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
        $response = $this->client->call(array("BannedUser"), $request);
        return $response;
    }    
    
    public function getBannedOrgs()
    {
        $request = "{$this->siteApi}v0/admins/getBannedOrgs";
        $response = $this->client->call(array("BannedOrganisation"), $request);
        return $response;
    }
    
    public function banUser($bannedUser)
    {
        $request = "{$this->siteApi}v0/admins/banUser";
        $this->client->call(null, $request, HttpMethodEnum::POST, $bannedUser);
    }
    
    public function banOrg($bannedOrg)
    {
        $request = "{$this->siteApi}v0/admins/banOrg";
        $this->client->call(null, $request, HttpMethodEnum::POST, $bannedOrg);
    }
    
    public function unBanUser($userId)
    {
        $request = "{$this->siteApi}v0/admins/unBanUser/$userId";
        $this->client->call(null, $request, HttpMethodEnum::DELETE);
    }
    
    public function unBanOrg($orgId)
    {
        $request = "{$this->siteApi}v0/admins/unBanOrg/$orgId";
        $this->client->call(null, $request, HttpMethodEnum::DELETE);
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
