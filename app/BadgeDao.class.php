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

    public function getAllBadges()
    {
        $db = new PDOWrapper();
        $db->init();
        $result=$db->call("getBadge", "null,null,null");
        return $result;
//        $db = new MySQLWrapper();
//        $db->init();
//        $query = 'SELECT *
//                    FROM badges';
//        $results = $db->Select($query);
//        return $results;
    }

    public function assignBadge($user, $badge)
    {
        $badgeValidator = new BadgeValidator();
        if($badgeValidator->validateUserBadge($user, $badge)) {
            $db = new MySQLWrapper();
            $db->init();
            $query = "INSERT INTO user_badges (user_id, badge_id)
                        VALUES (".$db->cleanse($user->getUserId()).", 
                                ".$db->cleanse($badge->getBadgeId()).")";
            $db->insertStr($query);
        }
    }
}
