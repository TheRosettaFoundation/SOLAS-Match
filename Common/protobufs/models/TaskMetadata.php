<?php
namespace SolasMatch\Common\Protobufs\Models;

class TaskMetadata
{
  public $id;
  public $version;
  public $filename;
  public $content_type;
  public $user_id;
  public $upload_time;

  public function __construct() {
    $this->id = null;
    $this->version = null;
    $this->filename = '';
    $this->content_type = '';
    $this->user_id = null;
    $this->upload_time = '';
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

  public function getVersion() {
    return $this->version;
  }

  public function hasVersion() {
    return $this->version != null;
  }

  public function setVersion($version) {
    $this->version = $version;
  }

  public function getFilename() {
    return $this->filename;
  }

  public function setFilename($filename) {
    $this->filename = (string)$filename;
  }

  public function getContentType() {
    return $this->content_type;
  }

  public function setContentType($content_type) {
    $this->content_type = (string)$content_type;
  }

  public function getUserId() {
    return $this->user_id;
  }

  public function hasUserId() {
    return $this->user_id != null;
  }

  public function setUserId($user_id) {
    $this->user_id = $user_id;
  }

  public function getUploadTime() {
    return $this->upload_time;
  }

  public function setUploadTime($upload_time) {
    $this->upload_time = (string)$upload_time;
  }

}
