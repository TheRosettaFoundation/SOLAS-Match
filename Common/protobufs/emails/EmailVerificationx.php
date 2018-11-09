<?php
namespace SolasMatch\Common\Protobufs\Emails;

class EmailVerificatioX
{
  public email_type;
  public user_id;

  void __construct() {
    $this->email_type = 13;
    $this->user_id = null;
  }

  public function getUser_id() {
    return $this->user_id;
  }

  public function setUser_id($user_id) {
    $this->user_id = $user_id;
  }

}
