<?php

require_once __DIR__."/../../Common/models/Badge.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";

class BadgeDao
{    
    public static function getBadge($badgeId = null, $title = null, $description = null, $ownerId = null)
    {
        $args = PDOWrapper::cleanseNull($badgeId)
                .",".PDOWrapper::cleanseNullOrWrapStr($title)
                .",".PDOWrapper::cleanseNullOrWrapStr($description)
                .",".PDOWrapper::cleanseNull($ownerId); 
        
        if($result = PDOWrapper::call("getBadge", $args)) {
            $badges = array();
            foreach($result as $badge) {
                $badges[] = ModelFactory::buildModel("Badge", $badge);
            }
            return $badges;
        }        
        return null;
    }
    
    public static function insertAndUpdateBadge($badge)
    {
        $args = PDOWrapper::cleanseNullOrWrapStr($badge->getId())
                .",".PDOWrapper::cleanseNull($badge->getOwnerId())
                .",".PDOWrapper::cleanseNullOrWrapStr($badge->getTitle())
                .",".PDOWrapper::cleanseNullOrWrapStr($badge->getDescription());     
        
        if($result = PDOWrapper::call("badgeInsertAndUpdate", $args)) {
            return ModelFactory::buildModel("Badge", $result[0]);
        } else {
            return null;
        }
    }

    public static function getOrgBadges($org_id)
    {
        $ret = null;
        $args = "null,null,null,".PDOWrapper::cleanseNull($org_id);
        
        if ($badge_array = PDOWrapper::call("getBadge", $args)) {
            $ret = array();
            foreach ($badge_array as $badge) {
                $ret[] = ModelFactory::buildModel("Badge", $badge);
            }
        }         
        return $ret;
    }

    
    public static function assignBadge($userID, $badgeID)
    {
        $args = PDOWrapper::cleanseNull($userID)
                .",".PDOWrapper::cleanseNull($badgeID);
        
        if (!$validation = self::validateUserBadge($userID, $badgeID)) {
            if ($result = PDOWrapper::call("assignBadge", $args)) {
                return $result[0]["result"];
            }
        }
        
        return 0;
    }
    
    public static function removeUserBadge($userID, $badgeID)
    {
        $args = PDOWrapper::cleanseNull($userID)
                .",".PDOWrapper::cleanseNull($badgeID);
        
        $result = PDOWrapper::call("removeUserBadge", $args);
        return $result[0]["result"];
    }
    
    public static function deleteBadge($badgeID)
    { 
        $args = PDOWrapper::cleanseNull($badgeID);
        
        if($result = PDOWrapper::call("deleteBadge", $args)) {
            return $result[0]["result"];
        }        
        return 0;
    }
    
    public static function validateUserBadge($userID, $badgeID)
    {
        $args = PDOWrapper::cleanseNull($userID)
                .",".PDOWrapper::cleanseNull($badgeID);
        
        $result = PDOWrapper::call("userHasBadge", $args);
        return $result[0]['result'];
    }
}