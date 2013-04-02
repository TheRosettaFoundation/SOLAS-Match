<?php

/**
 * Description of Badges
 *
 * @author sean
 */

require_once __DIR__."/../DataAccessObjects/BadgeDao.class.php";

class Badges {
    
    public static function init()
    {
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/badges(:format)/',
                                                        function ($format = ".json") {
            
            
            Dispatcher::sendResponce(null, BadgeDao::getBadge(), null, $format);
        }, 'getBadges');
         
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/badges(:format)/',
                                                        function ($format = ".json") {
            
            $data = Dispatcher::getDispatcher()->request()->getBody();
            $client = new APIHelper($format);
            $data = $client->deserialize($data);
            $data = $client->cast("Badge", $data);
            $data->setId(null);            
            Dispatcher::sendResponce(null, BadgeDao::insertAndUpdateBadge($data), null, $format);
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
            Dispatcher::sendResponce(null, BadgeDao::insertAndUpdateBadge($data), null, $format);
        }, 'updateBadge');
        
        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/badges/:id/',
                                                        function ($id, $format = ".json") {
            
            if (!is_numeric($id) && strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            Dispatcher::sendResponce(null, BadgeDao::deleteBadge($id), null, $format);
        }, 'deleteBadge');
        
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/badges/:id/', 
                                                        function ($id, $format = ".json") {
            
            if (!is_numeric($id)&& strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            $data = BadgeDao::getBadge($id,null,null,null);
            if (is_array($data)) {
                $data = $data[0];
            }
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getBadge');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/badges/:id/users(:format)/', 
                                                        function ($id, $format=".json") {
            
            $data = UserDao::getUsersWithBadge($id);
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getusersWithBadge');        
    }
}
Badges::init();
