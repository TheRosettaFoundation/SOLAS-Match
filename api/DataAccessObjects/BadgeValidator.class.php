<?php

class BadgeValidator
{
    public function validateUserBadge($user, $badge)
    {
        return self::validateUserBadgeByID($user->getUserId(), $badge->getId());
    }
    
    public function validateUserBadgeByID($userID, $badgeID)
    {
        return !self::userHasBadgeByID($userID, $badgeID);
    }
    
    private function userHasBadge($user, $badge)
    {
        return self::userHasBadgeByID($user->getUserId(), $badge->getId());  
    }
    
    private function userHasBadgeByID($userID, $badgeID)
    {
        $result = PDOWrapper::call("userHasBadge", PDOWrapper::cleanse($userID).",".PDOWrapper::cleanse($badgeID));
        return $result[0]['result'];
    }
}
