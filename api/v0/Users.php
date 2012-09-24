<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Users
 *
 * @author sean
 */
require_once '../app/UserDao.class.php';
require_once '../app/TaskDao.class.php';
require_once '../app/lib/Notify.class.php';
require_once '../app/lib/NotificationTypes.class.php';
class Users {
   

    public static function init(){
        $dispatcher=Dispatcher::getDispatcher();
      
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/users(:format)/', function ($format=".json"){
           Dispatcher::sendResponce(null, "display all users", null, $format);
        },'getUsers');
        
         Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/users/:id/', function ($id,$format=".json"){
           if(!is_numeric($id)&& strstr($id, '.')){
               $id= explode('.', $id);
               $format='.'.$id[1];
               $id=$id[0];
           }
           $dao = new UserDao();
           $data= $dao->find(array("user_id"=>$id));
           if(is_array($data))$data=$data[0];
           Dispatcher::sendResponce(null, $data, null, $format);
        },'getUser');
        
       
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/users/:id/orgs(:format)/', function ($id,$format=".json"){
          $dao = new UserDao();
          Dispatcher::sendResponce(null, $dao->findOrganisationsUserBelongsTo($id), null, $format);
        },'getUserOrgs');
       
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/users/:id/badges(:format)/', function ($id,$format=".json"){
          $dao = new UserDao();
          Dispatcher::sendResponce(null, $dao->getUserBadgesbyID($id), null, $format);
        },'getUserbadges');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/users/:id/tags(:format)/', function ($id,$format=".json"){
           $dao = new UserDao();
           Dispatcher::sendResponce(null, $dao->getUserTags($id), null, $format);
        },'getUsertags');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/users/:id/tasks(:format)/', function ($id,$format=".json"){
           $dao = new TaskDao();
           Dispatcher::sendResponce(null, $dao->getUserTasksByID($id), null, $format);
        },'getUsertasks');
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/users/:id/tasks(:format)/', function ($id,$format=".json"){
            $data=Dispatcher::getDispatcher()->request()->getBody();
            $data= APIHelper::deserialiser($data, $format);
            $data= APIHelper::cast("Task", $data);
            $dao = new TaskDao;
            Dispatcher::sendResponce(null, array("result"=>$dao->claimTaskbyID($data->getTaskId(), $id)), null, $format);
            $dao = new UserDao();
            Notify::notifyUserClaimedTask($dao->find(array("user_id"=>$id)), $data);
            Notify::sendEmailNotifications($data, NotificationTypes::Claim);
        },'userClaimTask');
        
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/users/:id/top_tasks(:format)/', function ($id,$format=".json"){
            $limit=5;
            if(isset ($_GET['limit'])&& is_numeric($_GET['limit'])) $limit= $_GET['limit'];
            $dao = new TaskDao();
            $data=$dao->getUserTopTasks($id,$limit);
            Dispatcher::sendResponce(null, $data , null, $format);
        },'getUserTopTasks');
        
    }
    
   
    
}
Users::init();
?>
