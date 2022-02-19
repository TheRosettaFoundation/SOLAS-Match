<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API as API;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__."/../DataAccessObjects/TagsDao.class.php";

class Tags
{
    public static function init()
    {
        global $app;

        $app->get(
            '/v0/tags/getByLabel/:tagLabel/',
            '\SolasMatch\API\V0\Tags:getTagByLabel');

        $app->get(
            '/v0/tags/search/:tagName/',
            '\SolasMatch\API\V0\Tags:searchForTag');

        $app->get(
            '/v0/tags/:tagId/tasks/',
            '\SolasMatch\API\V0\Tags:getTaskForTag')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/v0/tags/:tagId/',
            '\SolasMatch\API\V0\Tags:getTag');

        $app->put(
            '/v0/tags/:tagId/',
            '\SolasMatch\API\V0\Tags:updateTag')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->delete(
            '/v0/tags/:tagId/',
            '\SolasMatch\API\V0\Tags:deleteTag')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateSiteAdmin');

        $app->get(
            '/v0/tags/',
            '\SolasMatch\API\V0\Tags:getTags');

        $app->post(
            '/v0/tags/',
            '\SolasMatch\API\V0\Tags:createTag')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateUserForOrgTask');
    }

    public static function getTagByLabel(Request $request, Response $response, $args)
    {
        $tagLabel = $args['tagLabel'];
        $data = DAO\TagsDao::getTag(null, $tagLabel);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function searchForTag(Request $request, Response $response, $args)
    {
        $tagName = $args['tagName'];
        $ret = DAO\TagsDao::searchForTag($tagName);
        return API\Dispatcher::sendResponse($response, $ret, null);
    }

    public static function getTaskForTag(Request $request, Response $response, $args)
    {
        $tagId = $args['tagId'];
        $limit = API\Dispatcher::clenseArgs($request, 'limit', 5);
        return API\Dispatcher::sendResponse($response, DAO\TaskDao::getTasksWithTag($tagId, $limit), null);
    }

    public static function getTag(Request $request, Response $response, $args)
    {
        $tagId = $args['tagId'];
        return API\Dispatcher::sendResponse($response, DAO\TagsDao::getTag($tagId), null);
    }

    public static function updateTag(Request $request, Response $response, $args)
    {
        $tagId = $args['tagId'];
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Tag");
        return API\Dispatcher::sendResponse($response, DAO\TagsDao::save($data), null);
    }

    public static function deleteTag(Request $request, Response $response, $args)
    {
        $tagId = $args['tagId'];
        return API\Dispatcher::sendResponse($response, DAO\TagsDao::delete($tagId), null);
    }

    public static function getTags(Request $request, Response $response)
    {
        $limit = API\Dispatcher::clenseArgs($request, 'limit', 30);
        $topTags = API\Dispatcher::clenseArgs($request, 'topTags', false);
        if ($topTags) {
            return API\Dispatcher::sendResponse($response, DAO\TagsDao::getTopTags(10), null);
        } else {
            return API\Dispatcher::sendResponse($response, DAO\TagsDao::getTags(null, null, $limit), null);
        }
    }

    public static function createTag(Request $request, Response $response)
    {
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data=$client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Tag");
        $data->setId("");
        return API\Dispatcher::sendResponse($response, DAO\TagsDao::save($data), null);
    }
}

Tags::init();
