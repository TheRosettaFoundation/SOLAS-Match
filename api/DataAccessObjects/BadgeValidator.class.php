<?php

class BadgeValidator
{   
    public static function validateUserBadge($userID, $badgeID)
    {
        $result = PDOWrapper::call("userHasBadge", PDOWrapper::cleanse($userID).",".PDOWrapper::cleanse($badgeID));
        return $result[0]['result'];
    }
}
