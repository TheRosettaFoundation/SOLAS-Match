<?php

require('models/Badge.class.php');
require('BadgeValidator.class.php');

class BadgeDao
{
    public function find($params)
    {
        $query = null;
        $db = new MySQLWrapper();
        $db->init();
        if(isset($params['badge_id'])) {
            $query = 'SELECT *
                        FROM badges
                        WHERE badge_id='.$db->cleanse($params['badge_id']);
        }

        $ret = null;
        if($results = $db->Select($query)) {
            $badge_data = array(
                'badge_id' => $results[0]['badge_id'],
                'title' => $results[0]['title'],
                'description' => $results[0]['description']
            );
            $ret = new Badge($badge_data);
        }

        return $ret;
    }

    public function getAllBadges()
    {
        $db = new MySQLWrapper();
        $db->init();
        $query = 'SELECT *
                    FROM badges';
        $results = $db->Select($query);
        return $results;
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
