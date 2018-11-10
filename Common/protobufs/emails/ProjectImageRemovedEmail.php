<?php
namespace SolasMatch\Common\Protobufs\Emails;

class ProjectImageRemovedEmail
{
  protected $email_type;
  protected $project_id;

  public function __construct() {
    $this->email_type = 30;
    $this->project_id = null;
  }

  public function getProject_id() {
    return $this->project_id;
  }

  public function setProject_id($project_id) {
    $this->project_id = $project_id;
  }

}
