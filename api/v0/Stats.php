<?php

/**
 * Description of Stats
 *
 * @author sean
 */

require_once 'DataAccessObjects/StatDao.php';

class Stats {
   
    public static function init()
    {              
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/stats/totalTasks(:format)/',
                                                        function ($format = ".json") {

            $datetime = Dispatcher::clenseArgs('datetime', HttpMethodEnum::GET, null);
            $data = StatDao::getTotalTasks($datetime);
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getTotalTasks');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/stats/totalArchivedTasks(:format)/',
                                                        function ($format = ".json") {
            
            $datetime = Dispatcher::clenseArgs('datetime', HttpMethodEnum::GET, null);
            $data = StatDao::getTotalArchivedTasks($datetime);
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getTotalArchivedTasks');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/stats/totalClaimedTasks(:format)/',
                                                        function ($format = ".json") {
            
            $datetime = Dispatcher::clenseArgs('datetime', HttpMethodEnum::GET, null);
            $data = StatDao::getTotalClaimedTasks($datetime);
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getTotalClaimedTasks');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/stats/totalUnclaimedTasks(:format)/',
                                                        function ($format = ".json") {
            
            $datetime = Dispatcher::clenseArgs('datetime', HttpMethodEnum::GET, null);
            $data = StatDao::getTotalUnclaimedTasks($datetime);
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getTotalUnclaimedTasks');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/stats/totalUsers(:format)/',
                                                        function ($format = ".json") {
            $data = StatDao::getTotalUsers();
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getTotalUsers');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/stats/totalOrgs(:format)/',
                                                        function ($format = ".json") {
            $data = StatDao::getTotalOrgs();
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getTotalOrgs'); 
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/stats(:format)/',
                                                        function ($format = ".json") {
            $data = StatDao::getStatistics('');
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getStatistics'); 
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/stats/:name/',
                                                        function ($name,$format = ".json") {
            if (!is_numeric($name) && strstr($name, '.')) {
                $name = explode('.', $name);
                $format = '.'.$name[1];
                $name = $name[0];
            }
            $data = StatDao::getStatistics($name);
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getStatisticByName'); 
    }
}
Stats::init();