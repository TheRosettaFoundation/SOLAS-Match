<?php

class TagDao
{
    public function getTag($params, $limit = null)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tags";
        
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
        
        $response = $client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        $ret = $client->cast(array("Tag"), $response);
        
        if ((!is_null($id) || !is_null($label)) && is_array($ret)) {
            $ret = $ret[0];
        }
        
        return $ret;
    }

    public function getTopTags($limit = null)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/topTags";

        $args = null;
        if ($limit) {
            $args = array("limit" => $limit);
        }

        $response = $client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        $ret = $client->cast(array("Tag"), $response);
        return $ret;
    }

    public function getTasksWithTag($tagId, $limit = null)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tags/$tagId/tasks";

        $args = null;
        if ($limit) {
            $args = array("limit" => $limit);
        }

        $response = $client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        $ret = $client->cast(array("Task"), $response);
        return $ret;
    }

    public function createTag($tag)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tags";
        $response = $client->call($request, HTTP_Request2::METHOD_POST, $tag);
        $ret = $client->cast("Tag", $response);
        return $ret;
    }

    public function updateTag($tag)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tags/{$tag->getId()}";
        $response = $client->call($request, HTTP_Request2::METHOD_PUT, $tag);
        $ret = $client->cast("Tag", $response);
        return $ret;
    }

    public function deleteTag($tagId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/tags/{$tag->getId()}";
        $response = $client->call($request, HTTP_Request2::METHOD_DELETE);
        $ret = $client->cast("Tag", $response);
        return $ret;
    }
}
