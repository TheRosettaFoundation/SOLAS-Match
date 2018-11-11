<?php
namespace SolasMatch\Common\Protobufs\Requests;

class DeadlineCheckRequest
{
  protected $class_name;
  protected $task_id;
  protected $org_id;
  protected $user_id;

  public function __construct() {
    $this->class_name = 'DeadlineCheckRequest';
    $this->task_id = null;
    $this->org_id = null;
    $this->user_id = null;
  }

  public function getClassName() {
    return $this->class_name;
  }

  public function setClassName($class_name) {
    $this->class_name = $class_name;
  }

  public function getTaskId() {
    return $this->task_id;
  }

  public function setTaskId($task_id) {
    $this->task_id = $task_id;
  }

  public function getOrgId() {
    return $this->org_id;
  }

  public function setOrgId($org_id) {
    $this->org_id = $org_id;
  }

  public function getUserId() {
    return $this->user_id;
  }

  public function setUserId($user_id) {
    $this->user_id = $user_id;
  }

}
