<?php

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
        /**
         * Gets a single badge object based on its $badgeId
         **/
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/badges/:badgeId/',
            function ($badgeId, $format = ".json") {
                if (!is_numeric($badgeId)&& strstr($badgeId, '.')) {
                    $badgeId = explode('.', $badgeId);
                    $format = '.'.$badgeId[1];
                    $badgeId = $badgeId[0];
                }
                Dispatcher::sendResponce(null, BadgeDao::getBadge($badgeId), null, $format);
            },
            'getBadge'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/badges(:format)/',
            function ($format = ".json") {
                Dispatcher::sendResponce(null, BadgeDao::getBadges(), null, $format);
            },
            'getBadges'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::POST,
            '/v0/badges(:format)/',
            function ($format = ".json") {
                $data = Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data = $client->deserialize($data, "Badge");
                $data->setId(null);
                Dispatcher::sendResponce(null, BadgeDao::insertAndUpdateBadge($data), null, $format);
            },
            'createBadge',
            'Middleware::authenticateUserMembership'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::PUT,
            '/v0/badges/:badgeId/',
            function ($badgeId, $format = ".json") {
                if (!is_numeric($badgeId) && strstr($badgeId, '.')) {
                    $badgeId = explode('.', $badgeId);
                    $format = '.'.$badgeId[1];
                    $badgeId = $badgeId[0];
                }
                $data = Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data = $client->deserialize($data, "Badge");
                Dispatcher::sendResponce(null, BadgeDao::insertAndUpdateBadge($data), null, $format);
            },
            'updateBadge',
            'Middleware::authenticateUserForOrgBadge'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::DELETE,
            '/v0/badges/:badgeId/',
            function ($badgeId, $format = ".json") {
                if (!is_numeric($badgeId) && strstr($badgeId, '.')) {
                    $badgeId = explode('.', $badgeId);
                    $format = '.'.$badgeId[1];
                    $badgeId = $badgeId[0];
                }
                Dispatcher::sendResponce(null, BadgeDao::deleteBadge($badgeId), null, $format);
            },
            'deleteBadge',
            'Middleware::authenticateUserForOrgBadge'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/badges/:badgeId/users(:format)/',
            function ($badgeId, $format = ".json") {
                $data = UserDao::getUsersWithBadge($badgeId);
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'getUsersWithBadge'
        );
        
        /*
         * Checks if a user has a particular badge
         */
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/badges/:badgeId/:userId/',
            function ($badgeId, $userId, $format = ".json") {
                if (!is_numeric($userId)&& strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = UserDao::userHasBadge($badgeId, $userId);
                if (is_array($data)) {
                    $data = $data[0];
                }
                Dispatcher::sendResponce(null, $data, null, $format);
            },
            'userHasBadge'
        );

    }
}
Badges::init();
