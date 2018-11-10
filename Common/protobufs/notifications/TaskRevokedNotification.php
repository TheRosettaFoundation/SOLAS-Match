<?php
namespace SolasMatch\Common\Protobufs\Notifications;

class TaskRevokedNotification
{
  protected $class_name;
  protected $task_id;
  protected $claimant_id;

  public function __construct() {
    $this->class_name = 'TaskRevokedNotification';
    $this->task_id = null;
    $this->claimant_id = null;
  }

  public function getTask_id() {
    return $this->task_id;
  }

  public function setTask_id($task_id) {
    $this->task_id = $task_id;
  }

  public function getClaimant_id() {
    return $this->claimant_id;
  }

  public function setClaimant_id($claimant_id) {
    $this->claimant_id = $claimant_id;
  }

}
