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
require_once '../app/models/User.class.php';
require_once '../app/TaskDao.class.php';
class Users {
   

    public static function init(){
        $dispatcher=Dispatcher::getDispatcher();
      
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/users(:format)', function ($format=".json"){
           Dispatcher::sendResponce(null, "display all users", null, $format);
        },'getUsers');
        
         Dispatcher::registerNamed(HttpMethodEnum::GET, '/users/:id', function ($id,$format=".json"){
           if(!is_numeric($id)&& strstr($id, '.')){
               $id= explode('.', $id);
               $format=$id[1];
               $id=$id[0];
           }
           $dao = new UserDao();
           Dispatcher::sendResponce(null, $dao->find(array("user_id"=>$id)), null, $format);
        },'getUser');
        
       
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/users/:id/orgs(:format)', function ($id,$format=".json"){
          $dao = new UserDao();
          Dispatcher::sendResponce(null, $dao->findOrganisationsUserBelongsTo($id), null, $format);
        },'getUserOrgs');
       
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/users/:id/badges(:format)', function ($id,$format=".json"){
          $dao = new UserDao();
          Dispatcher::sendResponce(null, $dao->getUserBadgesbyID($id), null, $format);
        },'getUserbadges');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/users/:id/tags(:format)', function ($id,$format=".json"){
           $dao = new UserDao();
           Dispatcher::sendResponce(null, $dao->getUserTags($id), null, $format);
        },'getUsertags');
        
         Dispatcher::registerNamed(HttpMethodEnum::GET, '/users/:id/tasks(:format)', function ($id,$format=".json"){
           $dao = new TaskDao();
           Dispatcher::sendResponce(null, $dao->getUserTasksByID($id), null, $format);
        },'getUsertasks');

    }
    
   
    
}
Users::init();
?>
