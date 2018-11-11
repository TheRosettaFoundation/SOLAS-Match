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

  public function getClassName() {
    return $this->class_name;
  }

  public function setClassName($class_name) {
    $this->class_name = $class_name;
  }

  public function getTaskId() {
    return $this->task_id;
  }

  public function setTaskId($task_id) {
    $this->task_id = $task_id;
  }

  public function getFileVersion() {
    return $this->file_version;
  }

  public function setFileVersion($file_version) {
    $this->file_version = $file_version;
  }

}
