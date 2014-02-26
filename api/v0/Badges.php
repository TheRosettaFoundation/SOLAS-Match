<?php

namespace SolasMatch\API\V0;

use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API as API;

/**
 * Description of Badges
 *
 * @author sean
 */

require_once __DIR__."/../DataAccessObjects/BadgeDao.class.php";

class Badges
{
    public static function init()
    {
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/badges(:format)/',
            function ($format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\BadgeDao::getBadge(), null, $format);
            },
            'getBadges'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::POST,
            '/v0/badges(:format)/',
            function ($format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new \APIHelper($format);
                $data = $client->deserialize($data, "Badge");
                $data->setId(null);
                API\Dispatcher::sendResponse(null, DAO\BadgeDao::insertAndUpdateBadge($data), null, $format);
            },
            'createBadge',
            '\SolasMatch\API\Lib\Middleware::authenticateUserMembership'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::PUT,
            '/v0/badges/:badgeId/',
            function ($badgeId, $format = ".json") {
                if (!is_numeric($badgeId) && strstr($badgeId, '.')) {
                    $badgeId = explode('.', $badgeId);
                    $format = '.'.$badgeId[1];
                    $badgeId = $badgeId[0];
                }
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new \APIHelper($format);
                $data = $client->deserialize($data, "Badge");
                API\Dispatcher::sendResponse(null, DAO\BadgeDao::insertAndUpdateBadge($data), null, $format);
            },
            'updateBadge',
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgBadge'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::DELETE,
            '/v0/badges/:badgeId/',
            function ($badgeId, $format = ".json") {
                if (!is_numeric($badgeId) && strstr($badgeId, '.')) {
                    $badgeId = explode('.', $badgeId);
                    $format = '.'.$badgeId[1];
                    $badgeId = $badgeId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\BadgeDao::deleteBadge($badgeId), null, $format);
            },
            'deleteBadge',
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgBadge'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/badges/:badgeId/',
            function ($badgeId, $format = ".json") {
                if (!is_numeric($badgeId)&& strstr($badgeId, '.')) {
                    $badgeId = explode('.', $badgeId);
                    $format = '.'.$badgeId[1];
                    $badgeId = $badgeId[0];
                }
                $data = DAO\BadgeDao::getBadge($badgeId, null, null, null);
                if (is_array($data)) {
                    $data = $data[0];
                }
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getBadge'
        );
        
        API\Dispatcher::registerNamed(
            \HttpMethodEnum::GET,
            '/v0/badges/:badgeId/users(:format)/',
            function ($badgeId, $format = ".json") {
                $data = DAO\UserDao::getUsersWithBadge($badgeId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUsersWithBadge'
        );
    }
}
Badges::init();
