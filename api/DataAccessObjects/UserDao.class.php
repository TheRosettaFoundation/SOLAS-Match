<?php

namespace SolasMatch\API\DAO;

use \SolasMatch\API\Lib as Lib;
use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/protobufs/models/User.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";
require_once __DIR__."/../../Common/lib/Authentication.class.php";
require_once __DIR__."/../lib/MessagingClient.class.php";
require_once __DIR__."/../../Common/protobufs/emails/UserReferenceEmail.php";

class UserDao
{
    public static function getLoggedInUser($token = null)
    {
        if (is_null($token)) {
            try {
                $resource = new \League\OAuth2\Server\Resource(new \League\OAuth2\Server\Storage\PDO\Session());
                // Test for token existance and validity
                $resource->isValid(true);
                $parts = explode(" ", $_SERVER['HTTP_AUTHORIZATION']);
                $token = $parts[1];
            } catch (\League\OAuth2\Server\Exception\InvalidAccessTokenException $e) {
                //The access token is missing or invalid...
                return null;
            }
        }
        return self::getByOAuthToken($token);
    }
    
    public static function create($email, $clear_password)
    {
        $nonce = Common\Lib\Authentication::generateNonce();
        $password = Common\Lib\Authentication::hashPassword($clear_password, $nonce);
        $user = new \User();
        $user->setEmail($email);
        $user->setNonce($nonce);
        $user->setPassword($password);
        return self::save($user);
    }

    public static function changePassword($user_id, $password)
    {
        $user = self::getUser($user_id);

        $nonce = Common\Lib\Authentication::generateNonce();
        $pass = Common\Lib\Authentication::hashPassword($password, $nonce);

        $user[0]->setNonce($nonce);
        $user[0]->setPassword($pass);

        return self::save($user[0]);
    }

    public static function save($user)
    {
        $userId = $user->getId();
        $nativeLanguageCode = null;
        $nativeCountryCode = null;
        if (!is_null($userId) && $user->hasNativeLocale()) {
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
            Lib\PDOWrapper::cleanseNull($user->getNonce()).",".
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

    private static function clearPasswordMatchesUsersPassword($user, $clear_password)
    {
        $hashed_input_password = Common\Lib\Authentication::hashPassword($clear_password, $user->getNonce());
        return $hashed_input_password == $user->getPassword();
    }

    public static function login($email, $clear_password)
    {
        $user = self::getUser(null, $email);

        if (!is_object($user)) {
            throw new \InvalidArgumentException(
                'Sorry, the password or username entered is incorrect. Please check the credentials used and try again.'
            );
        }

        if (!self::clearPasswordMatchesUsersPassword($user, $clear_password)) {
            throw new \InvalidArgumentException(
                'Sorry, the password or username entered is incorrect. Please check the credentials used and try again.'
            );
        }

        if ($clear_password === '') {
            throw new \InvalidArgumentException(
                'Sorry, an empty password is not allowed. Please contact the site administrator for details'
            );
        }

        Common\Lib\UserSession::setSession($user->getId());
        return true;
    }

    public static function apiLogin($email, $clear_password)
    {
        $user = self::getUser(null, $email);
        
        if (is_array($user)) {
            $user = $user[0];
        }
        
        if (!is_object($user)) {
            self::logLoginAttempt(null, $email, 0);
            throw new Common\Exceptions\SolasMatchException(
                "Unable to find user",
                Common\Enums\HttpStatusEnum::NOT_FOUND
            );
        }
                
        if (!self::isUserVerified($user->getId())) {
            self::logLoginAttempt($user->getId(), $email, 0);
            throw new Common\Exceptions\SolasMatchException(
                "Account is unverified",
                Common\Enums\HttpStatusEnum::UNAUTHORIZED
            );
        }

        if (AdminDao::isUserBanned($user->getId())) {
            self::logLoginAttempt($user->getId(), $email, 0);
            throw new Common\Exceptions\SolasMatchException(
                'user is banned',
                Common\Enums\HttpStatusEnum::FORBIDDEN
            );
        }

        if (!self::clearPasswordMatchesUsersPassword($user, $clear_password)) {
            self::logLoginAttempt($user->getId(), $email, 0);
            throw new Common\Exceptions\SolasMatchException(
                'Unable to find user',
                Common\Enums\HttpStatusEnum::NOT_FOUND
            );
        }
        
        self::logLoginAttempt($user->getId(), $email, 1);

        return $user->getId();
    }

    public static function apiRegister($email, $clear_password, $verificationRequired = true)
    {
        $ret = null;
        $user = self::getUser(null, $email);
        if (is_array($user)) {
            $user = $user[0];
        }

        if (!is_object($user) && $clear_password != "") {
            $user = self::create($email, $clear_password);
            if ($verificationRequired) {
                self::registerUser($user->getId());
                Lib\Notify::sendEmailVerification($user->getId());
            }
            if ($user) {
                $ret = '1';
            }
        }
        return $ret;
    }

    private static function registerUser($userId)
    {
        $ret = null;
        $uid = md5(uniqid(rand()));
        $args = Lib\PDOWrapper::cleanseNull($userId).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($uid);
        $result = Lib\PDOWrapper::call("registerUser", $args);
        if ($result) {
            $ret = $result[0]['result'];
        }
        return $ret;
    }

    public static function finishRegistration($userId)
    {
        $args = Lib\PDOWrapper::cleanseNull($userId);
        $response = Lib\PDOWrapper::call('finishRegistration', $args);
        BadgeDao::assignBadge($userId, Common\Enums\BadgeTypes::REGISTERED);
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
        if ($result = Lib\PDOWrapper::call('isUserVerified', $args)) {
            $ret = $result[0]['result'];
        }
        return $ret;
    }
       
    public static function openIdLogin($openid, $app)
    {
        if (!$openid->mode) {
            try {
                $openid->identity = $openid->data['openid_identifier'];
                $openid->required = array('contact/email');
                $url = $openid->authUrl();
                $app->redirect($openid->authUrl());
            } catch (ErrorException $e) {
                echo $e->getMessage();
            }
        } elseif ($openid->mode == 'cancel') {
            throw new \InvalidArgumentException('User has canceled authentication!');
            return false;
        } else {
            $retvals = $openid->getAttributes();
            if ($openid->validate()) {
                $user = self::getUser(null, $retvals['contact/email']);
                if (is_array($user)) {
                    $user = $user[0];
                }
                if (!is_object($user)) {
                    $user = self::create($retvals['contact/email'], md5($retvals['contact/email']));
                }
                Common\Lib\UserSession::setSession($user->getId());
            }
            return true;
        }
    }

    public static function logout()
    {
        Common\Lib\UserSession::destroySession();
    }

    public static function getCurrentUser()
    {
        $ret = null;
        if ($user_id = Common\Lib\UserSession::getCurrentUserId()) {
                $ret = self::getUser($user_id);
        }
        return $ret;
    }

    public static function isLoggedIn()
    {
        return (!is_null(Common\Lib\UserSession::getCurrentUserId()));
    }

    public static function findOrganisationsUserBelongsTo($user_id)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanse($user_id);
        if ($result = Lib\PDOWrapper::call("findOrganisationsUserBelongsTo", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Organisation", $row);
            }
        }
        return $ret;
    }

    public static function getUserBadges($user_id)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanse($user_id);
        if ($result = Lib\PDOWrapper::call("getUserBadges", $args)) {
            $ret = array();
            foreach ($result as $badge) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Badge", $badge);
            }
        }
        return $ret;
    }

    public static function getUserTaskStreamNotification($userId)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanse($userId);
        if ($result = Lib\PDOWrapper::call("getUserTaskStreamNotification", $args)) {
            $ret = Common\Lib\ModelFactory::buildModel("UserTaskStreamNotification", $result[0]);
        }
        return $ret;
    }

    public static function removeTaskStreamNotification($userId)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanse($userId);
        if ($result = Lib\PDOWrapper::call('removeTaskStreamNotification', $args)) {
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
        
        if ($result = Lib\PDOWrapper::call("userTaskStreamNotificationInsertAndUpdate", $args)) {
            $ret = 1;
        }
        return $ret;
    }

    public static function getUserTags($user_id, $limit = null)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanse($user_id).",".
            Lib\PDOWrapper::cleanseNull($limit);
        if ($result = Lib\PDOWrapper::call("getUserTags", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Tag", $row);
            }
        }
        return $ret;
    }
    
    public static function getUser(
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
        
        if ($result = Lib\PDOWrapper::call("getUser", $args)) {
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
        if ($result = Lib\PDOWrapper::call("getUsersWithBadge", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("User", $row);
            }
        }
        return $ret;
    }
    
    /*
        Add the tag to a list of the user's preferred tags
    */
    public static function likeTag($user_id, $tag_id)
    {
        $args = Lib\PDOWrapper::cleanse($user_id).",".
            Lib\PDOWrapper::cleanse($tag_id);
        
        if ($result = Lib\PDOWrapper::call("userLikeTag", $args)) {
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
        
        if ($result = Lib\PDOWrapper::call("removeUserTag", $args)) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }

    /*
        Get list of tasks in User's notification list
    */
    public static function getUserNotificationList($user_id)
    {
        $args = Lib\PDOWrapper::cleanseNull($user_id);
        if ($result = Lib\PDOWrapper::call('getUserNotifications', $args)) {
            return $result;
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
        
        if ($result = Lib\PDOWrapper::call('userSubscribedToTask', $args)) {
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
        
        if ($result = Lib\PDOWrapper::call('userSubscribedToProject', $args)) {
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
        
        if ($result = Lib\PDOWrapper::call("UserTrackTask", $args)) {
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
        
        if ($result = Lib\PDOWrapper::call("userUnTrackTask", $args)) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }
    
    public static function getTrackedTasks($user_id)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($user_id);
        if ($result = Lib\PDOWrapper::call("getUserTrackedTasks", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $task = Common\Lib\ModelFactory::buildModel("Task", $row);
                $ret[] = $task;
            }
        }

        return $ret;
    }

    public static function createPasswordReset($user)
    {
        $ret = null;
        if (!self::hasRequestedPasswordReset($user->getEmail())) {
            $uid = md5(uniqid(rand()));
            $ret = self::addPasswordResetRequest($uid, $user->getId());
        }
        return $ret;
    }
    
    /*
        Add password reset request to DB for this user
    */
    public static function addPasswordResetRequest($unique_id, $user_id)
    {
        $args = Lib\PDOWrapper::cleanseWrapStr($unique_id).",".
            Lib\PDOWrapper::cleanse($user_id);
        $result = Lib\PDOWrapper::call("addPasswordResetRequest", $args);
        
        if ($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }

    public static function removePasswordResetRequest($user_id)
    {
        $args = Lib\PDOWrapper::cleanse($user_id);
        $result = Lib\PDOWrapper::call("removePasswordResetRequest", $args);
        
        if ($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }

    /*
        Check if a user has requested a password reset
    */
    public static function hasRequestedPasswordReset($email)
    {
        if (self::getPasswordResetRequests($email)) {
            return true;
        } else {
            return false;
        }
    }

    /*
        Get Password Reset Requests
    */
    public static function getPasswordResetRequests($email, $uniqueId = null)
    {
        $args = Lib\PDOWrapper::cleanseNullOrWrapStr($uniqueId).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($email);
        if ($result = Lib\PDOWrapper::call("getPasswordResetRequests", $args)) {
            return Common\Lib\ModelFactory::buildModel("PasswordResetRequest", $result[0]);
        } else {
            return null;
        }
    }

    public static function passwordReset($password, $key)
    {
        $reset_request = self::getPasswordResetRequests(null, $key);
        if (is_null($reset_request->getUserId())) {
            return 0;
        } elseif (self::changePassword($reset_request->getUserId(), $password)) {
            self::removePasswordResetRequest($reset_request->getUserId());
            return 1;
        } else {
            return 0;
        }
    }
    
    public static function getTrackedProjects($user_id)
    {
        $args = Lib\PDOWrapper::cleanse($user_id);
        if ($result = Lib\PDOWrapper::call("getTrackedProjects", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Project", $row);
            }
            return $ret;
        }
        return null;
    }

    public static function requestReference($userId)
    {
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            $request = new \UserReferenceEmail();
            $request->setUserId($userId);
            $message = $messagingClient->createMessageFromProto($request);
            $messagingClient->sendTopicMessage(
                $message,
                $messagingClient->MainExchange,
                $messagingClient->UserReferenceRequestTopic
            );
        }
    }

    public static function trackProject($projectID, $userID)
    {
        $args = Lib\PDOWrapper::cleanse($projectID).",".
            Lib\PDOWrapper::cleanse($userID);
        if ($result = Lib\PDOWrapper::call("userTrackProject", $args)) {
            return $result[0]["result"];
        }
        return null;
    }
    
    public static function unTrackProject($projectID, $userID)
    {
        $args = Lib\PDOWrapper::cleanse($projectID).",".
            Lib\PDOWrapper::cleanse($userID);
        if ($result = Lib\PDOWrapper::call("userUnTrackProject", $args)) {
            return $result[0]["result"];
        }
        return null;
    }
    
    public static function createPersonalInfo($userInfo)
    {
        return self::savePersonalInfo($userInfo);
    }
            
    public static function updatePersonalInfo($userInfo)
    {
        return self::savePersonalInfo($userInfo);
    }
    
    private static function savePersonalInfo($userInfo)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($userInfo->getId()).",".
            Lib\PDOWrapper::cleanseNull($userInfo->getUserId()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($userInfo->getFirstName()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($userInfo->getLastName()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($userInfo->getMobileNumber()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($userInfo->getBusinessNumber()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($userInfo->getSip()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($userInfo->getJobTitle()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($userInfo->getAddress()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($userInfo->getCity()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($userInfo->getCountry());
        if ($result = Lib\PDOWrapper::call("userPersonalInfoInsertAndUpdate", $args)) {
            $ret = Common\Lib\ModelFactory::buildModel("UserPersonalInformation", $result[0]);
        }
        return $ret;
    }
    
    public static function getPersonalInfo(
        $id,
        $userId = null,
        $firstName = null,
        $lastName = null,
        $mobileNumber = null,
        $businessNumber = null,
        $sip = null,
        $jobTitle = null,
        $address = null,
        $city = null,
        $country = null
    ) {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($id).",".
            Lib\PDOWrapper::cleanseNull($userId).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($firstName).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($lastName).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($mobileNumber).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($businessNumber).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($sip).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($jobTitle).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($address).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($city).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($country);
        if ($result = Lib\PDOWrapper::call("getUserPersonalInfo", $args)) {
            $ret = Common\Lib\ModelFactory::buildModel("UserPersonalInformation", $result[0]);
        }
        return $ret;
    }
    
    public static function createSecondaryLanguage($userId, $locale)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($userId).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($locale->getLanguageCode()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($locale->getCountryCode());
        if ($result = Lib\PDOWrapper::call("userSecondaryLanguageInsert", $args)) {
            $ret = Common\Lib\ModelFactory::buildModel("Locale", $result[0]);
        }
        if (count(self::getSecondaryLanguages($userId)) > 1) {
            BadgeDao::assignBadge($userId, Common\Enums\BadgeTypes::POLYGLOT);
        }
        return $ret;
    }
    
    public static function getSecondaryLanguages($userId = null)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($userId);
        if ($result = Lib\PDOWrapper::call("getUserSecondaryLanguages", $args)) {
            $ret = array();
            foreach ($result as $locale) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Locale", $locale);
            }
        }
        return $ret;
    }
    
    public static function deleteSecondaryLanguage($userId, $languageCode, $countryCode)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($userId).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($languageCode).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($countryCode);
        if ($result = Lib\PDOWrapper::call("deleteUserSecondaryLanguage", $args)) {
            return $result[0]['result'];
        }
        return $ret;
    }
    
    public static function deleteUser($userId)
    {
        $args = Lib\PDOWrapper::cleanseNull($userId);
        Lib\PDOWrapper::call("deleteUser", $args);
    }
    
    private static function logLoginAttempt($userId, $email, $loginSuccess)
    {
        $args = Lib\PDOWrapper::cleanseNull($userId).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($email).",".
            Lib\PDOWrapper::cleanseNull($loginSuccess);
        Lib\PDOWrapper::call("userLoginInsert", $args);
    }
    
    public static function isBlacklistedForTask($userId, $taskId)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($userId).",".
            Lib\PDOWrapper::cleanseNull($taskId);
        if ($result = Lib\PDOWrapper::call("isUserBlacklistedForTask", $args)) {
            return $result[0]['result'];
        }
        return $ret;
    }
    
    public static function getByOAuthToken($token)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNullOrWrapStr($token);
        $result = Lib\PDOWrapper::call('getUserByOAuthToken', $args);
        if ($result) {
            $ret = Common\Lib\ModelFactory::buildModel("User", $result[0]);
        }
        return $ret;
    }
}
