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

require_once '../app/OrganisationDao.class.php';
require_once '../app/BadgeDao.class.php';

class Orgs {
    public static function init(){
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs(:format)/', function ($format=".json"){
           $dao = new OrganisationDao();
           Dispatcher::sendResponce(null, $dao->getOrg(null, null, null, null), null, $format);
        },'getOrgs');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/', function ($id,$format=".json"){
           if(!is_numeric($id)&& strstr($id, '.')){
               $id= explode('.', $id);
               $format='.'.$id[1];
               $id=$id[0];
           }
           $dao = new OrganisationDao();
           Dispatcher::sendResponce(null, $dao->getOrg($id, null, null, null), null, $format);
        },'getOrg');
        
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
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/orgs/:id/tasks(:format)/', function ($id,$format=".json"){
           $dao = new TaskDao();
           Dispatcher::sendResponce(null, $dao->findTasksByOrg(array("organisation_ids"=>$id)), null, $format);
        },'getOrgTasks');
        
        
    }
}
    Orgs::init();
?>
