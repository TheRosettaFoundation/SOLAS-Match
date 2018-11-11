<?php
namespace SolasMatch\Common\Protobufs\Models;

class ProjectFile
{
  public $projectId;
  public $filename;
  public $token;
  public $userId;
  public $mime;

  public function __construct() {
    $this->projectId = null;
    $this->filename = '';
    $this->token = '';
    $this->userId = null;
    $this->mime = '';
  }

  public function getProjectId() {
    return $this->projectId;
  }

  public function setProjectId($projectId) {
    $this->projectId = $projectId;
  }

  public function getFilename() {
    return $this->filename;
  }

  public function setFilename($filename) {
    $this->filename = (string)$filename;
  }

  public function getToken() {
    return $this->token;
  }

  public function setToken($token) {
    $this->token = (string)$token;
  }

  public function getUserId() {
    return $this->userId;
  }

  public function setUserId($userId) {
    $this->userId = $userId;
  }

  public function getMime() {
    return $this->mime;
  }

  public function setMime($mime) {
    $this->mime = (string)$mime;
  }

}
