<?php

/**
 * Description of Orgs
 *
 * @author sean
 */

require_once __DIR__."/../DataAccessObjects/OrganisationDao.class.php";
require_once __DIR__."/../DataAccessObjects/BadgeDao.class.php";
require_once __DIR__."/../DataAccessObjects/ProjectDao.class.php";

class Orgs {
    
    public static function init()
    {
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs(:format)/', function ($format = ".json") {
            Dispatcher::sendResponce(null, OrganisationDao::getOrg(null, null, null, null), null, $format);
        }, 'getOrgs');        
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/orgs(:format)/', function ($format = ".json") {
            $data = Dispatcher::getDispatcher()->request()->getBody();
            $client = new APIHelper($format);
            $data = $client->deserialize($data,"Organisation");
//            $data = $client->cast("Organisation", $data);
            $data->setId(null);
            Dispatcher::sendResponce(null, OrganisationDao::insertAndUpdate($data), null, $format);
        }, 'createOrg');
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/orgs/:id/', function ($id, $format = ".json") {
            if (!is_numeric($id) && strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            $data = Dispatcher::getDispatcher()->request()->getBody();
            $client = new APIHelper($format);
            $data = $client->deserialize($data,"Organisation");
//            $data = $client->cast("Organisation", $data);
            Dispatcher::sendResponce(null, OrganisationDao::insertAndUpdate($data), null, $format);
        }, 'updateOrg');
        
        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/orgs/:id/', function ($id, $format = ".json"){
            if (!is_numeric($id) && strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            Dispatcher::sendResponce(null, OrganisationDao::delete($id), null, $format);
        }, 'deleteOrg');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/', function ($id, $format = ".json"){
            if (!is_numeric($id)&& strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            $data = OrganisationDao::getOrg($id, null, null, null);
            if (is_array($data)) {
                $data = $data[0];
            }
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getOrg');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/isMember/:orgID/:id/', function ($orgID,$id, $format = ".json"){
            if (!is_numeric($id)&& strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            $data = OrganisationDao::isMember($orgID, $id);
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
            $data= OrganisationDao::searchForOrg($name);
            if (!is_array($data) && !is_null($data)) {
                $data = array($data);
            }
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getOrgByName');

        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/projects(:format)/',
            function ($id, $format = '.json')
            {
                Dispatcher::sendResponce(null, ProjectDao::getProject(null,null,null,null,null,$id), null, $format);
            }, 'getOrgProjects');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/archivedProjects(:format)/',
            function ($id, $format = '.json')
            {
                $params = array();
                $params['organisation_id'] = $id;
                Dispatcher::sendResponce(null, ProjectDao::getArchivedProject(null,null,null,null,null,$id), null, $format);
            }, 'getOrgArchivedProjects');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/badges(:format)/',
                                                        function ($id, $format= ".json") {
            
            Dispatcher::sendResponce(null, BadgeDao::getOrgBadges($id), null, $format);
        }, 'getOrgBadges');    
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/members(:format)/',
                                                        function ($id, $format = ".json") {
            
            Dispatcher::sendResponce(null, OrganisationDao::getOrgMembers($id), null, $format);
        }, 'getOrgMembers');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/requests(:format)/',
                                                        function ($id, $format = ".json") {
            
            Dispatcher::sendResponce(null, OrganisationDao::getMembershipRequests($id), null, $format);
        }, 'getMembershipRequests');
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/orgs/:id/requests/:uid/',
                                                        function ($id, $uid, $format = ".json") {
            
            if (!is_numeric($uid) && strstr($uid, '.')) {
                $uid = explode('.', $uid);
                $format = '.'.$uid[1];
                $uid = $uid[0];
            }
            Dispatcher::sendResponce(null, OrganisationDao::requestMembership($uid, $id), null, $format);
        }, 'createMembershipRequests');
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/orgs/:id/requests/:uid/',
                                                        function ($id, $uid, $format = ".json") {
            
            if (!is_numeric($uid)&& strstr($uid, '.')) {
                $uid = explode('.', $uid);
                $format = '.'.$uid[1];
                $uid = $uid[0];
            }
            Notify::notifyUserOrgMembershipRequest($uid, $id, true);
            Dispatcher::sendResponce(null, OrganisationDao::acceptMemRequest($id, $uid), null, $format);
        }, 'acceptMembershipRequests');
        
        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/orgs/:id/requests/:uid/',
                                                        function ($id, $uid, $format = ".json") {
            
            if (!is_numeric($uid) && strstr($uid, '.')) {
                $uid = explode('.', $uid);
                $format = '.'.$uid[1];
                $uid = $uid[0];
            }
            Notify::notifyUserOrgMembershipRequest($uid, $id, false);
            Dispatcher::sendResponce(null, OrganisationDao::refuseMemRequest($id, $uid), null, $format);
        }, 'rejectMembershipRequests');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/tasks(:format)/',
                                                        function ($id, $format=".json") {
            
            Dispatcher::sendResponce(null, TaskDao::findTasksByOrg(array("organisation_ids" => $id)), null, $format);
        }, 'getOrgTasks');        
        
    }
}
Orgs::init();

