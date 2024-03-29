<?php

namespace SolasMatch\API\DAO;

use \SolasMatch\API\Lib as Lib;
use \SolasMatch\Common as Common;

//! The Administrator Data Access Object
/*!
  A class for retrieving Admin related data from the database.
  Used by the API Route Handlers to supply info requested through the API.
*/

require_once __DIR__."/OrganisationDao.class.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";

class AdminDao
{
    public static function add_org_TWB_contact($org_id, $user_id)
    {
        Lib\PDOWrapper::call('add_org_TWB_contact', Lib\PDOWrapper::cleanse($org_id) . ',' . Lib\PDOWrapper::cleanse($user_id));
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
        $args = Lib\PDOWrapper::cleanseNull($userId)
                .",".Lib\PDOWrapper::cleanseNull($userIdAdmin)
                .",".Lib\PDOWrapper::cleanseNull($bannedTypeId)
                .",".Lib\PDOWrapper::cleanseNullOrWrapStr($adminComment);
        
        Lib\PDOWrapper::call("bannedUserInsert", $args);
    }
    
    //! Remove a ban on a User
    /*
      Remove a user from the banned list, thereby allowing them to log in again.
      @param int $userId is the id of the User being removed from the banned list
      @return No return
    */
    public static function unBanUser($userId)
    {
        $args = Lib\PDOWrapper::cleanseNull($userId);
        
        Lib\PDOWrapper::call("removeBannedUser", $args);
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
    public static function getBannedUser(
        $userId = null,
        $userIdAdmin = null,
        $bannedTypeId = null,
        $adminComment = null,
        $bannedDate = null
    ) {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($userId).",".
            Lib\PDOWrapper::cleanseNull($userIdAdmin).",".
            Lib\PDOWrapper::cleanseNull($bannedTypeId).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($adminComment).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($bannedDate);
        $result = Lib\PDOWrapper::call("getBannedUser", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $bannedUser) {
                $ret[] = Common\Lib\ModelFactory::buildModel("BannedUser", $bannedUser);
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
        $args = Lib\PDOWrapper::cleanseNull($orgId).",".
            Lib\PDOWrapper::cleanseNull($userIdAdmin).",".
            Lib\PDOWrapper::cleanseNull($bannedTypeId).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($adminComment);
        
        Lib\PDOWrapper::call("bannedOrgInsert", $args);
    }
    
    //! Remove a ban on an organisation.
    /*!
      Remove a ban placed on an organisation.
      @param int $orgId is the id of the organisation that is being reinstated
      @return No return
    */
    public static function unBanOrg($orgId)
    {
        $args = Lib\PDOWrapper::cleanseNull($orgId);
        Lib\PDOWrapper::call("removeBannedOrg", $args);
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
        $args = Lib\PDOWrapper::cleanseNull($orgId).",".
            Lib\PDOWrapper::cleanseNull($userIdAdmin).",".
            Lib\PDOWrapper::cleanseNull($bannedTypeId).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($adminComment).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($bannedDate);
        $result = Lib\PDOWrapper::call("getBannedOrg", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $bannedOrg) {
                $ret[] = Common\Lib\ModelFactory::buildModel("BannedOrganisation", $bannedOrg);
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
        $args = Lib\PDOWrapper::cleanseNull($userId);
        $result = Lib\PDOWrapper::call("isUserBanned", $args);
        if ($result) {
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
        $args = Lib\PDOWrapper::cleanseNull($orgId);
        $result = Lib\PDOWrapper::call("isOrgBanned", $args);
        if ($result) {
            $ret = $result[0]['result'];
        }
        
        return $ret;
    }

    public static function get_roles($user_id, $org_id = 0)
    {
        $result = Lib\PDOWrapper::call('get_roles', Lib\PDOWrapper::cleanse($user_id) . ',' . Lib\PDOWrapper::cleanse($org_id));
        if (empty($result)) return 0;
        return $result[0]['roles'];
    }
}
