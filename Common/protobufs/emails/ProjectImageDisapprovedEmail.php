<?php
namespace SolasMatch\Common\Protobufs\Emails;

class ProjectImageDisapprovedEmail
{
  protected $email_type;
  protected $project_id;
  protected $user_id;

  public function __construct() {
    $this->email_type = 32;
    $this->project_id = null;
    $this->user_id = null;
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

  public function getUserId() {
    return $this->user_id;
  }

  public function setUserId($user_id) {
    $this->user_id = $user_id;
  }

}
