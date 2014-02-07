<?php

require_once __DIR__."/../DataAccessObjects/AdminDao.class.php";

class Admins
{
    public static function init()
    {
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/admins(:format)/',
            function ($format = ".json") {
                Dispatcher::sendResponce(null, AdminDao::getAdmins(), null, $format);
            },
            'getSiteAdmins'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/admins/getOrgAdmins/:orgId/',
            function ($orgId, $format = ".json") {
                if (!is_numeric($orgId) && strstr($orgId, '.')) {
                    $orgId = explode('.', $orgId);
                    $format = '.'.$orgId[1];
                    $orgId = $orgId[0];
                }
                Dispatcher::sendResponce(null, AdminDao::getAdmins($orgId), null, $format);
            },
            'getOrgAdmins'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::PUT,
            '/v0/admins/:userId/',
            function ($userId, $format = '.json') {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                AdminDao::addSiteAdmin($userId);
                Dispatcher::sendResponce(null, null, null, $format);
            },
            'createSiteAdmin',
            'Middleware::authenticateSiteAdmin'
        );
        
               
        Dispatcher::registerNamed(
            HttpMethodEnum::DELETE,
            '/v0/admins/:userId/',
            function ($userId, $format = '.json') {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                AdminDao::removeAdmin($userId);
                Dispatcher::sendResponce(null, null, null, $format);
            },
            'deleteSiteAdmin',
            'Middleware::authenticateSiteAdmin'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::PUT,
            '/v0/admins/createOrgAdmin/:orgId/:userId/',
            function ($orgId, $userId, $format = '.json') {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                AdminDao::addOrgAdmin($userId, $orgId);
                Dispatcher::sendResponce(null, null, null, $format);
            },
            'createOrgAdmin',
            'Middleware::authenticateOrgAdmin'
        );
        
               
        Dispatcher::registerNamed(
            HttpMethodEnum::DELETE,
            '/v0/admins/removeOrgAdmin/:orgId/:userId/',
            function ($orgId, $userId, $format = '.json') {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                AdminDao::removeOrgAdmin($userId, $orgId);
                Dispatcher::sendResponce(null, null, null, $format);
            },
            'deleteOrgAdmin',
            'Middleware::authenticateOrgAdmin'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/admins/isSiteAdmin/:userId/',
            function ($userId, $format = '.json') {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $ret = false;
                $ret = AdminDao::isAdmin($userId, null);
                Dispatcher::sendResponce(null, $ret, null, $format);
            },
            'isSiteAdmin'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/admins/isOrgAdmin/:orgId/:userId/',
            function ($orgId, $userId, $format = '.json') {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                     $userId = explode('.', $userId);
                     $format = '.'.$userId[1];
                     $userId = $userId[0];
                }
                $ret = 0;
                $ret = AdminDao::isAdmin($userId, $orgId);
                if (is_null($orgId)) {
                    $ret = 0;
                }
                Dispatcher::sendResponce(null, $ret, null, $format);
            },
            'isOrgAdmin'
        );
        
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/admins/getBannedUsers(:format)/',
            function ($format = ".json") {
                Dispatcher::sendResponce(null, AdminDao::getBannedUser(), null, $format);
            },
            'getBannedUsers'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/admins/getBannedUser/:userId/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data=AdminDao::getBannedUser($userId);
                Dispatcher::sendResponce(null, $data[0], null, $format);
            },
            'getBannedUser'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/admins/getBannedOrgs(:format)/',
            function ($format = ".json") {
                Dispatcher::sendResponce(null, AdminDao::getBannedOrg(), null, $format);
            },
            'getBannedOrgs'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/admins/getBannedOrg/:orgId/',
            function ($orgId, $format = ".json") {
                if (!is_numeric($orgId) && strstr($orgId, '.')) {
                    $orgId = explode('.', $orgId);
                    $format = '.'.$orgId[1];
                    $orgId = $orgId[0];
                }
                $data=AdminDao::getBannedOrg($orgId);
                Dispatcher::sendResponce(null, $data[0], null, $format);
            },
            'getBannedOrg'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::POST,
            '/v0/admins/banUser(:format)/',
            function ($format = '.json') {
                $data = Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data = $client->deserialize($data, 'BannedUser');
                AdminDao::saveBannedUser($data);
                Dispatcher::sendResponce(null, null, null, $format);
                Notify::sendBannedLoginEmail($data->getUserId());
            },
            'banUser',
            'Middleware::authenticateSiteAdmin'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::POST,
            '/v0/admins/banOrg(:format)/',
            function ($format = '.json') {
                $data = Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data = $client->deserialize($data, 'BannedOrganisation');
                AdminDao::saveBannedOrg($data);
                Dispatcher::sendResponce(null, null, null, $format);
            },
            'banOrg',
            'Middleware::authenticateSiteAdmin'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::DELETE,
            '/v0/admins/unBanUser/:userId/',
            function ($userId, $format = '.json') {
                if (!is_numeric($userId)&& strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                AdminDao::unBanUser($userId);
                Dispatcher::sendResponce(null, null, null, $format);
            },
            'unBanUser',
            'Middleware::authenticateSiteAdmin'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::DELETE,
            '/v0/admins/unBanOrg/:orgId/',
            function ($orgId, $format = '.json') {
                if (!is_numeric($orgId)&& strstr($orgId, '.')) {
                    $orgId = explode('.', $orgId);
                    $format = '.'.$orgId[1];
                    $orgId = $orgId[0];
                }
                AdminDao::unBanOrg($orgId);
                Dispatcher::sendResponce(null, null, null, $format);
            },
            'unBanOrg',
            'Middleware::authenticateSiteAdmin'
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/admins/isUserBanned/:userId/',
            function ($userId, $format = ".json") {
                if (!is_numeric($userId)&& strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                Dispatcher::sendResponce(null, AdminDao::isUserBanned($userId), null, $format);
            },
            'isUserBanned',
            null
        );
        
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/admins/isOrgBanned/:orgId/',
            function ($orgId, $format = ".json") {
                if (!is_numeric($orgId)&& strstr($orgId, '.')) {
                    $orgId = explode('.', $orgId);
                    $format = '.'.$orgId[1];
                    $orgId = $orgId[0];
                }
                Dispatcher::sendResponce(null, AdminDao::isOrgBanned($orgId), null, $format);
            },
            'isOrgBanned'
        );
    }
}
Admins::init();
