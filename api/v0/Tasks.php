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
require_once '../app/IO.class.php';
require_once '../app/TaskFile.class.php';
require_once '../app/lib/Upload.class.php';
class Tasks {
  public static function init(){
        $dispatcher=Dispatcher::getDispatcher();
      
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks(:format)/', function ($format=".json"){
            $dao = new TaskDao();
           Dispatcher::sendResponce(null, $dao->getTask(null), null, $format);
        },'getTasks');
        
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/tasks(:format)/', function ($format=".json"){
            $data=Dispatcher::getDispatcher()->request()->getBody();
            $data= APIHelper::deserialiser($data, $format);
            $dao = new TaskDao();
            Dispatcher::sendResponce(null, $dao->create($data), null, $format);
        },'createTask');
        
         
        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/tasks/:id/', function ($id,$format=".json"){
            if(!is_numeric($id)&& strstr($id, '.')){
               $id= explode('.', $id);
               $format='.'.$id[1];
               $id=$id[0];
            }
            $dao = new TaskDao();
            Dispatcher::sendResponce(null, $dao->delete($id), null, $format);
        },'deleteTask');
        
         Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/', function ($id,$format=".json"){
           if(!is_numeric($id)&& strstr($id, '.')){
               $id= explode('.', $id);
               $format='.'.$id[1];
               $id=$id[0];
           }
           $dao = new TaskDao();
           $data= $dao->getTask(array("task_id"=>$id));
           if(is_array($data))$data=$data[0];
           Dispatcher::sendResponce(null, $data, null, $format);
        },'getTask');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/tags(:format)/', function ($id,$format=".json"){
            $dao = new TaskTags();
           Dispatcher::sendResponce(null, $dao->getTags($id), null, $format);
        },'getTasksTags');
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/tasks/:id/tags(:format)/', function ($id,$format=".json"){
            $dao = new TaskDao();
            $data=Dispatcher::getDispatcher()->request()->getBody();
            $data= APIHelper::deserialiser($data, $format);
            $task   = APIHelper::cast(new Task(), $params);
            $result = $dao->_updateTags($task);
            Dispatcher::sendResponce(null, array("result"=>$result,"message"=>result==1?"update suceeded":"update failed"), null, $format);
        },'setTasksTags');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/status(:format)/', function ($id,$format=".json"){
            $dao = new TaskDao();
           Dispatcher::sendResponce(null, array("status message"=>$dao->getTaskStatus($id)), null, $format);
        },'getTaskStatus');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/file/', function ($id,$format=".json"){
            $version=0;
            if(isset ($_GET['version'])&& is_numeric($_GET['version'])) $version= $_GET['version'];
            TaskDao::downloadTask($id,$version);
        },'getTaskFile');
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/tasks/:id/file/:userId/:filename', function ($id,$userId,$filename){
            $data=Dispatcher::getDispatcher()->request()->getBody();
            $dao = new TaskDao();
            $task= $dao->getTask(array("task_id"=>$id));
            if(is_array($task))$task=$task[0];
            Upload::apiSaveFile($task, $userId, $data, $filename);
        },'saveTaskFile');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/version(:format)/', function ($id,$format=".json"){
            $userID=null;
            if(isset ($_GET['userID'])&& is_numeric($_GET['userID'])) $userID= $_GET['userID'];
           Dispatcher::sendResponce(null, array("version"=>TaskFile::getLatestFileVersionByTaskID($id,$userID)), null, $format);
        },'getTaskVersion');
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/tasks/addTarget/:languageCode/:countryCode/', function ($languageCode,$countryCode,$format=".json"){
            $dao = new TaskDao();
            $data = Dispatcher::getDispatcher()->request()->getBody();
            $data = APIHelper::deserialiser($data, $format);
            $task = APIHelper::cast(new Task(), $params);
            $result = $dao->duplicateTaskForTarget($task, $language_id, $countryCode);
            Dispatcher::sendResponce(null, array("result"=>$result,"message"=>$result==1?"duplicated sucessfully":"duplication failed"), null, $format);
        },'addTarget');
        
    }
    
   
    
}
Tasks::init();
?>


