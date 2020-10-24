<?php
namespace SolasMatch\Common\Protobufs\Emails;

class UserBadgeAwardedEmail
{
  public $email_type;
  public $user_id;
  public $badge_id;

  public function __construct() {
    $this->email_type = 22;
    $this->user_id = null;
    $this->badge_id = null;
  }

  public function getEmailType() {
    return $this->email_type;
  }

  public function setEmailType($email_type) {
    $this->email_type = $email_type;
  }

  public function getUserId() {
    return $this->user_id;
  }

  public function hasUserId() {
    return $this->user_id != null;
  }

  public function setUserId($user_id) {
    $this->user_id = $user_id;
  }

  public function getBadgeId() {
    return $this->badge_id;
  }

  public function hasBadgeId() {
    return $this->badge_id != null;
  }

  public function setBadgeId($badge_id) {
    $this->badge_id = $badge_id;
  }

}
