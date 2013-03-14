<?php

/**
 * Description of StatDao
 *
 * @author sean
 */

class StatDao {
    
    public static function getTotalTasks($dateTime)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getTotalTasks", PDOWrapper::cleanseNullOrWrapStr($dateTime))) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }
    
    public static function getTotalArchivedTasks($dateTime)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getTotalArchivedTasks", PDOWrapper::cleanseNullOrWrapStr($dateTime))) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }
    
    public static function getTotalClaimedTasks($dateTime)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getTotalClaimedTasks", PDOWrapper::cleanseNullOrWrapStr($dateTime))) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }
    
    public static function getTotalOrgs()
    {
        $ret = null;
        if ($result = PDOWrapper::call("getTotalOrgs", "")) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }
    
    public static function getTotalUsers()
    {
        $ret = null;
        if ($result = PDOWrapper::call("getTotalUsers", "")) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }
    
    public static function getTotalUnclaimedTasks($dateTime)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getTotalUnclaimedTasks", PDOWrapper::cleanseNullOrWrapStr($dateTime))) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }
    
    public static function getStatistics($name)
    {
        $ret = null;
        $result = PDOWrapper::call('getStatistics', PDOWrapper::cleanseNullOrWrapStr($name));
        if ($result) {
            
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Statistic", $row);
                //$ret[$stat->getName()] = $stat;
            }
        }
        return $ret;
    }
}
