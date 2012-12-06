<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Orgs
 *
 * @author sean
 */

require_once 'DataAccessObjects/OrganisationDao.class.php';
require_once 'DataAccessObjects/BadgeDao.class.php';

class Orgs {
    public static function init(){
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs(:format)/', function ($format=".json"){
           $dao = new OrganisationDao();
           Dispatcher::sendResponce(null, $dao->getOrg(null, null, null, null), null, $format);
        },'getOrgs');
        
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/orgs(:format)/', function ($format=".json"){
            $data=Dispatcher::getDispatcher()->request()->getBody();
            $data= APIHelper::deserialiser($data, $format);
            $data = APIHelper::cast("Organisation", $data);
            $data->setId(null);
            $dao = new OrganisationDao();
            Dispatcher::sendResponce(null, $dao->save($data), null, $format);
        },'createOrg');
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/orgs/:id/', function ($id,$format=".json"){
            if(!is_numeric($id)&& strstr($id, '.')){
               $id= explode('.', $id);
               $format='.'.$id[1];
               $id=$id[0];
            }
            $data=Dispatcher::getDispatcher()->request()->getBody();
            $data= APIHelper::deserialiser($data, $format);
            $data = APIHelper::cast("Organisation", $data);
            $dao = new OrganisationDao();
            Dispatcher::sendResponce(null, $dao->save($data), null, $format);
        },'updateOrg');
        
        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/orgs/:id/', function ($id,$format=".json"){
            if(!is_numeric($id)&& strstr($id, '.')){
               $id= explode('.', $id);
               $format='.'.$id[1];
               $id=$id[0];
            }
            $dao = new OrganisationDao();
            Dispatcher::sendResponce(null, $dao->delete($id), null, $format);
        },'deleteOrg');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/', function ($id,$format=".json"){
           if(!is_numeric($id)&& strstr($id, '.')){
               $id= explode('.', $id);
               $format='.'.$id[1];
               $id=$id[0];
           }
           $dao = new OrganisationDao();
           $data= $dao->getOrg($id, null, null, null);
           if(is_array($data))$data=$data[0];
           Dispatcher::sendResponce(null, $data, null, $format);
        },'getOrg');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/getByName/:name/', function ($name,$format=".json"){
            if(!is_numeric($name)&& strstr($name, '.')){
               $temp = array();
               $temp= explode('.', $name);
               $lastIndex = sizeof($temp)-1;
               if($lastIndex>0){
                   $format='.'.$temp[$lastIndex];
                   $name=$temp[0];
                   for($i = 1; $i < $lastIndex; $i++){
                       $name="{$name}.{$temp[$i]}";
                   }
               }
           }
           $dao = new OrganisationDao();
           $data= $dao->searchForOrg($name);
           if(!is_array($data)&& !is_null($data))$data=array($data);
           Dispatcher::sendResponce(null, $data, null, $format);
        },'getOrgByName');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/badges(:format)/', function ($id,$format=".json"){
           $dao = new BadgeDao;
           Dispatcher::sendResponce(null, $dao->getOrgBadges($id), null, $format);
        },'getOrgBadges');    
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/members(:format)/', function ($id,$format=".json"){
           $dao = new OrganisationDao();
           Dispatcher::sendResponce(null, $dao->getOrgMembers($id), null, $format);
        },'getOrgMembers');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/requests(:format)/', function ($id,$format=".json"){
           $dao = new OrganisationDao();
          
           Dispatcher::sendResponce(null, $dao->getMembershipRequests($id), null, $format);
        },'getMembershipRequests');
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/orgs/:id/requests/:uid/', function ($id,$uid,$format=".json"){
           if(!is_numeric($uid)&& strstr($uid, '.')){
              $uid= explode('.', $uid);
              $format='.'.$uid[1];
              $uid=$uid[0];
           }
           $dao = new OrganisationDao();
           Dispatcher::sendResponce(null, $dao->requestMembership($uid, $id), null, $format);
        },'createMembershipRequests');
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/orgs/:id/requests/:uid/', function ($id,$uid,$format=".json"){
            if(!is_numeric($uid)&& strstr($uid, '.')){
                $uid= explode('.', $uid);
                $format='.'.$uid[1];
                $uid=$uid[0];
            }
            Notify::notifyUserOrgMembershipRequest($uid, $id, true);

            $dao = new OrganisationDao();
            Dispatcher::sendResponce(null, $dao->acceptMemRequest($id,$uid), null, $format);
        },'acceptMembershipRequests');
        
         Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/orgs/:id/requests/:uid/', function ($id,$uid,$format=".json"){
            if(!is_numeric($uid)&& strstr($uid, '.')){
                $uid= explode('.', $uid);
                $format='.'.$uid[1];
                $uid=$uid[0];
            }
            Notify::notifyUserOrgMembershipRequest($uid, $id, false);

            $dao = new OrganisationDao();
            Dispatcher::sendResponce(null, $dao->refuseMemRequest($id,$uid), null, $format);
        },'rejectMembershipRequests');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/tasks(:format)/', function ($id,$format=".json"){
           $dao = new TaskDao();
           Dispatcher::sendResponce(null, $dao->findTasksByOrg(array("organisation_ids"=>$id)), null, $format);
        },'getOrgTasks');
        
        
    }
}
    Orgs::init();
?>
