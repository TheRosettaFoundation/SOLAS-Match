<?php
namespace SolasMatch\Common\Protobufs\Models;

class ArchivedTask
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
  public $version;
  public $fileName;
  public $contentType;
  public $uploadTime;
  public $userIdClaimed;
  public $userIdArchived;
  public $prerequisites;
  public $userIdTaskCreator;
  public $archivedDate;

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
    $this->taskStatus = '';
    $this->published = false;
    $this->version = null;
    $this->fileName = '';
    $this->contentType = '';
    $this->uploadTime = '';
    $this->userIdClaimed = null;
    $this->userIdArchived = null;
    $this->prerequisites = '';
    $this->userIdTaskCreator = null;
    $this->archivedDate = '';
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
    $this->taskStatus = (string)$taskStatus;
  }

  public function getPublished() {
    return $this->published;
  }

  public function setPublished($published) {
    $this->published = (boolean)$published;
  }

  public function getVersion() {
    return $this->version;
  }

  public function setVersion($version) {
    $this->version = $version;
  }

  public function getFileName() {
    return $this->fileName;
  }

  public function setFileName($fileName) {
    $this->fileName = (string)$fileName;
  }

  public function getContentType() {
    return $this->contentType;
  }

  public function setContentType($contentType) {
    $this->contentType = (string)$contentType;
  }

  public function getUploadTime() {
    return $this->uploadTime;
  }

  public function setUploadTime($uploadTime) {
    $this->uploadTime = (string)$uploadTime;
  }

  public function getUserIdClaimed() {
    return $this->userIdClaimed;
  }

  public function setUserIdClaimed($userIdClaimed) {
    $this->userIdClaimed = $userIdClaimed;
  }

  public function getUserIdArchived() {
    return $this->userIdArchived;
  }

  public function setUserIdArchived($userIdArchived) {
    $this->userIdArchived = $userIdArchived;
  }

  public function getPrerequisites() {
    return $this->prerequisites;
  }

  public function setPrerequisites($prerequisites) {
    $this->prerequisites = (string)$prerequisites;
  }

  public function getUserIdTaskCreator() {
    return $this->userIdTaskCreator;
  }

  public function setUserIdTaskCreator($userIdTaskCreator) {
    $this->userIdTaskCreator = $userIdTaskCreator;
  }

  public function getArchivedDate() {
    return $this->archivedDate;
  }

  public function setArchivedDate($archivedDate) {
    $this->archivedDate = (string)$archivedDate;
  }

}
