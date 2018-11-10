<?php
namespace SolasMatch\Common\Protobufs\Models;

class PasswordResetRequest
{
  protected $user_id;
  protected $key;
  protected $requestTime;

  public function __construct() {
    $this->user_id = null;
    $this->key = '';
    $this->requestTime = '';
  }

  public function getUser_id() {
    return $this->user_id;
  }

  public function setUser_id($user_id) {
    $this->user_id = $user_id;
  }

  public function getKey() {
    return $this->key;
  }

  public function setKey($key) {
    $this->key = (string)$key;
  }

  public function getRequestTime() {
    return $this->requestTime;
  }

  public function setRequestTime($requestTime) {
    $this->requestTime = (string)$requestTime;
  }

}
