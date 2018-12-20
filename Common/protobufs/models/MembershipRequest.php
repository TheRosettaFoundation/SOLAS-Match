<?php
namespace SolasMatch\Common\Protobufs\Models;

class MembershipRequest
{
  public $id;
  public $user_id;
  public $org_id;
  public $request_time;

  public function __construct() {
    $this->id = null;
    $this->user_id = null;
    $this->org_id = null;
    $this->request_time = '';
  }

  public function getId() {
    return $this->id;
  }

  public function hasId() {
    return $this->id != null;
  }

  public function setId($id) {
    $this->id = $id;
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

  public function getOrgId() {
    return $this->org_id;
  }

  public function hasOrgId() {
    return $this->org_id != null;
  }

  public function setOrgId($org_id) {
    $this->org_id = $org_id;
  }

  public function getRequestTime() {
    return $this->request_time;
  }

  public function setRequestTime($request_time) {
    $this->request_time = (string)$request_time;
  }

}
