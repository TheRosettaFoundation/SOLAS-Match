<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__."/../DataAccessObjects/AdminDao.class.php";
require_once __DIR__."/../DataAccessObjects/OrganisationDao.class.php";
require_once __DIR__."/../DataAccessObjects/BadgeDao.class.php";
require_once __DIR__."/../DataAccessObjects/ProjectDao.class.php";

class Orgs
{
    public static function init()
    {
        global $app;

        $app->get(
            '/api/v0/orgs/{orgId}/archivedProjects/{projectId}/tasks/',
            '\SolasMatch\API\V0\Orgs:getOrgArchivedProjectTasks')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/api/v0/orgs/{orgId}/archivedProjects/{projectId}/',
            '\SolasMatch\API\V0\Orgs:getOrgArchivedProject')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/api/v0/orgs/{orgId}/projects/',
            '\SolasMatch\API\V0\Orgs:getOrgProjects')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/api/v0/orgs/{orgId}/archivedProjects/',
            '\SolasMatch\API\V0\Orgs:getOrgArchivedProjects')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/api/v0/orgs/{orgId}/badges/',
            '\SolasMatch\API\V0\Orgs:getOrgBadges')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/api/v0/orgs/{orgId}/trackingUsers/',
            '\SolasMatch\API\V0\Orgs:getUsersTrackingOrg')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateOrgAdmin');

        $app->get(
            '/api/v0/orgs/getByName/{name}/',
            '\SolasMatch\API\V0\Orgs:getOrgByName')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/api/v0/orgs/searchByName/{name}/',
            '\SolasMatch\API\V0\Orgs:searchByName')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/api/v0/orgs/{orgId}/',
            '\SolasMatch\API\V0\Orgs:getOrg');

        $app->put(
            '/api/v0/orgs/{orgId}/',
            '\SolasMatch\API\V0\Orgs:updateOrg')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateOrgAdmin');

        $app->delete(
            '/api/v0/orgs/{orgId}/',
            '\SolasMatch\API\V0\Orgs:deleteOrg')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateOrgAdmin');

        $app->get(
            '/api/v0/orgextended/{orgId}/',
            '\SolasMatch\API\V0\Orgs:getOrganisationExtendedProfile');

        $app->put(
            '/api/v0/orgextended/{orgId}/',
            '\SolasMatch\API\V0\Orgs:updateOrgExtendedProfile')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateOrgAdmin');

        $app->get(
            '/api/v0/subscription/{org_id}/',
            '\SolasMatch\API\V0\Orgs:getSubscription')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->post(
            '/api/v0/subscription/{org_id}/level/{level}/spare/{spare}/start_date/{start_date}/',
            '\SolasMatch\API\V0\Orgs:updateSubscription')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateSiteAdmin');

        $app->get(
            '/api/v0/orgs/',
            '\SolasMatch\API\V0\Orgs:getOrgs')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->post(
            '/api/v0/orgs/',
            '\SolasMatch\API\V0\Orgs:createOrg')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');
    }

    public static function getOrgArchivedProjectTasks(Request $request, Response $response, $args)
    {
        $orgId = $args['orgId'];
        $projectId = $args['projectId'];
        return API\Dispatcher::sendResponse($response, DAO\ProjectDao::getArchivedTask($projectId), null);
    }

    public static function getOrgArchivedProject(Request $request, Response $response, $args)
    {
        $orgId = $args['orgId'];
        $projectId = $args['projectId'];
        $data=DAO\ProjectDao::getArchivedProject($projectId, $orgId);
        return API\Dispatcher::sendResponse($response, $data[0], null);
    }

    public static function getOrgProjects(Request $request, Response $response, $args)
    {
        $orgId = $args['orgId'];
        return API\Dispatcher::sendResponse($response,
            DAO\ProjectDao::getProjects(null, null, null, null, null, $orgId),
            null
        );
    }

    public static function getOrgArchivedProjects(Request $request, Response $response, $args)
    {
        $orgId = $args['orgId'];
        return API\Dispatcher::sendResponse($response, DAO\ProjectDao::getArchivedProject(null, $orgId), null);
    }

    public static function getUsersTrackingOrg(Request $request, Response $response, $args)
    {
        $organisationId = $args['organisationId'];
        $data = DAO\OrganisationDao::getUsersTrackingOrg($organisationId);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getOrgByName(Request $request, Response $response, $args)
    {
        $name = $args['name'];
        $data= DAO\OrganisationDao::getOrgs(null, urldecode($name));
        $data = $data[0];
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function searchByName(Request $request, Response $response, $args)
    {
        $name = $args['name'];
        $data= DAO\OrganisationDao::searchForOrg(urldecode($name));
        if (!is_array($data) && !is_null($data)) {
            $data = array($data);
        }
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getOrgBadges(Request $request, Response $response, $args)
    {
        $orgId = $args['orgId'];
        return API\Dispatcher::sendResponse($response, DAO\BadgeDao::getOrgBadges($orgId), null);
    }

    public static function getOrg(Request $request, Response $response, $args)
    {
        $orgId = $args['orgId'];
        $org = DAO\OrganisationDao::getOrg($orgId);
        return API\Dispatcher::sendResponse($response, $org, null);
    }

    public static function getOrganisationExtendedProfile(Request $request, Response $response, $args)
    {
        $orgId = $args['orgId'];
        $org = DAO\OrganisationDao::getOrganisationExtendedProfile($orgId);
        return API\Dispatcher::sendResponse($response, $org, null);
    }

    public static function updateOrg(Request $request, Response $response, $args)
    {
        $orgId = $args['orgId'];
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Organisation");
        $data->setId($orgId);
        
        $organisation = DAO\OrganisationDao::getOrg(null, $data->getName());
        if ($organisation != null && $organisation->getId() != $data->getId()) {
            return API\Dispatcher::sendResponse($response, null, Common\Enums\HttpStatusEnum::CONFLICT);
        }
        return API\Dispatcher::sendResponse($response, DAO\OrganisationDao::insertAndUpdate($data), null);
    }

    public static function updateOrgExtendedProfile(Request $request, Response $response, $args)
    {
        $orgId = $args['orgId'];
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\OrganisationExtendedProfile");
        $data->setId($orgId);
        return API\Dispatcher::sendResponse($response, DAO\OrganisationDao::insertAndUpdateExtendedProfile($data), null);
    }

    public static function deleteOrg(Request $request, Response $response, $args)
    {
        $orgId = $args['orgId'];
        return API\Dispatcher::sendResponse($response, DAO\OrganisationDao::delete($orgId), null);
    }

    public static function getOrgs(Request $request, Response $response)
    {
        return API\Dispatcher::sendResponse($response, DAO\OrganisationDao::getOrgs(), null);
    }

    public static function createOrg(Request $request, Response $response)
    {
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Organisation");
        $data->setId("");
        $org = null;
        $name = $data->getName();
        //Is org name already in use?
        if (DAO\OrganisationDao::getOrg(null, $name) != null) {
            return API\Dispatcher::sendResponse($response, null, Common\Enums\HttpStatusEnum::CONFLICT);
        } else {
            $org = DAO\OrganisationDao::insertAndUpdate($data);
            $user = DAO\UserDao::getLoggedInUser();
            if (!is_null($org) && $org->getId() > 0) {
                error_log('Calling addOrgAdmin(' . $user->getId() . ', ' . $org->getId() . ')');
                DAO\AdminDao::addOrgAdmin($user->getId(), $org->getId());
                Lib\Notify::sendOrgCreatedNotifications($org->getId());
            }
            return API\Dispatcher::sendResponse($response, $org, null);
        }
    }

    public static function getSubscription(Request $request, Response $response, $args)
    {
        $org_id = $args['org_id'];
        $ret = DAO\OrganisationDao::getSubscription($org_id);
        return API\Dispatcher::sendResponse($response, $ret, null);
    }

    public static function updateSubscription(Request $request, Response $response, $args)
    {
        $org_id = $args['org_id'];
        $level = $args['level'];
        $spare = $args['spare'];
        $start_date = $args['start_date'];
        $comment = (string)$request->getBody();
        $comment = trim($comment);
        return API\Dispatcher::sendResponse($response, DAO\OrganisationDao::updateSubscription($org_id, $level, $spare, urldecode($start_date), $comment), null);
    }
}

Orgs::init();
