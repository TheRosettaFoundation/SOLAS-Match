<?php
namespace SolasMatch\Common\Protobufs\Models;

class UserTaskStreamNotification
{
  public $user_id;
  public $interval;
  public $last_sent;
  public $strict;

  public function __construct() {
    $this->user_id = null;
    $this->interval = null;
    $this->last_sent = '';
    $this->strict = false;
  }

  public function getUserId() {
    return $this->user_id;
  }

  public function hasUserId() {
    return $this->user_id != null;
  }

  public function setUserId($user_id) {
    $this->user_id = $user_id;
  }

  public function getInterval() {
    return $this->interval;
  }

  public function hasInterval() {
    return $this->interval != null;
  }

  public function setInterval($interval) {
    $this->interval = $interval;
  }

  public function getLastSent() {
    return $this->last_sent;
  }

  public function setLastSent($last_sent) {
    $this->last_sent = (string)$last_sent;
  }

  public function getStrict() {
    return $this->strict;
  }

  public function setStrict($strict) {
    $this->strict = (boolean)$strict;
  }

}
