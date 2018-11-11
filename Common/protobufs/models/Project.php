<?php
namespace SolasMatch\Common\Protobufs\Models;

class Project
{
  public $id;
  public $title;
  public $description;
  public $deadline;
  public $organisationId;
  public $impact;
  public $reference;
  public $wordCount;
  public $createdTime;
  public $status;
  public $sourceLocale;
  public $tag;
  public $imageUploaded;
  public $imageApproved;

  public function __construct() {
    $this->id = null;
    $this->title = '';
    $this->description = '';
    $this->deadline = '';
    $this->organisationId = null;
    $this->impact = '';
    $this->reference = '';
    $this->wordCount = null;
    $this->createdTime = '';
    $this->status = '';
    $this->sourceLocale = null;
    $this->tag = array();
    $this->imageUploaded = false;
    $this->imageApproved = false;
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

  public function getDeadline() {
    return $this->deadline;
  }

  public function setDeadline($deadline) {
    $this->deadline = (string)$deadline;
  }

  public function getOrganisationId() {
    return $this->organisationId;
  }

  public function setOrganisationId($organisationId) {
    $this->organisationId = $organisationId;
  }

  public function getImpact() {
    return $this->impact;
  }

  public function setImpact($impact) {
    $this->impact = (string)$impact;
  }

  public function getReference() {
    return $this->reference;
  }

  public function setReference($reference) {
    $this->reference = (string)$reference;
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

  public function getStatus() {
    return $this->status;
  }

  public function setStatus($status) {
    $this->status = (string)$status;
  }

  public function getSourceLocale() {
    return $this->sourceLocale;
  }

  public function setSourceLocale($sourceLocale) {
    $this->sourceLocale = $sourceLocale;
  }

  public function getTag() {
    return $this->tag;
  }

  public function hasTag() {
    return count($this->tag) > 0;
  }

  public function setTag($tag, $index) {
    $this->tag[$index] = $tag;
  }

  public function clearTag() {
    $this->tag = array();
  }

  public function addTag($tag) {
    $this->tag[] = $tag;
  }

  public function appendTag($tag) {
    $this->tag[] = $tag;
  }

  public function getImageUploaded() {
    return $this->imageUploaded;
  }

  public function setImageUploaded($imageUploaded) {
    $this->imageUploaded = (boolean)$imageUploaded;
  }

  public function getImageApproved() {
    return $this->imageApproved;
  }

  public function setImageApproved($imageApproved) {
    $this->imageApproved = (boolean)$imageApproved;
  }

}
