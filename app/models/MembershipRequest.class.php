<?php
class MembershipRequest
{
    private $requestId;
    private $userId;
    private $orgId;
    private $requestTime;

    public function __construct($params = array()) {
        if(isset($params['requestId'])) {
            $this->$requestId = $params['requestId'];
        }
        if(isset($params['userId'])) {
            $this->$userId = $params['userId'];
        }
        if(isset($params['orgId'])) {
            $this->$orgId = $params['orgId'];
        }
        if(isset($params['requestTime'])) {
            $this->$requestTime = $params['requestTime'];
        }
    }
}

?>
