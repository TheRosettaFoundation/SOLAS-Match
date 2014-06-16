<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;

require_once __DIR__."/../DataAccessObjects/AdminDao.class.php";

class Admins
{
    public static function init()
    {
        $app = \Slim\Slim::getInstance();

        $app->group('/v0', function () use ($app) {
            $app->group('/admins', function () use ($app) {
                /* Routes starting /v0/admins */
                $app->get(
                    '/getOrgAdmin/:userId/:orgId',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Admins::getOrgAdmin'
                );

                $app->get(
                    '/getOrgAdmins/:orgId/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Admins::getOrgAdmins'
                );

                $app->put(
                    '/createOrgAdmin/:orgId/:userId/',
                    '\SolasMatch\API\Lib\Middleware::authenticateOrgAdmin',
                    '\SolasMatch\API\V0\Admins::createOrgAdmin'
                );

                $app->delete(
                    '/removeOrgAdmin/:orgId/:userId/',
                    '\SolasMatch\API\Lib\Middleware::authenticateOrgAdmin',
                    '\SolasMatch\API\V0\Admins::deleteOrgAdmin'
                );

                $app->get(
                    '/isOrgAdmin/:orgId/:userId/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Admins::isOrgAdmin'
                );

                $app->get(
                    '/isSiteAdmin/:userId/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Admins::isSiteAdmin'
                );

                $app->get(
                    '/getBannedUser/:userId/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Admins::getBannedUser'
                );

                $app->get(
                    '/isUserBanned/:userId/',
                    '\SolasMatch\API\V0\Admins::isUserBanned'
                );

                $app->get(
                    '/getBannedOrg/:orgId/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Admins::getBannedOrg'
                );

                $app->get(
                    '/isOrgBanned/:orgId/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Admins::isOrgBanned'
                );

                $app->delete(
                    '/unBanUser/:userId/',
                    '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
                    '\SolasMatch\API\V0\Admins::unBanUser'
                );

                $app->delete(
                    '/unBanOrg/:orgId/',
                    '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
                    '\SolasMatch\API\V0\Admins::unBanOrg'
                );

                $app->get(
                    '/getBannedUsers(:format)/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Admins::getBannedUsers'
                );

                $app->get(
                    '/getBannedOrgs(:format)/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Admins::getBannedOrgs'
                );

                $app->post(
                    '/banUser(:format)/',
                    '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
                    '\SolasMatch\API\V0\Admins::banUser'
                );

                $app->post(
                    '/banOrg(:format)/',
                    '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
                    '\SolasMatch\API\V0\Admins::banOrg'
                );

                $app->get(
                    '/:userId/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Admins::getSiteAdmin'
                );

                $app->put(
                    '/:userId/',
                    '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
                    '\SolasMatch\API\V0\Admins::createSiteAdmin'
                );

                $app->delete(
                    '/:userId/',
                    '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
                    '\SolasMatch\API\V0\Admins::deleteSiteAdmin'
                );
                
                $app->delete(
	                '/revokeTask/:taskId/:userId(:format)/',
                    '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
                    '\SolasMatch\API\V0\Admins::revokeTaskFromUser'
                );
            });

            /* Routes starting /v0 */
            $app->get(
                '/admins(:format)/',
                '\SolasMatch\API\Lib\Middleware::isloggedIn',
                '\SolasMatch\API\V0\Admins::getSiteAdmins'
            );
        });
    }

    public static function getOrgAdmin($userId, $orgId, $format = '.json')
    {
        if (!is_numeric($orgId) && strstr($orgId, '.')) {
            $orgId = explode('.', $orgId);
            $format = '.'.$orgId[1];
            $orgId = $orgId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\AdminDao::getAdmins($userId, $orgId), null, $format);
    }

    public static function getOrgAdmins($orgId, $format = '.json')
    {
        if (!is_numeric($orgId) && strstr($orgId, '.')) {
            $orgId = explode('.', $orgId);
            $format = '.'.$orgId[1];
            $orgId = $orgId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\AdminDao::getAdmins(null, $orgId), null, $format);
    }

    public static function createOrgAdmin($orgId, $userId, $format = '.json')
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        DAO\AdminDao::addOrgAdmin($userId, $orgId);
        API\Dispatcher::sendResponse(null, null, null, $format);
    }

    public static function deleteOrgAdmin($orgId, $userId, $format = '.json')
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        DAO\AdminDao::removeOrgAdmin($userId, $orgId);
        API\Dispatcher::sendResponse(null, null, null, $format);
    }

    public static function isOrgAdmin($orgId, $userId, $format = '.json')
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        $ret = 0;
        $ret = DAO\AdminDao::isAdmin($userId, $orgId);
        if (is_null($orgId)) {
            $ret = 0;
        }
        API\Dispatcher::sendResponse(null, $ret, null, $format);
    }

    public static function isSiteAdmin($userId, $format = '.json')
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        $ret = false;
        $ret = DAO\AdminDao::isAdmin($userId, null);
        API\Dispatcher::sendResponse(null, $ret, null, $format);
    }

    public static function getBannedUser($userId, $format = '.json')
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        $data = DAO\AdminDao::getBannedUser($userId);
        API\Dispatcher::sendResponse(null, $data[0], null, $format);
    }

    public static function isUserBanned($userId, $format = ".json")
    {
        if (!is_numeric($userId)&& strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\AdminDao::isUserBanned($userId), null, $format);
    }

    public static function getBannedOrg($orgId, $format = ".json")
    {
        if (!is_numeric($orgId) && strstr($orgId, '.')) {
            $orgId = explode('.', $orgId);
            $format = '.'.$orgId[1];
            $orgId = $orgId[0];
        }
        $data = DAO\AdminDao::getBannedOrg($orgId);
        API\Dispatcher::sendResponse(null, $data[0], null, $format);
    }

    public static function isOrgBanned($orgId, $format = ".json")
    {
        if (!is_numeric($orgId)&& strstr($orgId, '.')) {
            $orgId = explode('.', $orgId);
            $format = '.'.$orgId[1];
            $orgId = $orgId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\AdminDao::isOrgBanned($orgId), null, $format);
    }

    public static function unBanUser($userId, $format = '.json')
    {
        if (!is_numeric($userId)&& strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        DAO\AdminDao::unBanUser($userId);
        API\Dispatcher::sendResponse(null, null, null, $format);
    }

    public static function unBanOrg($orgId, $format = '.json')
    {
        if (!is_numeric($orgId)&& strstr($orgId, '.')) {
            $orgId = explode('.', $orgId);
            $format = '.'.$orgId[1];
            $orgId = $orgId[0];
        }
        DAO\AdminDao::unBanOrg($orgId);
        API\Dispatcher::sendResponse(null, null, null, $format);
    }

    public static function getBannedUsers($format = ".json")
    {
        API\Dispatcher::sendResponse(null, DAO\AdminDao::getBannedUser(), null, $format);
    }

    public static function getBannedOrgs($format = ".json")
    {
        API\Dispatcher::sendResponse(null, DAO\AdminDao::getBannedOrg(), null, $format);
    }

    public static function banUser($format = '.json')
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\BannedUser');
        DAO\AdminDao::saveBannedUser($data);
        API\Dispatcher::sendResponse(null, null, null, $format);
        Lib\Notify::sendBannedLoginEmail($data->getUserId());
    }

    public static function banOrg($format = '.json')
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\BannedOrganisation');
        DAO\AdminDao::saveBannedOrg($data);
        API\Dispatcher::sendResponse(null, null, null, $format);
    }

    public static function getSiteAdmin($userId, $format = '.json')
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\AdminDao::getAdmins($userId), null, $format);
    }

    public static function createSiteAdmin($userId, $format = '.json')
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        DAO\AdminDao::addSiteAdmin($userId);
        API\Dispatcher::sendResponse(null, null, null, $format);
    }

    public static function deleteSiteAdmin($userId, $format = '.json')
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        DAO\AdminDao::removeAdmin($userId);
        API\Dispatcher::sendResponse(null, null, null, $format);
    }

    public static function getSiteAdmins($format = '.json')
    {
        API\Dispatcher::sendResponse(null, DAO\AdminDao::getAdmins(), null, $format);
    }
    
    public static function revokeTaskFromUser($taskId, $userId, $format = ".json")
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\TaskDao::unClaimTask($taskId, $userId, true), null, $format);
    }
}

Admins::init();
