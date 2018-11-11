<?php
namespace SolasMatch\Common\Protobufs\Emails;

class TaskArchived
{
  public $email_type;
  public $user_id;
  public $task_id;

  public function __construct() {
    $this->email_type = 6;
    $this->user_id = null;
    $this->task_id = null;
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

  public function getTaskId() {
    return $this->task_id;
  }

  public function setTaskId($task_id) {
    $this->task_id = $task_id;
  }

}
