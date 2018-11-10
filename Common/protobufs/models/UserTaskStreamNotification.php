<?php
namespace SolasMatch\Common\Protobufs\Models;

class UserTaskStreamNotification
{
  protected $user_id;
  protected $interval;
  protected $last_sent;
  protected $strict;

  public function __construct() {
    $this->user_id = null;
    $this->interval = null;
    $this->last_sent = '';
    $this->strict = false;
  }

  public function getUser_id() {
    return $this->user_id;
  }

  public function setUser_id($user_id) {
    $this->user_id = $user_id;
  }

  public function getInterval() {
    return $this->interval;
  }

  public function setInterval($interval) {
    $this->interval = $interval;
  }

  public function getLast_sent() {
    return $this->last_sent;
  }

  public function setLast_sent($last_sent) {
    $this->last_sent = (string)$last_sent;
  }

  public function getStrict() {
    return $this->strict;
  }

  public function setStrict($strict) {
    $this->strict = (boolean)$strict;
  }

}
