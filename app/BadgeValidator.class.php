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
        $db = new PDOWrapper();
        $db->init();
        $result = $db->call("userHasBadge", "{$db->cleanse($user->getUserId())},{$db->cleanse($badge->getBadgeId())}");
        return $result[0]['result'];
    }
}
