<?php

require_once __DIR__."/../../Common/models/Badge.php";
require_once __DIR__."/../../Common/lib/PDOWrapper.class.php";

class BadgeDao
{    
    public static function getBadge($badgeId = null, $title = null, $description = null, $ownerId = null)
    {
        $result = PDOWrapper::call("getBadge", PDOWrapper::cleanseNull($badgeId).",".PDOWrapper::cleanseNullOrWrapStr($title).",".
                PDOWrapper::cleanseNullOrWrapStr($description).",".PDOWrapper::cleanseNull($ownerId));
        if($result) {
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
     
        $result = PDOWrapper::call("badgeInsertAndUpdate", PDOWrapper::cleanseNullOrWrapStr($badge->getId())
                                    .",".PDOWrapper::cleanseNull($badge->getOwnerId()).",".PDOWrapper::cleanseNullOrWrapStr($badge->getTitle())
                                    .",".PDOWrapper::cleanseNullOrWrapStr($badge->getDescription()));
        if(is_array($result)) {
            return ModelFactory::buildModel("Badge", $result[0]);
        } else {
            return null;
        }
    }

    public static function getOrgBadges($org_id)
    {
        $ret = null;
        if ($badge_array = PDOWrapper::call("getBadge", "null,null,null,".PDOWrapper::cleanse($org_id))) {
            $ret = array();
            foreach ($badge_array as $badge) {
                $ret[] = ModelFactory::buildModel("Badge", $badge);
            }
        }         
        return $ret;
    }

    
    public static function assignBadge($userID, $badgeID)
    {
        if (!$validation = self::validateUserBadge($userID, $badgeID)) {
            if ($result = PDOWrapper::call("assignBadge", PDOWrapper::cleanse($userID)
                                                        .",".PDOWrapper::cleanse($badgeID))) {
                return $result[0]["result"];
            }
        }
        
        return 0;
    }
    
    public static function removeUserBadge($userID, $badgeID)
    {
        $result = PDOWrapper::call("removeUserBadge", PDOWrapper::cleanse($userID)
                                                        .",".PDOWrapper::cleanse($badgeID));
        return $result[0]["result"];
    }
    
    public static function deleteBadge($badgeID)
    { 
        if($result = PDOWrapper::call("deleteBadge", PDOWrapper::cleanseNull($badgeID))) {
            return $result[0]["result"];
        }        
        return 0;
    }
    
    public static function validateUserBadge($userID, $badgeID)
    {
        $result = PDOWrapper::call("userHasBadge", PDOWrapper::cleanse($userID).",".PDOWrapper::cleanse($badgeID));
        return $result[0]['result'];
    }
}