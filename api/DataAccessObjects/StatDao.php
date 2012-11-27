<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StatDao
 *
 * @author sean
 */
class StatDao {
    public static function getTotalTasks($dateTime)
    {
        $ret = null;
        $db= new PDOWrapper();
        $db->init();
        if ($result = $db->call("getTotalTasks", "{$db->cleanseNullOrWrapStr($dateTime)}")) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }
    
    public static function getTotalArchivedTasks($dateTime)
    {
        $ret = null;
        $db= new PDOWrapper();
        $db->init();
        if ($result = $db->call("getTotalArchivedTasks", "{$db->cleanseNullOrWrapStr($dateTime)}")) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }
    
    public static function getTotalClaimedTasks($dateTime)
    {
        $ret = null;
        $db= new PDOWrapper();
        $db->init();
        if ($result = $db->call("getTotalClaimedTasks", "{$db->cleanseNullOrWrapStr($dateTime)}")) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }
    
    public static function getTotalOrgs()
    {
        $ret = null;
        $db= new PDOWrapper();
        $db->init();
        if ($result = $db->call("getTotalOrgs", "")) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }
    
    public static function getTotalUsers()
    {
        $ret = null;
        $db= new PDOWrapper();
        $db->init();
        if ($result = $db->call("getTotalUsers", "")) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }
    
    public static function getTotalUnclaimedTasks($dateTime)
    {
        $ret = null;
        $db= new PDOWrapper();
        $db->init();
        if ($result = $db->call("getTotalUnclaimedTasks", "{$db->cleanseNullOrWrapStr($dateTime)}")) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }
}

?>
