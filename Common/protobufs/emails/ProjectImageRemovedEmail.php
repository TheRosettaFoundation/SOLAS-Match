<?php
namespace SolasMatch\Common\Protobufs\Emails;

class ProjectImageRemovedEmail
{
  public $email_type;
  public $project_id;

  public function __construct() {
    $this->email_type = 30;
    $this->project_id = null;
  }

  public function getEmailType() {
    return $this->email_type;
  }

  public function setEmailType($email_type) {
    $this->email_type = $email_type;
  }

  public function getProjectId() {
    return $this->project_id;
  }

  public function setProjectId($project_id) {
    $this->project_id = $project_id;
  }

}
