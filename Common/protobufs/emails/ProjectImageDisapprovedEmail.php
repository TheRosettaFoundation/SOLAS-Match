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

  public function getProject_id() {
    return $this->project_id;
  }

  public function setProject_id($project_id) {
    $this->project_id = $project_id;
  }

  public function getUser_id() {
    return $this->user_id;
  }

  public function setUser_id($user_id) {
    $this->user_id = $user_id;
  }

}
