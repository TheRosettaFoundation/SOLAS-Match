<?php
namespace SolasMatch\Common\Protobufs\Emails;

class PasswordResetEmail
{
  protected $email_type;
  protected $user_id;

  public function __construct() {
    $this->email_type = 5;
    $this->user_id = null;
  }

  public function getUser_id() {
    return $this->user_id;
  }

  public function setUser_id($user_id) {
    $this->user_id = $user_id;
  }

}
