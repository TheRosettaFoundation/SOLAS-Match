<?php
namespace SolasMatch\Common\Protobufs\Emails;

class TaskScoreEmail
{
  protected $email_type;
  protected $body;

  public function __construct() {
    $this->email_type = 1;
    $this->body = '';
  }

  public function getBody() {
    return $this->body;
  }

  public function setBody($body) {
    $this->body = (string)$body;
  }

}
