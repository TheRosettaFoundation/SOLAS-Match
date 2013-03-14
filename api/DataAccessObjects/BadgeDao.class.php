<?php

require_once __DIR__.'/../../Common/models/Badge.php';
require_once __DIR__.'/BadgeValidator.class.php';
require_once __DIR__.'/../../Common/lib/PDOWrapper.class.php';

class BadgeDao
{
    public function getBadge($params)
    {
        $result = PDOWrapper::call("getBadge", PDOWrapper::cleanse($params['badge_id']).",null,null,null");
        return ModelFactory::buildModel("Badge", $result[0]);
    }
    
    public function insertAndUpdateBadge($badge)
    {
     
        $result = PDOWrapper::call("badgeInsertAndUpdate", PDOWrapper::cleanseNullOrWrapStr($badge->getId())
        .",".PDOWrapper::cleanseNull($badge->getOwnerId()).",".PDOWrapper::cleanseNullOrWrapStr($badge->getTitle())
        .",".PDOWrapper::cleanseNullOrWrapStr($badge->getDescription()));
        if(is_array($result)) {
            return ModelFactory::buildModel("Badge", $result[0]);
        } else {
            return null;
        }
    }
 
    public function getAllBadges()
    {
        $results = PDOWrapper::call("getBadge", "null,null,null,null");
        $ret = null;
        foreach ($results as $result) {
            $ret[]= ModelFactory::buildModel("Badge", $result);
        }
        
        return $ret;
    }

    public function getOrgBadges($org_id)
    {
        $ret = null;
        if ($badge_array = PDOWrapper::call("getBadge", "null,null,null,".PDOWrapper::cleanse($org_id))) {
            $ret = array();
            foreach ($badge_array as $badge) {
                $ret[] = ModelFactory::buildModel("Badge", $badge);
            }
        }         
        return $ret;
    }

    public function assignBadge($user, $badge)
    {
        $this->assignBadgeByID($user->getUserId(), $badge->getId());
    }
    
    public function assignBadgeByID($userID, $badgeID)
    {
        $badgeValidator = new BadgeValidator();
        if ($badgeValidator->validateUserBadgeByID($userID, $badgeID)) {
            if ($result = PDOWrapper::call("assignBadge", PDOWrapper::cleanse($userID)
                                                        .",".PDOWrapper::cleanse($badgeID))) {
                return $result[0]['result'];
            }
        }
        
        return 0;
    }

    public function removeUserBadge($user, $badge)
    {
        $this->removeUserBadgeByID($user->getUserId(), $badge->getId());
    }
    
    public function removeUserBadgeByID($userID, $badgeID)
    {
        if ($result = PDOWrapper::call("removeUserBadge", PDOWrapper::cleanse($userID)
                                                        .",".PDOWrapper::cleanse($badgeID))) {
            return $result[0]['result'];
        }
        
        return 0;
    }
    
    public function deleteBadge($badgeID)
    { 
        return PDOWrapper::call("deleteBadge", PDOWrapper::cleanseNull($badgeID));
    }    
}