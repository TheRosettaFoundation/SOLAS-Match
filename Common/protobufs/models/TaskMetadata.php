<?php
namespace SolasMatch\Common\Protobufs\Models;

class TaskMetadata
{
  protected $id;
  protected $version;
  protected $filename;
  protected $content_type;
  protected $user_id;
  protected $upload_time;

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

  public function setId($id) {
    $this->id = $id;
  }

  public function getVersion() {
    return $this->version;
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

  public function getContent_type() {
    return $this->content_type;
  }

  public function setContent_type($content_type) {
    $this->content_type = (string)$content_type;
  }

  public function getUser_id() {
    return $this->user_id;
  }

  public function setUser_id($user_id) {
    $this->user_id = $user_id;
  }

  public function getUpload_time() {
    return $this->upload_time;
  }

  public function setUpload_time($upload_time) {
    $this->upload_time = (string)$upload_time;
  }

}
