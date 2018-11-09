<?php
namespace SolasMatch\Common\Protobufs\Requests;

class CalculateProjectDeadlinesRequesX
{
  public class_name;
  public project_id;

  void __construct() {
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
