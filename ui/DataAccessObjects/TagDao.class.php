<?php

class TagDao
{
    private $client;
    private $siteApi;
    
    public function __construct()
    {
        $this->client = new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi = Settings::get("site.api");
    }

    public function getTag($params, $limit = null)
    {
        $ret = null;
        $request = "{$this->siteApi}/v0/tags";
        
        $id = null;
        $label = null;
        if (isset($params['id'])) {
            $id = $params['id'];
            $request = "$request/$id";
        } elseif (isset($params['label'])) {
            $label = $params['label'];
            $request = "$request/getByLabel/$label";
        }

        $args = null;
        if ($limit) {
            $args = array("limit" => $limit);
        }
        
        $response = $this->client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        if (!is_null($id) || !is_null($label)) {
            $ret = $this->client->cast("Tag", $response);
        } else {
            $ret = $this->client->cast(array("Tag"), $response);
        }
        
        return $ret;
    }

    public function getTopTags($limit = null)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/tags/topTags";

        $args = null;
        if ($limit) {
            $args = array("limit" => $limit);
        }

        $response = $this->client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        $ret = $this->client->cast(array("Tag"), $response);
        return $ret;
    }

    public function getTasksWithTag($tagId, $limit = null)
    {
        $ret = null;
        $request = "{$this->siteApi}/v0/tags/$tagId/tasks";

        $args = null;
        if ($limit) {
            $args = array("limit" => $limit);
        }

        $response = $this->client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        $ret = $this->client->cast(array("Task"), $response);
        return $ret;
    }

    public function createTag($tag)
    {
        $ret = null;
        $request = "{$this->siteApi}/v0/tags";
        $response = $this->client->call($request, HTTP_Request2::METHOD_POST, $tag);
        $ret = $this->client->cast("Tag", $response);
        return $ret;
    }

    public function updateTag($tag)
    {
        $ret = null;
        $request = "{$this->siteApi}/v0/tags/{$tag->getId()}";
        $response = $this->client->call($request, HTTP_Request2::METHOD_PUT, $tag);
        $ret = $this->client->cast("Tag", $response);
        return $ret;
    }

    public function deleteTag($tagId)
    {
        $ret = null;
        $request = "{$this->siteApi}/v0/tags/{$tag->getId()}";
        $response = $this->client->call($request, HTTP_Request2::METHOD_DELETE);
        $ret = $this->client->cast("Tag", $response);
        return $ret;
    }
}
