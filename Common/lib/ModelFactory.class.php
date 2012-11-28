<?php

require_once __DIR__."/../models/MembershipRequest.php";

class ModelFactory
{
    public static function BuildModel($modelName, $modelData)
    {
        $ret = null;

        switch($modelName)
        {
            case "MembershipRequest":
                $ret = ModelFactory::GenerateMembershipRequest($modelData);
                break;
            default:
                echo "Unable to build model $modelName";
        }

        return $ret;
    }

    private static function GenerateMembershipRequest($modelData)
    {
        $ret = new MembershipRequest();
        $ret ->setId($modelData["request_id"]);
        if(isset($modelData['user_id'])) {
            $ret->setUserId($modelData['user_id']);
        }
        if(isset($modelData['org_id'])) {
            $ret->setOrgId($modelData['org_id']);
        }
        if(isset($modelData['request_datetime'])) {
            $ret->setRequestTime($modelData['request_datetime']);
        }
        return $ret;
    }
}
