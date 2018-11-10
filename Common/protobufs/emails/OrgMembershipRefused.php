<?php
namespace SolasMatch\Common\Protobufs\Emails;

class OrgMembershipRefused
{
  protected $email_type;
  protected $user_id;
  protected $org_id;

  public function __construct() {
    $this->email_type = 4;
    $this->user_id = null;
    $this->org_id = null;
  }

  public function getUser_id() {
    return $this->user_id;
  }

  public function setUser_id($user_id) {
    $this->user_id = $user_id;
  }

  public function getOrg_id() {
    return $this->org_id;
  }

  public function setOrg_id($org_id) {
    $this->org_id = $org_id;
  }

}
