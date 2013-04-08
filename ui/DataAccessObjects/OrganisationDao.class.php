<?php

require_once __DIR__."/../../Common/lib/APIHelper.class.php";

class OrganisationDao
{
    private $client;
    private $siteApi;
    
    public function __construct()
    {
        $this->client = new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi = Settings::get("site.api");
    }
    
    public function getOrganisation($id)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$id";
        $ret = $this->client->castCall("Organisation", $request);
        return $ret;
    }
    
    public function getOrganisationByName($name)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/getByName/$name";
        $ret = $this->client->castCall("Organisation", $request);
        return $ret;       
    }
    
    public function searchForOrgByName($name)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/searchByName/$name";
        $ret = $this->client->castCall(array("Organisation"), $request);
        return $ret;       
    }
    
    public function getOrganisations()
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs";
        $ret = $this->client->castCall(array("Organisation"), $request);
        return $ret;
    }

    public function getOrgProjects($orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/projects";
        $ret = $this->client->castCall(array("Project"), $request);
        return $ret;
    }

    public function getOrgArchivedProjects($orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/archivedProjects";
        $ret = $this->client->castCall(array("ArchivedProject"), $request);
        return $ret;
    }

    public function getOrgBadges($orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/badges";
        $ret = $this->client->castCall(array("Badge"), $request);
        return $ret;
    }

    public function getOrgMembers($orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/members";
        $ret = $this->client->castCall(array("User"), $request);
        return $ret;
    }

    public function getMembershipRequests($orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/requests";
        $ret = $this->client->castCall(array("MembershipRequest"), $request);
        return $ret;
    }

    public function getOrgTasks($orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/tasks";
        $ret = $this->client->castCall(array("Task"), $request);
        return $ret;
    }

    public function isMember($orgId, $userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/isMember/$orgId/$userId";
        $ret = $this->client->castCall(null, $request);
        return $ret;
    }

    public function createOrg($org)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs";
        $ret = $this->client->castCall("Organisation", $request, HTTP_Request2::METHOD_POST, $org);
        return $ret;
    }

    public function updateOrg($org)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/{$org->getId()}";
        $ret = $this->client->castCall(array("Organisation"), $request, HTTP_Request2::METHOD_PUT, $org);
        return $ret;
    }

    public function deleteOrg($orgId)
    {
        $request = "{$this->siteApi}v0/orgs/$orgId";
        $this->client->castCall(null,$request, HTTP_Request2::METHOD_DELETE);
    }

    public function createMembershipRequest($orgId, $userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/requests/$userId";
        $ret = $this->client->castCall(null, $request, HTTP_Request2::METHOD_POST);
        return $ret;
    }

    public function acceptMembershipRequest($orgId, $userId)
    {
        $request = "{$this->siteApi}v0/orgs/$orgId/requests/$userId";
        $this->client->castCall(null, $request, HTTP_Request2::METHOD_PUT);
    }

    public function rejectMembershipRequest($orgId, $userId)
    {
        $request = "{$this->siteApi}v0/orgs/$orgId/requests/$userId";
        $this->client->castCall(null, $request, HTTP_Request2::METHOD_DELETE);
    }
}
