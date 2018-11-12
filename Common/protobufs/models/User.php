<?php
namespace SolasMatch\Common\Protobufs\Models;

class User
{
  public $id;
  public $display_name;
  public $email;
  public $password;
  public $biography;
  public $nonce;
  public $created_time;
  public $nativeLocale;
  public $secondaryLocales;

  public function __construct() {
    $this->id = null;
    $this->display_name = '';
    $this->email = '';
    $this->password = '';
    $this->biography = '';
    $this->nonce = '';
    $this->created_time = '';
    $this->nativeLocale = null;
    $this->secondaryLocales = array();
  }

  public function getId() {
    return $this->id;
  }

  public function setId($id) {
    $this->id = $id;
  }

  public function getDisplayName() {
    return $this->display_name;
  }

  public function setDisplayName($display_name) {
    $this->display_name = (string)$display_name;
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

  public function getBiography() {
    return $this->biography;
  }

  public function setBiography($biography) {
    $this->biography = (string)$biography;
  }

  public function getNonce() {
    return $this->nonce;
  }

  public function setNonce($nonce) {
    $this->nonce = (string)$nonce;
  }

  public function getCreatedTime() {
    return $this->created_time;
  }

  public function setCreatedTime($created_time) {
    $this->created_time = (string)$created_time;
  }

  public function getNativeLocale() {
    return $this->nativeLocale;
  }

  public function setNativeLocale($nativeLocale) {
    $this->nativeLocale = $nativeLocale;
  }

  public function getSecondaryLocales() {
    return $this->secondaryLocales;
  }

  public function hasSecondaryLocales() {
    return count($this->secondaryLocales) > 0;
  }

  public function setSecondaryLocales($secondaryLocales, $index) {
    $this->secondaryLocales[$index] = $secondaryLocales;
  }

  public function clearSecondaryLocales() {
    $this->secondaryLocales = array();
  }

  public function addSecondaryLocales($secondaryLocales) {
    $this->secondaryLocales[] = $secondaryLocales;
  }

  public function appendSecondaryLocales($secondaryLocales) {
    $this->secondaryLocales[] = $secondaryLocales;
  }

}
