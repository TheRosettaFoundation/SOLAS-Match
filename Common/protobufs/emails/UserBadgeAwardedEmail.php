<?php
namespace SolasMatch\Common\Protobufs\Emails;

class UserBadgeAwardedEmail
{
  protected $email_type;
  protected $user_id;
  protected $badge_id;

  public function __construct() {
    $this->email_type = 22;
    $this->user_id = null;
    $this->badge_id = null;
  }

  public function getUser_id() {
    return $this->user_id;
  }

  public function setUser_id($user_id) {
    $this->user_id = $user_id;
  }

  public function getBadge_id() {
    return $this->badge_id;
  }

  public function setBadge_id($badge_id) {
    $this->badge_id = $badge_id;
  }

}
