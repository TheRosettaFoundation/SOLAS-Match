<?php

require_once __DIR__."/../../Common/models/User.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";
require_once __DIR__."/../../Common/lib/Authentication.class.php";
require_once __DIR__."/../../Common/HttpStatusEnum.php";
require_once __DIR__."/../lib/MessagingClient.class.php";
require_once __DIR__."/../../Common/protobufs/emails/UserReferenceEmail.php";

class UserDao
{
	
	public static function getLoggedInUser()
    {
		$resource = new League\OAuth2\Server\Resource(new League\OAuth2\Server\Storage\PDO\Session());
        // Test for token existance and validity
        try {
            $resource->isValid(true);
            $parts =explode(" ",$_SERVER['HTTP_AUTHORIZATION']);
            return UserDao::getByOauthToken($parts[1]);
        }
        // The access token is missing or invalid...
        catch (League\OAuth2\Server\Exception\InvalidAccessTokenException $e)
        {
			// print_r($response);
            //Dispatcher::getDispatcher()->halt(HttpStatusEnum::UNAUTHORIZED, $e->getMessage());
            return null;
        }
    } 
    
    public static function create($email, $clear_password)
    {
        $nonce = Authentication::generateNonce();
        $password = Authentication::hashPassword($clear_password, $nonce);
        $user = new User();
        $user->setEmail($email);
        $user->setNonce($nonce);
        $user->setPassword($password);
        return self::save($user);
    }

    public static function changePassword($user_id, $password)
    {
        $user = self::getUser($user_id);

        $nonce = Authentication::generateNonce();
        $pass = Authentication::hashPassword($password, $nonce);

        $user[0]->setNonce($nonce);
        $user[0]->setPassword($pass);

        return self::save($user[0]);
    }

    public static function save($user)
    {
        $userId = $user->getId();
        $nativeLanguageCode = null;
        $nativeCountryCode = null;
        
        if(!is_null($userId) && $user->hasNativeLocale()) {
            $nativeLocale = $user->getNativeLocale();
            $nativeLanguageCode = $nativeLocale->getLanguageCode();
            $nativeCountryCode = $nativeLocale->getCountryCode();
            if ($nativeLanguageCode != '' && $nativeCountryCode != '') {
                BadgeDao::assignBadge($user->getId(), BadgeTypes::NATIVE_LANGUAGE);
            }
        }

        if ($user->getBiography() != '') {
            BadgeDao::assignBadge($user->getId(), BadgeTypes::PROFILE_FILLER);
        }

        $args = PDOWrapper::cleanseNullOrWrapStr($user->getEmail())
                .",".PDOWrapper::cleanseNull($user->getNonce())
                .",".PDOWrapper::cleanseNullOrWrapStr($user->getPassword())
                .",".PDOWrapper::cleanseNullOrWrapStr($user->getBiography())
                .",".PDOWrapper::cleanseNullOrWrapStr($user->getDisplayName())
                .",".PDOWrapper::cleanseNullOrWrapStr($nativeLanguageCode)
                .",".PDOWrapper::cleanseNullOrWrapStr($nativeCountryCode)
                .",".PDOWrapper::cleanseNull($userId);
        
        $result = PDOWrapper::call('userInsertAndUpdate', $args);
        if(!is_null($result)) {
            return ModelFactory::buildModel("User", $result[0]);
        } else {
            return null;
        }
    }


    private static function clearPasswordMatchesUsersPassword($user, $clear_password)
    {
        $hashed_input_password = Authentication::hashPassword($clear_password, $user->getNonce());
        
        return $hashed_input_password == $user->getPassword();
    }

    public static function login($email, $clear_password)
    {
        $user = self::getUser(null, $email);

        if (!is_object($user)) {
            throw new InvalidArgumentException('Sorry, the  password or username entered is incorrect.
                                                Please check the credentials used and try again.');
        }

        if (!self::clearPasswordMatchesUsersPassword($user, $clear_password)) {
            throw new InvalidArgumentException('Sorry, the  password or username entered is incorrect.
                                                Please check the credentials used and try again.');
        }

        if ($clear_password === '') {
            throw new InvalidArgumentException('Sorry, an empty password is not allowed.
                                                Please contact the site administrator for details');
        }

        UserSession::setSession($user->getId());
        return true;
    }

    public static function apiLogin($email, $clear_password)
    {
        $user = self::getUser(null, $email);
        
        if(is_array($user)) {
            $user = $user[0];
        }
        
        if (!is_object($user)) {            
            self::logLoginAttempt(null, $email, 0);
            throw new Exception(HttpStatusEnum::NOT_FOUND);
        }        
                
        if ( !self::isUserVerified($user->getId())) {
             self::logLoginAttempt($user->getId(), $email, 0);
             throw new Exception(HttpStatusEnum::UNAUTHORIZED);
        }

        if (AdminDao::isUserBanned($user->getId())) {
            self::logLoginAttempt($user->getId(), $email, 0);
//            Notify::sendBannedLoginEmail($user->getId());
            throw new Exception(HttpStatusEnum::FORBIDDEN);
        }

        if (!self::clearPasswordMatchesUsersPassword($user, $clear_password)) {
            self::logLoginAttempt($user->getId(), $email, 0);
            throw new Exception(HttpStatusEnum::NOT_FOUND);
        }
        
        self::logLoginAttempt($user->getId(), $email, 1);

        return $user;
    }

    public static function apiRegister($email, $clear_password, $verificationRequired = true)
    {
        $user = self::getUser(null, $email);
        
        if(is_array($user)) {
            $user = $user[0];
        }

        if (!is_object($user) && $clear_password != "") {
            $user = self::create($email, $clear_password);
            if ($verificationRequired) {
                self::registerUser($user->getId());
                Notify::sendEmailVerification($user->getId());
            }
        }
        return $user;
    }

    private static function registerUser($userId)
    {
        $ret = null;
        $uid = md5(uniqid(rand()));
        $args = PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNullOrWrapStr($uid);
        
        $result = PDOWrapper::call("registerUser", $args);
        if ($result) {
            $ret = $result[0]['result'];
        }
        return $ret;
    }

    public static function finishRegistration($userId)
    {
        $args = PDOWrapper::cleanseNull($userId);
        $response = PDOWrapper::call('finishRegistration', $args);
        BadgeDao::assignBadge($userId, BadgeTypes::REGISTERED);
        return $response[0]['result'];
    }

    public static function getRegisteredUser($uuid)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNullOrWrapStr($uuid);
        $result = PDOWrapper::call('getRegisteredUser', $args);
        if ($result) {
            $ret = ModelFactory::buildModel("User", $result[0]);
        }
        return $ret;
    }

    public static function isUserVerified($userId)
    {
        $ret = '0';
        $args = PDOWrapper::cleanseNull($userId);
        if ($result = PDOWrapper::call('isUserVerified', $args)) {
            $ret = $result[0]['result'];
        }
        return $ret;
    }
       
    public static function openIdLogin($openid,$app)
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
            throw new InvalidArgumentException('User has canceled authentication!');
            return false;
        } else {
            $retvals = $openid->getAttributes();
            if ($openid->validate()) {
                $user = self::getUser(null, $retvals['contact/email']);
                if(is_array($user)) $user = $user[0];
                if (!is_object($user)) {
                    $user = self::create($retvals['contact/email'], md5($retvals['contact/email']));
                }
                UserSession::setSession($user->getId());
            }
            return true;
        }
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

    public static function findOrganisationsUserBelongsTo($user_id) 
    {
        $ret = null;
        $args = PDOWrapper::cleanse($user_id);
        if ($result = PDOWrapper::call("findOrganisationsUserBelongsTo", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Organisation", $row);
            }
        }
        return $ret;
    }

    public static function getUserBadges($user_id)
    {
        $ret = null;
        $args = PDOWrapper::cleanse($user_id);
        if ($result = PDOWrapper::call("getUserBadges", $args)) {
            $ret = array();
            foreach ($result as $badge) {
                $ret[] = ModelFactory::buildModel("Badge", $badge);
            }
        }
        return $ret;
    }

    public static function getUserTaskStreamNotification($userId)
    {
        $ret = null;
        $args = PDOWrapper::cleanse($userId);
        if ($result = PDOWrapper::call("getUserTaskStreamNotification", $args)) {
            $ret = ModelFactory::buildModel("UserTaskStreamNotification", $result[0]);;
        }
        return $ret;
    }

    public static function removeTaskStreamNotification($userId)
    {
        $ret = null;
        $args = PDOWrapper::cleanse($userId);
        if ($result = PDOWrapper::call('removeTaskStreamNotification', $args)) {
            $ret = true;
        }
        return $ret;
    }

    public static function requestTaskStreamNotification($request)
    {
        $ret = 0;
        $args = PDOWrapper::cleanse($request->getUserId()).', '.
                PDOWrapper::cleanse($request->getInterval()).', ';
        if ($request->getStrict()) {
            $strict = 1;
        } else {
            $strict = 0;
        }
        $args .= PDOWrapper::cleanse($strict);
        
        if ($result = PDOWrapper::call("userTaskStreamNotificationInsertAndUpdate", $args)) {
            $ret = 1;
        }
        return $ret;
    }

    public static function getUserTags($user_id, $limit=null)
    {
        $ret = null;
        $args = PDOWrapper::cleanse($user_id)
                .",".PDOWrapper::cleanseNull($limit);
        
        if ($result = PDOWrapper::call("getUserTags", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Tag", $row);
            }
        }

        return $ret;
    }
    

    public static function getUser($user_id=null, $email=null, $nonce=null, $password=null, $display_name=null, $biography=null
                            , $native_language_id=null, $native_region_id=null, $created=null)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($user_id)
                .",".PDOWrapper::cleanseNullOrWrapStr($display_name)
                .",".PDOWrapper::cleanseNullOrWrapStr($email)
                .",".PDOWrapper::cleanseNullOrWrapStr($password)
                .",".PDOWrapper::cleanseNullOrWrapStr($biography)
                .",".PDOWrapper::cleanseNull($nonce)
                .",".PDOWrapper::cleanseNull($created)
                .",".PDOWrapper::cleanseNull($native_language_id)
                .",".PDOWrapper::cleanseNull($native_region_id);
        
        if ($result = PDOWrapper::call("getUser", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("User", $row);
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
        $args = PDOWrapper::cleanse($badge_ID);
        if ($result = PDOWrapper::call("getUsersWithBadge", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("User", $row);
            }
        }
        return $ret;
    }
    
    /*
        Add the tag to a list of the user's preferred tags
    */
    public static function likeTag($user_id, $tag_id)
    {
        $args = PDOWrapper::cleanse($user_id)
                .",".PDOWrapper::cleanse($tag_id);
        
        if ($result = PDOWrapper::call("userLikeTag", $args)) {
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
        $args = PDOWrapper::cleanse($user_id)
                .",".PDOWrapper::cleanse($tag_id);
        
        if ($result = PDOWrapper::call("removeUserTag", $args)) {
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
        $args = PDOWrapper::cleanseNull($user_id);
        if ($result = PDOWrapper::call('getUserNotifications', $args)) {
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
        $args = PDOWrapper::cleanse($user_id)
                .",".PDOWrapper::cleanse($task_id);
        
        if ($result = PDOWrapper::call('userSubscribedToTask', $args)) {
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
        $args = PDOWrapper::cleanse($user_id)
                .",".PDOWrapper::cleanse($project_id);
        
        if ($result = PDOWrapper::call('userSubscribedToProject', $args)) {
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
        $args = PDOWrapper::cleanseNull($user_id)
                .",".PDOWrapper::cleanseNull($task_id);
        
        if ($result = PDOWrapper::call("UserTrackTask", $args)) {
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
        $args = PDOWrapper::cleanseNull($user_id)
                .",".PDOWrapper::cleanseNull($task_id);
        
        if ($result = PDOWrapper::call("userUnTrackTask", $args)) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }
    
    public static function getTrackedTasks($user_id)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($user_id);
        if ($result = PDOWrapper::call("getUserTrackedTasks", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $task = ModelFactory::buildModel("Task", $row);
                $task->setTaskStatus(TaskDao::getTaskStatus($task->getId()));
                $ret[] = $task;
            }
        }

        return $ret;
    }

    public static function createPasswordReset($user)
    {
        $ret = null;
        if(!self::hasRequestedPasswordReset($user->getEmail())) { 
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
        $args = PDOWrapper::cleanseWrapStr($unique_id)
                .",".PDOWrapper::cleanse($user_id);
        
        $result = PDOWrapper::call("addPasswordResetRequest", $args);
        
        if($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }

    public static function removePasswordResetRequest($user_id)
    {
        $args = PDOWrapper::cleanse($user_id);
        $result = PDOWrapper::call("removePasswordResetRequest", $args);
        
        if($result) {
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
        if(self::getPasswordResetRequests($email)) {
            return true;
        } else {
            return false;
        }
    }

    /*
        Get Password Reset Requests
    */
    public static function getPasswordResetRequests($email, $uniqueId=null)
    {
        $args = PDOWrapper::cleanseNullOrWrapStr($uniqueId)
                .",".PDOWrapper::cleanseNullOrWrapStr($email);
        
        if($result = PDOWrapper::call("getPasswordResetRequests", $args)) {
            return ModelFactory::buildModel("PasswordResetRequest", $result[0]);            
        } else {
            return null;
        }
    }    

    public static function passwordReset($password, $key)
    {
        $reset_request = self::getPasswordResetRequests(null, $key);
        if(is_null($reset_request->getUserId())) {
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
        $args = PDOWrapper::cleanse($user_id);
        if ($result = PDOWrapper::call("getTrackedProjects", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Project", $row);
            }
            return $ret;
        }
        return null;
    }

    public static function requestReference($userId)
    {
        $messagingClient = new MessagingClient();
        if ($messagingClient->init()) {
            $request = new UserReferenceEmail();
            $request->setUserId($userId);
            $message = $messagingClient->createMessageFromProto($request);
            $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange, 
                    $messagingClient->UserReferenceRequestTopic);
        }
    }

    public static function trackProject($projectID,$userID)
    {
        $args = PDOWrapper::cleanse($projectID)
                .",".PDOWrapper::cleanse($userID);
        
        if ($result = PDOWrapper::call("userTrackProject", $args)) {
            return $result[0]["result"];
        }
        return null;
    }
    
    public static function unTrackProject($projectID,$userID)
    {
        $args = PDOWrapper::cleanse($projectID)
                .",".PDOWrapper::cleanse($userID);
        
        if ($result = PDOWrapper::call("userUnTrackProject", $args)) {
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
        $args = PDOWrapper::cleanseNull($userInfo->getId())
                .",".PDOWrapper::cleanseNull($userInfo->getUserId())
                .",".PDOWrapper::cleanseNullOrWrapStr($userInfo->getFirstName())
                .",".PDOWrapper::cleanseNullOrWrapStr($userInfo->getLastName())
                .",".PDOWrapper::cleanseNullOrWrapStr($userInfo->getMobileNumber())
                .",".PDOWrapper::cleanseNullOrWrapStr($userInfo->getBusinessNumber())
                .",".PDOWrapper::cleanseNullOrWrapStr($userInfo->getSip())
                .",".PDOWrapper::cleanseNullOrWrapStr($userInfo->getJobTitle())
                .",".PDOWrapper::cleanseNullOrWrapStr($userInfo->getAddress())
                .",".PDOWrapper::cleanseNullOrWrapStr($userInfo->getCity())
                .",".PDOWrapper::cleanseNullOrWrapStr($userInfo->getCountry());
        
        if ($result = PDOWrapper::call("userPersonalInfoInsertAndUpdate", $args)) {
            $ret = ModelFactory::buildModel("UserPersonalInformation", $result[0]);
            
        }
        return $ret;
    }
    
    public static function getPersonalInfo($id, $userId=null, $firstName=null, $lastName=null, $mobileNumber=null,
                            $businessNumber=null, $sip=null, $jobTitle=null, $address=null, $city=null, $country=null)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($id)
                .",".PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNullOrWrapStr($firstName)
                .",".PDOWrapper::cleanseNullOrWrapStr($lastName)
                .",".PDOWrapper::cleanseNullOrWrapStr($mobileNumber)
                .",".PDOWrapper::cleanseNullOrWrapStr($businessNumber)
                .",".PDOWrapper::cleanseNullOrWrapStr($sip)
                .",".PDOWrapper::cleanseNullOrWrapStr($jobTitle)
                .",".PDOWrapper::cleanseNullOrWrapStr($address)
                .",".PDOWrapper::cleanseNullOrWrapStr($city)
                .",".PDOWrapper::cleanseNullOrWrapStr($country);
        
        if ($result = PDOWrapper::call("getUserPersonalInfo", $args)) {
            $ret = ModelFactory::buildModel("UserPersonalInformation", $result[0]);
            
        }
        return $ret;        
    }
    
    public static function createSecondaryLanguage($userId, $locale)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNullOrWrapStr($locale->getLanguageCode())
                .",".PDOWrapper::cleanseNullOrWrapStr($locale->getCountryCode());
        
        if ($result = PDOWrapper::call("userSecondaryLanguageInsert", $args)) {
            $ret = ModelFactory::buildModel("Locale", $result[0]);
        }

        if (count(self::getSecondaryLanguages($userId)) > 1) {
            BadgeDao::assignBadge($userId, BadgeTypes::POLYGLOT);
        }
        return $ret;
    }
    
    public static function getSecondaryLanguages($userId=null)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($userId);
        if ($result = PDOWrapper::call("getUserSecondaryLanguages", $args)) {
            $ret = array();
            foreach($result as $locale) {
                $ret[] = ModelFactory::buildModel("Locale", $locale);
            }
            
        }
        return $ret;        
    }
    
    public static function deleteSecondaryLanguage($userId, $languageCode, $countryCode)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNullOrWrapStr($languageCode)
                .",".PDOWrapper::cleanseNullOrWrapStr($countryCode);
        
        if ($result = PDOWrapper::call("deleteUserSecondaryLanguage", $args)) {
            return $result[0]['result'];            
        }
        return $ret;        
    }
    
    public static function deleteUser($userId)
    {
        $args = PDOWrapper::cleanseNull($userId);
        PDOWrapper::call("deleteUser", $args);
    }
    
    private static function logLoginAttempt($userId, $email, $loginSuccess)
    {
        $args = PDOWrapper::cleanseNull($userId)
            .",".PDOWrapper::cleanseNullOrWrapStr($email)
            .",".PDOWrapper::cleanseNull($loginSuccess);        
        PDOWrapper::call("userLoginInsert", $args);
    }
    
    public static function isBlacklistedForTask($userId, $taskId)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNull($taskId);
        if($result = PDOWrapper::call("isUserBlacklistedForTask", $args)) {
            return $result[0]['result'];            
        }
        return $ret;
    }
    
    public static function getByOauthToken($token)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNullOrWrapStr($token);
        $result = PDOWrapper::call('getUserByOAuthToken', $args);
        if ($result) {
            $ret = ModelFactory::buildModel("User", $result[0]);
        }
        return $ret;  
    }
    
}
