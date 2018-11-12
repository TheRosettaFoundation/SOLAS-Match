<?php
namespace SolasMatch\Common\Protobufs\Models;

class UserPersonalInformation
{
  public $id;
  public $userId;
  public $firstName;
  public $lastName;
  public $mobileNumber;
  public $businessNumber;
  public $languagePreference;
  public $jobTitle;
  public $address;
  public $city;
  public $country;
  public $receive_credit;

  public function __construct() {
    $this->id = null;
    $this->userId = null;
    $this->firstName = '';
    $this->lastName = '';
    $this->mobileNumber = '';
    $this->businessNumber = '';
    $this->languagePreference = null;
    $this->jobTitle = '';
    $this->address = '';
    $this->city = '';
    $this->country = '';
    $this->receive_credit = false;
  }

  public function getId() {
    return $this->id;
  }

  public function setId($id) {
    $this->id = $id;
  }

  public function getUserId() {
    return $this->userId;
  }

  public function setUserId($userId) {
    $this->userId = $userId;
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

  public function getMobileNumber() {
    return $this->mobileNumber;
  }

  public function setMobileNumber($mobileNumber) {
    $this->mobileNumber = (string)$mobileNumber;
  }

  public function getBusinessNumber() {
    return $this->businessNumber;
  }

  public function setBusinessNumber($businessNumber) {
    $this->businessNumber = (string)$businessNumber;
  }

  public function getLanguagePreference() {
    return $this->languagePreference;
  }

  public function setLanguagePreference($languagePreference) {
    $this->languagePreference = $languagePreference;
  }

  public function getJobTitle() {
    return $this->jobTitle;
  }

  public function setJobTitle($jobTitle) {
    $this->jobTitle = (string)$jobTitle;
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

  public function getReceiveCredit() {
    return $this->receive_credit;
  }

  public function setReceiveCredit($receive_credit) {
    $this->receive_credit = (boolean)$receive_credit;
  }

}
