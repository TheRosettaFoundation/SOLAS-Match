<?php
namespace SolasMatch\Common\Protobufs\Requests;

class CalculateProjectDeadlinesRequest
{
  public $class_name;
  public $project_id;

  public function __construct() {
    $this->class_name = 'CalculateProjectDeadlinesRequest';
    $this->project_id = null;
  }

  public function getClassName() {
    return $this->class_name;
  }

  public function setClassName($class_name) {
    $this->class_name = $class_name;
  }

  public function getProjectId() {
    return $this->project_id;
  }

  public function setProjectId($project_id) {
    $this->project_id = $project_id;
  }

}
