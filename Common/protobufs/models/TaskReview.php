<?php
namespace SolasMatch\Common\Protobufs\Models;

class TaskReview
{
  protected $project_id;
  protected $task_id;
  protected $user_id;
  protected $corrections;
  protected $grammar;
  protected $spelling;
  protected $consistency;
  protected $comment;

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

  public function getProject_id() {
    return $this->project_id;
  }

  public function setProject_id($project_id) {
    $this->project_id = $project_id;
  }

  public function getTask_id() {
    return $this->task_id;
  }

  public function setTask_id($task_id) {
    $this->task_id = $task_id;
  }

  public function getUser_id() {
    return $this->user_id;
  }

  public function setUser_id($user_id) {
    $this->user_id = $user_id;
  }

  public function getCorrections() {
    return $this->corrections;
  }

  public function setCorrections($corrections) {
    $this->corrections = $corrections;
  }

  public function getGrammar() {
    return $this->grammar;
  }

  public function setGrammar($grammar) {
    $this->grammar = $grammar;
  }

  public function getSpelling() {
    return $this->spelling;
  }

  public function setSpelling($spelling) {
    $this->spelling = $spelling;
  }

  public function getConsistency() {
    return $this->consistency;
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
