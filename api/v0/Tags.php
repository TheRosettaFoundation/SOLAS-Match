<?php

/**
 * Description of Tags
 *
 * @author sean
 */

require_once __DIR__."/../DataAccessObjects/TagsDao.class.php";

class Tags
{
    public static function init()
    {
        /**
         * Gets a single tag based on its id
         */
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/tags/:tagId/',
            function ($tagId, $format = ".json") {
                if (!is_numeric($tagId) && strstr($tagId, '.')) {
                    $tagId = explode('.', $tagId);
                    $format = '.'.$tagId[1];
                    $tagId = $tagId[0];
                }
                Dispatcher::sendResponce(null, TagsDao::getTag($tagId), null, $format);
            },
            'getTag',
            null
        );

        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/tags(:format)/',
            function ($format = ".json") {
                $limit = Dispatcher::clenseArgs('limit', HttpMethodEnum::GET, 20);
                $topTags = Dispatcher::clenseArgs('topTags', HttpMethodEnum::GET, false);
                if ($topTags) {
                    Dispatcher::sendResponce(null, TagsDao::getTopTags($limit), null, $format);
                } else {
                    Dispatcher::sendResponce(null, TagsDao::getTags(null, null, $limit), null, $format);
                }
            },
            'getTags',
            null
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::POST,
            '/v0/tags(:format)/',
            function ($format = ".json") {
                $data = Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data=$client->deserialize($data, "Tag");
                $data->setId(null);
                Dispatcher::sendResponce(null, TagsDao::save($data), null, $format);
            },
            'createTag',
            'Middleware::authenticateUserForOrgTask'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/tags/getByLabel/:tagLabel/',
            function ($tagLabel, $format = ".json") {
                if (!is_numeric($tagLabel) && strstr($tagLabel, '.')) {
                    $temp = array();
                    $temp = explode('.', $tagLabel);
                    $lastIndex = sizeof($temp)-1;
                    if ($lastIndex > 0) {
                        $format = '.'.$temp[$lastIndex];
                        $tagLabel = $temp[0];
                        for ($i = 1; $i < $lastIndex; $i++) {
                            $tagLabel = "{$tagLabel}.{$temp[$i]}";
                        }
                    }
                }
                $data = TagsDao::getTag(null, $tagLabel);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'getTagByLabel',
            null
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/tags/search/:tagName/',
            function ($tagName, $format = '.json') {
                if (!is_numeric($tagName) && strstr($tagName, '.')) {
                    $temp = array();
                    $temp = explode('.', $tagName);
                    $lastIndex = sizeof($temp)-1;
                    if ($lastIndex > 0) {
                        $format = '.'.$temp[$lastIndex];
                        $tagName = $temp[0];
                        for ($i = 1; $i < $lastIndex; $i++) {
                            $tagName = "{$tagName}.{$temp[$i]}";
                        }
                    }
                }
                $ret = TagsDao::searchForTag($tagName);
                Dispatcher::sendResponce(null, $ret, null, $format);
            },
            'searchForTag',
            null
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/tags/topTags(:format)/',
            function ($format = ".json") {
                $limit = Dispatcher::clenseArgs('limit', HttpMethodEnum::GET, 30);
                $data= TagsDao::getTopTags($limit);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'getTopTags',
            null
        );

        Dispatcher::registerNamed(
            HttpMethodEnum::PUT,
            '/v0/tags/:tagId/',
            function ($tagId, $format = ".json") {
                if (!is_numeric($tagId) && strstr($tagId, '.')) {
                    $tagId = explode('.', $tagId);
                    $format = '.'.$tagId[1];
                    $tagId = $tagId[0];
                }
                $data = Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data = $client->deserialize($data, "Tag");
                Dispatcher::sendResponce(null, TagsDao::save($data), null, $format);
            },
            'updateTag'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::DELETE,
            '/v0/tags/:tagId/',
            function ($tagId, $format = ".json") {
                if (!is_numeric($tagId) && strstr($tagId, '.')) {
                    $tagId = explode('.', $tagId);
                    $format = '.'.$tagId[1];
                    $tagId = $tagId[0];
                }
                Dispatcher::sendResponce(null, TagsDao::delete($tagId), null, $format);
            },
            'deleteTag',
            'Middleware::authenticateSiteAdmin'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/tags/:tagId/tasks(:format)/',
            function ($tagId, $format = ".json") {
                $limit = Dispatcher::clenseArgs('limit', HttpMethodEnum::GET, 5);
                Dispatcher::sendResponce(null, TaskDao::getTasksWithTag($tagId, $limit), null, $format);
            },
            'getTaskForTag'
        );
    }
}
Tags::init();
