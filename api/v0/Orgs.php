<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;

/**
 * Description of Orgs
 *
 * @author sean
 */
require_once __DIR__."/../DataAccessObjects/AdminDao.class.php";
require_once __DIR__."/../DataAccessObjects/OrganisationDao.class.php";
require_once __DIR__."/../DataAccessObjects/BadgeDao.class.php";
require_once __DIR__."/../DataAccessObjects/ProjectDao.class.php";

class Orgs
{
    public static function init()
    {
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/orgs(:format)/',
            function ($format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\OrganisationDao::getOrg(), null, $format);
            },
            'getOrgs'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/orgs(:format)/',
            function ($format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Organisation");
                $data->setId(null);
                $org = DAO\OrganisationDao::insertAndUpdate($data);
                $user = DAO\UserDao::getLoggedInUser();
                if (!is_null($org) && $org->getId() > 0) {
                    DAO\OrganisationDao::acceptMemRequest($org->getId(), $user->getId());
                    DAO\AdminDao::addOrgAdmin($user->getId(), $org->getId());
                    if (!DAO\AdminDao::isAdmin($user->getId(), $org->getId())) {
                        DAO\OrganisationDao::delete($org->getId());
                    }
                }
                API\Dispatcher::sendResponse(null, $org, null, $format);
                if (!is_null($org) && $org->getId() > 0) {
                    Lib\Notify::sendOrgCreatedNotifications($org->getId());
                }
            },
            'createOrg',
            '\SolasMatch\API\Lib\Middleware::isloggedIn'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/orgs/:orgId/',
            function ($orgId, $format = ".json") {
                if (!is_numeric($orgId) && strstr($orgId, '.')) {
                    $orgId = explode('.', $orgId);
                    $format = '.'.$orgId[1];
                    $orgId = $orgId[0];
                }
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Organisation");
                $data->setId($orgId);
                API\Dispatcher::sendResponse(null, DAO\OrganisationDao::insertAndUpdate($data), null, $format);
            },
            'updateOrg',
            '\SolasMatch\API\Lib\Middleware::authenticateOrgAdmin'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/orgs/:orgId/',
            function ($orgId, $format = ".json") {
                if (!is_numeric($orgId) && strstr($orgId, '.')) {
                    $orgId = explode('.', $orgId);
                    $format = '.'.$orgId[1];
                    $orgId = $orgId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\OrganisationDao::delete($orgId), null, $format);
            },
            'deleteOrg',
            '\SolasMatch\API\Lib\Middleware::authenticateOrgAdmin'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/orgs/:orgId/',
            function ($orgId, $format = ".json") {
                if (!is_numeric($orgId) && strstr($orgId, '.')) {
                    $orgId = explode('.', $orgId);
                    $format = '.'.$orgId[1];
                    $orgId = $orgId[0];
                }
                $data = DAO\OrganisationDao::getOrg($orgId);
                if (is_array($data)) {
                    $data = $data[0];
                }
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getOrg',
            null
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/orgs/isMember/:orgId/:userId/',
            function ($orgId, $userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = DAO\OrganisationDao::isMember($orgId, $userId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'isMember'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/orgs/getByName/:name/',
            function ($name, $format = ".json") {
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
                $data= DAO\OrganisationDao::getOrg(null, urldecode($name));
                $data = $data[0];
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getOrgByName'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/orgs/searchByName/:name/',
            function ($name, $format = ".json") {
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
            },
            'searchByName'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/orgs/:orgId/projects(:format)/',
            function ($orgId, $format = '.json') {
                API\Dispatcher::sendResponse(
                    null,
                    DAO\ProjectDao::getProject(null, null, null, null, null, $orgId),
                    null,
                    $format
                );
            },
            'getOrgProjects'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/orgs/:orgId/archivedProjects(:format)/',
            function ($orgId, $format = '.json') {
                API\Dispatcher::sendResponse(null, DAO\ProjectDao::getArchivedProject(null, $orgId), null, $format);
            },
            'getOrgArchivedProjects'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/orgs/:orgId/archivedProjects/:projectId/',
            function ($orgId, $projectId, $format = '.json') {
                if (!is_numeric($projectId) && strstr($projectId, '.')) {
                    $projectId = explode('.', $projectId);
                    $format = '.'.$projectId[1];
                    $projectId = $projectId[0];
                }
                $data=DAO\ProjectDao::getArchivedProject($projectId, $orgId);
                API\Dispatcher::sendResponse(null, $data[0], null, $format);
            },
            'getOrgArchivedProject'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/orgs/:orgId/archivedProjects/:projectId/tasks(:format)/',
            function ($orgId, $projectId, $format = '.json') {
                API\Dispatcher::sendResponse(null, DAO\ProjectDao::getArchivedTask($projectId), null, $format);
            },
            'getOrgArchivedProjectTasks'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/orgs/:orgId/badges(:format)/',
            function ($orgId, $format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\BadgeDao::getOrgBadges($orgId), null, $format);
            },
            'getOrgBadges'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/orgs/:orgId/members(:format)/',
            function ($orgId, $format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\OrganisationDao::getOrgMembers($orgId), null, $format);
            },
            'getOrgMembers'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/orgs/:orgId/requests(:format)/',
            function ($orgId, $format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\OrganisationDao::getMembershipRequests($orgId), null, $format);
            },
            'getMembershipRequests'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/orgs/:orgId/requests/:uid/',
            function ($orgId, $uid, $format = ".json") {
                if (!is_numeric($uid) && strstr($uid, '.')) {
                    $uid = explode('.', $uid);
                    $format = '.'.$uid[1];
                    $uid = $uid[0];
                }
                API\Dispatcher::sendResponse(null, DAO\OrganisationDao::requestMembership($uid, $orgId), null, $format);
            },
            'createMembershipRequests'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/orgs/:orgId/requests/:uid/',
            function ($orgId, $uid, $format = ".json") {
                if (!is_numeric($uid)&& strstr($uid, '.')) {
                    $uid = explode('.', $uid);
                    $format = '.'.$uid[1];
                    $uid = $uid[0];
                }
                API\Dispatcher::sendResponse(null, DAO\OrganisationDao::acceptMemRequest($orgId, $uid), null, $format);
                Lib\Notify::notifyUserOrgMembershipRequest($uid, $orgId, true);
            },
            'acceptMembershipRequests',
            '\SolasMatch\API\Lib\Middleware::authenticateOrgMember'
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/orgs/addMember/:email/:orgId/',
            function ($email, $orgId, $format = ".json") {
                if (!is_numeric($orgId) && strstr($orgId, '.')) {
                    $orgId = explode('.', $orgId);
                    $format = '.'.$orgId[1];
                    $orgId = $orgId[0];
                }
                $ret = false;
                $user = DAO\UserDao::getUser(null, $email);
                if (count($user) > 0) {
                    $user = $user[0];
                    $ret = DAO\OrganisationDao::acceptMemRequest($orgId, $user->getId());
                }
                API\Dispatcher::sendResponse(null, $ret, null, $format);
            },
            'addMember',
            '\SolasMatch\API\Lib\Middleware::authenticateOrgMember'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/orgs/:orgId/requests/:uid/',
            function ($orgId, $uid, $format = ".json") {
                if (!is_numeric($uid) && strstr($uid, '.')) {
                    $uid = explode('.', $uid);
                    $format = '.'.$uid[1];
                    $uid = $uid[0];
                }
                API\Dispatcher::sendResponse(null, DAO\OrganisationDao::refuseMemRequest($orgId, $uid), null, $format);
                Lib\Notify::notifyUserOrgMembershipRequest($uid, $orgId, false);
            },
            'rejectMembershipRequests',
            '\SolasMatch\API\Lib\Middleware::authenticateOrgMember'
        );
    }
}

Orgs::init();
