<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Dispatcher;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__."/../DataAccessObjects/BadgeDao.class.php";

class Badges
{
    public static function init()
    {
        global $app;

        $app->get(
            '/v0/badges/:badgeId/users/',
            '\SolasMatch\API\V0\Badges:getUsersWithBadge')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/v0/badges/:badgeId/:userId/',
            '\SolasMatch\API\V0\Badges:userHasBadge')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/v0/badges/:badgeId/',
            '\SolasMatch\API\V0\Badges:getBadge')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->put(
            '/v0/badges/:badgeId/',
            '\SolasMatch\API\V0\Badges:updateBadge')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateUserForOrgBadge');

        $app->delete(
            '/v0/badges/:badgeId/',
            '\SolasMatch\API\V0\Badges:deleteBadge')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateUserForOrgBadge');

        $app->get(
            '/v0/badges/',
            '\SolasMatch\API\V0\Badges:getBadges')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->post(
            '/v0/badges/',
            '\SolasMatch\API\V0\Badges:createBadge')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateUserMembership');
    }

    public static function getUsersWithBadge(Request $request, Response $response, $args)
    {
        $badgeId = $args['badgeId'];
        $data = DAO\UserDao::getUsersWithBadge($badgeId);
        Dispatcher::sendResponse($response, $data, null);
    }

    public static function userHasBadge(Request $request, Response $response, $args)
    {
        $badgeId = $args['badgeId'];
        $userId = $args['userId'];

        $data = DAO\UserDao::userHasBadge($badgeId, $userId);
        if (is_array($data)) {
            $data = $data[0];
        }
        Dispatcher::sendResponse($response, $data, null);
    }

    public static function getBadge(Request $request, Response $response, $args)
    {
        $badgeId = $args['badgeId'];
        Dispatcher::sendResponse($response, DAO\BadgeDao::getBadge($badgeId), null);
    }

    public static function updateBadge(Request $request, Response $response, $args)
    {
        $badgeId = $args['badgeId'];
        $data = Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Badge");
        Dispatcher::sendResponse($response, DAO\BadgeDao::insertAndUpdateBadge($data), null);
    }

    public static function deleteBadge(Request $request, Response $response, $args)
    {
        $badgeId = $args['badgeId'];
        Dispatcher::sendResponse($response, DAO\BadgeDao::deleteBadge($badgeId), null);
    }

    public static function getBadges(Request $request, Response $response)
    {
        Dispatcher::sendResponse($response, DAO\BadgeDao::getBadges(), null);
    }

    public static function createBadge(Request $request, Response $response)
    {
        $data = Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Badge");
        $data->setId("");
        Dispatcher::sendResponse($response, DAO\BadgeDao::insertAndUpdateBadge($data), null);
    }
}

Badges::init();
