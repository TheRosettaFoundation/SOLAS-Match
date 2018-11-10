<?php
namespace SolasMatch\Common\Protobufs\Models;

class OAuthResponse
{
  protected $token;
  protected $token_type;
  protected $expires;
  protected $expires_in;

  public function __construct() {
    $this->token = '';
    $this->token_type = '';
    $this->expires = '';
    $this->expires_in = '';
  }

  public function getToken() {
    return $this->token;
  }

  public function setToken($token) {
    $this->token = (string)$token;
  }

  public function getToken_type() {
    return $this->token_type;
  }

  public function setToken_type($token_type) {
    $this->token_type = (string)$token_type;
  }

  public function getExpires() {
    return $this->expires;
  }

  public function setExpires($expires) {
    $this->expires = (string)$expires;
  }

  public function getExpires_in() {
    return $this->expires_in;
  }

  public function setExpires_in($expires_in) {
    $this->expires_in = (string)$expires_in;
  }

}
