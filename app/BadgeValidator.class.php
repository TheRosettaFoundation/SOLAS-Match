<?php

class BadgeValidator
{
    public function validateUserBadge($user, $badge)
    {
       return self::validateUserBadgeByID($user->getUserId(),$badge->getBadgeId());
    }
    
    public function validateUserBadgeByID($userID, $badgeID)
    {
        return !self::userHasBadgeByID($userID, $badgeID);
    }
    
    private function userHasBadge($user, $badge)
    {
      return self::userHasBadgeByID($user->getUserId(),$badge->getBadgeId());  
    }
    private function userHasBadgeByID($userID, $badgeID)
    {
        $ret = false;
        $db = new PDOWrapper();
        $db->init();
        $result = $db->call("userHasBadge", "{$db->cleanse($userID)},{$db->cleanse($badgeID)}");
        return $result[0]['result'];
    }
}
