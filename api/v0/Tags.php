<?php

/**
 * Description of Tags
 *
 * @author sean
 */

require_once __DIR__."/../DataAccessObjects/TagsDao.class.php";

class Tags {
    
    public static function init()
    {
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tags(:format)/',
                                                        function ($format = ".json") {
            $limit = Dispatcher::clenseArgs('limit', HttpMethodEnum::GET, 20);

            $topTags = Dispatcher::clenseArgs('topTags', HttpMethodEnum::GET, false);

            if ($topTags) {
                Dispatcher::sendResponce(null, TagsDao::getTopTags($limit), null, $format);
            } else { 
                Dispatcher::sendResponce(null, TagsDao::getTag(null,null,$limit), null, $format);
            }
        }, 'getTags');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tags/getByLabel/:label/',
                                                        function ($label, $format = ".json") {
            
            if (!is_numeric($label) && strstr($label, '.')) {
                $temp = array();
                $temp = explode('.', $label);
                $lastIndex = sizeof($temp)-1;
                if ($lastIndex > 0) {
                    $format = '.'.$temp[$lastIndex];
                    $label = $temp[0];
                    for ($i = 1; $i < $lastIndex; $i++) {
                        $label = "{$label}.{$temp[$i]}";
                    }
                }
            }
            $data = TagsDao::getTag(null,$label);
            if (is_array($data)) {
                $data = $data[0];
            }
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getTagByLabel');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tags/topTags(:format)/',
                                                        function ($format = ".json") {
            $limit = Dispatcher::clenseArgs('limit', HttpMethodEnum::GET, 30);
            $data= TagsDao::getTopTags($limit);
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getTopTags');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tags/:id/', 
                                                        function ($id, $format = ".json") {
            
            if (!is_numeric($id) && strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            $data = TagsDao::getTag($id);
            if (is_array($data)) {
                $data = $data[0];
            }
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getTag');
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/tags(:format)/',
                                                        function ($format = ".json") {
            $data = Dispatcher::getDispatcher()->request()->getBody();
            $client = new APIHelper($format);
            $client->deserialize($data);
            $client->cast("Tag", $data);
            $data->setBadgeId(null);
            Dispatcher::sendResponce(null, TagsDao::save($tag), null, $format);
        }, 'createTag');
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/tags/:id/',
                                                        function ($id, $format = ".json") {
            if (!is_numeric($id) && strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            $data = Dispatcher::getDispatcher()->request()->getBody();
            $client = new APIHelper($format);
            $data = $client->deserialize($data);
            $data = $client->cast("Tag", $data);
            Dispatcher::sendResponce(null, TagsDao::save($data), null, $format);
        }, 'updateTag');
        
        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/tags/:id/',
                                                            function ($id, $format = ".json") {
            if (!is_numeric($id) && strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            Dispatcher::sendResponce(null, TagsDao::delete($id), null, $format);
        }, 'deleteTag');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tags/:id/tasks(:format)/',
                                                        function ($id, $format=".json") {
            $limit = Dispatcher::clenseArgs('limit', HttpMethodEnum::GET, 5);
            Dispatcher::sendResponce(null, TaskDao::getTasksWithTag($id, $limit), null, $format);
        }, 'getTaskForTag');
    }
}
Tags::init();
