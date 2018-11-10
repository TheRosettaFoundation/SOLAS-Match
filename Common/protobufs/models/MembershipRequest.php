<?php
namespace SolasMatch\Common\Protobufs\Models;

class MembershipRequest
{
  protected $id;
  protected $user_id;
  protected $org_id;
  protected $request_time;

  public function __construct() {
    $this->id = null;
    $this->user_id = null;
    $this->org_id = null;
    $this->request_time = '';
  }

  public function getId() {
    return $this->id;
  }

  public function setId($id) {
    $this->id = $id;
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

  public function getRequest_time() {
    return $this->request_time;
  }

  public function setRequest_time($request_time) {
    $this->request_time = (string)$request_time;
  }

}
