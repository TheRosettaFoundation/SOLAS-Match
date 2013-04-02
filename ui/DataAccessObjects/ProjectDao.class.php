<?php

require_once 'Common/lib/APIHelper.class.php';

class ProjectDao
{
    private $client;
    private $siteApi;
    
    public function __construct()
    {
        $this->client = new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi = Settings::get("site.api");
    }

    public function getProject($params)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects";
        
        $id = null;
        if (isset($params['id'])) {
            $id = $params['id'];
            $request = "$request/{$params['id']}";
        }

        $response = $this->client->call($request);
        if (!is_null($id)) {
            $ret = $this->client->cast("Project", $response);
        } else {
            $ret = $this->client->cast(array("Project"), $response);
        }

        return $ret;
    }

    public function getProjectTasks($projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$projectId/tasks";
        $response = $this->client->call($request);
        $ret = $this->client->cast(array("Task"), $response);
        return $ret;
    }

    public function getProjectGraph($projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/buildGraph/$projectId";
        $response = $this->client->call($request);
        $ret = $this->client->cast("WorkflowGraph", $response);
        return $ret; 
    }

    public function getProjectTags($projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$projectId/tags";
        $response = $this->client->call($request);
        $ret = $this->client->cast(array("Tag"), $response);
        return $ret;
    }

    public function createProject($project)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects";
        $response = $this->client->call($request, HTTP_Request2::METHOD_POST, $project);
        $ret = $this->client->cast("Project", $response);
        return $ret;
    }

    public function updateProject($project)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/{$project->getId()}";
        $response = $this->client->call($request, HTTP_Request2::METHOD_PUT, $project);
        $ret = $this->client->cast("Project", $response);
        return $ret;
    }

    public function archiveProject($projectId, $userId)
    {
        $request = "{$this->siteApi}v0/projects/archiveProject/$projectId/user/$userId";
        return $this->client->call($request, HTTP_Request2::METHOD_PUT);

    }

    public function getArchivedProject($params)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/archivedProjects";
        
        $id = null;
        if (isset($params['id'])) {
            $id = $params['id'];
            $request = "$request/$id";
        }

        $response = $this->client->call($request);
        $ret = $this->client->cast(array("ArchivedProject"), $response);

        if (!is_null($id) && is_array($ret)) {
            $ret = $ret[0];
        }

        return $ret;
    }
    
    public function saveProjectFile($id, $data, $filename,$userId)
    {
        $ret = null;
        $filename = urlencode($filename);
        $url = "{$this->siteApi}v0/projects/$id/file/$filename/$userId";
        $ret = $this->client->call($url, HTTP_Request2::METHOD_PUT, null, null, $data);       
        return $ret;
    }
    
    public function getProjectFile($project_id)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$project_id/file";
        $response = $this->client->call($request);
        return $response;        
    }
    
    public function getProjectFileInfo($project_id)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$project_id/info";
        $response = $this->client->call($request);        
        $ret = $this->client->cast("ProjectFile", $response);
        return $ret;        
    }
}
