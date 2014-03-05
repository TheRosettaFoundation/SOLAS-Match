<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API as API;

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
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/tags(:format)/',
            function ($format = ".json") {
                $limit = API\Dispatcher::clenseArgs('limit', Common\Enums\HttpMethodEnum::GET, 20);
                $topTags = API\Dispatcher::clenseArgs('topTags', Common\Enums\HttpMethodEnum::GET, false);
                if ($topTags) {
                    API\Dispatcher::sendResponse(null, DAO\TagsDao::getTopTags($limit), null, $format);
                } else {
                    API\Dispatcher::sendResponse(null, DAO\TagsDao::getTag(null, null, $limit), null, $format);
                }
            },
            'getTags',
            null
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/tags(:format)/',
            function ($format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data=$client->deserialize($data, "Tag");
                $data->setId(null);
                API\Dispatcher::sendResponse(null, DAO\TagsDao::save($data), null, $format);
            },
            'createTag',
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgTask'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
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
                $data = DAO\TagsDao::getTag(null, $label);
                if (is_array($data)) {
                    $data = $data[0];
                }
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getTagByLabel',
            null
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
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
                $ret = DAO\TagsDao::searchForTag($tagName);
                API\Dispatcher::sendResponse(null, $ret, null, $format);
            },
            'searchForTag',
            null
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/tags/topTags(:format)/',
            function ($format = ".json") {
                $limit = API\Dispatcher::clenseArgs('limit', Common\Enums\HttpMethodEnum::GET, 30);
                $data= DAO\TagsDao::getTopTags($limit);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getTopTags',
            null
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/tags/:tagId/',
            function ($tagId, $format = ".json") {
                if (!is_numeric($tagId) && strstr($tagId, '.')) {
                    $tagId = explode('.', $tagId);
                    $format = '.'.$tagId[1];
                    $tagId = $tagId[0];
                }
                $data = DAO\TagsDao::getTag($tagId);
                if (is_array($data)) {
                    $data = $data[0];
                }
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getTag',
            null
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/tags/:tagId/',
            function ($tagId, $format = ".json") {
                if (!is_numeric($tagId) && strstr($tagId, '.')) {
                    $tagId = explode('.', $tagId);
                    $format = '.'.$tagId[1];
                    $tagId = $tagId[0];
                }
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, "Tag");
                API\Dispatcher::sendResponse(null, DAO\TagsDao::save($data), null, $format);
            },
            'updateTag'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/tags/:tagId/',
            function ($tagId, $format = ".json") {
                if (!is_numeric($tagId) && strstr($tagId, '.')) {
                    $tagId = explode('.', $tagId);
                    $format = '.'.$tagId[1];
                    $tagId = $tagId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\TagsDao::delete($tagId), null, $format);
            },
            'deleteTag',
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/tags/:tagId/tasks(:format)/',
            function ($tagId, $format = ".json") {
                $limit = API\Dispatcher::clenseArgs('limit', Common\Enums\HttpMethodEnum::GET, 5);
                API\Dispatcher::sendResponse(null, DAO\TaskDao::getTasksWithTag($tagId, $limit), null, $format);
            },
            'getTaskForTag'
        );
    }
}
Tags::init();
