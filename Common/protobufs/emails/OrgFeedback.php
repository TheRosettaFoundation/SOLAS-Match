<?php
namespace SolasMatch\Common\Protobufs\Emails;

class OrgFeedback
{
  protected $email_type;
  protected $task_id;
  protected $claimant_id;
  protected $feedback;
  protected $user_id;

  public function __construct() {
    $this->email_type = 18;
    $this->task_id = null;
    $this->claimant_id = null;
    $this->feedback = '';
    $this->user_id = null;
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

  public function getUser_id() {
    return $this->user_id;
  }

  public function setUser_id($user_id) {
    $this->user_id = $user_id;
  }

}
