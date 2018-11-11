<?php
namespace SolasMatch\Common\Protobufs\Models;

class BannedOrganisation
{
  public $orgId;
  public $userIdAdmin;
  public $banType;
  public $comment;
  public $bannedDate;

  public function __construct() {
    $this->orgId = null;
    $this->userIdAdmin = null;
    $this->banType = '';
    $this->comment = '';
    $this->bannedDate = '';
  }

  public function getOrgId() {
    return $this->orgId;
  }

  public function setOrgId($orgId) {
    $this->orgId = $orgId;
  }

  public function getUserIdAdmin() {
    return $this->userIdAdmin;
  }

  public function setUserIdAdmin($userIdAdmin) {
    $this->userIdAdmin = $userIdAdmin;
  }

  public function getBanType() {
    return $this->banType;
  }

  public function setBanType($banType) {
    $this->banType = (string)$banType;
  }

  public function getComment() {
    return $this->comment;
  }

  public function setComment($comment) {
    $this->comment = (string)$comment;
  }

  public function getBannedDate() {
    return $this->bannedDate;
  }

  public function setBannedDate($bannedDate) {
    $this->bannedDate = (string)$bannedDate;
  }

}
