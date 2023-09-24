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

        $app->put(
            '/api/v0/admins/createOrgAdmin/{orgId}/{userId}/',
            '\SolasMatch\API\V0\Admins:createOrgAdmin')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateOrgAdmin');

        $app->get(
            '/api/v0/admins/getBannedUser/{userId}/',
            '\SolasMatch\API\V0\Admins:getBannedUser')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/api/v0/admins/isUserBanned/{userId}/',
            '\SolasMatch\API\V0\Admins:isUserBanned');

        $app->get(
            '/api/v0/admins/getBannedOrg/{orgId}/',
            '\SolasMatch\API\V0\Admins:getBannedOrg')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/api/v0/admins/isOrgBanned/{orgId}/',
            '\SolasMatch\API\V0\Admins:isOrgBanned')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/api/v0/admins/getBannedUsers/',
            '\SolasMatch\API\V0\Admins:getBannedUsers')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/api/v0/admins/getBannedOrgs/',
            '\SolasMatch\API\V0\Admins:getBannedOrgs')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->post(
            '/api/v0/admins/banUser/',
            '\SolasMatch\API\V0\Admins:banUser')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateSiteAdmin');

        $app->post(
            '/api/v0/admins/banOrg/',
            '\SolasMatch\API\V0\Admins:banOrg')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateSiteAdmin');

        $app->get(
            '/api/v0/admins/{userId}/',
            '\SolasMatch\API\V0\Admins:getSiteAdmin')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->put(
            '/api/v0/admins/{userId}/',
            '\SolasMatch\API\V0\Admins:createSiteAdmin')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateSiteAdmin');

        $app->delete(
            '/api/v0/admins/revokeTask/{taskId}/{userId}/',
            '\SolasMatch\API\V0\Admins:revokeTaskFromUser')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateSiteAdmin');

        $app->delete(
            '/api/v0/admins/unBanUser/{userId}/',
            '\SolasMatch\API\V0\Admins:unBanUser')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateSiteAdmin');

        $app->delete(
            '/api/v0/admins/unBanOrg/{orgId}/',
            '\SolasMatch\API\V0\Admins:unBanOrg')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateSiteAdmin');

        $app->get(
            '/api/v0/admins/',
            '\SolasMatch\API\V0\Admins:getSiteAdmins')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');
    }

    public static function createOrgAdmin(Request $request, Response $response, $args)
    {
        $orgId = $args['orgId'];
        $userId = $args['userId'];
        DAO\AdminDao::addOrgAdmin($userId, $orgId);
        return API\Dispatcher::sendResponse($response, null, null);
    }

    public static function getBannedUser(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $data = DAO\AdminDao::getBannedUser($userId);
        return API\Dispatcher::sendResponse($response, $data[0], null);
    }

    public static function isUserBanned(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        return API\Dispatcher::sendResponse($response, DAO\AdminDao::isUserBanned($userId), null);
    }

    public static function getBannedOrg(Request $request, Response $response, $args)
    {
        $orgId = $args['orgId'];

        $data = DAO\AdminDao::getBannedOrg($orgId);
        return API\Dispatcher::sendResponse($response, $data[0], null);
    }

    public static function isOrgBanned(Request $request, Response $response, $args)
    {
        $orgId = $args['orgId'];
        return API\Dispatcher::sendResponse($response, DAO\AdminDao::isOrgBanned($orgId), null);
    }

    public static function unBanUser(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        DAO\AdminDao::unBanUser($userId);
        return API\Dispatcher::sendResponse($response, null, null);
    }

    public static function unBanOrg(Request $request, Response $response, $args)
    {
        $orgId = $args['orgId'];
        DAO\AdminDao::unBanOrg($orgId);
        return API\Dispatcher::sendResponse($response, null, null);
    }

    public static function getBannedUsers(Request $request, Response $response)
    {
        return API\Dispatcher::sendResponse($response, DAO\AdminDao::getBannedUser(), null);
    }

    public static function getBannedOrgs(Request $request, Response $response)
    {
        return API\Dispatcher::sendResponse($response, DAO\AdminDao::getBannedOrg(), null);
    }

    public static function banUser(Request $request, Response $response)
    {
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\BannedUser');
        DAO\AdminDao::saveBannedUser($data);
        Lib\Notify::sendBannedLoginEmail($data->getUserId());
        return API\Dispatcher::sendResponse($response, null, null);
    }

    public static function banOrg(Request $request, Response $response)
    {
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\BannedOrganisation');
        DAO\AdminDao::saveBannedOrg($data);
        return API\Dispatcher::sendResponse($response, null, null);
    }

    public static function getSiteAdmin(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        return API\Dispatcher::sendResponse($response, DAO\AdminDao::getAdmins($userId), null);
    }

    public static function getSiteAdmins(Request $request, Response $response)
    {
        return API\Dispatcher::sendResponse($response, DAO\AdminDao::getAdmins(), null);
    }
    
    public static function revokeTaskFromUser(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        $userId = $args['userId'];
        return API\Dispatcher::sendResponse($response, DAO\TaskDao::unClaimTask($taskId, $userId, true), null);
    }
}

Admins::init();
