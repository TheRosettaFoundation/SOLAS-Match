<?php

require_once __DIR__."/../../Common/lib/APIHelper.class.php";

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
        $response=$this->client->castCall("Tag", $request, HttpMethodEnum::GET, null, $args);
        return $response;
    }

    public function getTags($limit = null)
    {
        $request="{$this->siteApi}v0/tags";
        $args=$limit ? array("limit" => $limit) : null;
        $response=$this->client->castCall(array("Tag"), $request, HttpMethodEnum::GET, null, $args);
        return $response;
    }

    public function getTagByLabel($label, $limit = null)
    {
        $request="{$this->siteApi}v0/tags/getByLabel/$label";
        $args=$limit ? array("limit" => $limit) : null;
        $response=$this->client->castCall("Tag", $request, HttpMethodEnum::GET, null, $args);
        return $response;
    }

    public function getTopTags($limit = null)
    {
        $request="{$this->siteApi}v0/tags/topTags";
        $args=$limit ? array("limit" => $limit) : null;
        $response=$this->client->castCall(array("Tag"), $request, HttpMethodEnum::GET, null, $args);
        return $response;
    }

    public function getTasksWithTag($tagId, $limit = null)
    {
        $args=$limit ? array("limit" => $limit) : null;
        $request="{$this->siteApi}v0/tags/$tagId/tasks";
        $response=$this->client->castCall(array("Task"), $request, HttpMethodEnum::GET, null, $args);
        return $response;
    }

    public function createTag($tag)
    {
        $request="{$this->siteApi}v0/tags";
        $response=$this->client->castCall("Tag", $request, HttpMethodEnum::POST, $tag);
        return $response;
    }

    public function updateTag($tag)
    {
        $request="{$this->siteApi}v0/tags/{$tag->getId()}";
        $response=$this->client->castCall("Tag", $request, HttpMethodEnum::PUT, $tag);
        return $response;
    }

    public function deleteTag($tagId)
    {
        $request="{$this->siteApi}v0/tags/{$tag->getId()}";
        $response=$this->client->castCall(null, $request, HttpMethodEnum::DELETE);
        return $response;
    }

}
