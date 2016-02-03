<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;

require_once __DIR__."/../DataAccessObjects/AdminDao.class.php";
require_once __DIR__."/../DataAccessObjects/OrganisationDao.class.php";
require_once __DIR__."/../DataAccessObjects/BadgeDao.class.php";
require_once __DIR__."/../DataAccessObjects/ProjectDao.class.php";

class Orgs
{
    public static function init()
    {
        $app = \Slim\Slim::getInstance();

        $app->group('/v0', function () use ($app) {
            $app->group('/orgs', function () use ($app) {
                $app->group('/:orgId', function () use ($app) {
                    $app->group('/archivedProjects', function () use ($app) {

                        /* Routes starting /v0/orgs/:orgId/archivedProjects */
                        $app->get(
                            '/:projectId/tasks(:format)/',
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
                        '/projects(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Orgs::getOrgProjects'
                    );

                    $app->get(
                        '/archivedProjects(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Orgs::getOrgArchivedProjects'
                    );

                    $app->get(
                        '/badges(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Orgs::getOrgBadges'
                    );

                    $app->get(
                        '/members(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Orgs::getOrgMembers'
                    );

                    $app->get(
                        '/requests(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Orgs::getMembershipRequests'
                    );

                    $app->get(
                        '/trackingUsers(:format)/',
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

            /* Routes starting /v0 */
            $app->get(
                '/orgs(:format)/',
                '\SolasMatch\API\Lib\Middleware::isloggedIn',
                '\SolasMatch\API\V0\Orgs::getOrgs'
            );

            $app->post(
                '/orgs(:format)/',
                '\SolasMatch\API\Lib\Middleware::isloggedIn',
                '\SolasMatch\API\V0\Orgs::createOrg'
            );
        });
    }

    public static function getOrgArchivedProjectTasks($orgId, $projectId, $format = '.json')
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::getArchivedTask($projectId), null, $format);
    }

    public static function getOrgArchivedProject($orgId, $projectId, $format = '.json')
    {
        if (!is_numeric($projectId) && strstr($projectId, '.')) {
            $projectId = explode('.', $projectId);
            $format = '.'.$projectId[1];
            $projectId = $projectId[0];
        }
        $data=DAO\ProjectDao::getArchivedProject($projectId, $orgId);
        API\Dispatcher::sendResponse(null, $data[0], null, $format);
    }

    public static function createMembershipRequests($orgId, $uid, $format = ".json")
    {
        if (!is_numeric($uid) && strstr($uid, '.')) {
            $uid = explode('.', $uid);
            $format = '.'.$uid[1];
            $uid = $uid[0];
        }
        API\Dispatcher::sendResponse(null, DAO\OrganisationDao::requestMembership($uid, $orgId), null, $format);
    }

    public static function acceptMembershipRequests($orgId, $uid, $format = ".json")
    {
        if (!is_numeric($uid)&& strstr($uid, '.')) {
            $uid = explode('.', $uid);
            $format = '.'.$uid[1];
            $uid = $uid[0];
        }
        API\Dispatcher::sendResponse(null, DAO\OrganisationDao::acceptMemRequest($orgId, $uid), null, $format);
        Lib\Notify::notifyUserOrgMembershipRequest($uid, $orgId, true);
    }

    public static function rejectMembershipRequests($orgId, $uid, $format = ".json")
    {
        if (!is_numeric($uid) && strstr($uid, '.')) {
            $uid = explode('.', $uid);
            $format = '.'.$uid[1];
            $uid = $uid[0];
        }
        API\Dispatcher::sendResponse(null, DAO\OrganisationDao::refuseMemRequest($orgId, $uid), null, $format);
        Lib\Notify::notifyUserOrgMembershipRequest($uid, $orgId, false);
    }

    public static function getOrgProjects($orgId, $format = '.json')
    {
        API\Dispatcher::sendResponse(
            null,
            DAO\ProjectDao::getProjects(null, null, null, null, null, $orgId),
            null,
            $format
        );
    }

    public static function getOrgArchivedProjects($orgId, $format = '.json')
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::getArchivedProject(null, $orgId), null, $format);
    }

    public static function getUsersTrackingOrg($organisationId, $format = ".json")
    {
        $data = DAO\OrganisationDao::getUsersTrackingOrg($organisationId);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function addMember($email, $orgId, $format = ".json")
    {
        if (!is_numeric($orgId) && strstr($orgId, '.')) {
            $orgId = explode('.', $orgId);
            $format = '.'.$orgId[1];
            $orgId = $orgId[0];
        }
        $ret = false;
        $user = DAO\UserDao::getUser(null, $email);
        if (!is_null($user)) {
            $ret = DAO\OrganisationDao::acceptMemRequest($orgId, $user->getId());
        }
        API\Dispatcher::sendResponse(null, $ret, null, $format);
    }

    public static function isMember($orgId, $userId, $format = ".json")
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        $data = DAO\OrganisationDao::isMember($orgId, $userId);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getOrgByName($name, $format = ".json")
    {
        if (!is_numeric($name) && strstr($name, '.')) {
            $temp = explode('.', $name);
            $lastIndex = sizeof($temp)-1;
            if ($lastIndex > 0) {
                $format = '.'.$temp[$lastIndex];
                $name = $temp[0];
                for ($i = 1; $i < $lastIndex; $i++) {
                    $name = "{$name}.{$temp[$i]}";
                }
            }
        }
        $data= DAO\OrganisationDao::getOrgs(null, urldecode($name));
        $data = $data[0];
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function searchByName($name, $format = ".json")
    {
        if (!is_numeric($name) && strstr($name, '.')) {
            $temp = array();
            $temp = explode('.', $name);
            $lastIndex = sizeof($temp)-1;
            if ($lastIndex > 0) {
                $format = '.'.$temp[$lastIndex];
                $name = $temp[0];
                for ($i = 1; $i < $lastIndex; $i++) {
                    $name = "{$name}.{$temp[$i]}";
                }
            }
        }
        $data= DAO\OrganisationDao::searchForOrg(urldecode($name));
        if (!is_array($data) && !is_null($data)) {
            $data = array($data);
        }
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getOrgBadges($orgId, $format = ".json")
    {
        API\Dispatcher::sendResponse(null, DAO\BadgeDao::getOrgBadges($orgId), null, $format);
    }

    public static function getOrgMembers($orgId, $format = ".json")
    {
        API\Dispatcher::sendResponse(null, DAO\OrganisationDao::getOrgMembers($orgId), null, $format);
    }

    public static function getMembershipRequests($orgId, $format = ".json")
    {
        API\Dispatcher::sendResponse(null, DAO\OrganisationDao::getMembershipRequests($orgId), null, $format);
    }

    public static function getOrg($orgId, $format = ".json")
    {
        if (!is_numeric($orgId) && strstr($orgId, '.')) {
            $orgId = explode('.', $orgId);
            $format = '.'.$orgId[1];
            $orgId = $orgId[0];
        }
        $org = DAO\OrganisationDao::getOrg($orgId);
        API\Dispatcher::sendResponse(null, $org, null, $format);
    }

    public static function updateOrg($orgId, $format = ".json")
    {
        if (!is_numeric($orgId) && strstr($orgId, '.')) {
            $orgId = explode('.', $orgId);
            $format = '.'.$orgId[1];
            $orgId = $orgId[0];
        }
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Organisation");
        $data->setId($orgId);
        
        $organisation = DAO\OrganisationDao::getOrg(null, $data->getName());
        if ($organisation != null && $organisation->getId() != $data->getId()) {
            API\Dispatcher::sendResponse(null, null, Common\Enums\HttpStatusEnum::CONFLICT);
        }
        API\Dispatcher::sendResponse(null, DAO\OrganisationDao::insertAndUpdate($data), null, $format);
    }

    public static function deleteOrg($orgId, $format = ".json")
    {
        if (!is_numeric($orgId) && strstr($orgId, '.')) {
            $orgId = explode('.', $orgId);
            $format = '.'.$orgId[1];
            $orgId = $orgId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\OrganisationDao::delete($orgId), null, $format);
    }

    public static function getOrgs($format = ".json")
    {
        API\Dispatcher::sendResponse(null, DAO\OrganisationDao::getOrgs(), null, $format);
    }

    public static function createOrg($format = ".json")
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
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
            API\Dispatcher::sendResponse(null, $org, null, $format);
            if (!is_null($org) && $org->getId() > 0) {
                Lib\Notify::sendOrgCreatedNotifications($org->getId());
            }
        }
    }
}

Orgs::init();
