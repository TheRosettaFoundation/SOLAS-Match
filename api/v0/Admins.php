<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__."/../DataAccessObjects/AdminDao.class.php";

class Admins
{
    public static function init()
    {
        global $app;

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

                $app->get(
                    '/getBannedUsers/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Admins::getBannedUsers'
                );

                $app->get(
                    '/getBannedOrgs/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Admins::getBannedOrgs'
                );

                $app->post(
                    '/banUser/',
                    '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
                    '\SolasMatch\API\V0\Admins::banUser'
                );

                $app->post(
                    '/banOrg/',
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
                        '/removeOrgAdmin/:orgId/:userId/',
                        '\SolasMatch\API\Lib\Middleware::authenticateOrgAdmin',
                        '\SolasMatch\API\V0\Admins::deleteOrgAdmin'
                );
                
                $app->delete(
                        '/revokeTask/:taskId/:userId/',
                        '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
                        '\SolasMatch\API\V0\Admins::revokeTaskFromUser'
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
                
                $app->delete(
                    '/:userId/',
                    '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
                    '\SolasMatch\API\V0\Admins::deleteSiteAdmin'
                );
            });

            /* Routes starting /v0 */
            $app->get(
                '/admins/',
                '\SolasMatch\API\Lib\Middleware::isloggedIn',
                '\SolasMatch\API\V0\Admins::getSiteAdmins'
            );
        });
    }

    public static function getOrgAdmin($userId, $orgId)
    {
        API\Dispatcher::sendResponse(null, DAO\AdminDao::getAdmins($userId, $orgId), null);
    }

    public static function getOrgAdmins($orgId)
    {
        API\Dispatcher::sendResponse(null, DAO\AdminDao::getAdmins(null, $orgId), null);
    }

    public static function createOrgAdmin($orgId, $userId)
    {
        DAO\AdminDao::addOrgAdmin($userId, $orgId);
        API\Dispatcher::sendResponse(null, null, null);
    }

    public static function deleteOrgAdmin($orgId, $userId)
    {
        DAO\AdminDao::removeOrgAdmin($userId, $orgId);
        API\Dispatcher::sendResponse(null, null, null);
    }

    public static function isOrgAdmin($orgId, $userId)
    {
        $ret = 0;
        $ret = DAO\AdminDao::isAdmin($userId, $orgId);
        if (is_null($orgId)) {
            $ret = 0;
        }
        API\Dispatcher::sendResponse(null, $ret, null);
    }

    public static function isSiteAdmin($userId)
    {
        $ret = false;
        $ret = DAO\AdminDao::isAdmin($userId, null);
        API\Dispatcher::sendResponse(null, $ret, null);
    }

    public static function getBannedUser($userId)
    {
        $data = DAO\AdminDao::getBannedUser($userId);
        API\Dispatcher::sendResponse(null, $data[0], null);
    }

    public static function isUserBanned($userId)
    {
        API\Dispatcher::sendResponse(null, DAO\AdminDao::isUserBanned($userId), null);
    }

    public static function getBannedOrg($orgId)
    {
        $data = DAO\AdminDao::getBannedOrg($orgId);
        API\Dispatcher::sendResponse(null, $data[0], null);
    }

    public static function isOrgBanned($orgId)
    {
        API\Dispatcher::sendResponse(null, DAO\AdminDao::isOrgBanned($orgId), null);
    }

    public static function unBanUser($userId)
    {
        DAO\AdminDao::unBanUser($userId);
        API\Dispatcher::sendResponse(null, null, null);
    }

    public static function unBanOrg($orgId)
    {
        DAO\AdminDao::unBanOrg($orgId);
        API\Dispatcher::sendResponse(null, null, null);
    }

    public static function getBannedUsers()
    {
        API\Dispatcher::sendResponse(null, DAO\AdminDao::getBannedUser(), null);
    }

    public static function getBannedOrgs()
    {
        API\Dispatcher::sendResponse(null, DAO\AdminDao::getBannedOrg(), null);
    }

    public static function banUser()
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\BannedUser');
        DAO\AdminDao::saveBannedUser($data);
        API\Dispatcher::sendResponse(null, null, null);
        Lib\Notify::sendBannedLoginEmail($data->getUserId());
    }

    public static function banOrg()
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\BannedOrganisation');
        DAO\AdminDao::saveBannedOrg($data);
        API\Dispatcher::sendResponse(null, null, null);
    }

    public static function getSiteAdmin($userId)
    {
        API\Dispatcher::sendResponse(null, DAO\AdminDao::getAdmins($userId), null);
    }

    public static function createSiteAdmin($userId)
    {
        DAO\AdminDao::addSiteAdmin($userId);
        API\Dispatcher::sendResponse(null, null, null);
    }

    public static function deleteSiteAdmin($userId)
    {
        DAO\AdminDao::removeAdmin($userId);
        API\Dispatcher::sendResponse(null, null, null);
    }

    public static function getSiteAdmins()
    {
        API\Dispatcher::sendResponse(null, DAO\AdminDao::getAdmins(), null);
    }
    
    public static function revokeTaskFromUser($taskId, $userId)
    {
        API\Dispatcher::sendResponse(null, DAO\TaskDao::unClaimTask($taskId, $userId, true), null);
    }
}

Admins::init();
