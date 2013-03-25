<?php

require_once __DIR__.'/../../Common/models/User.php';
require_once __DIR__.'/../../Common/lib/PDOWrapper.class.php';
require_once __DIR__.'/../../Common/lib/Authentication.class.php';

class UserDao
{
    
    public static function find($params)
    {
        $args = "";
        
        if (isset($params['user_id']) || isset($params['email'])) {
            $args.=(isset($params['user_id']) && $params['user_id'] != null) ? 
                                            PDOWrapper::cleanse($params['user_id']) : "null";
            $args.=(isset($params['password']) && $params['password'] != null) ?
                                            ",".PDOWrapper::cleanseNullOrWrapStr($params['password']) : ",null";
            $args.=(isset($params['email']) && $params['email'] != null) ?
                                            ",".PDOWrapper::cleanseNullOrWrapStr($params['email']) : ",null";
            $args.=(isset($params['role']) && $params['role'] == 'organisation_member') ? 
                                            ",1" : ",0";
        } else {
            throw new InvalidArgumentException('Cannot search for user, as no valid parameters were given.');
        }

        $ret = null;
        if ($r = PDOWrapper::call("userFindByUserData", $args)) {
            $ret = ModelFactory::buildModel("User", $r[0]);
        }
        return $ret;
    }

    public static function create($email, $clear_password)
    {
        if (is_array($ret=self::getUser(null, $email, null, null, null, null, null, null, null)&&!empty($ret))) {
            throw new InvalidArgumentException('Oops, you already have an account here with that email address.
                                                Please log in instread.');
        }

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
        $user = self::getUser($user_id, null, null, null, null, null, null, null, null);

        $nonce = Authentication::generateNonce();
        $pass = Authentication::hashPassword($password, $nonce);

        $user->setNonce($nonce);
        $user->setPassword($pass);

        return self::save($user);
    }

    public static function save($user)
    {
        if (is_null($user->getUserId())) {
            return self::insert($user);
        } else {
            return self::update($user);
        }
    }

    private static function update($user)
    {
        $result = PDOWrapper::call('userInsertAndUpdate', PDOWrapper::cleanseNullOrWrapStr($user->getEmail()).",".
        PDOWrapper::cleanseNull($user->getNonce()).",".PDOWrapper::cleanseNullOrWrapStr($user->getPassword()).",".
        PDOWrapper::cleanseNullOrWrapStr($user->getBiography()).",".
        PDOWrapper::cleanseNullOrWrapStr($user->getDisplayName()).",".
        PDOWrapper::cleanseNullOrWrapStr($user->getNativeLangId()).",".
        PDOWrapper::cleanseNullOrWrapStr($user->getNativeRegionId()).",".
        PDOWrapper::cleanse($user->getUserId()));
        if(!is_null($result)) {
            return ModelFactory::buildModel("User", $result[0]);
        } else {
            return null;
        }
    }

    private static function insert($user) 
    {
        $result = PDOWrapper::call('userInsertAndUpdate', PDOWrapper::cleanseNullOrWrapStr($user->getEmail())
                                        .",".PDOWrapper::cleanse($user->getNonce())
                                        .",".PDOWrapper::cleanseNullOrWrapStr($user->getPassword())
                                        .",null,null,null,null,null");
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
        $user = self::getUser(null, $email, null, null, null, null, null, null, null);

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

        UserSession::setSession($user->getUserId());
        return true;
    }

    public static function apiLogin($email, $clear_password)
    {
        $user = self::getUser(null, $email, null, null, null, null, null, null, null);
        
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
        $user = self::getUser(null, $email, null, null, null, null, null, null, null);
        
        if(is_array($user)) {
            $user = $user[0];
        }

        if (!is_object($user) && $clear_password != "") {
            $user = self::create($email, $clear_password);
            BadgeDao::assignBadge($user->getUserId(), BadgeTypes::REGISTERED);
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
                $user = self::getUser(null, $retvals['contact/email'], null, null, null, null, null, null, null);
                if(is_array($user)) $user = $user[0];
                if (!is_object($user)) {
                    $user = self::create($retvals['contact/email'], md5($retvals['contact/email']));
                    BadgeDao::assignBadge($user->getUserId(),  BadgeTypes::REGISTERED);
                }
                UserSession::setSession($user->getUserId());
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
                $ret = self::getUser($user_id, null, null, null, null, null, null, null, null);
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
            $user_found = self::find(array(
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

    public static function getUserBadges(User $user)
    {
        return self::getUserBadgesbyID($user->getUserId());
    }

    public static function getUserBadgesbyID($user_id)
    {
        $ret = array();
        if ($result = PDOWrapper::call("getUserBadges", PDOWrapper::cleanse($user_id))) {
            foreach ($result as $badge) {
                $ret[] = ModelFactory::buildModel("Badge", $badge);
            }
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
    
    public static function getUser($user_id, $email, $nonce, $password, $display_name, $biography
                            , $native_language_id, $native_region_id, $created)
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
    public static function getUsersWithBadgeByID($badge_ID)
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
    
    public static function getUsersWithBadge($badge)
    {
        return self::getUsersWithBadgeByID($badge->getId());
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
        }
        return 0;
    }

    /*
        The opposite of likeTag
    */
    public static function removeTag($user_id, $tag_id)
    {
        $ret = false;
        if ($result = PDOWrapper::call("removeUserTag", PDOWrapper::cleanse($user_id).","
                                        .PDOWrapper::cleanse($tag_id))) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }

    /*
        Get list of tasks in User's notification list
    */
    public static function getUserNotificationList($user_id) 
    {
        $ret = null;
        $args = array();
        $args['id'] = $user_id;
        if ($return = PDOWrapper::call('getUserNotifications', $args)) {
            $ret = $return;
        }

        return $ret;
    }

    /*
        returns true if the user has registered for notifications for this task
    */
    public static function isSubscribedToTask($user_id, $task_id)
    {
        $ret = false;
        $args = array();
        $args[] = PDOWrapper::cleanse($user_id);
        $args[] = PDOWrapper::cleanse($task_id);
        if ($result = PDOWrapper::call('userSubscribedToTask', $args)) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }

    /*
        returns true if the user has registered for notifications for this project
    */
    public static function isSubscribedToProject($user_id, $project_id)
    {
        $ret = false;
        $args = PDOWrapper::cleanse($user_id);
        $args .= ", ".PDOWrapper::cleanse($project_id);
        if ($result = PDOWrapper::call('userSubscribedToProject', $args)) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }
    
    /*
        Add the task to the user's notification List
    */
    public static function trackTask($user_id, $task_id)
    {
        $ret = false;
        $args = PDOWrapper::cleanseNull($user_id);
        $args .= ", ".PDOWrapper::cleanseNull($task_id);
        if ($result = PDOWrapper::call("UserTrackTask", $args)) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }

    /*
        Remove the task from the user's notification list
    */
    public static function ignoreTask($user_id, $task_id)
    {
        $ret = false;
        $args = PDOWrapper::cleanseNull($user_id);
        $args .= ", ".PDOWrapper::cleanseNull($task_id);
        if ($result = PDOWrapper::call("userUnTrackTask", $args)) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }
    
    public static function getTrackedTasks($user_id)
    {
        $ret = array();
        if ($result = PDOWrapper::call("getUserTrackedTasks", PDOWrapper::cleanseNull($user_id))) {
            foreach ($result as $row) {
                $task = ModelFactory::buildModel("Task", $row);
                $task->setStatus(self::getTaskStatus($task->getId()));
                $ret[] = $task;
            }
        }

        return $ret;
    }

    public static function createPasswordReset($user_id)
    {
        $uid = null;
        if(!self::hasRequestedPasswordResetID($user_id)) {            
            $uid = md5(uniqid(rand()));
            self::addPasswordResetRequest($uid, $user_id);
        } else {
            $request = self::getPasswordResetRequests(array("user_id" => $user_id));
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
        PDOWrapper::call("addPasswordResetRequest", PDOWrapper::cleanseWrapStr($unique_id)
                        .",".PDOWrapper::cleanse($user_id));
    }

    public static function removePasswordResetRequest($user_id)
    {
        PDOWrapper::call("removePasswordResetRequest", PDOWrapper::cleanse($user_id));
    }

    /*
        Check if a user has requested a password reset
    */
    public static function hasRequestedPasswordReset($user)
    {
        return self::hasRequestedPasswordResetID($user->getUserId());
    }
    
    public static function hasRequestedPasswordResetID($user_id)
    {
        $ret = false;
        if (self::getPasswordResetRequests(array('user_id'=>$user_id))) {
            $ret = true;
        }
        return $ret;
    }

    /*
        Get Password Reset Requests
    */
    public static function getPasswordResetRequests($args)
    {
        $ret = false;
        if (isset($args['uid']) && $args['uid'] != '') {
            $uid = $args['uid'];
            if ($result = PDOWrapper::call("getPasswordResetRequests", PDOWrapper::cleanseWrapStr($uid).",null")) {
                $ret = ModelFactory::buildModel("PasswordResetRequest", $result[0]);
            }
        } elseif (isset($args['user_id']) && $args['user_id'] != '') {
            $user_id = $args['user_id'];

            if ($result = PDOWrapper::call("getPasswordResetRequests", "null,".PDOWrapper::cleanse($user_id))) {
                $ret = ModelFactory::buildModel("PasswordResetRequest", $result[0]);
            }
        }
        return $ret;
    }    

    public static function passwordReset($password, $key)
    {
        $reset_request = self::getPasswordResetRequests(array('uid' => $key));
        if ($reset_request->getUserId() == '') {
            return array("result" => 0, "message" => "Incorrect Unique ID. Are you sure you copied the URL correctly?");
        } elseif (self::changePassword($reset_request->getUserId(), $password)) {
            self::removePasswordResetRequest($reset_request->getUserId());
            return array("result" => 1, "message" => "You have successfully changed your password");
        }
    }
    
    public static function getTrackedProjects($user_id)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getTrackedProjects", PDOWrapper::cleanse($user_id))) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Project", $row);
            }
        }
        return $ret;
    }
    public static function trackProject($projectID,$userID)
    {
        $ret = null;
        if ($result = PDOWrapper::call("userTrackProject", PDOWrapper::cleanse($projectID).",".PDOWrapper::cleanse($userID))) {
            $ret = $result[0]["result"];
        }
        return $ret;
    }
    
    public static function unTrackProject($projectID,$userID)
    {
        $ret = null;
        if ($result = PDOWrapper::call("userUnTrackProject", PDOWrapper::cleanse($projectID).",".PDOWrapper::cleanse($userID))) {
            $ret = $result[0]["result"];
        }
        return $ret;
    }
}
