<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Dispatcher;

require_once __DIR__."/../DataAccessObjects/BadgeDao.class.php";

class Badges
{
    public static function init()
    {
        $app = \Slim\Slim::getInstance();

        $app->group('/v0', function () use ($app) {
            $app->group('/badges', function () use ($app) {
                $app->group('/:badgeId', function () use ($app) {
                    /* Routes beginning /v0/badges/:badgeId */
                    $app->get(
                        '/users(:format)/',
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
                '/badges(:format)/',
                '\SolasMatch\API\Lib\Middleware::isloggedIn',
                '\SolasMatch\API\V0\Badges::getBadges'
            );

            $app->post(
                '/badges(:format)/',
                '\SolasMatch\API\Lib\Middleware::authenticateUserMembership',
                '\SolasMatch\API\V0\Badges::createBadge'
            );
        });
    }

    public static function getUsersWithBadge($badgeId, $format = '.json')
    {
        $data = DAO\UserDao::getUsersWithBadge($badgeId);
        Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function userHasBadge($badgeId, $userId, $format = '.json')
    {
        if (!is_numeric($userId)&& strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        $data = DAO\UserDao::userHasBadge($badgeId, $userId);
        if (is_array($data)) {
            $data = $data[0];
        }
        Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getBadge($badgeId, $format = '.json')
    {
        if (!is_numeric($badgeId) && strstr($badgeId, '.')) {
            $badgeId = explode('.', $badgeId);
            $format = '.'.$badgeId[1];
            $badgeId = $badgeId[0];
        }
        Dispatcher::sendResponse(null, DAO\BadgeDao::getBadge($badgeId), null, $format);
    }

    public static function updateBadge($badgeId, $format = '.json')
    {
        if (!is_numeric($badgeId) && strstr($badgeId, '.')) {
            $badgeId = explode('.', $badgeId);
            $format = '.'.$badgeId[1];
            $badgeId = $badgeId[0];
        }
        $data = Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Badge");
        Dispatcher::sendResponse(null, DAO\BadgeDao::insertAndUpdateBadge($data), null, $format);
    }

    public static function deleteBadge($badgeId, $format = '.json')
    {
        if (!is_numeric($badgeId) && strstr($badgeId, '.')) {
            $badgeId = explode('.', $badgeId);
            $format = '.'.$badgeId[1];
            $badgeId = $badgeId[0];
        }
        Dispatcher::sendResponse(null, DAO\BadgeDao::deleteBadge($badgeId), null, $format);
    }

    public static function getBadges($format = '.json')
    {
        Dispatcher::sendResponse(null, DAO\BadgeDao::getBadges(), null, $format);
    }

    public static function createBadge($format = '.json')
    {
        $data = Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Badge");
        $data->setId("");
        Dispatcher::sendResponse(null, DAO\BadgeDao::insertAndUpdateBadge($data), null, $format);
    }
}

Badges::init();
