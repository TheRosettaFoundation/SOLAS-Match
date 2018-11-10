<?php
namespace SolasMatch\Common\Protobufs\Requests;

class CalculateProjectDeadlinesRequest
{
  protected $class_name;
  protected $project_id;

  public function __construct() {
    $this->class_name = 'CalculateProjectDeadlinesRequest';
    $this->project_id = null;
  }

  public function getProject_id() {
    return $this->project_id;
  }

  public function setProject_id($project_id) {
    $this->project_id = $project_id;
  }

}
