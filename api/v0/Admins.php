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

        $app->get(
            '/v0/admins/getOrgAdmin/:userId/:orgId',
            '\SolasMatch\API\V0\Admins::getOrgAdmin'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->get(
            '/v0/admins/getOrgAdmins/:orgId/',
            '\SolasMatch\API\V0\Admins::getOrgAdmins'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->put(
            '/v0/admins/createOrgAdmin/:orgId/:userId/',
            '\SolasMatch\API\V0\Admins::createOrgAdmin'
            '\SolasMatch\API\Lib\Middleware::authenticateOrgAdmin',
        );

        $app->get(
            '/v0/admins/isOrgAdmin/:orgId/:userId/',
            '\SolasMatch\API\V0\Admins::isOrgAdmin'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->get(
            '/v0/admins/isSiteAdmin/:userId/',
            '\SolasMatch\API\V0\Admins::isSiteAdmin'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->get(
            '/v0/admins/getBannedUser/:userId/',
            '\SolasMatch\API\V0\Admins::getBannedUser'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->get(
            '/v0/admins/isUserBanned/:userId/',
            '\SolasMatch\API\V0\Admins::isUserBanned'
        );

        $app->get(
            '/v0/admins/getBannedOrg/:orgId/',
            '\SolasMatch\API\V0\Admins::getBannedOrg'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->get(
            '/v0/admins/isOrgBanned/:orgId/',
            '\SolasMatch\API\V0\Admins::isOrgBanned'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->get(
            '/v0/admins/getBannedUsers/',
            '\SolasMatch\API\V0\Admins::getBannedUsers'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->get(
            '/v0/admins/getBannedOrgs/',
            '\SolasMatch\API\V0\Admins::getBannedOrgs'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->post(
            '/v0/admins/banUser/',
            '\SolasMatch\API\V0\Admins::banUser'
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
        );

        $app->post(
            '/v0/admins/banOrg/',
            '\SolasMatch\API\V0\Admins::banOrg'
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
        );

        $app->get(
            '/v0/admins/:userId/',
            '\SolasMatch\API\V0\Admins::getSiteAdmin'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->put(
            '/v0/admins/:userId/',
            '\SolasMatch\API\V0\Admins::createSiteAdmin'
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
        );

        $app->delete(
            '/v0/admins/removeOrgAdmin/:orgId/:userId/',
            '\SolasMatch\API\V0\Admins::deleteOrgAdmin'
            '\SolasMatch\API\Lib\Middleware::authenticateOrgAdmin',
        );

        $app->delete(
            '/v0/admins/revokeTask/:taskId/:userId/',
            '\SolasMatch\API\V0\Admins::revokeTaskFromUser'
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
       );

        $app->delete(
            '/v0/admins/unBanUser/:userId/',
            '\SolasMatch\API\V0\Admins::unBanUser'
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
        );

        $app->delete(
            '/v0/admins/unBanOrg/:orgId/',
            '\SolasMatch\API\V0\Admins::unBanOrg'
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
        );

        $app->delete(
            '/v0/admins/:userId/',
            '\SolasMatch\API\V0\Admins::deleteSiteAdmin'
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
        );

        $app->get(
            '/v0/admins/',
            '\SolasMatch\API\V0\Admins::getSiteAdmins'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );
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
