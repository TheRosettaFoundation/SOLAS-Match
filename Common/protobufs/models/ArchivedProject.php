<?php
namespace SolasMatch\Common\Protobufs\Models;

class ArchivedProject
{
  public $id;
  public $title;
  public $description;
  public $impact;
  public $deadline;
  public $organisationId;
  public $reference;
  public $wordCount;
  public $createdTime;
  public $sourceLocale;
  public $userIdArchived;
  public $userIdProjectCreator;
  public $fileName;
  public $fileToken;
  public $mimeType;
  public $archivedDate;
  public $tags;
  public $imageUploaded;
  public $imageApproved;

  public function __construct() {
    $this->id = null;
    $this->title = '';
    $this->description = '';
    $this->impact = '';
    $this->deadline = '';
    $this->organisationId = null;
    $this->reference = '';
    $this->wordCount = null;
    $this->createdTime = '';
    $this->sourceLocale = null;
    $this->userIdArchived = null;
    $this->userIdProjectCreator = null;
    $this->fileName = '';
    $this->fileToken = '';
    $this->mimeType = '';
    $this->archivedDate = '';
    $this->tags = '';
    $this->imageUploaded = false;
    $this->imageApproved = false;
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

  public function getImpact() {
    return $this->impact;
  }

  public function setImpact($impact) {
    $this->impact = (string)$impact;
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

  public function hasOrganisationId() {
    return $this->organisationId != null;
  }

  public function setOrganisationId($organisationId) {
    $this->organisationId = $organisationId;
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

  public function hasWordCount() {
    return $this->wordCount != null;
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

  public function hasSourceLocale() {
    return $this->sourceLocale != null;
  }

  public function setSourceLocale($sourceLocale) {
    $this->sourceLocale = $sourceLocale;
  }

  public function getUserIdArchived() {
    return $this->userIdArchived;
  }

  public function hasUserIdArchived() {
    return $this->userIdArchived != null;
  }

  public function setUserIdArchived($userIdArchived) {
    $this->userIdArchived = $userIdArchived;
  }

  public function getUserIdProjectCreator() {
    return $this->userIdProjectCreator;
  }

  public function hasUserIdProjectCreator() {
    return $this->userIdProjectCreator != null;
  }

  public function setUserIdProjectCreator($userIdProjectCreator) {
    $this->userIdProjectCreator = $userIdProjectCreator;
  }

  public function getFileName() {
    return $this->fileName;
  }

  public function setFileName($fileName) {
    $this->fileName = (string)$fileName;
  }

  public function getFileToken() {
    return $this->fileToken;
  }

  public function setFileToken($fileToken) {
    $this->fileToken = (string)$fileToken;
  }

  public function getMimeType() {
    return $this->mimeType;
  }

  public function setMimeType($mimeType) {
    $this->mimeType = (string)$mimeType;
  }

  public function getArchivedDate() {
    return $this->archivedDate;
  }

  public function setArchivedDate($archivedDate) {
    $this->archivedDate = (string)$archivedDate;
  }

  public function getTags() {
    return $this->tags;
  }

  public function setTags($tags) {
    $this->tags = (string)$tags;
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
