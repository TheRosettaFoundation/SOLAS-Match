<?php

namespace SolasMatch\API\DAO;

use \SolasMatch\API\Lib as Lib;
use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/protobufs/models/User.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";
require_once __DIR__."/../../Common/lib/Authentication.class.php";
require_once __DIR__ . '/../../Common/lib/MoodleRest.php';

class UserDao
{
    public static function getLoggedInUser()
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) return null;
        $parts = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);
        if (empty($parts[1])) return null;
        $token = $parts[1];
        $key = hex2bin($token);
        $iv = substr($key, -16);
        $encrypted = substr($key, 0, -18);
        $user_id_time = openssl_decrypt($encrypted, 'aes-256-cbc', base64_decode(Common\Lib\Settings::get('badge.key')), 0, $iv);
        $user_id_time = explode(';', $user_id_time);
        if (empty($user_id_time[1])) return null;
        $user_id = $user_id_time[0];
        if (time() > $user_id_time[1] + 24*60*60) return null;
        return self::getUser($user_id);
    }
    
    public static function save($user)
    {
        $userId = $user->getId();
        $nativeLanguageCode = null;
        $nativeCountryCode = null;
        if (!is_null($userId) && !is_null($user->getNativeLocale())) {
            $nativeLocale = $user->getNativeLocale();
            $nativeLanguageCode = $nativeLocale->getLanguageCode();
            $nativeCountryCode = $nativeLocale->getCountryCode();
            if ($nativeLanguageCode != '' && $nativeCountryCode != '') {
                BadgeDao::assignBadge($user->getId(), Common\Enums\BadgeTypes::NATIVE_LANGUAGE);
            }
        }

        if (!is_null($userId) && $user->getBiography() != '') {
            BadgeDao::assignBadge($user->getId(), Common\Enums\BadgeTypes::PROFILE_FILLER);
        }

        $args = Lib\PDOWrapper::cleanseNullOrWrapStr($user->getEmail()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($user->getNonce()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($user->getPassword()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($user->getBiography()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($user->getDisplayName()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($nativeLanguageCode).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($nativeCountryCode).",".
            Lib\PDOWrapper::cleanseNull($userId);
        $result = Lib\PDOWrapper::call('userInsertAndUpdate', $args);

        if (!is_null($result)) {
            return Common\Lib\ModelFactory::buildModel("User", $result[0]);
        } else {
            return null;
        }
    }

    public static function finishRegistration($userId)
    {
        $args = Lib\PDOWrapper::cleanseNull($userId);
        $response = Lib\PDOWrapper::call('finishRegistration', $args);
        Lib\PDOWrapper::call('userTaskStreamNotificationInsertAndUpdate', Lib\PDOWrapper::cleanse($userId) . ',2,1');
        return $response[0]['result'];
    }

    public static function getRegisteredUser($uuid)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNullOrWrapStr($uuid);
        $result = Lib\PDOWrapper::call('getRegisteredUser', $args);
        if ($result) {
            $ret = Common\Lib\ModelFactory::buildModel("User", $result[0]);
        }
        return $ret;
    }

    public static function isUserVerified($userId)
    {
        $ret = '0';
        $args = Lib\PDOWrapper::cleanseNull($userId);
        $result = Lib\PDOWrapper::call('isUserVerified', $args);
        if ($result) {
            $ret = $result[0]['result'];
        }
        return $ret;
    }

    public static function logout()
    {
        UserSession::destroySession();
    }

    public static function getCurrentUser()
    {
        $ret = null;
        if ($user_id = UserSession::getCurrentUserId()) {
                $ret = self::getUser($user_id);
        }
        return $ret;
    }

    public static function isLoggedIn()
    {
        return (!is_null(UserSession::getCurrentUserId()));
    }

    public static function belongsToRole($user, $role)
    {
        $ret = false;
        if ($role == 'translator') {
            $ret = true;
        } elseif ($role == 'organisation_member') {
            $user_found = $this->find(array(
                    'user_id' => $user->getUserId(),
                    'role' => 'organisation_member'
            ));
            if (is_object($user_found)) {
                $ret = true;
            }
        }
        return $ret;
    }

    public static function getUserTaskStreamNotification($userId)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanse($userId);
        $result = Lib\PDOWrapper::call("getUserTaskStreamNotification", $args);
        if ($result) {
            $ret = Common\Lib\ModelFactory::buildModel("UserTaskStreamNotification", $result[0]);
        }
        return $ret;
    }

    public static function removeTaskStreamNotification($userId)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanse($userId);
        $result = Lib\PDOWrapper::call('removeTaskStreamNotification', $args);
        if ($result) {
            $ret = true;
        }
        return $ret;
    }

    public static function requestTaskStreamNotification($request)
    {
        $ret = 0;
        $args = Lib\PDOWrapper::cleanse($request->getUserId()).', '.
                Lib\PDOWrapper::cleanse($request->getInterval()).', ';
        if ($request->getStrict()) {
            $strict = 1;
        } else {
            $strict = 0;
        }
        $args .= Lib\PDOWrapper::cleanse($strict);
        $result = Lib\PDOWrapper::call("userTaskStreamNotificationInsertAndUpdate", $args);
        if ($result) {
            $ret = 1;
        }
        return $ret;
    }

    public static function getUserTags($user_id, $limit = null)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanse($user_id).",".
            Lib\PDOWrapper::cleanseNull($limit);
        $result = Lib\PDOWrapper::call("getUserTags", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Tag", $row);
            }
        }
        return $ret;
    }

    /**
     * Gets a single user by their id
     * @param The id of a user
     * @param The email of the user
     * @return User
     * @author Tadhg O'Flaherty
     **/
    public static function getUser($user_id = null, $email = null)
    {
        $user = null;
        if (!is_null($user_id) || !is_null($email)) {
            $args = Lib\PDOWrapper::cleanseNull($user_id)
                    .","."null"
                    .",".Lib\PDOWrapper::cleanseNullOrWrapStr($email)
                    .","."null"
                    .","."null"
                    .","."null"
                    .","."null"
                    .","."null"
                    .","."null";
            $result = Lib\PDOWrapper::call("getUser", $args);
            if ($result) {
                $user = Common\Lib\ModelFactory::buildModel("User", $result[0]);
            }
        }
        return $user;
    }

    public static function getUsers(
        $user_id = null,
        $email = null,
        $nonce = null,
        $password = null,
        $display_name = null,
        $biography = null,
        $native_language_id = null,
        $native_region_id = null,
        $created = null
    ) {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($user_id).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($display_name).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($email).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($password).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($biography).",".
            Lib\PDOWrapper::cleanseNull($nonce).",".
            Lib\PDOWrapper::cleanseNull($created).",".
            Lib\PDOWrapper::cleanseNull($native_language_id).",".
            Lib\PDOWrapper::cleanseNull($native_region_id);
        
        $result = Lib\PDOWrapper::call("getUser", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("User", $row);
            }
        }
        return $ret;
    }
    
    /*
        Get all users with $badge assigned
    */
    public static function getUsersWithBadge($badge_ID)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanse($badge_ID);
        $result = Lib\PDOWrapper::call("getUsersWithBadge", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("User", $row);
            }
        }
        
        return $ret;
    }

    /*
     * Checks if a user has a particular badge
    */
    public static function userHasBadge($badge_ID, $user_id)
    {
        $args = PDOWrapper::cleanse($badge_ID)
                .",".PDOWrapper::cleanse($user_id);
        
        $result = PDOWrapper::call('userHasBadge', $args);
        if ($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }

    /*
        Add the tag to a list of the user's preferred tags
    */
    public static function likeTag($user_id, $tag_id)
    {
        $args = Lib\PDOWrapper::cleanse($user_id).",".
            Lib\PDOWrapper::cleanse($tag_id);
        $result = Lib\PDOWrapper::call("userLikeTag", $args);
        if ($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }

    /*
        The opposite of likeTag
    */
    public static function removeTag($user_id, $tag_id)
    {
        $args = Lib\PDOWrapper::cleanse($user_id).",".
            Lib\PDOWrapper::cleanse($tag_id);
        $result = Lib\PDOWrapper::call("removeUserTag", $args);
        if ($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }

    /*
        returns true if the user has registered for notifications for this task
    */
    public static function isSubscribedToTask($user_id, $task_id)
    {
        $args = Lib\PDOWrapper::cleanse($user_id).",".
            Lib\PDOWrapper::cleanse($task_id);
        $result = Lib\PDOWrapper::call('userSubscribedToTask', $args);
        if ($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }

    /*
        returns true if the user has registered for notifications for this project
    */
    public static function isSubscribedToProject($user_id, $project_id)
    {
        $args = Lib\PDOWrapper::cleanse($user_id).",".
            Lib\PDOWrapper::cleanse($project_id);
        $result = Lib\PDOWrapper::call('userSubscribedToProject', $args);
        if ($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }
    
    /*
        Add the task to the user's notification List
    */
    public static function trackTask($user_id, $task_id)
    {
        $args = Lib\PDOWrapper::cleanseNull($user_id).",".
            Lib\PDOWrapper::cleanseNull($task_id);
        $result = Lib\PDOWrapper::call("UserTrackTask", $args);
        if ($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }

    /*
        Remove the task from the user's notification list
    */
    public static function ignoreTask($user_id, $task_id)
    {
        $args = Lib\PDOWrapper::cleanseNull($user_id).",".
            Lib\PDOWrapper::cleanseNull($task_id);
        $result = Lib\PDOWrapper::call("userUnTrackTask", $args);
        if ($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }
    
    public static function getTrackedTasks($user_id)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($user_id);
        $result = Lib\PDOWrapper::call("getUserTrackedTasks", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $row) {
                $task = Common\Lib\ModelFactory::buildModel("Task", $row);
                $ret[] = $task;
            }
        }

        return $ret;
    }

    public static function getTrackedProjects($user_id)
    {
        $args = Lib\PDOWrapper::cleanse($user_id);
        $result = Lib\PDOWrapper::call("getTrackedProjects", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Project", $row);
            }
            return $ret;
        }
        return null;
    }

    public static function requestReference($user_id)
    {
        self::insert_queue_request(
            PROJECTQUEUE,
            UserReferenceEmail,
            $user_id,
            0,
            0,
            0,
            0,
            0,
            '');
    }

    public static function trackProject($projectID, $userID)
    {
        $args = Lib\PDOWrapper::cleanse($projectID).",".
            Lib\PDOWrapper::cleanse($userID);
        $result = Lib\PDOWrapper::call("userTrackProject", $args);
        if ($result) {
            return $result[0]["result"];
        }
        return null;
    }
    
    public static function unTrackProject($projectID, $userID)
    {
        $args = Lib\PDOWrapper::cleanse($projectID).",".
            Lib\PDOWrapper::cleanse($userID);
        $result = Lib\PDOWrapper::call("userUnTrackProject", $args);
        if ($result) {
            return $result[0]["result"];
        }
        return null;
    }
    
    public static function savePersonalInfo($userInfo)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($userInfo->getId()).",".
            Lib\PDOWrapper::cleanseNull($userInfo->getUserId()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($userInfo->getFirstName()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($userInfo->getLastName()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($userInfo->getMobileNumber()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($userInfo->getBusinessNumber()).",".
            Lib\PDOWrapper::cleanseNull($userInfo->getLanguagePreference()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($userInfo->getJobTitle()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($userInfo->getAddress()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($userInfo->getCity()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($userInfo->getCountry()).','.
            Lib\PDOWrapper::cleanseNull($userInfo->getReceiveCredit() ? 1 : 0);
        $result = Lib\PDOWrapper::call("userPersonalInfoInsertAndUpdate", $args);
        if ($result) {
            $ret = Common\Lib\ModelFactory::buildModel("UserPersonalInformation", $result[0]);
        }
        return $ret;
    }

    public static function deleteUser($user_id)
    {
        $user = self::getUser($user_id);
        $old_email = $user->getEmail();

        Lib\PDOWrapper::call('deleteUser', Lib\PDOWrapper::cleanseNull($user_id));

        $result = Lib\PDOWrapper::call('get_memsource_user', Lib\PDOWrapper::cleanseNull($user_id));
        if (empty($result)) {
            error_log("No get_memsource_user($user_id) in deleteUser");
        } else {
                    $uid = $result[0]['memsource_user_uid'];
                    $ch = curl_init("https://cloud.memsource.com/web/api2/v3/users/$uid");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $data = [
                        'email' => $old_email,
                        'firstName' => 'xxx',
                        'lastName'  => 'xxx',
                        'role' => 'LINGUIST',
                        'timezone' => 'Europe/Rome',
                        'userName' => $result[0]['memsource_user_userName'],
                        'receiveNewsletter' => false,
                        'active' => false,
                        'note' => 'xxx',
                    ];
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    $authorization = 'Authorization: Bearer ' . Common\Lib\Settings::get('memsource.memsource_api_token');
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]);
                    $result = curl_exec($ch);
                    curl_close($ch);
                    error_log($result);
        }

        $ip = Common\Lib\Settings::get('moodle.ip');
        $token = Common\Lib\Settings::get('moodle.token');
        $MoodleRest = new Common\Lib\MoodleRest();
        $MoodleRest->setServerAddress("http://$ip/webservice/rest/server.php");
        $MoodleRest->setToken($token);
        $MoodleRest->setReturnFormat(Common\Lib\MoodleRest::RETURN_ARRAY);

        try {
        $results = $MoodleRest->request('core_user_get_users_by_field', ['field' => 'email', 'values' => [$old_email]]);
        error_log("deleteUser($user_id) core_user_get_users_by_field: " . print_r($results, 1));
        if (!empty($results) && empty($results['warnings']) && count($results) == 1) {
            $results = $MoodleRest->request('core_user_delete_users', ['userids' => [$results[0]['id']]]);
            error_log('core_user_delete_users: ' . print_r($results, 1));
        }
        } catch (\Exception $e) {
            error_log("deleteUser($user_id) access to Moodle failed: " . $e->getMessage());
        }

        $ch = curl_init(Common\Lib\Settings::get('discourse.url') . "/admin/users/list/all.json?email=$old_email");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Api-Key: ' . Common\Lib\Settings::get('discourse.api_key'), 'Api-Username: ' . Common\Lib\Settings::get('discourse.api_username')]);
        $result = curl_exec($ch);
        curl_close($ch);
        $response_data = json_decode($result, true);
        if (!empty($response_data) && !empty($response_data[0]['id']) && count($response_data) == 1) {
            $id = $response_data[0]['id'];
            $ch = curl_init(Common\Lib\Settings::get('discourse.url') . "/admin/users/$id.json");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['delete_posts' => true, 'block_email' => false, 'block_urls' => false, 'block_ip' => false]));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Api-Key: ' . Common\Lib\Settings::get('discourse.api_key'), 'Api-Username: ' . Common\Lib\Settings::get('discourse.api_username')]);
            $result = curl_exec($ch);
            curl_close($ch);
            error_log("deleteUser($user_id) /admin/users/$id.json: $result");
        } else error_log("deleteUser($user_id) /admin/users/list/all.json?email=$old_email: $result");
    }
    
    public static function isBlacklistedForTask($userId, $taskId)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($userId).",".
            Lib\PDOWrapper::cleanseNull($taskId);
        $result = Lib\PDOWrapper::call("isUserBlacklistedForTask", $args);
        if ($result) {
            return $result[0]['result'];
        }
        return $ret;
    }
    
    public static function insert_queue_request(
        $queue,
        $type,
        $user_id,
        $badge_id,
        $org_id,
        $project_id,
        $task_id,
        $claimant_id,
        $feedback)
    {
        if (empty($feedback)) $feedback = '';
        $args =
            Lib\PDOWrapper::cleanse($queue) . ',' .
            Lib\PDOWrapper::cleanse($type) . ',' .
            Lib\PDOWrapper::cleanse($user_id) . ',' .
            Lib\PDOWrapper::cleanse($badge_id) . ',' .
            Lib\PDOWrapper::cleanse($org_id) . ',' .
            Lib\PDOWrapper::cleanse($project_id) . ',' .
            Lib\PDOWrapper::cleanse($task_id) . ',' .
            Lib\PDOWrapper::cleanse($claimant_id) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($feedback);
        Lib\PDOWrapper::call('insert_queue_request', $args);
    }
}
