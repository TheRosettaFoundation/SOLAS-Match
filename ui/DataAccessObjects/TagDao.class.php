<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/BaseDao.php";

class TagDao extends BaseDao
{
    public function __construct()
    {
        $this->client = new Common\Lib\APIHelper(Common\Lib\Settings::get("ui.api_format"));
        $this->siteApi = Common\Lib\Settings::get("site.api");
    }

    public function getTag($id, $limit = null)
    {
        $request = "{$this->siteApi}v0/tags/$id";
        $args = $limit ? array("limit" => $limit) : null;
        $response = $this->client->call(
            "\SolasMatch\Common\Protobufs\Models\Tag",
            $request,
            Common\Enums\HttpMethodEnum::GET,
            null,
            $args
        );
        return $response;
    }

    public function getTags($limit = null)
    {
        $request = "{$this->siteApi}v0/tags";
        $args = $limit ? array("limit" => $limit) : null;
        $response = $this->client->call(
            array("\SolasMatch\Common\Protobufs\Models\Tag"),
            $request,
            Common\Enums\HttpMethodEnum::GET,
            null,
            $args
        );
        return $response;
    }

    public function getTagByLabel($label, $limit = null)
    {
        $request = "{$this->siteApi}v0/tags/getByLabel/$label";
        $args = $limit ? array("limit" => $limit) : null;
        $response = $this->client->call(
            "\SolasMatch\Common\Protobufs\Models\Tag",
            $request,
            Common\Enums\HttpMethodEnum::GET,
            null,
            $args
        );
        return $response;
    }

    public function searchForTag($name)
    {
        $request = "{$this->siteApi}v0/tags/search/$name";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\Tag"), $request);
        return $ret;
    }

    public function getTopTags($limit = null)
    {
        $args = array();
        $args['topTags'] = true;
        $args['limit'] = $limit ? $limit : null;
        $topTags = Common\Lib\CacheHelper::getCached(
            Common\Lib\CacheHelper::TOP_TAGS,
            Common\Enums\TimeToLiveEnum::QUARTER_HOUR,
            function ($params) {
                $request = "{$params[2]}v0/tags";
                return $params[1]->call(
                    array("\SolasMatch\Common\Protobufs\Models\Tag"),
                    $request,
                    Common\Enums\HttpMethodEnum::GET,
                    null,
                    $params[0]
                );
            },
            array($args, $this->client, $this->siteApi)
        );
        return $topTags;
    }

    public function getTasksWithTag($tagId, $limit = null)
    {
        $args = $limit ? array("limit" => $limit) : null;
        $request = "{$this->siteApi}v0/tags/$tagId/tasks";
        $response = $this->client->call(
            array("\SolasMatch\Common\Protobufs\Models\Task"),
            $request,
            Common\Enums\HttpMethodEnum::GET,
            null,
            $args
        );
        return $response;
    }

    public function createTag($tag)
    {
        $request = "{$this->siteApi}v0/tags";
        $response = $this->client->call(
            "\SolasMatch\Common\Protobufs\Models\Tag",
            $request,
            Common\Enums\HttpMethodEnum::POST,
            $tag
        );
        return $response;
    }

    public function updateTag($tag)
    {
        $request = "{$this->siteApi}v0/tags/{$tag->getId()}";
        $response = $this->client->call(
            "\SolasMatch\Common\Protobufs\Models\Tag",
            $request,
            Common\Enums\HttpMethodEnum::PUT,
            $tag
        );
        return $response;
    }

    public function deleteTag($tagId)
    {
        $request = "{$this->siteApi}v0/tags/{$tag->getId()}";
        $response = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
        return $response;
    }
}
