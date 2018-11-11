<?php
namespace SolasMatch\Common\Protobufs\Models;

class Task
{
  public $id;
  public $projectId;
  public $title;
  public $comment;
  public $deadline;
  public $wordCount;
  public $createdTime;
  public $sourceLocale;
  public $targetLocale;
  public $taskType;
  public $taskStatus;
  public $published;

  public function __construct() {
    $this->id = null;
    $this->projectId = null;
    $this->title = '';
    $this->comment = '';
    $this->deadline = '';
    $this->wordCount = null;
    $this->createdTime = '';
    $this->sourceLocale = null;
    $this->targetLocale = null;
    $this->taskType = null;
    $this->taskStatus = null;
    $this->published = false;
  }

  public function getId() {
    return $this->id;
  }

  public function setId($id) {
    $this->id = $id;
  }

  public function getProjectId() {
    return $this->projectId;
  }

  public function setProjectId($projectId) {
    $this->projectId = $projectId;
  }

  public function getTitle() {
    return $this->title;
  }

  public function setTitle($title) {
    $this->title = (string)$title;
  }

  public function getComment() {
    return $this->comment;
  }

  public function setComment($comment) {
    $this->comment = (string)$comment;
  }

  public function getDeadline() {
    return $this->deadline;
  }

  public function setDeadline($deadline) {
    $this->deadline = (string)$deadline;
  }

  public function getWordCount() {
    return $this->wordCount;
  }

  public function setWordCount($wordCount) {
    $this->wordCount = $wordCount;
  }

  public function getCreatedTime() {
    return $this->createdTime;
  }

  public function setCreatedTime($createdTime) {
    $this->createdTime = (string)$createdTime;
  }

  public function getSourceLocale() {
    return $this->sourceLocale;
  }

  public function setSourceLocale($sourceLocale) {
    $this->sourceLocale = $sourceLocale;
  }

  public function getTargetLocale() {
    return $this->targetLocale;
  }

  public function setTargetLocale($targetLocale) {
    $this->targetLocale = $targetLocale;
  }

  public function getTaskType() {
    return $this->taskType;
  }

  public function setTaskType($taskType) {
    $this->taskType = $taskType;
  }

  public function getTaskStatus() {
    return $this->taskStatus;
  }

  public function setTaskStatus($taskStatus) {
    $this->taskStatus = $taskStatus;
  }

  public function getPublished() {
    return $this->published;
  }

  public function setPublished($published) {
    $this->published = (boolean)$published;
  }

}
