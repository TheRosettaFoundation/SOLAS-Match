<?php
namespace SolasMatch\Common\Protobufs\Emails;

class TaskClaimed
{
  protected $email_type;
  protected $user_id;
  protected $translator_id;
  protected $task_id;

  public function __construct() {
    $this->email_type = 7;
    $this->user_id = null;
    $this->translator_id = null;
    $this->task_id = null;
  }

  public function getUser_id() {
    return $this->user_id;
  }

  public function setUser_id($user_id) {
    $this->user_id = $user_id;
  }

  public function getTranslator_id() {
    return $this->translator_id;
  }

  public function setTranslator_id($translator_id) {
    $this->translator_id = $translator_id;
  }

  public function getTask_id() {
    return $this->task_id;
  }

  public function setTask_id($task_id) {
    $this->task_id = $task_id;
  }

}
