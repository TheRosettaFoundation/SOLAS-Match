<?php
namespace SolasMatch\Common\Protobufs\Models;

class Organisation
{
  public $id;
  public $name;
  public $biography;
  public $homepage;
  public $email;
  public $address;
  public $city;
  public $country;
  public $regionalFocus;

  public function __construct() {
    $this->id = null;
    $this->name = '';
    $this->biography = '';
    $this->homepage = '';
    $this->email = '';
    $this->address = '';
    $this->city = '';
    $this->country = '';
    $this->regionalFocus = '';
  }

  public function getId() {
    return $this->id;
  }

  public function setId($id) {
    $this->id = $id;
  }

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = (string)$name;
  }

  public function getBiography() {
    return $this->biography;
  }

  public function setBiography($biography) {
    $this->biography = (string)$biography;
  }

  public function getHomepage() {
    return $this->homepage;
  }

  public function setHomepage($homepage) {
    $this->homepage = (string)$homepage;
  }

  public function getEmail() {
    return $this->email;
  }

  public function setEmail($email) {
    $this->email = (string)$email;
  }

  public function getAddress() {
    return $this->address;
  }

  public function setAddress($address) {
    $this->address = (string)$address;
  }

  public function getCity() {
    return $this->city;
  }

  public function setCity($city) {
    $this->city = (string)$city;
  }

  public function getCountry() {
    return $this->country;
  }

  public function setCountry($country) {
    $this->country = (string)$country;
  }

  public function getRegionalFocus() {
    return $this->regionalFocus;
  }

  public function setRegionalFocus($regionalFocus) {
    $this->regionalFocus = (string)$regionalFocus;
  }

}
