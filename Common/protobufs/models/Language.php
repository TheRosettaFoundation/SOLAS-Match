<?php
namespace SolasMatch\Common\Protobufs\Models;

class Language
{
  public $id;
  public $code;
  public $name;

  public function __construct() {
    $this->id = null;
    $this->code = '';
    $this->name = '';
  }

  public function getId() {
    return $this->id;
  }

  public function setId($id) {
    $this->id = $id;
  }

  public function getCode() {
    return $this->code;
  }

  public function setCode($code) {
    $this->code = (string)$code;
  }

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = (string)$name;
  }

}
