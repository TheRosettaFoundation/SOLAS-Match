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

            $datetime = null;
            if (isset($_GET['datetime'])) {
                $datetime = $_GET['datetime'];
            }
            $data = StatDao::getTotalTasks($datetime);
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getTotalTasks');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/stats/totalArchivedTasks(:format)/',
                                                        function ($format = ".json") {
            
            $datetime = null;
            if (isset($_GET['datetime'])) {
                $datetime = $_GET['datetime'];
            }
            $data = StatDao::getTotalArchivedTasks($datetime);
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getTotalArchivedTasks');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/stats/totalClaimedTasks(:format)/',
                                                        function ($format = ".json") {
            
            $datetime = null;
            if (isset($_GET['datetime'])) {
                $datetime= $_GET['datetime'];
            }
            $data = StatDao::getTotalClaimedTasks($datetime);
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getTotalClaimedTasks');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/stats/totalUnclaimedTasks(:format)/',
                                                        function ($format = ".json") {
            
            $datetime = null;
            if (isset($_GET['datetime'])) {
                $datetime= $_GET['datetime'];
            }
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
    }
}
Stats::init();