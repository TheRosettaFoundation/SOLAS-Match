<?php
namespace SolasMatch\Common\Protobufs\Emails;

class TaskArchived
{
  protected $email_type;
  protected $user_id;
  protected $task_id;

  public function __construct() {
    $this->email_type = 6;
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
