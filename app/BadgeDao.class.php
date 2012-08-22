<?php

require('models/Badge.class.php');
require('BadgeValidator.class.php');

class BadgeDao
{
    public function find($params)
    {
        $db = new PDOWrapper();
        $db->init();
        $result=$db->call("getBadge", "{$db->cleanse($params['badge_id'])},null,null");
        return new Badge($result[0]);
    }

    public function addBadge($badge)
    {
        $db = new PDOWrapper();
        $db->init();
        $result=$db->call("addBadge", "{$db->cleanseWrapStr($badge->getTitle())},{$db->cleanseWrapStr($badge->getDescription())},{$db->cleanseNull($badge->getOwnerId())}");
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
        return $db->call("getBadge", "null,null,null,{$db->cleanse($org_id)}");
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
}
