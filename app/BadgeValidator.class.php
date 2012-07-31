<?php

class BadgeValidator
{
    public function validateUserBadge($user, $badge)
    {
        $ret = true;
        if(self::userHasBadge($user, $badge)) {
            $ret = false;
        }
        return $ret;
    }

    private function userHasBadge($user, $badge)
    {
        $ret = false;
        $db = new mySQLWrapper();
        $db->init();
        $query = "SELECT *
                    FROM user_badges
                    WHERE user_id = ".$db->cleanse($user->getUserId())."
                    AND badge_id = ".$db->cleanse($badge->getBadgeId());
        if($result = $db->Select($query)) {
            $ret = true;
        }
        return $ret;
    }
}
