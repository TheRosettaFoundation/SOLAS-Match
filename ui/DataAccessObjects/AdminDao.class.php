<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\API\Lib as LibAPI;
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
        $result = LibAPI\PDOWrapper::call('getAdmins', '0,' . LINGUIST);
        if (empty($result)) return [];
        return $result;
    }

    public function insert_special_registration($role, $email, $org_id, $admin_id)
    {       
        $args = LibAPI\PDOWrapper::cleanse($role) . ',' .
                LibAPI\PDOWrapper::cleanseWrapStr($email) . ',' .          
                LibAPI\PDOWrapper::cleanse($org_id) . ',' .
                LibAPI\PDOWrapper::cleanse($admin_id);
        $result = LibAPI\PDOWrapper::call('insert_special_registration' , $args);
        LibAPI\PDOWrapper::call('insert_queue_request', '3,37,0,' . LibAPI\PDOWrapper::cleanse($result[0]['id']) . ",0,0,0,0,''");
        return $result;         
    }
    
    public function getOrgMembers($orgId)
    {
        $result = LibAPI\PDOWrapper::call('getAdmins', LibAPI\PDOWrapper::cleanse($orgId) . ',0');
        if (empty($result)) return [];
        return $result;
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

    public function get_roles($user_id, $org_id = 0)
    {
        $result = LibAPI\PDOWrapper::call('get_roles', LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanse($org_id));
        if (empty($result)) return 0;
        return $result[0]['roles'];
    }

    public function isSiteAdmin_any_or_org_admin_any_for_any_org($user_id)
    {
        $result = LibAPI\PDOWrapper::call('isSiteAdmin_any_or_org_admin_any_for_any_org', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) return 0;
        return $result[0]['roles'];
    }

    public function isSiteAdmin_any_or_org_admin_any_or_linguist_for_any_org($user_id)
    {
        $result = LibAPI\PDOWrapper::call('isSiteAdmin_any_or_org_admin_any_or_linguist_for_any_org', LibAPI\PDOWrapper::cleanse($user_id));
        if (empty($result)) return 0;
        return $result[0]['roles'];
    }

    public function adjust_org_admin($user_id, $org_id, $remove, $add)
    {
error_log("adjust_org_admin($user_id, $org_id, $remove, $add)");
        LibAPI\PDOWrapper::call('adjust_org_admin', LibAPI\PDOWrapper::cleanse($user_id) . ',' .  LibAPI\PDOWrapper::cleanse($org_id) . ',' . LibAPI\PDOWrapper::cleanse($remove) . ',' . LibAPI\PDOWrapper::cleanse($add));
    }

    public function current_user_is_NGO_admin_or_PO_for_special_registration_email($user_id, $email)
    {
        return LibAPI\PDOWrapper::call('current_user_is_NGO_admin_or_PO_for_special_registration_email', LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($email));
    }

    public function get_special_registration()
    {
        $result = LibAPI\PDOWrapper::call('get_special_registration', LibAPI\PDOWrapper::cleanseWrapStr($_SESSION['reg_data']) . ',' . LibAPI\PDOWrapper::cleanseWrapStr(Common\Lib\Settings::get('site.reg_key')) . ",0,''");
      
        if (empty($result)) {
            error_log("Bad reg_data: {$_SESSION['reg_data']}");
            unset($_SESSION['reg_data']);
            return "Bad reg_data: {$_SESSION['reg_data']}.";
        }

        if (empty($result)) $error = 'This link is invalid.';
        else {
            $special_registration = $result[0];

            $error = null;
            if     ($special_registration['used'])    $error = 'This link has already been used to register, you cannot register.';
            elseif ($special_registration['expired']) $error = 'This link has expired, you cannot register.';
        }
        if ($error) {
            unset($_SESSION['reg_data']);
            return ['', $error];
        }
        return [$special_registration['email'], null];
    }

    public function get_special_registration_records($org_id)
    {       
        $result = LibAPI\PDOWrapper::call('get_special_registration_records', LibAPI\PDOWrapper::cleanse($org_id) . ',' . LibAPI\PDOWrapper::cleanseWrapStr(Common\Lib\Settings::get('site.reg_key')));
        if (empty($result)) return [];
        return $result;
    }

    public function copy_roles_from_special_registration($user_id, $email)
    {
error_log("copy_roles_from_special_registration($user_id, $email)");
        if (empty($_SESSION['reg_data'])) {
            $this->adjust_org_admin($user_id, 0, 0, LINGUIST);
            return 0;
        }
        $result = LibAPI\PDOWrapper::call('get_special_registration', LibAPI\PDOWrapper::cleanseWrapStr($_SESSION['reg_data']) . ',' . LibAPI\PDOWrapper::cleanseWrapStr(Common\Lib\Settings::get('site.reg_key')) . ',' . LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($email));
        if (empty($result)) {
            error_log("Bad reg_data: {$_SESSION['reg_data']}");
            unset($_SESSION['reg_data']);
            return "Bad reg_data: {$_SESSION['reg_data']}.";
        }
        unset($_SESSION['reg_data']);

        $special_registration = $result[0];
        if ($special_registration['mismatch']) {
            $this->adjust_org_admin($user_id, 0, 0, LINGUIST);
            error_log("special_registration[mismatch] on: $email (not {$special_registration['email']}), making $user_id LINGUIST");
            return "Expected special registration email: {$special_registration['email']}, got $email from Google, can't give special role, gave TWB linguist.";
        }

        if ($special_registration['roles'] != (NGO_LINGUIST + LINGUIST)) $this->adjust_org_admin($user_id, $special_registration['org_id'], 0, $special_registration['roles']);
        else {
            $this->adjust_org_admin($user_id, $special_registration['org_id'], 0, NGO_LINGUIST);
            $this->adjust_org_admin($user_id,                               0, 0, LINGUIST);
        }
        return 0;
    }
}
