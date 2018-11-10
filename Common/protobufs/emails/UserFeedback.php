<?php
namespace SolasMatch\Common\Protobufs\Emails;

class UserFeedback
{
  protected $email_type;
  protected $task_id;
  protected $claimant_id;
  protected $feedback;

  public function __construct() {
    $this->email_type = 11;
    $this->task_id = null;
    $this->claimant_id = null;
    $this->feedback = '';
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

  public function getFeedback() {
    return $this->feedback;
  }

  public function setFeedback($feedback) {
    $this->feedback = (string)$feedback;
  }

}
