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

  public function getClassName() {
    return $this->class_name;
  }

  public function setClassName($class_name) {
    $this->class_name = $class_name;
  }

  public function getOrgId() {
    return $this->org_id;
  }

  public function setOrgId($org_id) {
    $this->org_id = $org_id;
  }

}
