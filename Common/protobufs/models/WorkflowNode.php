<?php
namespace SolasMatch\Common\Protobufs\Models;

class WorkflowNode
{
  public $taskId;
  public $task;
  public $next;
  public $previous;

  public function __construct() {
    $this->taskId = null;
    $this->task = null;
    $this->next = array();
    $this->previous = array();
  }

  public function getTaskId() {
    return $this->taskId;
  }

  public function setTaskId($taskId) {
    $this->taskId = $taskId;
  }

  public function getTask() {
    return $this->task;
  }

  public function setTask($task) {
    $this->task = $task;
  }

  public function getNext() {
    return $this->next;
  }

  public function hasNext() {
    return count($this->next) > 0;
  }

  public function setNext($next, $index) {
    $this->next[$index] = $next;
  }

  public function clearNext() {
    $this->next = array();
  }

  public function addNext($next) {
    $this->next[] = $next;
  }

  public function appendNext($next) {
    $this->next[] = $next;
  }

  public function getPrevious() {
    return $this->previous;
  }

  public function hasPrevious() {
    return count($this->previous) > 0;
  }

  public function setPrevious($previous, $index) {
    $this->previous[$index] = $previous;
  }

  public function clearPrevious() {
    $this->previous = array();
  }

  public function addPrevious($previous) {
    $this->previous[] = $previous;
  }

  public function appendPrevious($previous) {
    $this->previous[] = $previous;
  }

}
