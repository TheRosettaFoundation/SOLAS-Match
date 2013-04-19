<?php

require_once __DIR__."/../../Common/models/User.php";
require_once __DIR__."/../../Common/lib/PDOWrapper.class.php";
require_once __DIR__."/../../Common/lib/Authentication.class.php";

class UserDao
{
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
        }
        
        $result = PDOWrapper::call('userInsertAndUpdate', PDOWrapper::cleanseNullOrWrapStr($user->getEmail()).",".
        PDOWrapper::cleanseNull($user->getNonce()).",".PDOWrapper::cleanseNullOrWrapStr($user->getPassword()).",".
        PDOWrapper::cleanseNullOrWrapStr($user->getBiography()).",".
        PDOWrapper::cleanseNullOrWrapStr($user->getDisplayName()).",".
        PDOWrapper::cleanseNullOrWrapStr($nativeLanguageCode).",".
        PDOWrapper::cleanseNullOrWrapStr($nativeCountryCode).",".
        PDOWrapper::cleanseNull($userId));
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
            return null;
        }

        if (!self::clearPasswordMatchesUsersPassword($user, $clear_password)) {
            return null;
        }

        return $user;
    }

    public static function apiRegister($email, $clear_password)
    {
        $user = self::getUser(null, $email);
        
        if(is_array($user)) {
            $user = $user[0];
        }

        if (!is_object($user) && $clear_password != "") {
            $user = self::create($email, $clear_password);
            BadgeDao::assignBadge($user->getId(), BadgeTypes::REGISTERED);
        } else {
            $user = null;
            //array("message"=>'sorry the account you enerted already exists.
            // \n please login.',"status code"=>500);
        }
        return $user;
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
                    BadgeDao::assignBadge($user->getId(),  BadgeTypes::REGISTERED);
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


    public static function isAdmin($userId, $orgId)
    {
        $ret = false;
        $args = PDOWrapper::cleanse($userId).", ";
        $args .= PDOWrapper::cleanseNullOrWrapStr($orgId);
        if ($result = PDOWrapper::call("isAdmin", $args)) {
            $ret = $result[0]['result'];
        }
        return $ret;
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
        if ($result = PDOWrapper::call("findOrganisationsUserBelongsTo", PDOWrapper::cleanse($user_id))) {
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
        if ($result = PDOWrapper::call("getUserBadges", PDOWrapper::cleanse($user_id))) {
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
        if ($result = PDOWrapper::call("getUserTaskStreamNotification", PDOWrapper::cleanse($userId))) {
            $ret = ModelFactory::buildModel("UserTaskStreamNotification", $result[0]);;
        }
        return $ret;
    }

    public static function removeTaskStreamNotification($userId)
    {
        $ret = null;
        if ($result = PDOWrapper::call('removeTaskStreamNotification', PDOWrapper::cleanse($userId))) {
            $ret = true;
        }
        return $ret;
    }

    public static function requestTaskStreamNotification($userId, $interval)
    {
        $ret = 0;
        $args = PDOWrapper::cleanse($userId).', ';
        $args .= PDOWrapper::cleanse($interval);
        if ($result = PDOWrapper::call("userTaskStreamNotificationInsertAndUpdate", $args)) {
            $ret = 1;
        }
        return $ret;
    }

    public static function getUserTags($user_id, $limit=null)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getUserTags", PDOWrapper::cleanse($user_id)
                                                    .",".PDOWrapper::cleanseNull($limit))) {
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
        if ($result = PDOWrapper::call("getUser", PDOWrapper::cleanseNull($user_id)
                                .",".PDOWrapper::cleanseNullOrWrapStr($display_name)
                                .",".PDOWrapper::cleanseNullOrWrapStr($email)
                                .",".PDOWrapper::cleanseNullOrWrapStr($password)
                                .",".PDOWrapper::cleanseNullOrWrapStr($biography)
                                .",".PDOWrapper::cleanseNull($nonce)
                                .",".PDOWrapper::cleanseNull($created)
                                .",".PDOWrapper::cleanseNull($native_language_id)
                                .",".PDOWrapper::cleanseNull($native_region_id))) {
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
        if ($result = PDOWrapper::call("getUsersWithBadge", PDOWrapper::cleanse($badge_ID))) {
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
        $args = array();
        $args['user_id'] = PDOWrapper::cleanse($user_id);
        $args['tag_id'] = PDOWrapper::cleanse($tag_id);
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
        if ($result = PDOWrapper::call("removeUserTag", PDOWrapper::cleanse($user_id).","
                                        .PDOWrapper::cleanse($tag_id))) {
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
        $args = array();
        $args['id'] = $user_id;
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
        $args = array();
        $args[] = PDOWrapper::cleanse($user_id);
        $args[] = PDOWrapper::cleanse($task_id);
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
        $args = PDOWrapper::cleanse($user_id);
        $args .= ", ".PDOWrapper::cleanse($project_id);
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
        $args = PDOWrapper::cleanseNull($user_id);
        $args .= ", ".PDOWrapper::cleanseNull($task_id);
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
        $args = PDOWrapper::cleanseNull($user_id);
        $args .= ", ".PDOWrapper::cleanseNull($task_id);
        if ($result = PDOWrapper::call("userUnTrackTask", $args)) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }
    
    public static function getTrackedTasks($user_id)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getUserTrackedTasks", PDOWrapper::cleanseNull($user_id))) {
            $ret = array();
            foreach ($result as $row) {
                $task = ModelFactory::buildModel("Task", $row);
                $task->setTaskStatus(TaskDao::getTaskStatus($task->getId()));
                $ret[] = $task;
            }
        }

        return $ret;
    }

    public static function createPasswordReset($user_id)
    {
        $uid = null;
        if(!self::hasRequestedPasswordReset($user_id)) {            
            $uid = md5(uniqid(rand()));
            self::addPasswordResetRequest($uid, $user_id);
        } else {
            $request = self::getPasswordResetRequests($user_id);
            $uid = $request->getKey();
        }
        
        Notify::sendPasswordResetEmail($user_id);
        return 1;
    }    
    
    /*
        Add password reset request to DB for this user
    */
    public static function addPasswordResetRequest($unique_id, $user_id)
    {
        $result = PDOWrapper::call("addPasswordResetRequest", PDOWrapper::cleanseWrapStr($unique_id)
                        .",".PDOWrapper::cleanse($user_id));
        
        if($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }

    public static function removePasswordResetRequest($user_id)
    {
        $result = PDOWrapper::call("removePasswordResetRequest", PDOWrapper::cleanse($user_id));
        
        if($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }

    /*
        Check if a user has requested a password reset
    */    
    public static function hasRequestedPasswordReset($user_id)
    {
        if(self::getPasswordResetRequests($user_id)) {
            return true;
        } else {
            return false;
        }
    }

    /*
        Get Password Reset Requests
    */
    public static function getPasswordResetRequests($userId, $uniqueId=null)
    {
        if($result = PDOWrapper::call("getPasswordResetRequests", PDOWrapper::cleanseNullOrWrapStr($uniqueId).",".PDOWrapper::cleanseNull($userId))) {
            return ModelFactory::buildModel("PasswordResetRequest", $result[0]);            
        } else {
            return null;
        }
    }    

    public static function passwordReset($password, $key)
    {
        $reset_request = self::getPasswordResetRequests(null, $key);
        if(is_null($reset_request->getUserId())) {
            return array("result" => 0, "message" => "Incorrect Unique ID. Are you sure you copied the URL correctly?");
        } elseif (self::changePassword($reset_request->getUserId(), $password)) {
            self::removePasswordResetRequest($reset_request->getUserId());
            return array("result" => 1, "message" => "You have successfully changed your password");
        }
    }
    
    public static function getTrackedProjects($user_id)
    {
        if ($result = PDOWrapper::call("getTrackedProjects", PDOWrapper::cleanse($user_id))) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Project", $row);
            }
            return $ret;
        }
        return null;
    }
    public static function trackProject($projectID,$userID)
    {
        if ($result = PDOWrapper::call("userTrackProject", PDOWrapper::cleanse($projectID).",".PDOWrapper::cleanse($userID))) {
            return $result[0]["result"];
        }
        return null;
    }
    
    public static function unTrackProject($projectID,$userID)
    {
        if ($result = PDOWrapper::call("userUnTrackProject", PDOWrapper::cleanse($projectID).",".PDOWrapper::cleanse($userID))) {
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
        if ($result = PDOWrapper::call("userPersonalInfoInsertAndUpdate", PDOWrapper::cleanseNull($userInfo->getId())
                                .",".PDOWrapper::cleanseNull($userInfo->getUserId())
                                .",".PDOWrapper::cleanseNullOrWrapStr($userInfo->getFirstName())
                                .",".PDOWrapper::cleanseNullOrWrapStr($userInfo->getLastName())
                                .",".PDOWrapper::cleanseNullOrWrapStr($userInfo->getMobileNumber())
                                .",".PDOWrapper::cleanseNullOrWrapStr($userInfo->getBusinessNumber())
                                .",".PDOWrapper::cleanseNullOrWrapStr($userInfo->getSip())
                                .",".PDOWrapper::cleanseNullOrWrapStr($userInfo->getJobTitle())
                                .",".PDOWrapper::cleanseNullOrWrapStr($userInfo->getAddress())
                                .",".PDOWrapper::cleanseNullOrWrapStr($userInfo->getCity())
                                .",".PDOWrapper::cleanseNullOrWrapStr($userInfo->getCountry()))) {
            $ret = ModelFactory::buildModel("UserPersonalInformation", $result[0]);
            
        }
        return $ret;
    }
    
    public static function getPersonalInfo($id, $userId=null, $firstName=null, $lastName=null, $mobileNumber=null,
                            $businessNumber=null, $sip=null, $jobTitle=null, $address=null, $city=null, $country=null)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getUserPersonalInfo", PDOWrapper::cleanseNull($id)
                                .",".PDOWrapper::cleanseNull($userId)
                                .",".PDOWrapper::cleanseNullOrWrapStr($firstName)
                                .",".PDOWrapper::cleanseNullOrWrapStr($lastName)
                                .",".PDOWrapper::cleanseNullOrWrapStr($mobileNumber)
                                .",".PDOWrapper::cleanseNullOrWrapStr($businessNumber)
                                .",".PDOWrapper::cleanseNullOrWrapStr($sip)
                                .",".PDOWrapper::cleanseNullOrWrapStr($jobTitle)
                                .",".PDOWrapper::cleanseNullOrWrapStr($address)
                                .",".PDOWrapper::cleanseNullOrWrapStr($city)
                                .",".PDOWrapper::cleanseNullOrWrapStr($country))) {
            $ret = ModelFactory::buildModel("UserPersonalInformation", $result[0]);
            
        }
        return $ret;        
    }
    
    public static function createSecondaryLanguage($userId, $locale)
    {
        $ret = null;
        if ($result = PDOWrapper::call("userSecondaryLanguageInsert", PDOWrapper::cleanseNull($userId)
                                .",".PDOWrapper::cleanseNullOrWrapStr($locale->getLanguageCode())
                                .",".PDOWrapper::cleanseNullOrWrapStr($locale->getCountryCode()))) {
            $ret = ModelFactory::buildModel("Locale", $result[0]);
            
        }
        return $ret;
    }
    
    public static function getSecondaryLanguages($userId=null)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getUserSecondaryLanguages", PDOWrapper::cleanseNull($userId))) {
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
        if ($result = PDOWrapper::call("deleteUserSecondaryLanguage", PDOWrapper::cleanseNull($userId).",".
                                        PDOWrapper::cleanseNullOrWrapStr($languageCode).",".
                                        PDOWrapper::cleanseNullOrWrapStr($countryCode))) {
            return $result[0]['result'];            
        }
        return $ret;        
    }
    
}
