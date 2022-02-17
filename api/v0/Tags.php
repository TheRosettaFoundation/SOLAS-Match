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
                    '/:tagId/tasks/',
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
                '/tags/',
                '\SolasMatch\API\V0\Tags::getTags'
            );

            $app->post(
                '/tags/',
                '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgTask',
                '\SolasMatch\API\V0\Tags::createTag'
            );
        });
    }

    public static function getTagByLabel($tagLabel)
    {
        $data = DAO\TagsDao::getTag(null, $tagLabel);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function searchForTag($tagName)
    {
        $ret = DAO\TagsDao::searchForTag($tagName);
        API\Dispatcher::sendResponse(null, $ret, null);
    }

    public static function getTaskForTag($tagId)
    {
        $limit = API\Dispatcher::clenseArgs('limit', Common\Enums\HttpMethodEnum::GET, 5);
        API\Dispatcher::sendResponse(null, DAO\TaskDao::getTasksWithTag($tagId, $limit), null);
    }

    public static function getTag($tagId)
    {
        API\Dispatcher::sendResponse(null, DAO\TagsDao::getTag($tagId), null);
    }

    public static function updateTag($tagId)
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Tag");
        API\Dispatcher::sendResponse(null, DAO\TagsDao::save($data), null);
    }

    public static function deleteTag($tagId)
    {
        API\Dispatcher::sendResponse(null, DAO\TagsDao::delete($tagId), null);
    }

    public static function getTags()
    {
        $limit = API\Dispatcher::clenseArgs('limit', Common\Enums\HttpMethodEnum::GET, 30);
        $topTags = API\Dispatcher::clenseArgs('topTags', Common\Enums\HttpMethodEnum::GET, false);
        if ($topTags) {
            API\Dispatcher::sendResponse(null, DAO\TagsDao::getTopTags(10), null);
        } else {
            API\Dispatcher::sendResponse(null, DAO\TagsDao::getTags(null, null, $limit), null);
        }
    }

    public static function createTag()
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data=$client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Tag");
        $data->setId("");
        API\Dispatcher::sendResponse(null, DAO\TagsDao::save($data), null);
    }
}

Tags::init();
