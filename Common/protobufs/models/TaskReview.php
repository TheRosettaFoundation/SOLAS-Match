<?php
namespace SolasMatch\Common\Protobufs\Models;

class TaskReview
{
  public $project_id;
  public $task_id;
  public $user_id;
  public $corrections;
  public $grammar;
  public $spelling;
  public $consistency;
  public $comment;

  public function __construct() {
    $this->project_id = null;
    $this->task_id = null;
    $this->user_id = null;
    $this->corrections = null;
    $this->grammar = null;
    $this->spelling = null;
    $this->consistency = null;
    $this->comment = '';
  }

  public function getProjectId() {
    return $this->project_id;
  }

  public function hasProjectId() {
    return $this->project_id != null;
  }

  public function setProjectId($project_id) {
    $this->project_id = $project_id;
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

  public function getUserId() {
    return $this->user_id;
  }

  public function hasUserId() {
    return $this->user_id != null;
  }

  public function setUserId($user_id) {
    $this->user_id = $user_id;
  }

  public function getCorrections() {
    return $this->corrections;
  }

  public function hasCorrections() {
    return $this->corrections != null;
  }

  public function setCorrections($corrections) {
    $this->corrections = $corrections;
  }

  public function getGrammar() {
    return $this->grammar;
  }

  public function hasGrammar() {
    return $this->grammar != null;
  }

  public function setGrammar($grammar) {
    $this->grammar = $grammar;
  }

  public function getSpelling() {
    return $this->spelling;
  }

  public function hasSpelling() {
    return $this->spelling != null;
  }

  public function setSpelling($spelling) {
    $this->spelling = $spelling;
  }

  public function getConsistency() {
    return $this->consistency;
  }

  public function hasConsistency() {
    return $this->consistency != null;
  }

  public function setConsistency($consistency) {
    $this->consistency = $consistency;
  }

  public function getComment() {
    return $this->comment;
  }

  public function setComment($comment) {
    $this->comment = (string)$comment;
  }

}
