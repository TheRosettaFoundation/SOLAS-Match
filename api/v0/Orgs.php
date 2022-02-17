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

        $app->group('/v0', function () use ($app) {
            $app->group('/orgs', function () use ($app) {
                $app->group('/:orgId', function () use ($app) {
                    $app->group('/archivedProjects', function () use ($app) {

                        /* Routes starting /v0/orgs/:orgId/archivedProjects */
                        $app->get(
                            '/:projectId/tasks/',
                            '\SolasMatch\API\Lib\Middleware::isloggedIn',
                            '\SolasMatch\API\V0\Orgs::getOrgArchivedProjectTasks'
                        );

                        $app->get(
                            '/:projectId/',
                            '\SolasMatch\API\Lib\Middleware::isloggedIn',
                            '\SolasMatch\API\V0\Orgs::getOrgArchivedProject'
                        );
                    });

                    $app->group('/requests', function () use ($app) {

                        /* Routes starting /v0/orgs/:orgId/requests */
                        $app->post(
                            '/:uid/',
                            '\SolasMatch\API\Lib\Middleware::isloggedIn',
                            '\SolasMatch\API\V0\Orgs::createMembershipRequests'
                        );

                        $app->put(
                            '/:uid/',
                            '\SolasMatch\API\Lib\Middleware::authenticateOrgMember',
                            '\SolasMatch\API\V0\Orgs::acceptMembershipRequests'
                        );

                        $app->delete(
                            '/:uid/',
                            '\SolasMatch\API\Lib\Middleware::authenticateOrgMember',
                            '\SolasMatch\API\V0\Orgs::rejectMembershipRequests'
                        );
                    });

                    /* Routes starting /v0/orgs/:orgId */
                    $app->get(
                        '/projects/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Orgs::getOrgProjects'
                    );

                    $app->get(
                        '/archivedProjects/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Orgs::getOrgArchivedProjects'
                    );

                    $app->get(
                        '/badges/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Orgs::getOrgBadges'
                    );

                    $app->get(
                        '/members/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Orgs::getOrgMembers'
                    );

                    $app->get(
                        '/requests/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Orgs::getMembershipRequests'
                    );

                    $app->get(
                        '/trackingUsers/',
                        '\SolasMatch\API\Lib\Middleware::authenticateOrgMember',
                        '\SolasMatch\API\V0\Orgs::getUsersTrackingOrg'
                    );
                });

                /* Routes starting /v0/orgs */
                $app->put(
                    '/addMember/:email/:orgId/',
                    '\SolasMatch\API\Lib\Middleware::authenticateOrgMember',
                    '\SolasMatch\API\V0\Orgs::addMember'
                );

                $app->get(
                    '/isMember/:orgId/:userId/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Orgs::isMember'
                );

                $app->get(
                    '/getByName/:name/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Orgs::getOrgByName'
                );

                $app->get(
                    '/searchByName/:name/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Orgs::searchByName'
                );

                $app->get(
                    '/:orgId/',
                    '\SolasMatch\API\V0\Orgs::getOrg'
                );

                $app->put(
                    '/:orgId/',
                    '\SolasMatch\API\Lib\Middleware::authenticateOrgAdmin',
                    '\SolasMatch\API\V0\Orgs::updateOrg'
                );

                $app->delete(
                    '/:orgId/',
                    '\SolasMatch\API\Lib\Middleware::authenticateOrgAdmin',
                    '\SolasMatch\API\V0\Orgs::deleteOrg'
                );
            });

            $app->group('/orgextended', function () use ($app) {

                /* Routes starting /v0/orgextended */
                $app->get(
                    '/:orgId/',
                    '\SolasMatch\API\V0\Orgs::getOrganisationExtendedProfile'
                );

                $app->put(
                    '/:orgId/',
                    '\SolasMatch\API\Lib\Middleware::authenticateOrgAdmin',
                    '\SolasMatch\API\V0\Orgs::updateOrgExtendedProfile'
                );
            });

            $app->group('/subscription', function () use ($app) {

                /* Routes starting /v0/subscription */
                $app->get(
                    '/:org_id/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Orgs::getSubscription'
                );

                $app->post(
                    '/:org_id/level/:level/spare/:spare/start_date/:start_date/',
                    '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
                    '\SolasMatch\API\V0\Orgs::updateSubscription'
                );
            });

            /* Routes starting /v0 */
            $app->get(
                '/orgs/',
                '\SolasMatch\API\Lib\Middleware::isloggedIn',
                '\SolasMatch\API\V0\Orgs::getOrgs'
            );

            $app->post(
                '/orgs/',
                '\SolasMatch\API\Lib\Middleware::isloggedIn',
                '\SolasMatch\API\V0\Orgs::createOrg'
            );
        });
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
