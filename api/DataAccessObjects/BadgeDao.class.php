<?php

require_once '../Common/models/Badge.php';
require_once 'BadgeValidator.class.php';
require_once '../Common/lib/PDOWrapper.class.php';

class BadgeDao
{
    public function find($params)
    {
        $db = new PDOWrapper();
        $db->init();
        $result=$db->call("getBadge", "{$db->cleanse($params['badge_id'])},null,null,null");
        return ModelFactory::BuildModel("Badge", $result[0]);
    }
    
    public function insertAndUpdateBadge($badge)
    {
        $db = new PDOWrapper();
        $db->init();        
        $result=$db->call("badgeInsertAndUpdate", "{$db->cleanseWrapStr($badge->getId())}
                                                ,{$db->cleanseWrapStr($badge->getOwnerId())}
                                                ,{$db->cleanseWrapStr($badge->getTitle())}
                                                ,{$db->cleanseWrapStr($badge->getDescription())}");
        return $result[0]['result'];
    }

    public function getAllBadges()
    {
        $db = new PDOWrapper();
        $db->init();
        $results=$db->call("getBadge", "null,null,null,null");
        $ret = null;
        foreach ($results as $result) {
            $ret[]= ModelFactory::BuildModel("Badge", $result);
        }
        
        return $ret;
    }

    public function getOrgBadges($org_id)
    {
        $db = new PDOWrapper();
        $db->init();
        $ret = null;
        if ($badge_array = $db->call("getBadge", "null,null,null,{$db->cleanse($org_id)}")) {
            $ret = array();
            foreach ($badge_array as $badge) {
                $ret[] = ModelFactory::BuildModel("Badge", $badge);
            }
        } 
        
        return $ret;
    }

    public function assignBadge($user, $badge)
    {
        self::assignBadgeByID($user->getUserId(), $badge->getId());
    }
    
    public function assignBadgeByID($userID, $badgeID)
    {
        $badgeValidator = new BadgeValidator();
        if ($badgeValidator->validateUserBadgeByID($userID, $badgeID)) {
            $db = new PDOWrapper();
            $db->init();
            if ($result=$db->call("assignBadge", "{$db->cleanse($userID)},{$db->cleanse($badgeID)}")) {
                return $result[0]['result'];
            }
        }
        
        return 0;
    }

    public function removeUserBadge($user, $badge)
    {
        $this->removeUserBadgeByID($user->getUserId(), $badge->getId());
    }
    
    public function removeUserBadgeByID($userID, $badgeID)
    {
        $db = new PDOWrapper();
        $db->init();
        if ($result = $db->call("removeUserBadge", "{$db->cleanse($userID)},{$db->cleanse($badgeID)}")) {
          return $result[0]['result'];
        }
        
        return 0;
    }
    
    public function deleteBadge($badgeID)
    {
        $db = new PDOWrapper();
        $db->init();      
        $result = $db->call("deleteBadge", "{$db->cleanseNull($badgeID)}");
    }    
}