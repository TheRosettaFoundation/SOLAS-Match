<?php
namespace SolasMatch\Common\Protobufs\Emails;

class UserFeedback
{
  public $email_type;
  public $task_id;
  public $claimant_id;
  public $feedback;

  public function __construct() {
    $this->email_type = 11;
    $this->task_id = null;
    $this->claimant_id = null;
    $this->feedback = '';
  }

  public function getEmailType() {
    return $this->email_type;
  }

  public function setEmailType($email_type) {
    $this->email_type = $email_type;
  }

  public function getTaskId() {
    return $this->task_id;
  }

  public function hasTaskId() {
    return $this->task_id != null;
  }

  public function setTaskId($task_id) {
    $this->task_id = $task_id;
  }

  public function getClaimantId() {
    return $this->claimant_id;
  }

  public function hasClaimantId() {
    return $this->claimant_id != null;
  }

  public function setClaimantId($claimant_id) {
    $this->claimant_id = $claimant_id;
  }

  public function getFeedback() {
    return $this->feedback;
  }

  public function setFeedback($feedback) {
    $this->feedback = (string)$feedback;
  }

}
