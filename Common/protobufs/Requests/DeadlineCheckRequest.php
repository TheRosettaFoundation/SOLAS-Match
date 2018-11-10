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

  public function getTask_id() {
    return $this->task_id;
  }

  public function setTask_id($task_id) {
    $this->task_id = $task_id;
  }

  public function getOrg_id() {
    return $this->org_id;
  }

  public function setOrg_id($org_id) {
    $this->org_id = $org_id;
  }

  public function getUser_id() {
    return $this->user_id;
  }

  public function setUser_id($user_id) {
    $this->user_id = $user_id;
  }

}
