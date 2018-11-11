<?php
namespace SolasMatch\Common\Protobufs\Emails;

class OrgMembershipAccepted
{
  protected $email_type;
  protected $user_id;
  protected $org_id;

  public function __construct() {
    $this->email_type = 3;
    $this->user_id = null;
    $this->org_id = null;
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

  public function setUserId($user_id) {
    $this->user_id = $user_id;
  }

  public function getOrgId() {
    return $this->org_id;
  }

  public function setOrgId($org_id) {
    $this->org_id = $org_id;
  }

}
