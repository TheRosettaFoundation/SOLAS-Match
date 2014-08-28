<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API as API;

require_once __DIR__."/../DataAccessObjects/TagsDao.class.php";

class Tags
{
    public static function init()
    {
        $app = \Slim\Slim::getInstance();

        $app->group('/v0', function () use ($app) {
            $app->group('/tags', function () use ($app) {

                /* Routes starting /v0/tags */
                $app->get(
                    '/getByLabel/:tagLabel/',
                    '\SolasMatch\API\V0\Tags::getTagByLabel'
                );

                $app->get(
                    '/search/:tagName/',
                    '\SolasMatch\API\V0\Tags::searchForTag'
                );

                $app->get(
                    '/:tagId/tasks(:format)/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Tags::getTaskForTag'
                );

                $app->get(
                    '/:tagId/',
                    '\SolasMatch\API\V0\Tags::getTag'
                );

                $app->put(
                    '/:tagId/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Tags::updateTag'
                );

                $app->delete(
                    '/:tagId/',
                    '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
                    '\SolasMatch\API\V0\Tags::deleteTag'
                );
            });

            /* Routes starting /v0 */
            $app->get(
                '/tags(:format)/',
                '\SolasMatch\API\V0\Tags::getTags'
            );

            $app->post(
                '/tags(:format)/',
                '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgTask',
                '\SolasMatch\API\V0\Tags::createTag'
            );
        });
    }

    public static function getTagByLabel($tagLabel, $format = ".json")
    {
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
        $data = DAO\TagsDao::getTag(null, $tagLabel);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function searchForTag($tagName, $format = '.json')
    {
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
    }

    public static function getTaskForTag($tagId, $format = ".json")
    {
        $limit = API\Dispatcher::clenseArgs('limit', Common\Enums\HttpMethodEnum::GET, 5);
        API\Dispatcher::sendResponse(null, DAO\TaskDao::getTasksWithTag($tagId, $limit), null, $format);
    }

    public static function getTag($tagId, $format = ".json")
    {
        if (!is_numeric($tagId) && strstr($tagId, '.')) {
            $tagId = explode('.', $tagId);
            $format = '.'.$tagId[1];
            $tagId = $tagId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\TagsDao::getTag($tagId), null, $format);
    }

    public static function updateTag($tagId, $format = ".json")
    {
        if (!is_numeric($tagId) && strstr($tagId, '.')) {
            $tagId = explode('.', $tagId);
            $format = '.'.$tagId[1];
            $tagId = $tagId[0];
        }
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Tag");
        API\Dispatcher::sendResponse(null, DAO\TagsDao::save($data), null, $format);
    }

    public static function deleteTag($tagId, $format = ".json")
    {
        if (!is_numeric($tagId) && strstr($tagId, '.')) {
            $tagId = explode('.', $tagId);
            $format = '.'.$tagId[1];
            $tagId = $tagId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\TagsDao::delete($tagId), null, $format);
    }

    public static function getTags($format = ".json")
    {
        $limit = API\Dispatcher::clenseArgs('limit', Common\Enums\HttpMethodEnum::GET, 30);
        $topTags = API\Dispatcher::clenseArgs('topTags', Common\Enums\HttpMethodEnum::GET, false);
        if ($topTags) {
            API\Dispatcher::sendResponse(null, DAO\TagsDao::getTopTags(10), null, $format);
        } else {
            API\Dispatcher::sendResponse(null, DAO\TagsDao::getTags(null, null, $limit), null, $format);
        }
    }

    public static function createTag($format = ".json")
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data=$client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Tag");
        $data->setId("");
        API\Dispatcher::sendResponse(null, DAO\TagsDao::save($data), null, $format);
    }
}

Tags::init();
