<?php
namespace SolasMatch\Common\Protobufs\Models;

class PasswordReset
{
  protected $password;
  protected $key;

  public function __construct() {
    $this->password = '';
    $this->key = '';
  }

  public function getPassword() {
    return $this->password;
  }

  public function setPassword($password) {
    $this->password = (string)$password;
  }

  public function getKey() {
    return $this->key;
  }

  public function setKey($key) {
    $this->key = (string)$key;
  }

}
