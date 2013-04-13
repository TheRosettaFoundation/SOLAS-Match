<?php

require_once __DIR__."/../../Common/lib/APIHelper.class.php";

class ProjectDao
{
    private $client;
    private $siteApi;
    
    public function __construct()
    {
        $this->client = new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi = Settings::get("site.api");
    }

    public function getProject($id)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$id";
        if (!is_null($id)) {
            $ret = $this->client->castCall("Project", $request);
            if($tags=$this->getProjectTags($id)){
                foreach($tags as $tag){
                    $ret->addTag($tag);
                }
            }
        }

        return $ret;
    }

    public function getProjectTasks($projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$projectId/tasks";
        $ret = $this->client->castCall(array("Task"), $request);
        return $ret;
    }

    public function getProjectTags($projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$projectId/tags";
        $ret = $this->client->castCall(array("Tag"), $request);
        return $ret;
    }

    public function createProject($project)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects";
        $ret = $this->client->castCall("Project", $request, HttpMethodEnum::POST, $project);
        return $ret;
    }

    public function updateProject($project)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/{$project->getId()}";
        $ret = $this->client->castCall("Project", $request, HttpMethodEnum::PUT, $project);
        return $ret;
    }

    public function archiveProject($projectId, $userId)
    {
        $request = "{$this->siteApi}v0/projects/archiveProject/$projectId/user/$userId";  
        $ret = $this->client->castCall("ArchivedProject", $request, HttpMethodEnum::PUT);
        return $ret;
    }

    public function getArchivedProject($id)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/archivedProjects/$id";
        $ret = $this->client->castCall(array("ArchivedProject"), $request);

        if (!is_null($id) && is_array($ret)) {
            $ret = $ret[0];
        }
        return $ret;
    }
    
    public function getArchivedProjects()
    {
        $ret = null;
        $request = "{$this->siteApi}v0/archivedProjects";
        $ret = $this->client->castCall(array("ArchivedProject"), $request);
        return $ret;
    }
    
    public function saveProjectFile($id, $data, $filename,$userId)
    {
        $ret = null;
        $filename = urlencode($filename);
        $url = "{$this->siteApi}v0/projects/$id/file/$filename/$userId";
        $ret = $this->client->castCall(null, $url, HttpMethodEnum::PUT, null, null, $data);       
        return $ret;
    }
    
    public function getProjectFile($project_id)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$project_id/file";
        $response = $this->client->castCall(null, $request);
        return $response;        
    }
    
    public function getProjectFileInfo($project_id)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$project_id/info";     
        $ret = $this->client->castCall("ProjectFile", $request);
        return $ret;        
    }
}
