<?php
namespace SolasMatch\Common\Protobufs\Emails;

class ClaimedTaskUploadeX
{
  public email_type;
  public user_id;
  public task_id;

  void __construct() {
    $this->email_type = 16;
    $this->user_id = null;
    $this->task_id = null;
  }

  public function getUser_id() {
    return $this->user_id;
  }

  public function setUser_id($user_id) {
    $this->user_id = $user_id;
  }

  public function getTask_id() {
    return $this->task_id;
  }

  public function setTask_id($task_id) {
    $this->task_id = $task_id;
  }

}
