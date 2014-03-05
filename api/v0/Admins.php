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
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/admins(:format)/',
            function ($format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\AdminDao::getAdmins(), null, $format);
            },
            'getSiteAdmins'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/admins/getOrgAdmins/:orgId/',
            function ($orgId, $format = ".json") {
                if (!is_numeric($orgId) && strstr($orgId, '.')) {
                    $orgId = explode('.', $orgId);
                    $format = '.'.$orgId[1];
                    $orgId = $orgId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\AdminDao::getAdmins($orgId), null, $format);
            },
            'getOrgAdmins'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/admins/:userId/',
            function ($userId, $format = '.json') {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                DAO\AdminDao::addSiteAdmin($userId);
                API\Dispatcher::sendResponse(null, null, null, $format);
            },
            'createSiteAdmin',
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin'
        );
        
               
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/admins/:userId/',
            function ($userId, $format = '.json') {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                DAO\AdminDao::removeAdmin($userId);
                API\Dispatcher::sendResponse(null, null, null, $format);
            },
            'deleteSiteAdmin',
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/admins/createOrgAdmin/:orgId/:userId/',
            function ($orgId, $userId, $format = '.json') {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                DAO\AdminDao::addOrgAdmin($userId, $orgId);
                API\Dispatcher::sendResponse(null, null, null, $format);
            },
            'createOrgAdmin',
            '\SolasMatch\API\Lib\Middleware::authenticateOrgAdmin'
        );
        
               
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/admins/removeOrgAdmin/:orgId/:userId/',
            function ($orgId, $userId, $format = '.json') {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                DAO\AdminDao::removeOrgAdmin($userId, $orgId);
                API\Dispatcher::sendResponse(null, null, null, $format);
            },
            'deleteOrgAdmin',
            '\SolasMatch\API\Lib\Middleware::authenticateOrgAdmin'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/admins/isSiteAdmin/:userId/',
            function ($userId, $format = '.json') {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $ret = false;
                $ret = DAO\AdminDao::isAdmin($userId, null);
                API\Dispatcher::sendResponse(null, $ret, null, $format);
            },
            'isSiteAdmin'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/admins/isOrgAdmin/:orgId/:userId/',
            function ($orgId, $userId, $format = '.json') {
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
            },
            'isOrgAdmin'
        );
        
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/admins/getBannedUsers(:format)/',
            function ($format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\AdminDao::getBannedUser(), null, $format);
            },
            'getBannedUsers'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/admins/getBannedUser/:userId/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = DAO\AdminDao::getBannedUser($userId);
                API\Dispatcher::sendResponse(null, $data[0], null, $format);
            },
            'getBannedUser'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/admins/getBannedOrgs(:format)/',
            function ($format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\AdminDao::getBannedOrg(), null, $format);
            },
            'getBannedOrgs'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/admins/getBannedOrg/:orgId/',
            function ($orgId, $format = ".json") {
                if (!is_numeric($orgId) && strstr($orgId, '.')) {
                    $orgId = explode('.', $orgId);
                    $format = '.'.$orgId[1];
                    $orgId = $orgId[0];
                }
                $data = DAO\AdminDao::getBannedOrg($orgId);
                API\Dispatcher::sendResponse(null, $data[0], null, $format);
            },
            'getBannedOrg'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/admins/banUser(:format)/',
            function ($format = '.json') {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, 'BannedUser');
                DAO\AdminDao::saveBannedUser($data);
                API\Dispatcher::sendResponse(null, null, null, $format);
                Lib\Notify::sendBannedLoginEmail($data->getUserId());
            },
            'banUser',
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/admins/banOrg(:format)/',
            function ($format = '.json') {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, 'BannedOrganisation');
                DAO\AdminDao::saveBannedOrg($data);
                API\Dispatcher::sendResponse(null, null, null, $format);
            },
            'banOrg',
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/admins/unBanUser/:userId/',
            function ($userId, $format = '.json') {
                if (!is_numeric($userId)&& strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                DAO\AdminDao::unBanUser($userId);
                API\Dispatcher::sendResponse(null, null, null, $format);
            },
            'unBanUser',
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/admins/unBanOrg/:orgId/',
            function ($orgId, $format = '.json') {
                if (!is_numeric($orgId)&& strstr($orgId, '.')) {
                    $orgId = explode('.', $orgId);
                    $format = '.'.$orgId[1];
                    $orgId = $orgId[0];
                }
                DAO\AdminDao::unBanOrg($orgId);
                API\Dispatcher::sendResponse(null, null, null, $format);
            },
            'unBanOrg',
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/admins/isUserBanned/:userId/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId)&& strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\AdminDao::isUserBanned($userId), null, $format);
            },
            'isUserBanned',
            null
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/admins/isOrgBanned/:orgId/',
            function ($orgId, $format = ".json") {
                if (!is_numeric($orgId)&& strstr($orgId, '.')) {
                    $orgId = explode('.', $orgId);
                    $format = '.'.$orgId[1];
                    $orgId = $orgId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\AdminDao::isOrgBanned($orgId), null, $format);
            },
            'isOrgBanned'
        );
    }
}
Admins::init();
