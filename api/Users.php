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
require_once '../app/TagsDao.class.php';
class Users {
   

    public static function init(){
      $dispatcher=Dispatcher::getDispatcher();
      
      $dispatcher->get('/users/', function () use ($dispatcher) {
           $dao = new TagsDao;
           Dispatcher::sendResponce(null, $dao->getAllTags(), null, FormatEnum::XML);
        })->name('getUsers');
    }
    
}
Users::init();
?>
