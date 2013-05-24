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

class Orgs {
    
    public static function init()
    {
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs(:format)/', function ($format = ".json") {
            Dispatcher::sendResponce(null, OrganisationDao::getOrg(), null, $format);
        }, 'getOrgs');        
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/orgs(:format)/', function ($format = ".json") {

            $data = Dispatcher::getDispatcher()->request()->getBody();
            $client = new APIHelper($format);
            $data = $client->deserialize($data,"Organisation");
            $data->setId(null);
            $org = OrganisationDao::insertAndUpdate($data);
            Dispatcher::sendResponce(null, $org, null, $format);
            if ($org->getId() > 0) {
                Notify::sendOrgCreatedNotifications($org->getId());
            }
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
            $data->setId($id);
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
            $data = OrganisationDao::getOrg($id);
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
            $data= OrganisationDao::getOrg(null, $name);
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
            $data= OrganisationDao::searchForOrg($name);
            if (!is_array($data) && !is_null($data)) {
                $data = array($data);
            }
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'searchByName');

        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/projects(:format)/',
            function ($id, $format = '.json'){
                Dispatcher::sendResponce(null, ProjectDao::getProject(null,null,null,null,null,$id), null, $format);
            }, 'getOrgProjects');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/archivedProjects(:format)/',
            function ($id, $format = '.json'){
                Dispatcher::sendResponce(null, ProjectDao::getArchivedProject(null,$id), null, $format);
            }, 'getOrgArchivedProjects');
            
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/archivedProjects/:projectID/',
            function ($id,$projectID, $format = '.json'){
                if (!is_numeric($projectID) && strstr($projectID, '.')) {
                    $projectID = explode('.', $projectID);
                    $format = '.'.$projectID[1];
                    $projectID = $projectID[0];
                }
                $data=ProjectDao::getArchivedProject($projectID,$id);
                Dispatcher::sendResponce(null,$data[0] , null, $format);
            }, 'getOrgArchivedProject');
            
            
         Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/archivedProjects/:projectID/tasks(:format)/',
            function ($id,$projectID, $format = '.json'){
                Dispatcher::sendResponce(null,ProjectDao::getArchivedTask($projectId), null, $format);
            }, 'getOrgArchivedProjectTasks');
        
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
            
            Dispatcher::sendResponce(null, OrganisationDao::acceptMemRequest($id, $uid), null, $format);
            
            Notify::notifyUserOrgMembershipRequest($uid, $id, true);

        }, 'acceptMembershipRequests');
        
        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/orgs/:id/requests/:uid/',
                                                        function ($id, $uid, $format = ".json") {
            
            if (!is_numeric($uid) && strstr($uid, '.')) {
                $uid = explode('.', $uid);
                $format = '.'.$uid[1];
                $uid = $uid[0];
            }
//            Notify::notifyUserOrgMembershipRequest($uid, $id, false); always put after failure to send notification should not break the site.
            Dispatcher::sendResponce(null, OrganisationDao::refuseMemRequest($id, $uid), null, $format);
            Notify::notifyUserOrgMembershipRequest($uid, $id, false);
        }, 'rejectMembershipRequests');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/tasks(:format)/',
                                                        function ($id, $format=".json") {
            
            Dispatcher::sendResponce(null, TaskDao::findTasksByOrg(array("organisation_ids" => $id)), null, $format);
        }, 'getOrgTasks');
   
       
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/archivedProjects/:pID/archiveMetaData(:format)/',
                                                        function ($id, $pID, $format = ".json") {
            if (!is_numeric($pID) && strstr($pID, '.')) {
                 $pID = explode('.', $pID);
                 $format = '.'.$pID[1];
                 $pID = $pID[0];
            }
            
            $data = ProjectDao::getArchivedProjectMetaData($pID);
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getOrgArchivedProjectMetaData');     
        
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/archivedTasks/:tID/archiveMetaData(:format)/',
                                                        function ($id, $tID, $format = ".json") {
            if (!is_numeric($tID) && strstr($tID, '.')) {
                 $tID = explode('.', $tID);
                 $format = '.'.$tID[1];
                 $tID = $tID[0];
            }
            
            $data = TaskDao::getArchivedTaskMetaData($tID);
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getOrgArchivedTaskMetaData'); 
        
    }
}
Orgs::init();

