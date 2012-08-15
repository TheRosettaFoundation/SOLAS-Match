<?php
class Badge
{
    var $_badge_id;
    var $_title;
    var $_description;
    var $_owner_id;

    //badge type = badge ID
    const PROFILE_FILLER = 3;
    const REGISTERED = 4;
    const NATIVE_LANGUAGE = 5;

    public function __construct($params) {
        if(isset($params['badge_id'])) {
            $this->setBadgeId($params['badge_id']);
        }
        if(isset($params['title'])) {
            $this->setTitle($params['title']);
        }
        if(isset($params['description'])) {
            $this->setDescription($params['description']);
        }
        if(isset($params['owner_id'])) {
            $this->setOwnerId($params['owner_id']);
        }
    }

    public function getBadgeId() {
        return $this->_badge_id;
    }

    public function setBadgeId($badgeId) {
        $this->_badge_id = $badgeId;
    }

    public function getTitle() {
        return $this->_title;
    }

    public function setTitle($title) {
        $this->_title = $title;
    }

    public function getDescription() {
        return $this->_description;
    }

    public function setDescription($description) {
        $this->_description = $description;
    }

    public function getOwnerId() {
        return $this->_owner_id;
    }

    public function setOwnerId($owner_id) {
        $this->_owner_id = $owner_id;
    }
}
