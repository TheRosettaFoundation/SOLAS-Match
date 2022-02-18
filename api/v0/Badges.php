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

    public static function getUsersWithBadge($badgeId)
    {
        $data = DAO\UserDao::getUsersWithBadge($badgeId);
        Dispatcher::sendResponse(null, $data, null);
    }

    public static function userHasBadge($badgeId, $userId)
    {
        $data = DAO\UserDao::userHasBadge($badgeId, $userId);
        if (is_array($data)) {
            $data = $data[0];
        }
        Dispatcher::sendResponse(null, $data, null);
    }

    public static function getBadge($badgeId)
    {
        Dispatcher::sendResponse(null, DAO\BadgeDao::getBadge($badgeId), null);
    }

    public static function updateBadge($badgeId)
    {
        $data = Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Badge");
        Dispatcher::sendResponse(null, DAO\BadgeDao::insertAndUpdateBadge($data), null);
    }

    public static function deleteBadge($badgeId)
    {
        Dispatcher::sendResponse(null, DAO\BadgeDao::deleteBadge($badgeId), null);
    }

    public static function getBadges()
    {
        Dispatcher::sendResponse(null, DAO\BadgeDao::getBadges(), null);
    }

    public static function createBadge()
    {
        $data = Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Badge");
        $data->setId("");
        Dispatcher::sendResponse(null, DAO\BadgeDao::insertAndUpdateBadge($data), null);
    }
}

Badges::init();
