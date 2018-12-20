<?php
namespace SolasMatch\Common\Protobufs\Models;

class Locale
{
  public $languageName;
  public $languageCode;
  public $countryName;
  public $countryCode;

  public function __construct() {
    $this->languageName = '';
    $this->languageCode = '';
    $this->countryName = '';
    $this->countryCode = '';
  }

  public function getLanguageName() {
    return $this->languageName;
  }

  public function setLanguageName($languageName) {
    $this->languageName = (string)$languageName;
  }

  public function getLanguageCode() {
    return $this->languageCode;
  }

  public function setLanguageCode($languageCode) {
    $this->languageCode = (string)$languageCode;
  }

  public function getCountryName() {
    return $this->countryName;
  }

  public function setCountryName($countryName) {
    $this->countryName = (string)$countryName;
  }

  public function getCountryCode() {
    return $this->countryCode;
  }

  public function setCountryCode($countryCode) {
    $this->countryCode = (string)$countryCode;
  }

}
