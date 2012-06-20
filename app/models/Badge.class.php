<?php
class Badge
{
    var $_badge_id;
    var $_title;
    var $_description;

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
}
