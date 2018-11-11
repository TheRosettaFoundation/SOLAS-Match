<?php
namespace SolasMatch\Common\Protobufs\Models;

class OrganisationExtendedProfile
{
  public $id;
  public $facebook;
  public $linkedin;
  public $primaryContactName;
  public $primaryContactTitle;
  public $primaryContactEmail;
  public $primaryContactPhone;
  public $otherContacts;
  public $structure;
  public $affiliations;
  public $urlVideo1;
  public $urlVideo2;
  public $urlVideo3;
  public $subjectMatters;
  public $activitys;
  public $employees;
  public $fundings;
  public $finds;
  public $translations;
  public $requests;
  public $contents;
  public $pages;
  public $sources;
  public $targets;
  public $oftens;

  public function __construct() {
    $this->id = null;
    $this->facebook = '';
    $this->linkedin = '';
    $this->primaryContactName = '';
    $this->primaryContactTitle = '';
    $this->primaryContactEmail = '';
    $this->primaryContactPhone = '';
    $this->otherContacts = '';
    $this->structure = '';
    $this->affiliations = '';
    $this->urlVideo1 = '';
    $this->urlVideo2 = '';
    $this->urlVideo3 = '';
    $this->subjectMatters = '';
    $this->activitys = '';
    $this->employees = '';
    $this->fundings = '';
    $this->finds = '';
    $this->translations = '';
    $this->requests = '';
    $this->contents = '';
    $this->pages = '';
    $this->sources = '';
    $this->targets = '';
    $this->oftens = '';
  }

  public function getId() {
    return $this->id;
  }

  public function setId($id) {
    $this->id = $id;
  }

  public function getFacebook() {
    return $this->facebook;
  }

  public function setFacebook($facebook) {
    $this->facebook = (string)$facebook;
  }

  public function getLinkedin() {
    return $this->linkedin;
  }

  public function setLinkedin($linkedin) {
    $this->linkedin = (string)$linkedin;
  }

  public function getPrimaryContactName() {
    return $this->primaryContactName;
  }

  public function setPrimaryContactName($primaryContactName) {
    $this->primaryContactName = (string)$primaryContactName;
  }

  public function getPrimaryContactTitle() {
    return $this->primaryContactTitle;
  }

  public function setPrimaryContactTitle($primaryContactTitle) {
    $this->primaryContactTitle = (string)$primaryContactTitle;
  }

  public function getPrimaryContactEmail() {
    return $this->primaryContactEmail;
  }

  public function setPrimaryContactEmail($primaryContactEmail) {
    $this->primaryContactEmail = (string)$primaryContactEmail;
  }

  public function getPrimaryContactPhone() {
    return $this->primaryContactPhone;
  }

  public function setPrimaryContactPhone($primaryContactPhone) {
    $this->primaryContactPhone = (string)$primaryContactPhone;
  }

  public function getOtherContacts() {
    return $this->otherContacts;
  }

  public function setOtherContacts($otherContacts) {
    $this->otherContacts = (string)$otherContacts;
  }

  public function getStructure() {
    return $this->structure;
  }

  public function setStructure($structure) {
    $this->structure = (string)$structure;
  }

  public function getAffiliations() {
    return $this->affiliations;
  }

  public function setAffiliations($affiliations) {
    $this->affiliations = (string)$affiliations;
  }

  public function getUrlVideo1() {
    return $this->urlVideo1;
  }

  public function setUrlVideo1($urlVideo1) {
    $this->urlVideo1 = (string)$urlVideo1;
  }

  public function getUrlVideo2() {
    return $this->urlVideo2;
  }

  public function setUrlVideo2($urlVideo2) {
    $this->urlVideo2 = (string)$urlVideo2;
  }

  public function getUrlVideo3() {
    return $this->urlVideo3;
  }

  public function setUrlVideo3($urlVideo3) {
    $this->urlVideo3 = (string)$urlVideo3;
  }

  public function getSubjectMatters() {
    return $this->subjectMatters;
  }

  public function setSubjectMatters($subjectMatters) {
    $this->subjectMatters = (string)$subjectMatters;
  }

  public function getActivitys() {
    return $this->activitys;
  }

  public function setActivitys($activitys) {
    $this->activitys = (string)$activitys;
  }

  public function getEmployees() {
    return $this->employees;
  }

  public function setEmployees($employees) {
    $this->employees = (string)$employees;
  }

  public function getFundings() {
    return $this->fundings;
  }

  public function setFundings($fundings) {
    $this->fundings = (string)$fundings;
  }

  public function getFinds() {
    return $this->finds;
  }

  public function setFinds($finds) {
    $this->finds = (string)$finds;
  }

  public function getTranslations() {
    return $this->translations;
  }

  public function setTranslations($translations) {
    $this->translations = (string)$translations;
  }

  public function getRequests() {
    return $this->requests;
  }

  public function setRequests($requests) {
    $this->requests = (string)$requests;
  }

  public function getContents() {
    return $this->contents;
  }

  public function setContents($contents) {
    $this->contents = (string)$contents;
  }

  public function getPages() {
    return $this->pages;
  }

  public function setPages($pages) {
    $this->pages = (string)$pages;
  }

  public function getSources() {
    return $this->sources;
  }

  public function setSources($sources) {
    $this->sources = (string)$sources;
  }

  public function getTargets() {
    return $this->targets;
  }

  public function setTargets($targets) {
    $this->targets = (string)$targets;
  }

  public function getOftens() {
    return $this->oftens;
  }

  public function setOftens($oftens) {
    $this->oftens = (string)$oftens;
  }

}
