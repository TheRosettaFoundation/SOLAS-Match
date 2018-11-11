<?php
namespace SolasMatch\Common\Protobufs\Notifications;

class TaskRevokedNotification
{
  public $class_name;
  public $task_id;
  public $claimant_id;

  public function __construct() {
    $this->class_name = 'TaskRevokedNotification';
    $this->task_id = null;
    $this->claimant_id = null;
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

  public function getClaimantId() {
    return $this->claimant_id;
  }

  public function setClaimantId($claimant_id) {
    $this->claimant_id = $claimant_id;
  }

}
