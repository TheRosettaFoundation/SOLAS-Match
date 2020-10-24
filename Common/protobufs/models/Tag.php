<?php
namespace SolasMatch\Common\Protobufs\Models;

class Tag
{
  public $id;
  public $label;

  public function __construct() {
    $this->id = null;
    $this->label = '';
  }

  public function getId() {
    return $this->id;
  }

  public function hasId() {
    return $this->id != null;
  }

  public function setId($id) {
    $this->id = $id;
  }

  public function getLabel() {
    return $this->label;
  }

  public function setLabel($label) {
    $this->label = (string)$label;
  }

}
