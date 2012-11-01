<?php

require('models/Badge.class.php');
require('BadgeValidator.class.php');
require_once ('PDOWrapper.class.php');

class BadgeDao
{
    public function find($params)
    {
        $db = new PDOWrapper();
        $db->init();
        $result=$db->call("getBadge", "{$db->cleanse($params['badge_id'])},null,null,null");
        return new Badge($result[0]);
    }

    public function insertAndUpdateBadge($badge)
    {
        $db = new PDOWrapper();
        $db->init();        
        $result=$db->call("badgeInsertAndUpdate", "{$db->cleanseWrapStr($badge->getBadgeId())},{$db->cleanseWrapStr($badge->getOwnerId())},{$db->cleanseWrapStr($badge->getTitle())},{$db->cleanseWrapStr($badge->getDescription())}");
        return $result[0]['result'];
    }

    public function getAllBadges()
    {
        $db = new PDOWrapper();
        $db->init();
        $result=$db->call("getBadge", "null,null,null,null");
        return $result;
    }

    public function getOrgBadges($org_id)
    {
        $db = new PDOWrapper();
        $db->init();
        $ret = null;
        if($badge_array = $db->call("getBadge", "null,null,null,{$db->cleanse($org_id)}")) {
            $ret = array();
            foreach($badge_array as $badge) {
                $ret[] = new Badge($badge);
            }
        } 
        return $ret;
    }

    public function assignBadge($user, $badge)
    {
        $badgeValidator = new BadgeValidator();
        if($badgeValidator->validateUserBadge($user, $badge)) {
            $db = new PDOWrapper();
            $db->init();
            $db->call("assignBadge", "{$db->cleanse($user->getUserId())},{$db->cleanse($badge->getBadgeId())}");
        }
    }

    public function removeUserBadge($user, $badge)
    {
        $db = new PDOWrapper();
        $db->init();
        if(!$db->call("removeUserBadge", "{$db->cleanse($user->getUserId())},{$db->cleanse($badge->getBadgeId())}")) {
           echo "<p>Cannot remove system badges</p>";
        }
    }
    
    public function deleteBadge($badgeID)
    {
        $db = new PDOWrapper();
        $db->init();      
        $result = $db->call("deleteBadge", "{$db->cleanseNull($badgeID)}");
    }    
}
