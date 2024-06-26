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
  public $word_count_partner_weighted;
  public $word_count_original;
  public $source_quantity;
  public $createdTime;
  public $sourceLocale;
  public $targetLocale;
  public $taskType;
  public $taskStatus;
  public $published;
  public $cancelled;

  public function __construct() {
    $this->id = null;
    $this->projectId = null;
    $this->title = '';
    $this->comment = '';
    $this->deadline = '';
    $this->wordCount = null;
    $this->word_count_partner_weighted = 0;
    $this->word_count_original = 0;
    $this->source_quantity = 0;
    $this->createdTime = '';
    $this->sourceLocale = null;
    $this->targetLocale = null;
    $this->taskType = null;
    $this->taskStatus = null;
    $this->published = false;
    $this->cancelled = 0;
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

  public function getProjectId() {
    return $this->projectId;
  }

  public function hasProjectId() {
    return $this->projectId != null;
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

  public function hasWordCount() {
    return $this->wordCount != null;
  }

  public function setWordCount($wordCount) {
    $this->wordCount = $wordCount;
  }

  public function get_word_count_partner_weighted() {
    return $this->word_count_partner_weighted;
  }

  public function set_word_count_partner_weighted($word_count_partner_weighted) {
    $this->word_count_partner_weighted = $word_count_partner_weighted;
  }

  public function get_word_count_original() {
    return $this->word_count_original;
  }

  public function set_word_count_original($word_count_original) {
    $this->word_count_original = $word_count_original;
  }

  public function get_source_quantity() {
    return $this->source_quantity;
  }

  public function set_source_quantity($source_quantity) {
    $this->source_quantity = $source_quantity;
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

  public function hasSourceLocale() {
    return $this->sourceLocale != null;
  }

  public function setSourceLocale($sourceLocale) {
    $this->sourceLocale = $sourceLocale;
  }

  public function getTargetLocale() {
    return $this->targetLocale;
  }

  public function hasTargetLocale() {
    return $this->targetLocale != null;
  }

  public function setTargetLocale($targetLocale) {
    $this->targetLocale = $targetLocale;
  }

  public function getTaskType() {
    return $this->taskType;
  }

  public function hasTaskType() {
    return $this->taskType != null;
  }

  public function setTaskType($taskType) {
    $this->taskType = $taskType;
  }

  public function getTaskStatus() {
    return $this->taskStatus;
  }

  public function hasTaskStatus() {
    return $this->taskStatus != null;
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

  public function get_cancelled() {
    return $this->cancelled;
  }

  public function set_cancelled($cancelled) {
    $this->cancelled = $cancelled;
  }
}
