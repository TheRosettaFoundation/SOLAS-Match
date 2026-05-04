<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/BaseDao.php";

class OrganisationDao extends BaseDao
{
    public function __construct()
    {
        $this->client = new Common\Lib\APIHelper(Common\Lib\Settings::get("ui.api_format"));
        $this->siteApi = Common\Lib\Settings::get("site.api");
    }
    
    public function getOrganisation($id)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$id";
        $ret = $this->client->call("\SolasMatch\Common\Protobufs\Models\Organisation", $request);
        return $ret;
    }

    public function getOrganisationByName($name)
    {
        $ret = null;
        $name = urlencode($name);
        $request = "{$this->siteApi}v0/orgs/getByName/$name";
        $ret = $this->client->call("\SolasMatch\Common\Protobufs\Models\Organisation", $request);
        return $ret;
    }
    
    public function searchForOrgByName($name)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/searchByName/$name";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\Organisation"), $request);
        return $ret;
    }
    
    public function getOrganisations()
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\Organisation"), $request);
        return $ret;
    }

    public function getOrgProjects($orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/projects";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\Project"), $request);
        return $ret;
    }

    public function createOrg($org, $userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs";
        $ret = $this->client->call(
            "\SolasMatch\Common\Protobufs\Models\Organisation",
            $request,
            Common\Enums\HttpMethodEnum::POST,
            $org
        );
        switch($this->client->getResponseCode()) {
            default:
                return $ret;
        
            case Common\Enums\HttpStatusEnum::CONFLICT:
                throw new Common\Exceptions\SolasMatchException($ret, $this->client->getResponseCode());
                break;
        }
    }

    public function updateOrg($org)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/{$org->getId()}";
        $ret = $this->client->call(
            "\SolasMatch\Common\Protobufs\Models\Organisation",
            $request,
            Common\Enums\HttpMethodEnum::PUT,
            $org
        );
        
        switch($this->client->getResponseCode()) {
        
            default:
                return $ret;
        
            case Common\Enums\HttpStatusEnum::CONFLICT:
                throw new Common\Exceptions\SolasMatchException($ret, $this->client->getResponseCode());
                break;
        }
    }
}
