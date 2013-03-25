<?php

require_once 'Common/lib/APIHelper.class.php';

class TagDao
{

    private $client;
    private $siteApi;

    public function __construct()
    {
        $this->client=new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi=Settings::get("site.api");
    }

    public function getTag($id, $limit = null)
    {
        $request="{$this->siteApi}v0/tags/$id";
        $args=$limit ? array("limit" => $limit) : null;
        $response=$this->client->castCall("Tag", $request, HTTP_Request2::METHOD_GET, null, $args);
        return $response;
    }

    public function getTags($limit = null)
    {
        $request="{$this->siteApi}v0/tags";
        $args=$limit ? array("limit" => $limit) : null;
        $response=$this->client->castCall(array("Tag"), $request, HTTP_Request2::METHOD_GET, null, $args);
        return $response;
    }

    public function getTagByLabel($label, $limit = null)
    {
        $request="{$this->siteApi}v0/tags/getByLabel/$label";
        $args=$limit ? array("limit" => $limit) : null;
        $response=$this->client->castCall("Tag", $request, HTTP_Request2::METHOD_GET, null, $args);
        return $response;
    }

    public function getTopTags($limit = null)
    {
        $request="{$this->siteApi}v0/tags/topTags";
        $args=$limit ? array("limit" => $limit) : null;
        $response=$this->client->castCall(array("Tag"), $request, HTTP_Request2::METHOD_GET, null, $args);
        return $response;
    }

    public function getTasksWithTag($tagId, $limit = null)
    {
        $args=$limit ? array("limit" => $limit) : null;
        $request="{$this->siteApi}v0/tags/$tagId/tasks";
        $response=$this->client->castCall(array("Task"), $request, HTTP_Request2::METHOD_GET, null, $args);
        return $response;
    }

    public function createTag($tag)
    {
        $request="{$this->siteApi}v0/tags";
        $response=$this->client->castCall("Tag", $request, HTTP_Request2::METHOD_POST, $tag);
        return $response;
    }

    public function updateTag($tag)
    {
        $request="{$this->siteApi}v0/tags/{$tag->getId()}";
        $response=$this->client->castCall("Tag", $request, HTTP_Request2::METHOD_PUT, $tag);
        return $response;
    }

    public function deleteTag($tagId)
    {
        $request="{$this->siteApi}v0/tags/{$tag->getId()}";
        $response=$this->client->castCall(null, $request, HTTP_Request2::METHOD_DELETE);
        return $response;
    }

}
