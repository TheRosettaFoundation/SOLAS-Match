<?php

/**
 * Description of Badges
 *
 * @author sean
 */

require_once 'DataAccessObjects/BadgeDao.class.php';

class Badges {
    
    public static function init()
    {
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/badges(:format)/',
                                                        function ($format = ".json") {
            
            $dao = new BadgeDao();
            Dispatcher::sendResponce(null, $dao->getAllBadges(), null, $format);
        }, 'getBadges');
         
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/badges(:format)/',
                                                        function ($format = ".json") {
            
            $data = Dispatcher::getDispatcher()->request()->getBody();
            $client = new APIHelper($format);
            $data = $client->deserialize($data);
            $data = $client->cast("Badge", $data);
            $data->setId(null);
            $dao = new BadgeDao();
            Dispatcher::sendResponce(null, $dao->insertAndUpdateBadge($data), null, $format);
        }, 'createBadge');
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/badges/:id/',
                                                        function ($id, $format = ".json") {
            
            if (!is_numeric($id) && strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            $data = Dispatcher::getDispatcher()->request()->getBody();
            $client = new APIHelper($format);
            $data = $client->deserialize($data);
            $data = $client->cast("Badge", $data);
            $dao = new BadgeDao();
            Dispatcher::sendResponce(null, $dao->insertAndUpdateBadge($data), null, $format);
        }, 'updateBadge');
        
        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/badges/:id/',
                                                        function ($id, $format = ".json") {
            
            if (!is_numeric($id) && strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            $dao = new BadgeDao();
            Dispatcher::sendResponce(null, $dao->deleteBadge($id), null, $format);
        }, 'deleteBadge');
        
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/badges/:id/', 
                                                        function ($id, $format = ".json") {
            
            if (!is_numeric($id)&& strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            $dao = new BadgeDao();
            $data = $dao->find(array('badge_id' => $id));
            if (is_array($data)) {
                $data = $data[0];
            }
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getBadge');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/badges/:id/users(:format)/', 
                                                        function ($id, $format=".json") {
            
            $dao = new UserDao();
            $data = $dao->getUsersWithBadgeByID($id);
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getusersWithBadge');        
    }
}
Badges::init();
