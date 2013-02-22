<?php

require_once 'Common/lib/APIHelper.class.php';

class OrganisationDao
{
    private $client;
    private $siteApi;
    
    public function __construct()
    {
        $this->client = new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi = Settings::get("site.api");
    }

    public function getOrganisation($params)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs";
        
        $id = null;
        $name = null;
        if (isset($params['id'])) {
            $id = $params['id'];
            $request = "$request/$id";
        } elseif (isset($params['name'])) {
            $name = $params['name'];
            $request = "$request/getByName/$name";
        }
       
        $response = $this->client->call($request);
        if (!is_null($id) || !is_null($name)) {
            $ret = $this->client->cast("Organisation", $response);
        } else {
            $ret = $this->client->cast(array("Organisation"), $response);
        }
        
        return $ret;
    }

    public function searchForOrg($name)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/getByName/$name";
        $response = $this->client->call($request);
        $ret = $this->client->cast(array("Organisation"), $response);
        return $ret;
    }

    public function getOrgProjects($orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/projects";
        $response = $this->client->call($request);
        $ret = $this->client->cast(array("Project"), $response);
        return $ret;
    }

    public function getOrgArchivedProjects($orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/archivedProjects";
        $response = $this->client->call($request);
        $ret = $this->client->cast(array("ArchivedProject"), $response);
        return $ret;
    }

    public function getOrgBadges($orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/badges";
        $response = $this->client->call($request);
        $ret = $this->client->cast(array("Badge"), $response);
        return $ret;
    }

    public function getOrgMembers($orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/members";
        $response = $this->client->call($request);
        $ret = $this->client->cast(array("User"), $response);
        return $ret;
    }

    public function getMembershipRequests($orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/requests";
        $response = $this->client->call($request);
        $ret = $this->client->cast(array("MembershipRequest"), $response);
        return $ret;
    }

    public function getOrgTasks($orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/tasks";
        $response = $this->client->call($request);
        $ret = $this->client->cast(array("Task"), $response);
        return $ret;
    }

    public function isMember($orgId, $userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/isMember/$orgId/$userId";
        $ret = $this->client->call($request);
        return $ret;
    }

    public function createOrg($org)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs";
        $response = $this->client->call($request, HTTP_Request2::METHOD_POST, $org);
        $ret = $this->client->cast("Organisation", $response);
        return $ret;
    }

    public function updateOrg($org)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/{$org->getId()}";
        $response = $this->client->call($request, HTTP_Request2::METHOD_PUT, $org);
        $ret = $this->client->cast(array("Organisation"), $response);
        return $ret;
    }

    public function deleteOrg($orgId)
    {
        $request = "{$this->siteApi}v0/orgs/{$org->getId()}";
        $this->client->call($request, HTTP_Request2::METHOD_DELETE);
    }

    public function createMembershipRequest($orgId, $userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/orgs/$orgId/requests/$userId";
        $ret = $this->client->call($request, HTTP_Request2::METHOD_POST);
        return $ret;
    }

    public function acceptMembershipRequest($orgId, $userId)
    {
        $request = "{$this->siteApi}v0/orgs/$orgId/requests/$userId";
        $this->client->call($request, HTTP_Request2::METHOD_PUT);
    }

    public function rejectMembershipRequest($orgId, $userId)
    {
        $request = "{$this->siteApi}v0/orgs/$orgId/requests/$userId";
        $this->client->call($request, HTTP_Request2::METHOD_DELETE);
    }
}
