<?php
namespace SolasMatch\Common\Protobufs\Requests;

class OrgCreatedNotificationRequest
{
  protected $class_name;
  protected $org_id;

  public function __construct() {
    $this->class_name = 'OrgCreatedNotificationRequest';
    $this->org_id = null;
  }

  public function getOrg_id() {
    return $this->org_id;
  }

  public function setOrg_id($org_id) {
    $this->org_id = $org_id;
  }

}
