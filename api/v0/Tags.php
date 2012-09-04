<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Tags
 *
 * @author sean
 */
require_once '../app/TagsDao.class.php';
require_once '../app/TaskTags.class.php';
class Tags {
  public static function init(){
        $dispatcher=Dispatcher::getDispatcher();
      
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tags(:format)/', function ($format=".json"){
            $limit=20;
            if(isset ($_GET['limit'])&& is_numeric($_GET['limit'])) $limit= $_GET['limit'];
            $topTags=false;
            if(isset ($_GET['topTags'])&& strcasecmp($_GET['topTags'],'true')==0) $topTags= true;
            $dao = new TagsDao();
            if($topTags){
                Dispatcher::sendResponce(null, $dao->getTopTags($limit), null, $format);
            }else{
                Dispatcher::sendResponce(null, $dao->getTag(array("limit"=>$limit)), null, $format);
            }
        },'getTags');
        
         Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tags/:id/', function ($id,$format=".json"){
           if(!is_numeric($id)&& strstr($id, '.')){
               $id= explode('.', $id);
               $format='.'.$id[1];
               $id=$id[0];
           }
           $dao = new TagsDao();
           Dispatcher::sendResponce(null, $dao->getTag(array("tag_id"=>$id)), null, $format);
        },'getTag');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tags/:id/tasks(:format)/', function ($id,$format=".json"){
            $limit=5;
            if(isset ($_GET['limit'])&& is_numeric($_GET['limit'])) $limit= $_GET['limit'];
            $dao = new TaskDao();
           Dispatcher::sendResponce(null,$dao->getTasksWithTag($id, $limit) , null, $format);
        },'getTaskForTag');

    }
    
   
    
}
Tags::init();
?>