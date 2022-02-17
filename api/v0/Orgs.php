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
            '/v0/orgs/:orgId/archivedProjects/:projectId/tasks/',
            '\SolasMatch\API\V0\Orgs::getOrgArchivedProjectTasks'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->get(
            '/v0/orgs/:orgId/archivedProjects/:projectId/',
            '\SolasMatch\API\V0\Orgs::getOrgArchivedProject'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->post(
            '/v0/orgs/:orgId/requests/:uid/',
            '\SolasMatch\API\V0\Orgs::createMembershipRequests'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->put(
            '/v0/orgs/:orgId/requests/:uid/',
            '\SolasMatch\API\V0\Orgs::acceptMembershipRequests'
            '\SolasMatch\API\Lib\Middleware::authenticateOrgMember',
        );

        $app->delete(
            '/v0/orgs/:orgId/requests/:uid/',
            '\SolasMatch\API\V0\Orgs::rejectMembershipRequests'
            '\SolasMatch\API\Lib\Middleware::authenticateOrgMember',
        );

        $app->get(
            '/v0/orgs/:orgId/projects/',
            '\SolasMatch\API\V0\Orgs::getOrgProjects'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->get(
            '/v0/orgs/:orgId/archivedProjects/',
            '\SolasMatch\API\V0\Orgs::getOrgArchivedProjects'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->get(
            '/v0/orgs/:orgId/badges/',
            '\SolasMatch\API\V0\Orgs::getOrgBadges'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->get(
            '/v0/orgs/:orgId/members/',
            '\SolasMatch\API\V0\Orgs::getOrgMembers'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->get(
            '/v0/orgs/:orgId/requests/',
            '\SolasMatch\API\V0\Orgs::getMembershipRequests'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->get(
            '/v0/orgs/:orgId/trackingUsers/',
            '\SolasMatch\API\V0\Orgs::getUsersTrackingOrg'
            '\SolasMatch\API\Lib\Middleware::authenticateOrgMember',
        );

        $app->put(
            '/v0/orgs/addMember/:email/:orgId/',
            '\SolasMatch\API\V0\Orgs::addMember'
            '\SolasMatch\API\Lib\Middleware::authenticateOrgMember',
        );

        $app->get(
            '/v0/orgs/isMember/:orgId/:userId/',
            '\SolasMatch\API\V0\Orgs::isMember'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->get(
            '/v0/orgs/getByName/:name/',
            '\SolasMatch\API\V0\Orgs::getOrgByName'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->get(
            '/v0/orgs/searchByName/:name/',
            '\SolasMatch\API\V0\Orgs::searchByName'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->get(
            '/v0/orgs/:orgId/',
            '\SolasMatch\API\V0\Orgs::getOrg'
        );

        $app->put(
            '/v0/orgs/:orgId/',
            '\SolasMatch\API\V0\Orgs::updateOrg'
            '\SolasMatch\API\Lib\Middleware::authenticateOrgAdmin',
        );

        $app->delete(
            '/v0/orgs/:orgId/',
            '\SolasMatch\API\V0\Orgs::deleteOrg'
            '\SolasMatch\API\Lib\Middleware::authenticateOrgAdmin',
        );

        $app->get(
            '/v0/orgextended/:orgId/',
            '\SolasMatch\API\V0\Orgs::getOrganisationExtendedProfile'
        );

        $app->put(
            '/v0/orgextended/:orgId/',
            '\SolasMatch\API\V0\Orgs::updateOrgExtendedProfile'
            '\SolasMatch\API\Lib\Middleware::authenticateOrgAdmin',
        );

        $app->get(
            '/v0/subscription/:org_id/',
            '\SolasMatch\API\V0\Orgs::getSubscription'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->post(
            '/v0/subscription/:org_id/level/:level/spare/:spare/start_date/:start_date/',
            '\SolasMatch\API\V0\Orgs::updateSubscription'
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
        );

        $app->get(
            '/v0/orgs/',
            '\SolasMatch\API\V0\Orgs::getOrgs'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->post(
            '/v0/orgs/',
            '\SolasMatch\API\V0\Orgs::createOrg'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );
    }

    public static function getOrgArchivedProjectTasks($orgId, $projectId)
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::getArchivedTask($projectId), null);
    }

    public static function getOrgArchivedProject($orgId, $projectId)
    {
        $data=DAO\ProjectDao::getArchivedProject($projectId, $orgId);
        API\Dispatcher::sendResponse(null, $data[0], null);
    }

    public static function createMembershipRequests($orgId, $uid)
    {
        API\Dispatcher::sendResponse(null, DAO\OrganisationDao::requestMembership($uid, $orgId), null);
    }

    public static function acceptMembershipRequests($orgId, $uid)
    {
        API\Dispatcher::sendResponse(null, DAO\OrganisationDao::acceptMemRequest($orgId, $uid), null);
        Lib\Notify::notifyUserOrgMembershipRequest($uid, $orgId, true);
    }

    public static function rejectMembershipRequests($orgId, $uid)
    {
        API\Dispatcher::sendResponse(null, DAO\OrganisationDao::refuseMemRequest($orgId, $uid), null);
        Lib\Notify::notifyUserOrgMembershipRequest($uid, $orgId, false);
    }

    public static function getOrgProjects($orgId)
    {
        API\Dispatcher::sendResponse(
            null,
            DAO\ProjectDao::getProjects(null, null, null, null, null, $orgId),
            null
        );
    }

    public static function getOrgArchivedProjects($orgId)
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::getArchivedProject(null, $orgId), null);
    }

    public static function getUsersTrackingOrg($organisationId)
    {
        $data = DAO\OrganisationDao::getUsersTrackingOrg($organisationId);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function addMember($email, $orgId)
    {
        $ret = false;
        $user = DAO\UserDao::getUser(null, $email);
        if (!is_null($user)) {
            $ret = DAO\OrganisationDao::acceptMemRequest($orgId, $user->getId());
            DAO\AdminDao::addOrgAdmin($user->getId(), $orgId); // When manually adding a user to the Organisation, make them an Admin for simplicity
        }
        API\Dispatcher::sendResponse(null, $ret, null);
    }

    public static function isMember($orgId, $userId)
    {
        $data = DAO\OrganisationDao::isMember($orgId, $userId);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getOrgByName($name)
    {
        $data= DAO\OrganisationDao::getOrgs(null, urldecode($name));
        $data = $data[0];
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function searchByName($name)
    {
        $data= DAO\OrganisationDao::searchForOrg(urldecode($name));
        if (!is_array($data) && !is_null($data)) {
            $data = array($data);
        }
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getOrgBadges($orgId)
    {
        API\Dispatcher::sendResponse(null, DAO\BadgeDao::getOrgBadges($orgId), null);
    }

    public static function getOrgMembers($orgId)
    {
        API\Dispatcher::sendResponse(null, DAO\OrganisationDao::getOrgMembers($orgId), null);
    }

    public static function getMembershipRequests($orgId)
    {
        API\Dispatcher::sendResponse(null, DAO\OrganisationDao::getMembershipRequests($orgId), null);
    }

    public static function getOrg($orgId)
    {
        $org = DAO\OrganisationDao::getOrg($orgId);
        API\Dispatcher::sendResponse(null, $org, null);
    }

    public static function getOrganisationExtendedProfile($orgId)
    {
        $org = DAO\OrganisationDao::getOrganisationExtendedProfile($orgId);
        API\Dispatcher::sendResponse(null, $org, null);
    }

    public static function updateOrg($orgId)
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Organisation");
        $data->setId($orgId);
        
        $organisation = DAO\OrganisationDao::getOrg(null, $data->getName());
        if ($organisation != null && $organisation->getId() != $data->getId()) {
            API\Dispatcher::sendResponse(null, null, Common\Enums\HttpStatusEnum::CONFLICT);
        }
        API\Dispatcher::sendResponse(null, DAO\OrganisationDao::insertAndUpdate($data), null);
    }

    public static function updateOrgExtendedProfile($orgId)
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\OrganisationExtendedProfile");
        $data->setId($orgId);
        API\Dispatcher::sendResponse(null, DAO\OrganisationDao::insertAndUpdateExtendedProfile($data), null);
    }

    public static function deleteOrg($orgId)
    {
        API\Dispatcher::sendResponse(null, DAO\OrganisationDao::delete($orgId), null);
    }

    public static function getOrgs()
    {
        API\Dispatcher::sendResponse(null, DAO\OrganisationDao::getOrgs(), null);
    }

    public static function createOrg()
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Organisation");
        $data->setId("");
        $org = null;
        $name = $data->getName();
        //Is org name already in use?
        if (DAO\OrganisationDao::getOrg(null, $name) != null) {
            API\Dispatcher::sendResponse(null, null, Common\Enums\HttpStatusEnum::CONFLICT);
        } else {
            $org = DAO\OrganisationDao::insertAndUpdate($data);
            $user = DAO\UserDao::getLoggedInUser();
            if (!is_null($org) && $org->getId() > 0) {
                error_log('Calling addOrgAdmin(' . $user->getId() . ', ' . $org->getId() . ')');
                DAO\AdminDao::addOrgAdmin($user->getId(), $org->getId());
                /*if (!DAO\AdminDao::isAdmin($user->getId(), $org->getId())) {
                    DAO\OrganisationDao::delete($org->getId());
                }*/
            }
            API\Dispatcher::sendResponse(null, $org, null);
            if (!is_null($org) && $org->getId() > 0) {
                Lib\Notify::sendOrgCreatedNotifications($org->getId());
            }
        }
    }

    public static function getSubscription($org_id)
    {
        $ret = DAO\OrganisationDao::getSubscription($org_id);
        API\Dispatcher::sendResponse(null, $ret, null);
    }

    public static function updateSubscription($org_id, $level, $spare, $start_date)
    {
        $comment = API\Dispatcher::getDispatcher()->request()->getBody();
        $comment = trim($comment);
        API\Dispatcher::sendResponse(null, DAO\OrganisationDao::updateSubscription($org_id, $level, $spare, urldecode($start_date), $comment), null);
    }
}

Orgs::init();
