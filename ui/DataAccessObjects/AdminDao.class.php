<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/BaseDao.php";

class AdminDao extends BaseDao
{
    public function __construct()
    {
        $this->client = new Common\Lib\APIHelper(Common\Lib\Settings::get('ui.api_format'));
        $this->siteApi = Common\Lib\Settings::get('site.api');
    }

    public function getSiteAdmins()
    {
        $request = "{$this->siteApi}v0/admins";
        $response = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\User"), $request);
        return $response;
[[
    //! Get User objects of Site/Organisation Administrators
    /*!
      Used to retrieve Users that are either site admins or organisation admins.
      @param int $orgId is the id of the organisation the user is an admin of or null if site admin
      @return Returns a list of User objects or null
    */
    public static function getAdmins($userId = null, $orgId = null)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNullOrWrapStr($userId)
                .",".Lib\PDOWrapper::cleanseNull($orgId);
        $result = Lib\PDOWrapper::call("getAdmin", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $user) {
                $ret[] = Common\Lib\ModelFactory::buildModel("User", $user);
            }
        }
        return $ret;
    }
   
]]
[[update oR MAKE NEW
DROP PROCEDURE IF EXISTS `getAdmin`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getAdmin`(IN `userId` INT, IN `orgId` INT)
BEGIN

  IF userId = null OR userId = '' THEN SET userId = NULL; END IF;
  IF orgId = null OR orgId = '' THEN SET orgId = NULL; END IF;

  IF userId IS NOT null AND orgId IS NOT null THEN
    SELECT u.id,u.`display-name` as display_name,u.email,u.password,u.biography,
        (SELECT `en-name` FROM Languages l WHERE l.id = u.`language_id`) AS `languageName`,
        (SELECT code FROM Languages l WHERE l.id = u.`language_id`) AS `languageCode`,
        (SELECT `en-name` FROM Countries c WHERE c.id = u.`country_id`) AS `countryName`,
        (SELECT code FROM Countries c WHERE c.id = u.`country_id`) AS `countryCode`,
        u.nonce,u.`created-time` as created_time

    FROM Users u JOIN Admins a ON a.user_id = u.id
    WHERE a.user_id = userId AND a.organisation_id = orgId;
  ELSEIF userId IS NOT null AND orgId IS null THEN
    SELECT u.id,u.`display-name` as display_name,u.email,u.password,u.biography,
        (SELECT `en-name` FROM Languages l WHERE l.id = u.`language_id`) AS `languageName`,
        (SELECT code FROM Languages l WHERE l.id = u.`language_id`) AS `languageCode`,
        (SELECT `en-name` FROM Countries c WHERE c.id = u.`country_id`) AS `countryName`,
        (SELECT code FROM Countries c WHERE c.id = u.`country_id`) AS `countryCode`,
        u.nonce,u.`created-time` as created_time

    FROM Users u JOIN Admins a ON a.user_id = u.id
    WHERE a.user_id = userId AND a.organisation_id is null;
  ELSEIF userId IS null AND orgId IS NOT null THEN
    SELECT u.id,u.`display-name` as display_name,u.email,u.password,u.biography,
        (SELECT `en-name` FROM Languages l WHERE l.id = u.`language_id`) AS `languageName`,
        (SELECT code FROM Languages l WHERE l.id = u.`language_id`) AS `languageCode`,
        (SELECT `en-name` FROM Countries c WHERE c.id = u.`country_id`) AS `countryName`,
        (SELECT code FROM Countries c WHERE c.id = u.`country_id`) AS `countryCode`,
        u.nonce,u.`created-time` as created_time

    FROM Users u JOIN Admins a ON a.user_id = u.id
    WHERE a.organisation_id = orgId;
  ELSEIF userId IS null AND orgId IS null THEN
    SELECT u.id,u.`display-name` as display_name,u.email,u.password,u.biography,
        (SELECT `en-name` FROM Languages l WHERE l.id = u.`language_id`) AS `languageName`,
        (SELECT code FROM Languages l WHERE l.id = u.`language_id`) AS `languageCode`,
        (SELECT `en-name` FROM Countries c WHERE c.id = u.`country_id`) AS `countryName`,
        (SELECT code FROM Countries c WHERE c.id = u.`country_id`) AS `countryCode`,
        u.nonce,u.`created-time` as created_time

    FROM Users u JOIN Admins a ON a.user_id = u.id
    WHERE (a.organisation_id is null or a.organisation_id = orgId);
  END IF;
END//
DELIMITER ;
]]
    }
    
    public function getOrgAdmins($orgId)
    {
        $request = "{$this->siteApi}v0/admins/getOrgAdmins/$orgId";
        $response = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\User"), $request);
        return $response;
    }

    public function createSiteAdmin($userId)
    {
        $request = "{$this->siteApi}v0/admins/$userId";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
    }
    
    public function removeSiteAdmin($userId)
    {
        $request = "{$this->siteApi}v0/admins/$userId";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
    }
    
    public function createOrgAdmin($userId, $orgId)
    {
        $request = "{$this->siteApi}v0/admins/createOrgAdmin/$orgId/$userId";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
    }
    
    public function removeOrgAdmin($userId, $orgId)
    {
        $request = "{$this->siteApi}v0/admins/removeOrgAdmin/$orgId/$userId";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
    }
    
    public function getBannedUsers()
    {
        $request = "{$this->siteApi}v0/admins/getBannedUsers";
        $response = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\BannedUser"), $request);
        return $response;
    }
    
    public function getBannedUser($userId)
    {
        $request = "{$this->siteApi}v0/admins/getBannedUser/$userId";
        $response = $this->client->call("\SolasMatch\Common\Protobufs\Models\BannedUser", $request);
        return $response;
    }
    
    public function getBannedOrgs()
    {
        $request = "{$this->siteApi}v0/admins/getBannedOrgs";
        $response = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\BannedOrganisation"), $request);
        return $response;
    }
    
    public function getBannedOrg($orgId)
    {
        $request = "{$this->siteApi}v0/admins/getBannedOrg/$orgId";
        $response = $this->client->call("\SolasMatch\Common\Protobufs\Models\BannedOrganisation", $request);
        return $response;
    }
    
    public function banUser($bannedUser)
    {
        $request = "{$this->siteApi}v0/admins/banUser";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::POST, $bannedUser);
    }
    
    public function banOrg($bannedOrg)
    {
        $request = "{$this->siteApi}v0/admins/banOrg";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::POST, $bannedOrg);
    }
    
    public function unBanUser($userId)
    {
        $request = "{$this->siteApi}v0/admins/unBanUser/$userId";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
    }
    
    public function unBanOrg($orgId)
    {
        $request = "{$this->siteApi}v0/admins/unBanOrg/$orgId";
        $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
    }
    
    public function isUserBanned($userId)
    {
        $request = "{$this->siteApi}v0/admins/isUserBanned/$userId";
        $response = $this->client->call(null, $request);
        return $response;
    }
    
    public function isOrgBanned($orgId)
    {
        $request = "{$this->siteApi}v0/admins/isOrgBanned/$orgId";
        $response = $this->client->call(null, $request);
        return $response;
    }
    
    public function revokeTaskFromUser($taskId, $userId)
    {
        $request = "{$this->siteApi}v0/admins/revokeTask/$taskId/$userId";
        $response = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
        return $response;
    }
}
