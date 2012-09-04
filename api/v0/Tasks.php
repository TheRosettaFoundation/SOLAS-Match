<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Tasks
 *
 * @author sean
 */

require_once '../app/TaskDao.class.php';
require_once '../app/TaskTags.class.php';
class Tasks {
  public static function init(){
        $dispatcher=Dispatcher::getDispatcher();
      
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks(:format)/', function ($format=".json"){
            $dao = new TaskDao();
           Dispatcher::sendResponce(null, $dao->getTask(null), null, $format);
        },'getTasks');
        
         Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/', function ($id,$format=".json"){
           if(!is_numeric($id)&& strstr($id, '.')){
               $id= explode('.', $id);
               $format='.'.$id[1];
               $id=$id[0];
           }
           $dao = new TaskDao();
           Dispatcher::sendResponce(null, $dao->getTask(array("task_id"=>$id)), null, $format);
        },'getTask');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/tags(:format)/', function ($id,$format=".json"){
            $dao = new TaskTags();
           Dispatcher::sendResponce(null, $dao->getTags($id), null, $format);
        },'getTasksTags');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/status(:format)/', function ($id,$format=".json"){
            $dao = new TaskDao();
           Dispatcher::sendResponce(null, $dao->getTaskStatus($id), null, $format);
        },'getTaskStatus');
        

    }
    
   
    
}
Tasks::init();
?>


