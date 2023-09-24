<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/BaseDao.php";

class AdminDao extends BaseDao
{
    public function __construct()
    {
        $this->client = new Common\Lib\APIHelper(Common\Lib\Settings::get('ui.api_format'));
        $this->siteApi = Common\Lib\Settings::get('site.api');
    }

    public function getSiteAdmins()
    {
        $ret = [];
        $result = LibAPI\PDOWrapper::call('getAdmin', '0,0,' . LINGUIST);
        if ($result) {
            foreach ($result as $user) {
                $ret[] = Common\Lib\ModelFactory::buildModel('User', $user);
            }
        }
        return $ret;
    }
    
    public function getOrgAdmins($orgId)
    {
        $ret = [];
        $result = LibAPI\PDOWrapper::call('getAdmin', '0,' . LibAPI\PDOWrapper::cleanse($orgId) . ',0');
        if ($result) {
            foreach ($result as $user) {
                $ret[] = Common\Lib\ModelFactory::buildModel('User', $user);
            }
        }
        return $ret;
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
    
    public function revokeTaskFromUser($taskId, $userId)
    {
        $request = "{$this->siteApi}v0/admins/revokeTask/$taskId/$userId";
        $response = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
        return $response;
    }
}
