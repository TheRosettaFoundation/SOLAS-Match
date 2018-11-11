<?php
namespace SolasMatch\Common\Protobufs\Requests;

class UserTaskScoreRequest
{
  public $class_name;
  public $task_id;

  public function __construct() {
    $this->class_name = 'UserTaskScoreRequest';
    $this->task_id = null;
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

}
