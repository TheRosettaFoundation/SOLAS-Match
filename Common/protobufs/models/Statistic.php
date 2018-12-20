<?php
namespace SolasMatch\Common\Protobufs\Models;

class Statistic
{
  public $name;
  public $value;

  public function __construct() {
    $this->name = '';
    $this->value = '';
  }

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = (string)$name;
  }

  public function getValue() {
    return $this->value;
  }

  public function setValue($value) {
    $this->value = (string)$value;
  }

}
