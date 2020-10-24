<?php
namespace SolasMatch\Common\Protobufs\Models;

class BannedUser
{
  public $userId;
  public $userIdAdmin;
  public $banType;
  public $comment;
  public $bannedDate;

  public function __construct() {
    $this->userId = null;
    $this->userIdAdmin = null;
    $this->banType = null;
    $this->comment = '';
    $this->bannedDate = '';
  }

  public function getUserId() {
    return $this->userId;
  }

  public function hasUserId() {
    return $this->userId != null;
  }

  public function setUserId($userId) {
    $this->userId = $userId;
  }

  public function getUserIdAdmin() {
    return $this->userIdAdmin;
  }

  public function hasUserIdAdmin() {
    return $this->userIdAdmin != null;
  }

  public function setUserIdAdmin($userIdAdmin) {
    $this->userIdAdmin = $userIdAdmin;
  }

  public function getBanType() {
    return $this->banType;
  }

  public function hasBanType() {
    return $this->banType != null;
  }

  public function setBanType($banType) {
    $this->banType = $banType;
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
