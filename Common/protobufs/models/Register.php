<?php
namespace SolasMatch\Common\Protobufs\Models;

class Register
{
  protected $email;
  protected $password;

  public function __construct() {
    $this->email = '';
    $this->password = '';
  }

  public function getEmail() {
    return $this->email;
  }

  public function setEmail($email) {
    $this->email = (string)$email;
  }

  public function getPassword() {
    return $this->password;
  }

  public function setPassword($password) {
    $this->password = (string)$password;
  }

}
