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

    public function getOrganisationExtendedProfile($id)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgextended/$id";
        $ret = $this->client->call("\SolasMatch\Common\Protobufs\Models\OrganisationExtendedProfile", $request);
        return $ret;
    }

    public function getSubscription($org_id)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/subscription/$org_id";
        $ret = $this->client->call(null, $request);
        return $ret;
    }

    public function updateSubscription($org_id, $level, $spare, $start_date, $comment)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/subscription/$org_id/level/$level/spare/$spare/start_date/" . urlencode($start_date);
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::POST, $comment);
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

    public function getOrgArchivedProjects($orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/archivedProjects";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\ArchivedProject"), $request);
        return $ret;
    }

    public function getOrgBadges($orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/badges";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\Badge"), $request);
        return $ret;
    }

    public function getOrgMembers($orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/members";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\User"), $request);
        return $ret;
    }

    public function getMembershipRequests($orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/requests";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\MembershipRequest"), $request);
        return $ret;
    }

    public function isMember($orgId, $userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/isMember/$orgId/$userId";
        $ret = $this->client->call(null, $request);
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

    public function updateOrgExtendedProfile($org)
    {
        $request = "{$this->siteApi}v0/orgextended/{$org->getId()}";
        $this->client->call(
            "\SolasMatch\Common\Protobufs\Models\OrganisationExtendedProfile",
            $request,
            Common\Enums\HttpMethodEnum::PUT,
            $org
        );
    }

    public function deleteOrg($orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
        return $ret;
    }

    public function createMembershipRequest($orgId, $userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/requests/$userId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::POST);
        return $ret;
    }

    public function acceptMembershipRequest($orgId, $userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/requests/$userId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
        return $ret;
    }

    public function addMember($email, $orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/addMember/".urlencode($email)."/$orgId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
        return $ret;
    }

    public function rejectMembershipRequest($orgId, $userId)
    {
        $request = "{$this->siteApi}v0/orgs/$orgId/requests/$userId";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
    }

    public function getUsersTrackingOrg($orgisationId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgisationId/trackingUsers";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\User"), $request);
        return $ret;
    }
}
