<?php
namespace SolasMatch\Common\Protobufs\Requests;

class UserTaskScoreRequest
{
  protected $class_name;
  protected $task_id;

  public function __construct() {
    $this->class_name = 'UserTaskScoreRequest';
    $this->task_id = null;
  }

  public function getTask_id() {
    return $this->task_id;
  }

  public function setTask_id($task_id) {
    $this->task_id = $task_id;
  }

}
