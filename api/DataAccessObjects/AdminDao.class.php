<?php

include_once __DIR__."/OrganisationDao.class.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";

class AdminDao
{ 
    
    public static function getAdmins($orgId=null)
    {
        $ret = null;
        $args= PDOWrapper::cleanseNullOrWrapStr($orgId);
        if ($result = PDOWrapper::call("getAdmin", $args)) {
            $ret = array();
            foreach($result as $user) {
                $ret[] = ModelFactory::buildModel("User", $user);
            }
        }
        return $ret;
    }
    
    public static function addSiteAdmin($userId)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNullOrWrapStr(null);
        
        if($result = PDOWrapper::call("addAdmin", $args)) {
            $ret = $result[0]['result'];
        }
        
        return $ret;
    }
    
    public static function removeAdmin($userId)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNullOrWrapStr(null);
        
        if($result = PDOWrapper::call("removeAdmin", $args)) {
            $ret = $result[0]['result'];
        }
        
        return $ret;
    }
    
    public static function addOrgAdmin($userId, $orgId)
    {
        $ret = null;
        OrganisationDao::acceptMemRequest($orgId, $userId);
        $args = PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNull($orgId);
        
        if($result = PDOWrapper::call("addAdmin", $args)) {
            $ret = $result[0]['result'];
        }
        
        return $ret;
    }
    
    public static function removeOrgAdmin($userId, $orgId)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNull($orgId);
        
        if($result = PDOWrapper::call("removeAdmin", $args)) {
            $ret = $result[0]['result'];
        }
        
        return $ret;
    }
    
    public static function saveBannedUser($bannedUser)
    {
        self::banUser($bannedUser->getUserId(), $bannedUser->getUserIdAdmin(), $bannedUser->getBanType(), $bannedUser->getComment());
    }
    
    
    private static function banUser($userId, $userIdAdmin, $bannedTypeId, $adminComment=null)
    {
        $args = PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNull($userIdAdmin)
                .",".PDOWrapper::cleanseNull($bannedTypeId)
                .",".PDOWrapper::cleanseNullOrWrapStr($adminComment);
        
        PDOWrapper::call("bannedUserInsert", $args);        
    }
    
    public static function unBanUser($userId)
    {
        $args = PDOWrapper::cleanseNull($userId);
        
        PDOWrapper::call("removeBannedUser", $args);       
    }
    
    public static function getBannedUser($userId=null, $userIdAdmin=null, $bannedTypeId=null, $adminComment=null, $bannedDate=null)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNull($userIdAdmin)
                .",".PDOWrapper::cleanseNull($bannedTypeId)
                .",".PDOWrapper::cleanseNullOrWrapStr($adminComment)
                .",".PDOWrapper::cleanseNullOrWrapStr($bannedDate);
        
        if($result = PDOWrapper::call("getBannedUser", $args)) {
            $ret = array();
            foreach($result as $bannedUser) {
                $ret[] = ModelFactory::buildModel("BannedUser", $bannedUser);
            }
        }
        
        return $ret;
    }
    
    public static function saveBannedOrg($bannedOrg)
    {
        self::banOrg($bannedOrg->getOrgId(), $bannedOrg->getUserIdAdmin(), $bannedOrg->getBanType(), $bannedOrg->getComment());
    }
    
    private static function banOrg($orgId, $userIdAdmin, $bannedTypeId, $adminComment=null)
    {
        $args = PDOWrapper::cleanseNull($orgId)
                .",".PDOWrapper::cleanseNull($userIdAdmin)
                .",".PDOWrapper::cleanseNull($bannedTypeId)
                .",".PDOWrapper::cleanseNullOrWrapStr($adminComment);
        
        PDOWrapper::call("bannedOrgInsert", $args);      
    }
    
    public static function unBanOrg($orgId)
    {
        $args = PDOWrapper::cleanseNull($orgId);
        
        PDOWrapper::call("removeBannedOrg", $args);      
    }
    
    public static function getBannedOrg($orgId=null, $userIdAdmin=null, $bannedTypeId=null, $adminComment=null, $bannedDate=null)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($orgId)
                .",".PDOWrapper::cleanseNull($userIdAdmin)
                .",".PDOWrapper::cleanseNull($bannedTypeId)
                .",".PDOWrapper::cleanseNullOrWrapStr($adminComment)
                .",".PDOWrapper::cleanseNullOrWrapStr($bannedDate);
        
        if($result = PDOWrapper::call("getBannedOrg", $args)) {
            $ret = array();
            foreach($result as $bannedOrg) {
                $ret[] = ModelFactory::buildModel("BannedOrganisation", $bannedOrg);
            }
        }
        
        return $ret;
    }
    
    public static function isUserBanned($userId)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($userId);
        
        if($result = PDOWrapper::call("isUserBanned", $args)) {
            $ret = $result[0]['result'];
        }
        
        return $ret;
    }
    
    public static function isOrgBanned($orgId)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($orgId);
        
        if($result = PDOWrapper::call("isOrgBanned", $args)) {
            $ret = $result[0]['result'];
        }
        
        return $ret;
    }
    
    public static function isAdmin($userId, $orgId)
    {
        $ret = false;
        $args = PDOWrapper::cleanse($userId)
                .",".PDOWrapper::cleanseNullOrWrapStr($orgId);
        if ($result = PDOWrapper::call("isAdmin", $args)) {
            $ret = $result[0]['result'];
        }
        return $ret;
    }
}