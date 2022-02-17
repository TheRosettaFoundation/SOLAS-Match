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

        $app->group('/v0', function () use ($app) {
            $app->group('/badges', function () use ($app) {
                $app->group('/:badgeId', function () use ($app) {
                    /* Routes beginning /v0/badges/:badgeId */
                    $app->get(
                        '/users/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Badges::getUsersWithBadge'
                    );

                    $app->get(
                        '/:userId/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Badges::userHasBadge'
                    );
                });

                /* Routes beginning /v0/badges */
                $app->get(
                    '/:badgeId/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Badges::getBadge'
                );

                $app->put(
                    '/:badgeId/',
                    '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgBadge',
                    '\SolasMatch\API\V0\Badges::updateBadge'
                );

                $app->delete(
                    '/:badgeId/',
                    '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgBadge',
                    '\SolasMatch\API\V0\Badges::deleteBadge'
                );
            });

            /* Routes beginning /v0 */
            $app->get(
                '/badges/',
                '\SolasMatch\API\Lib\Middleware::isloggedIn',
                '\SolasMatch\API\V0\Badges::getBadges'
            );

            $app->post(
                '/badges/',
                '\SolasMatch\API\Lib\Middleware::authenticateUserMembership',
                '\SolasMatch\API\V0\Badges::createBadge'
            );
        });
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
