<?php
namespace SolasMatch\Common\Protobufs\Requests;

class TaskUploadNotificationRequest
{
  protected $class_name;
  protected $task_id;
  protected $file_version;

  public function __construct() {
    $this->class_name = 'TaskUploadNotificationRequest';
    $this->task_id = null;
    $this->file_version = null;
  }

  public function getTask_id() {
    return $this->task_id;
  }

  public function setTask_id($task_id) {
    $this->task_id = $task_id;
  }

  public function getFile_version() {
    return $this->file_version;
  }

  public function setFile_version($file_version) {
    $this->file_version = $file_version;
  }

}
