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
                'owner_id' => $results[0]['owner_id'],
                'title' => $results[0]['title'],
                'description' => $results[0]['description']
            );
            $ret = new Badge($badge_data);
        }

        return $ret;
    }

    public function save($badge)
    {
        $db = new MySQLWrapper();
        $db->init();
        $insert = array();
        $insert['owner_id'] = $badge->getOwnerId();
        $insert['title'] = "\"".$badge->getTitle()."\"";
        $insert['description'] = "\"".$badge->getDescription()."\"";
        $db->Insert('badges', $insert);
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

    public function getOrgBadges($org_id)
    {
        $ret = NULL;
        $db = new MySQLWrapper();
        $db->init();
        $query = "SELECT *
                    FROM badges
                    WHERE owner_id = ".$db->cleanse($org_id);

        if($results = $db->Select($query)) {
            $ret = $results;
        }

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

    public function removeUserBadge($user, $badge)
    {
        if(!is_null($badge->getOwnerId())) {
            $db = new MySQLWrapper();
            $db->init();
            $delete = "DELETE FROM user_badges
                        WHERE user_id=".$db->cleanse($user->getUserId())."
                        AND badge_id=".$db->cleanse($badge->getBadgeId());
            $db->Delete($delete);
        } else {
            echo "<p>Cannot remove system badges</p>";
        }
    }
}
