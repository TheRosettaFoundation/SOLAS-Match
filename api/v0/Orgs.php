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
            '/api/v0/orgs/{orgId}/projects/',
            '\SolasMatch\API\V0\Orgs:getOrgProjects')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

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

        $app->get(
            '/api/v0/orgs/',
            '\SolasMatch\API\V0\Orgs:getOrgs')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->post(
            '/api/v0/orgs/',
            '\SolasMatch\API\V0\Orgs:createOrg')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');
    }

    public static function getOrgProjects(Request $request, Response $response, $args)
    {
        $orgId = $args['orgId'];
        return API\Dispatcher::sendResponse($response,
            DAO\ProjectDao::getProjects(null, null, null, null, null, $orgId),
            null
        );
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

    public static function getOrg(Request $request, Response $response, $args)
    {
        $orgId = $args['orgId'];
        $org = DAO\OrganisationDao::getOrg($orgId);
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
                DAO\AdminDao::add_org_TWB_contact($org->getId(), $user->getId());
                Lib\Notify::sendOrgCreatedNotifications($org->getId());
            }
            return API\Dispatcher::sendResponse($response, $org, null);
        }
    }
}

Orgs::init();
