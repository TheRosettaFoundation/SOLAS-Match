<?php
namespace SolasMatch\Common\Protobufs\Emails;

class ProjectImageUploadedEmail
{
  public $email_type;
  public $project_id;

  public function __construct() {
    $this->email_type = 29;
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

  public function hasProjectId() {
    return $this->project_id != null;
  }

  public function setProjectId($project_id) {
    $this->project_id = $project_id;
  }

}
