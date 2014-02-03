<?php

require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/BaseDao.php";

class TagDao extends BaseDao
{

    public function __construct()
    {
        $this->client=new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi=Settings::get("site.api");
    }

    public function getTag($id, $limit = null)
    {
        $request="{$this->siteApi}v0/tags/$id";
        $args=$limit ? array("limit" => $limit) : null;
        $response=$this->client->call("Tag", $request, HttpMethodEnum::GET, null, $args);
        return $response;
    }

    public function getTags($limit = null)
    {
        $request="{$this->siteApi}v0/tags";
        $args=$limit ? array("limit" => $limit) : null;
        $response=$this->client->call(array("Tag"), $request, HttpMethodEnum::GET, null, $args);
        return $response;
    }

    public function getTagByLabel($label, $limit = null)
    {
        $request="{$this->siteApi}v0/tags/getByLabel/$label";
        $args=$limit ? array("limit" => $limit) : null;
        $response=$this->client->call("Tag", $request, HttpMethodEnum::GET, null, $args);
        return $response;
    }

    public function searchForTag($name)
    {
        $request = "{$this->siteApi}v0/tags/search/$name";
        $ret = $this->client->call(array("Tag"), $request);
        return $ret;
    }

    public function getTopTags($limit = null)
    {
        $args=$limit ? array("limit" => $limit) : null;
        $topTags = CacheHelper::getCached(
            CacheHelper::TOP_TAGS,
            TimeToLiveEnum::QUARTER_HOUR,
            function ($args) {
                $request = "{$args[2]}v0/tags/topTags";
                return $args[1]->call(array("Tag"), $request, HttpMethodEnum::GET, null, $args[0]);
            },
            array($args, $this->client, $this->siteApi)
        );
        return $topTags;
    }

    public function getTasksWithTag($tagId, $limit = null)
    {
        $args=$limit ? array("limit" => $limit) : null;
        $request="{$this->siteApi}v0/tags/$tagId/tasks";
        $response=$this->client->call(array("Task"), $request, HttpMethodEnum::GET, null, $args);
        return $response;
    }

    public function createTag($tag)
    {
        $request="{$this->siteApi}v0/tags";
        $response=$this->client->call("Tag", $request, HttpMethodEnum::POST, $tag);
        return $response;
    }

    public function updateTag($tag)
    {
        $request="{$this->siteApi}v0/tags/{$tag->getId()}";
        $response=$this->client->call("Tag", $request, HttpMethodEnum::PUT, $tag);
        return $response;
    }

    public function deleteTag($tagId)
    {
        $request="{$this->siteApi}v0/tags/{$tag->getId()}";
        $response=$this->client->call(null, $request, HttpMethodEnum::DELETE);
        return $response;
    }
}
