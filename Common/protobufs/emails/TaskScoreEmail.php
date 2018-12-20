<?php
namespace SolasMatch\Common\Protobufs\Emails;

class TaskScoreEmail
{
  public $email_type;
  public $body;

  public function __construct() {
    $this->email_type = 1;
    $this->body = '';
  }

  public function getEmailType() {
    return $this->email_type;
  }

  public function setEmailType($email_type) {
    $this->email_type = $email_type;
  }

  public function getBody() {
    return $this->body;
  }

  public function setBody($body) {
    $this->body = (string)$body;
  }

}
