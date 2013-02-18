<?php

class ProjectDao
{
    public function getProject($params)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/projects";
        
        $id = null;
        if (isset($params['id'])) {
            $id = $params['id'];
            $request = "$request/{$params['id']}";
        }

        $response = $client->call($request);
        $ret = $client->cast(array("Project"), $response);

        if (!is_null($id) && is_array($ret)) {
            $ret = $ret[0];
        }

        return $ret;
    }

    public function getProjectTasks($projectId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/projects/$projectId/tasks";
        $response = $client->call($request);
        $ret = $client->cast(array("Task"), $response);
        return $ret;
    }

    public function getProjectTags($projectId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/projects/$projectId/tags";
        $response = $client->call($request);
        $ret = $client->cast(array("Tag"), $response);
        return $ret;
    }

    public function createProject($project)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/projects";
        $response = $client->call($request, HTTP_Request2::METHOD_POST, $project);
        $ret = $client->cast("Project", $response);
        return $ret;
    }

    public function updateProject($project)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/projects/{$project->getId()}";
        $response = $client->call($request, HTTP_Request2::METHOD_PUT, $project);
        $ret = $client->cast("Project", $response);
        return $ret;
    }

    public function archiveProject($projectId, $userId)
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/projects/archiveProject/$projectId/user/$userId";
        $response = $client->call($request, HTTP_Request2::METHOD_PUT, $project);
    }

    public function getArchivedProject($params)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/archivedProjects";
        
        $id = null
        if (isset($params['id'])) {
            $id = $params['id'];
            $request = "$request/$id";
        }

        $response = $client->call($request);
        $ret = $client->cast(array("Project"), $response);

        if (!is_null($id) && is_array($ret)) {
            $ret = $ret[0];
        }

        return $ret;
    }
}
