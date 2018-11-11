<?php
namespace SolasMatch\Common\Protobufs\Models;

class Badge
{
  protected $id;
  protected $title;
  protected $description;
  protected $owner_id;

  public function __construct() {
    $this->id = null;
    $this->title = '';
    $this->description = '';
    $this->owner_id = null;
  }

  public function getId() {
    return $this->id;
  }

  public function setId($id) {
    $this->id = $id;
  }

  public function getTitle() {
    return $this->title;
  }

  public function setTitle($title) {
    $this->title = (string)$title;
  }

  public function getDescription() {
    return $this->description;
  }

  public function setDescription($description) {
    $this->description = (string)$description;
  }

  public function getOwnerId() {
    return $this->owner_id;
  }

  public function setOwnerId($owner_id) {
    $this->owner_id = $owner_id;
  }

}
