<?php

//! The Administrator Data Access Object
/*!
  A class for retrieving Admin related data from the database.
  Used by the API Route Handlers to supply info requested through the API.
*/

include_once __DIR__."/OrganisationDao.class.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";

class AdminDao
{
    //! Get User objects of Site/Organisation Administrators
    /*!
      Used to retrieve Users that are either site admins or organisation admins.
      @param int $orgId is the id of the organisation the user is an admin of or null if site admin
      @return Returns a list of User objects or null
    */
    public static function getAdmins($orgId = null)
    {
        $ret = null;
        $args= PDOWrapper::cleanseNullOrWrapStr($orgId);
        if ($result = PDOWrapper::call("getAdmin", $args)) {
            $ret = array();
            foreach ($result as $user) {
                $ret[] = ModelFactory::buildModel("User", $user);
            }
        }
        return $ret;
    }
    
    //! Used to add a site administrator
    /*!
      Add a site administrator. Can only be called by existing site admins.
      @param int $userId is the id of the User that is being added as a site admin
      @return Returns 1 on success, 0 on failure
    */
    public static function addSiteAdmin($userId)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNullOrWrapStr(null);
        
        if ($result = PDOWrapper::call("addAdmin", $args)) {
            $ret = $result[0]['result'];
        }
        
        return $ret;
    }
    
    //! Remove a User from the site admin list
    /*!
      Remove a user from the site admin list.
      @param int $userId is the id of the User being removed from the site admin list
      @return Returns 1 on succes, 0 on failure
    */
    public static function removeAdmin($userId)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNullOrWrapStr(null);
        
        if ($result = PDOWrapper::call("removeAdmin", $args)) {
            $ret = $result[0]['result'];
        }
        
        return $ret;
    }
    
    //! Add a User as an administrator to an organisation
    /*!
      Adds a User to the Organisation Administrator list. This is called when an organisation os created
      to add the user that created it as an administrator. Can only be called by site admins and administrators
      of the organisation defined by the params
      @param int $userId is the id of the iser being added to the organisation administrator list
      @param int $orgId is the id of an organisation
      @return Returns 1 on success, 0 on failure
    */
    public static function addOrgAdmin($userId, $orgId)
    {
        $ret = null;
        OrganisationDao::acceptMemRequest($orgId, $userId);
        $args = PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNull($orgId);
        
        if ($result = PDOWrapper::call("addAdmin", $args)) {
            $ret = $result[0]['result'];
        }
        
        return $ret;
    }
    
    //! Remove a user from the Organisation Administrators List
    /*!
      Removes a User from the Organisation administrator list. Can only be called by Site Admins or
      administrators of the Organisation
      @param int $userId is the ID of the user being removed
      @param int $orgId is the of the Organisation being affected
      @return Returns 1 on success, 0 on failure
    */
    public static function removeOrgAdmin($userId, $orgId)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNull($orgId);
        
        if ($result = PDOWrapper::call("removeAdmin", $args)) {
            $ret = $result[0]['result'];
        }
        
        return $ret;
    }
    
    //! Bans a User to stop them from logging in
    /*!
      Ban a User to stop them from logging in. Users can only be banned by site admins. Bans can last
      for different time periods which is selected by the Site Admin while banning
      @param BannedUser $bannedUser stores the details of the ban
      @return No return
    */
    public static function saveBannedUser($bannedUser)
    {
        self::banUser(
            $bannedUser->getUserId(),
            $bannedUser->getUserIdAdmin(),
            $bannedUser->getBanType(),
            $bannedUser->getComment()
        );
    }
    
    private static function banUser($userId, $userIdAdmin, $bannedTypeId, $adminComment = null)
    {
        $args = PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNull($userIdAdmin)
                .",".PDOWrapper::cleanseNull($bannedTypeId)
                .",".PDOWrapper::cleanseNullOrWrapStr($adminComment);
        
        PDOWrapper::call("bannedUserInsert", $args);
    }
    
    //! Remove a ban on a User
    /*
      Remove a user from the banned list, thereby allowing them to log in again.
      @param int $userId is the id of the User being removed from the banned list
      @return No return
    */
    public static function unBanUser($userId)
    {
        $args = PDOWrapper::cleanseNull($userId);
        
        PDOWrapper::call("removeBannedUser", $args);
    }
    
    //! Get information on a Banned User
    /*!
      Get the Banned Data for users that are banned from logging into the site. Can be used to select Ban data
      on a number of different parameters. If any of the parameters are null then it will be ignored.
      @param int $userId the ID of the banned User or null for all users
      @param int $userIdAdmin is the ID of the User that did the banning or null for any
      @param BanTypeEnum $bannedTypeId is the enum value representing the length of the requested ban
      @param string $adminComment the comment provided by the Site Admin when sending the ban
      @param DateTime $bannedDate is the date and time of the requested ban
      @return Returns a list of BannedUser objects or null if none found with the specified criteria
    */
    public static function
    getBannedUser($userId = null, $userIdAdmin = null, $bannedTypeId = null, $adminComment = null, $bannedDate = null)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNull($userIdAdmin)
                .",".PDOWrapper::cleanseNull($bannedTypeId)
                .",".PDOWrapper::cleanseNullOrWrapStr($adminComment)
                .",".PDOWrapper::cleanseNullOrWrapStr($bannedDate);
        
        if ($result = PDOWrapper::call("getBannedUser", $args)) {
            $ret = array();
            foreach ($result as $bannedUser) {
                $ret[] = ModelFactory::buildModel("BannedUser", $bannedUser);
            }
        }
        
        return $ret;
    }
    
    //! Ban an organisation
    /*!
      Used to ban an organisation thereby preventing them from creating new projects.
      @param BannedOrganisation $bannedOrg stores the details of the organisation ban.
      @return No return.
    */
    public static function saveBannedOrg($bannedOrg)
    {
        self::banOrg(
            $bannedOrg->getOrgId(),
            $bannedOrg->getUserIdAdmin(),
            $bannedOrg->getBanType(),
            $bannedOrg->getComment()
        );
    }
    
    private static function banOrg($orgId, $userIdAdmin, $bannedTypeId, $adminComment = null)
    {
        $args = PDOWrapper::cleanseNull($orgId)
                .",".PDOWrapper::cleanseNull($userIdAdmin)
                .",".PDOWrapper::cleanseNull($bannedTypeId)
                .",".PDOWrapper::cleanseNullOrWrapStr($adminComment);
        
        PDOWrapper::call("bannedOrgInsert", $args);
    }
    
    //! Remove a ban on an organisation.
    /*!
      Remove a ban placed on an organisation.
      @param int $orgId is the id of the organisation that is being reinstated
      @return No return
    */
    public static function unBanOrg($orgId)
    {
        $args = PDOWrapper::cleanseNull($orgId);
        
        PDOWrapper::call("removeBannedOrg", $args);
    }
    
    //! Get information on Banned Organisations
    /*!
      Get information on Banned organisations. Each parameter is a filter that can be used to select the banned info
      returned by the function. For example if an orgId is passed then only info for that organisation will be
      returned. If any of the parameters are passed as null they will be ignored. If every parameter is null then
      all banned organisation data will be returned.
      @param int $orgId is the id of the organisation being requested
      @param int userIdAdmin the id of the Site Admin that banned the organisation
      @param BanTypeEnum $bannedTypeId is an enum value representing the length of the ban
      @param string $adminComment the comment the Site Admin made when applying the ban
      @param DateTime $bannedDate is the date and time the ban was applied
      @return Returns a list of BannedOrganisation or null
    */
    public static function
    getBannedOrg($orgId = null, $userIdAdmin = null, $bannedTypeId = null, $adminComment = null, $bannedDate = null)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($orgId)
                .",".PDOWrapper::cleanseNull($userIdAdmin)
                .",".PDOWrapper::cleanseNull($bannedTypeId)
                .",".PDOWrapper::cleanseNullOrWrapStr($adminComment)
                .",".PDOWrapper::cleanseNullOrWrapStr($bannedDate);
        
        if ($result = PDOWrapper::call("getBannedOrg", $args)) {
            $ret = array();
            foreach ($result as $bannedOrg) {
                $ret[] = ModelFactory::buildModel("BannedOrganisation", $bannedOrg);
            }
        }
        
        return $ret;
    }
    
    //! Determine if a User is currently banned
    /*!
      Determines if a User is currently banned. If the user was banned but the ban has expired then the ban data
      is removed from the Database
      @param int $userId is the id of the User
      @return Returs 1 if the user is banned, otherwise it returns 0
    */
    public static function isUserBanned($userId)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($userId);
        
        if ($result = PDOWrapper::call("isUserBanned", $args)) {
            $ret = $result[0]['result'];
        }
        
        return $ret;
    }
    
    //! Determines if an Organisation is currently banned from creating projects
    /*!
      Determines if an Organisation is currently banned from creating projects.
      @param int $orgId is the id of the Organisation being checked
      @return Returns 1 if the organisation is banned or 0 if it is not
    */
    public static function isOrgBanned($orgId)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($orgId);
        
        if ($result = PDOWrapper::call("isOrgBanned", $args)) {
            $ret = $result[0]['result'];
        }
        
        return $ret;
    }
    
    //! Determins if a user is a Site/Organisation Admin
    /*!
      If a valid organisation id is passed then this function determines if a user is on the organisation's
      administrator list. If null is passed for the organisation id this function determines if the user is a
      site administrator
      @param int $userId is the id of the User being checked
      @param int $orgId is the id of the organisation being checked or null for site admin checks
      @return Return 1 if the user is an admin, returns false otherwise
    */
    public static function isAdmin($userId, $orgId)
    {
        $ret = false;
        $args = PDOWrapper::cleanse($userId).",".PDOWrapper::cleanseNullOrWrapStr($orgId);
        if ($result = PDOWrapper::call("isAdmin", $args)) {
            $ret = $result[0]['result'];
        }
        return $ret;
    }
}
