<?php

class OrganisationDao
{
    public function getOrganisation($params)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/orgs";
        
        $id = null;
        $name = null;
        if (isset($params['id'])) {
            $id = $params['id'];
            $request = "$request/$id";
        } elseif (isset($params['name'])) {
            $code = $params['code'];
            $request = "$request/getByName/$name";
        }
        
        $response = $client->call($request);
        $ret = $client->cast(array("Organisation"), $response);
        
        if ((!is_null($id) || !is_null($name)) && is_array($ret)) {
            $ret = $ret[0];
        }
        
        return $ret;
    }

    public function getOrgProjects($orgId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/orgs/$orgId/projects";
        $response = $client->call($request);
        $ret = $client->cast(array("Project"), $response);
        return $ret;
    }

    public function getOrgArchivedProjects($orgId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/orgs/$orgId/archivedProjects";
        $response = $client->call($request);
        $ret = $client->cast(array("ArchivedProject"), $response);
        return $ret;
    }

    public function getOrgBadges($orgId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/orgs/$orgId/badges";
        $response = $client->call($request);
        $ret = $client->cast(array("Badge"), $response);
        return $ret;
    }

    public function getOrgMembers($orgId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/orgs/$orgId/members";
        $response = $client->call($request);
        $ret = $client->cast(array("User"), $response);
        return $ret;
    }

    public function getMembershipRequests($orgId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/orgs/$orgId/requests";
        $response = $client->call($request);
        $ret = $client->cast(array("MembershipRequest"), $response);
        return $ret;
    }

    public function getOrgTasks($orgId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/orgs/$orgId/tasks";
        $response = $client->call($request);
        $ret = $client->cast(array("Task"), $response);
        return $ret;
    }

    public function isMember($orgId, $userId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/orgs/isMember/$orgId/$userId";
        $ret = $client->call($request);
        return $ret;
    }

    public function createOrg($org)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/orgs";
        $response = $client->call($request, HTTP_Request2::METHOD_POST, $org);
        $ret = $client->cast(array("Organisation"), $response);
        return $ret;
    }

    public function updateOrg($org)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/orgs/{$org->getId()}";
        $response = $client->call($request, HTTP_Request2::METHOD_PUT, $org);
        $ret = $client->cast(array("Organisation"), $response);
        return $ret;
    }

    public function deleteOrg($orgId)
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/orgs/{$org->getId()}";
        $client->call($request, HTTP_Request2::METHOD_DELETE);
    }

    public function createMembershipRequest($orgId, $userId)
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/orgs/$orgId/requests/$userId";
        $client->call($request, HTTP_Request2::METHOD_POST);
    }

    public function acceptMembershipRequest($orgId, $userId)
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/orgs/$orgId/requests/$userId";
        $client->call($request, HTTP_Request2::METHOD_PUT);
    }

    public function rejectMembershipRequest($orgId, $userId)
    {
    }
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/orgs/$orgId/requests/$userId";
        $client->call($request, HTTP_Request2::METHOD_DELETE);
    }
}
