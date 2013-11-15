<?php

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
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs(:format)/', function ($format = ".json") {
            Dispatcher::sendResponce(null, OrganisationDao::getOrg(), null, $format);
        }, 'getOrgs');        
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/orgs(:format)/', 
        function ($format = ".json") 
        {

            $data = Dispatcher::getDispatcher()->request()->getBody();
            $client = new APIHelper($format);
            $data = $client->deserialize($data,"Organisation");
            $data->setId(null);
            $org = OrganisationDao::insertAndUpdate($data);
			$user = UserDao::getLoggedInUser();
//			if(is_null($org) || $org->getId() <= 0)
//			{
//				if(!is_numeric($org->getId()))
//				{
//					OrganisationDao::delete($org->getId());
//				}
//			}
			if (!is_null($org) && $org->getId() > 0) 
			{
				OrganisationDao::acceptMemRequest($org->getId(), $user->getId());
                AdminDao::addOrgAdmin($user->getId(), $org->getId());
                if(!AdminDao::isAdmin($user->getId(), $org->getId()))
                {
                	OrganisationDao::delete($org->getId());
                }	
            }					
            Dispatcher::sendResponce(null, $org, null, $format);
            if (!is_null($org) && $org->getId() > 0) {
                Notify::sendOrgCreatedNotifications($org->getId());
            }
        }
        , 'createOrg', 'Middleware::isloggedIn');
        
        
        /*
		 * 
		 * 	Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/admins/createOrgAdmin/:orgId/:userId/',
                                                        function ($orgId, $userId, $format = '.json') {            
            if (!is_numeric($userId) && strstr($userId, '.')) {
                 $userId = explode('.', $userId);
                 $format = '.'.$userId[1];
                 $userId = $userId[0];
            }
            AdminDao::addOrgAdmin($userId, $orgId);
            Dispatcher::sendResponce(null, null, null, $format);
        }, 'createOrgAdmin', 'Middleware::authenticateOrgAdmin');
		 * 
		 * 
		 * 
		 */
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/orgs/:orgId/', function ($orgId, $format = ".json") {
            if (!is_numeric($orgId) && strstr($orgId, '.')) {
                $orgId = explode('.', $orgId);
                $format = '.'.$orgId[1];
                $orgId = $orgId[0];
            }
            $data = Dispatcher::getDispatcher()->request()->getBody();
            $client = new APIHelper($format);
            $data = $client->deserialize($data,"Organisation");
            $data->setId($orgId);
//            $data = $client->cast("Organisation", $data);
            Dispatcher::sendResponce(null, OrganisationDao::insertAndUpdate($data), null, $format);
        }, 'updateOrg', 'Middleware::authenticateOrgAdmin');
        
        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/orgs/:orgId/', function ($orgId, $format = ".json"){
            if (!is_numeric($orgId) && strstr($orgId, '.')) {
                $orgId = explode('.', $orgId);
                $format = '.'.$orgId[1];
                $orgId = $orgId[0];
            }
            Dispatcher::sendResponce(null, OrganisationDao::delete($orgId), null, $format);
        }, 'deleteOrg', 'Middleware::authenticateOrgAdmin');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:orgId/', function ($orgId, $format = ".json"){
            if (!is_numeric($orgId)&& strstr($orgId, '.')) {
                $orgId = explode('.', $orgId);
                $format = '.'.$orgId[1];
                $orgId = $orgId[0];
            }
            $data = OrganisationDao::getOrg($orgId);
            if (is_array($data)) {
                $data = $data[0];
            }
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getOrg');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/isMember/:orgId/:userId/', function ($orgId,$userId , $format = ".json"){
            if (!is_numeric($userId)&& strstr($userId, '.')) {
                $userId = explode('.', $userId);
                $format = '.'.$userId[1];
                $userId = $userId[0];
            }
            $data = OrganisationDao::isMember($orgId, $userId);
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'isMember');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/getByName/:name/',
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
            $data= OrganisationDao::getOrg(null, urldecode($name));
            $data = $data[0];
//            if (!is_array($data) && !is_null($data)) {
//                $data = array($data);
//            }
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getOrgByName');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/searchByName/:name/',
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
            $data= OrganisationDao::searchForOrg(urldecode($name));
            if (!is_array($data) && !is_null($data)) {
                $data = array($data);
            }
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'searchByName');

        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:orgId/projects(:format)/',
            function ($orgId, $format = '.json'){
                Dispatcher::sendResponce(null, ProjectDao::getProject(null,null,null,null,null,$orgId), null, $format);
            }, 'getOrgProjects');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:orgId/archivedProjects(:format)/',
            function ($orgId, $format = '.json'){
                Dispatcher::sendResponce(null, ProjectDao::getArchivedProject(null,$orgId), null, $format);
            }, 'getOrgArchivedProjects');
            
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:orgId/archivedProjects/:projectId/',
            function ($orgId,$projectId, $format = '.json'){
                if (!is_numeric($projectId) && strstr($projectId, '.')) {
                    $projectId = explode('.', $projectId);
                    $format = '.'.$projectId[1];
                    $projectId = $projectId[0];
                }
                $data=ProjectDao::getArchivedProject($projectId,$orgId);
                Dispatcher::sendResponce(null,$data[0] , null, $format);
            }, 'getOrgArchivedProject');
            
            
         Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:orgId/archivedProjects/:projectId/tasks(:format)/',
            function ($orgId,$projectId, $format = '.json'){
                Dispatcher::sendResponce(null,ProjectDao::getArchivedTask($projectId), null, $format);
            }, 'getOrgArchivedProjectTasks');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:orgId/badges(:format)/',
                                                        function ($orgId, $format= ".json") {
            
            Dispatcher::sendResponce(null, BadgeDao::getOrgBadges($orgId), null, $format);
        }, 'getOrgBadges');    
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:orgId/members(:format)/',
                                                        function ($orgId, $format = ".json") {
            
            Dispatcher::sendResponce(null, OrganisationDao::getOrgMembers($orgId), null, $format);
        }, 'getOrgMembers');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:orgId/requests(:format)/',
                                                        function ($orgId, $format = ".json") {
            
            Dispatcher::sendResponce(null, OrganisationDao::getMembershipRequests($orgId), null, $format);
        }, 'getMembershipRequests');
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/orgs/:orgId/requests/:uid/',
                                                        function ($orgId, $uid, $format = ".json") {
            
            if (!is_numeric($uid) && strstr($uid, '.')) {
                $uid = explode('.', $uid);
                $format = '.'.$uid[1];
                $uid = $uid[0];
            }
            Dispatcher::sendResponce(null, OrganisationDao::requestMembership($uid, $orgId), null, $format);
        }, 'createMembershipRequests');
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/orgs/:orgId/requests/:uid/',
                                                        function ($orgId, $uid, $format = ".json") {
 	            	
 	        if (!is_numeric($uid)&& strstr($uid, '.')) {
                $uid = explode('.', $uid);
                $format = '.'.$uid[1];
                $uid = $uid[0];
            }
            
            Dispatcher::sendResponce(null, OrganisationDao::acceptMemRequest($orgId, $uid), null, $format);
			Notify::notifyUserOrgMembershipRequest($uid, $orgId, true);
        }, 'acceptMembershipRequests', 'Middleware::authenticateOrgMember');

        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/orgs/addMember/:email/:orgId/',
                function ($email, $orgId, $format = ".json")
                {
                    if (!is_numeric($orgId) && strstr($orgId, '.')) {
                        $orgId = explode('.', $orgId);
                        $format = '.'.$orgId[1];
                        $orgId = $orgId[0];
                    }
                    $ret = false;
                    $user = UserDao::getUser(null, $email);
                    if (count($user) > 0) {
                        $user = $user[0];
                        $ret = OrganisationDao::acceptMemRequest($orgId, $user->getId());
                    }
                    Dispatcher::sendResponce(null, $ret, null, $format);
                }, 'addMember', 'Middleware::authenticateOrgMember');      // Add middleware to authenticate the logged in user for the org
        
        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/orgs/:orgId/requests/:uid/',
                                                        function ($orgId, $uid, $format = ".json") {
            
            if (!is_numeric($uid) && strstr($uid, '.')) {
                $uid = explode('.', $uid);
                $format = '.'.$uid[1];
                $uid = $uid[0];
            }
//            Notify::notifyUserOrgMembershipRequest($uid, $id, false); always put after failure to send notification should not break the site.
            Dispatcher::sendResponce(null, OrganisationDao::refuseMemRequest($orgId, $uid), null, $format);
            Notify::notifyUserOrgMembershipRequest($uid, $orgId, false);
        }, 'rejectMembershipRequests', 'Middleware::authenticateOrgMember');
       
    }
}

Orgs::init();

