<?php
class MembershipRequest
{
    public $requestId;
    public $userId;
    public $orgId;
    public $requestTime;

    public function __construct($params = array()) {
        if(isset($params['request_id'])) {
            $this->requestId = $params['request_id'];
        }
        if(isset($params['user_id'])) {
            $this->userId = $params['user_id'];
        }
        if(isset($params['org_id'])) {
            $this->orgId = $params['org_id'];
        }
        if(isset($params['request_datetime'])) {
            $this->requestTime = $params['request_datetime'];
        }
    }
    public function getRequestId() {
        return $this->requestId;
    }

    public function setRequestId($requestId) {
        $this->requestId = $requestId;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function getOrgId() {
        return $this->orgId;
    }

    public function setOrgId($orgId) {
        $this->orgId = $orgId;
    }

    public function getRequestTime() {
        return $this->requestTime;
    }

    public function setRequestTime($requestTime) {
        $this->requestTime = $requestTime;
    }


}

?>
