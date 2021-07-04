<?php
namespace SolasMatch\Common\Protobufs\Models;

class Register
{
  public $email;
  public $password;
  public $firstName;
  public $lastName;
  public $communicationsConsent;

  public function __construct() {
    $this->email = '';
    $this->password = '';
    $this->firstName = '';
    $this->lastName = '';
    $this->communicationsConsent = null;
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

  public function getFirstName() {
    return $this->firstName;
  }

  public function setFirstName($firstName) {
    $this->firstName = (string)$firstName;
  }

  public function getLastName() {
    return $this->lastName;
  }

  public function setLastName($lastName) {
    $this->lastName = (string)$lastName;
  }

  public function getCommunicationsConsent() {
    return $this->communicationsConsent;
  }

  public function hasCommunicationsConsent() {
    return $this->communicationsConsent != null;
  }

  public function setCommunicationsConsent($communicationsConsent) {
    $this->communicationsConsent = $communicationsConsent;
  }

}
