<?php

require_once '../Common/models/User.php';
require_once '../Common/lib/PDOWrapper.class.php';
require_once '../Common/lib/Authentication.class.php';

class UserDao {
	public function find($params) {
		$query = null;
        $args = "";
		$db = new PDOWrapper();
		$db->init();
                if (isset($params['user_id']) || isset($params['email'])) {
                $args.=(isset($params['user_id'] )&&$params['user_id']!=null)?"{$db->cleanse($params['user_id'])}":"null";
                $args.=(isset($params['password'] )&&$params['password']!=null)?",'{$db->cleanse($params['password'])}'":",null";
                $args.=(isset($params['email'] )&&$params['email']!=null)?",'{$db->cleanse($params['email'])}'":",null";
                $args.=(isset($params['role'] )&&$params['role'] == 'organisation_member')?",1":",0";
                    
                }
		else {
			throw new InvalidArgumentException('Cannot search for user, as no valid parameters were given.');
		}

		$ret = null;
		if ($r = $db->call("userFindByUserData",$args)) {
			$user_data = array(
				'user_id' => $r[0]['user_id'],
				'email' => $r[0]['email'],
				'nonce' => $r[0]['nonce'],
				'display_name' => $r[0]['display_name'],
				'biography' => $r[0]['biography'],
                                'native_lang_id' => $r[0]['native_lang_id'],
                                'native_region_id' => $r[0]['native_region_id']
			);
			$ret = ModelFactory::BuildModel("User", $user_data);
		}
		return $ret;
	}

	public function create($email, $clear_password) {
		if (is_object($this->find(array('email' => $email)))) {
			throw new InvalidArgumentException('Oops, you already have an account here with that email address. Please log in instread.');
		}

		$nonce = Authentication::generateNonce();
		$password = Authentication::hashPassword($clear_password, $nonce);
		
		$user_data = array(
			'email' => $email,
			'nonce' => $nonce,
			'password' => $password
		);

		$user = ModelFactory::BuildModel("User", $user_data);
		return $this->save($user);
	}

    public function changePassword($user_id, $password)
    {
        $user = $this->find(array('user_id' => $user_id));

        $nonce = Authentication::generateNonce();
        $pass = Authentication::hashPassword($password, $nonce);

        $user->setNonce($nonce);
        $user->setPassword($pass);

        return $this->save($user);
    }

    public function save($user) {
            if (is_null($user->getUserId())) {
                    return $this->_insert($user);
            }
            else {
                    return $this->_update($user);
            }
    }

    private function _update($user) {
            $db = new PDOWrapper();
            $db->init();
            $result = $db->call('userInsertAndUpdate', "{$db->cleanseNullOrWrapStr($user->getEmail())},
            {$db->cleanseNull($user->getNonce())},{$db->cleanseNullOrWrapStr($user->getPassword())},
            {$db->cleanseNullOrWrapStr($user->getBiography())},{$db->cleanseNullOrWrapStr($user->getDisplayName())},
            {$db->cleanseNullOrWrapStr($user->getNativeLangId())},{$db->cleanseNullOrWrapStr($user->getNativeRegionId())},
            {$db->cleanse($user->getUserId())}");
            return $this->find(array('user_id' => $result[0]['user_id']));
    }

    private function _insert($user) {
            $db = new PDOWrapper();
            $db->init();
            if ($user_id = $db->call('userInsertAndUpdate', "{$db->cleanseNullOrWrapStr($user->getEmail())},{$db->cleanse($user->getNonce())},{$db->cleanseNullOrWrapStr
                    ($user->getPassword())},NULL,NULL,NULL,NULL,NULL")) {
                    return $this->find(array('user_id' => $user_id[0]['user_id']));
            }
            else {
                    return null;
            }
    }

    private function clearPasswordMatchesUsersPassword($user, $clear_password) {
            $hashed_input_password = Authentication::hashPassword($clear_password, $user->getNonce());

            return is_object(
                            $this->find(array(
                                    'user_id' => $user->getUserId(),
                                    'password' => $hashed_input_password
                            ))
            );
    }

    public function login($email, $clear_password) {
            $user = $this->find(array('email' => $email));

            if (!is_object($user)) {
                    throw new InvalidArgumentException('Sorry, the  password or username entered is incorrect. Please check the credentials used and try again.');
            }

            if (!$this->clearPasswordMatchesUsersPassword($user, $clear_password)) {
                    throw new InvalidArgumentException('Sorry, the  password or username entered is incorrect. Please check the credentials used and try again.');
            }

    if ($clear_password === '') {
        throw new InvalidArgumentException('Sorry, an empty password is not allowed. Please contact the site administrator for details');
    }

            UserSession::setSession($user->getUserId());

            return true;
    }

    public function APIlogin($email, $clear_password) {
            $user = $this->find(array('email' => $email));

            if (!is_object($user)) {
                    return array("error"=>'Sorry, the  password or username entered is incorrect. Please check the credentials used and try again.');
            }

            if (!$this->clearPasswordMatchesUsersPassword($user, $clear_password)) {
                    return array("error"=>'Sorry, the  password or username entered is incorrect. Please check the credentials used and try again.');
            }

            return $user;
    }

     public function APIRegister($email, $clear_password) {
            $user = $this->find(array('email' => $email));

            if (!is_object($user)&& $clear_password!="") {
                    $user = $this->create($email,$clear_password);
                    $badge_dao = new BadgeDao();
                    $badge = $badge_dao->find(array('badge_id' => BadgeTypes::REGISTERED));
                    $badge_dao->assignBadge($user, $badge);
            }else $user = null;//array("message"=>'sorry the account you enerted already exists. \n please login.',"status code"=>500);
            return $user;
    }



    public function OpenIDLogin($openid,$app) {
        if(!$openid->mode) {
            try {
            $openid->identity = $openid->data['openid_identifier'];
            $openid->required = array('contact/email');
            $url =$openid->authUrl();
            $app->redirect($openid->authUrl());
            }catch(ErrorException $e) {
                echo $e->getMessage();
            }
        } elseif($openid->mode == 'cancel') {
            throw new InvalidArgumentException('User has canceled authentication!');
            return false;
        } else {
            $retvals= $openid->getAttributes();
            if($openid->validate()){
               $user = $this->find(array('email' => $retvals['contact/email']));
                if (!is_object($user)) {
                    $user = $this->create($retvals['contact/email'],md5($retvals['contact/email']));
                    $badge_dao = new BadgeDao();
                    $badge = $badge_dao->find(array('badge_id' => BadgeTypes::REGISTERED));
                    $badge_dao->assignBadge($user, $badge);
                }
                UserSession::setSession($user->getUserId());
            }
            return true;
        }


    }

    public function logout() {
            UserSession::destroySession();
    }

    public function getCurrentUser() {
            $ret = null;
            if ($user_id = UserSession::getCurrentUserId()) {
                    $ret = $this->find(array('user_id' => $user_id));
            }
            return $ret;
    }

    public static function isLoggedIn()
    {
            return (!is_null(UserSession::getCurrentUserId()));
    }

    public function belongsToRole($user, $role) {
            $ret = false;
            if ($role == 'translator') {
                    $ret = true;
            }
            else if ($role == 'organisation_member') {
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

    public function findOrganisationsUserBelongsTo($user_id) 
    {
        $ret = null;
        $db = new PDOWrapper();
        $db->init();
        if ($result = $db->call("findOrganisationsUserBelongsTo", $db->cleanse($user_id))) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::BuildModel("Organisation", $row);
            }
        }
        return $ret;
    }

    public function getUserBadges(User $user) {
            return $this->getUserBadgesbyID($user->getUserId());
    }

    public function getUserBadgesbyID($user_id) {
            $ret = array();
            $db = new PDOWrapper();
            $db->init();
            if ($result = $db->call("getUserBadges", $db->cleanse($user_id))) {
                foreach ($result as $badge) {
                   $ret[] = ModelFactory::BuildModel("Badge", $badge);
                }
            }
            return $ret;
    }

    public function getUserTags($user_id,$limit=null)
    {
        $ret = null;
        $db = new PDOWrapper();
        $db->init();
        if($result = $db->call("getUserTags", "{$db->cleanse($user_id)},{$db->cleanseNull($limit)}")) {
            $ret = array();
            foreach($result as $row) {
                $ret[] = ModelFactory::BuildModel("Tag", $row);
            }
        }

        return $ret;
    }
    
    public function getUser($user_id, $email,$nonce,$password,$display_name,$biography,$native_language_id, $native_region_id,$created){
        $ret = null;
        $db = new PDOWrapper();
        $db->init();
        if($result = $db->call("getUser", "{$db->cleanseNull($user_id)},{$db->cleanseNullOrWrapStr($display_name)},{$db->cleanseNullOrWrapStr($email)}"
        .",{$db->cleanseNullOrWrapStr($password)},{$db->cleanseNullOrWrapStr($biography)},{$db->cleanseNull($nonce)},{$db->cleanseNull($created)}"
        .",{$db->cleanseNull($native_language_id)},{$db->cleanseNull($native_region_id)}")) {
            $ret = array();
            foreach($result as $row) {
                $ret[] = ModelFactory::BuildModel("User", $row);
            }
        }
        return $ret;
        
    }
    /*
        Get all users with $badge assigned
    */
    
    
    public function getUsersWithBadgeByID($badge_ID) {
        $ret = null;
        $db = new PDOWrapper();
        $db->init();
        if($result = $db->call("getUsersWithBadge", "{$db->cleanse($badge_ID)}")) {
            $ret = array();
            foreach($result as $row) {
                $ret[] = ModelFactory::BuildModel("User", $row);
            }
        }
        return $ret;
    }
    
    public function getUsersWithBadge($badge) {
        return $this->getUsersWithBadgeByID($badge->getId());
    }
    /*
        Add the tag to a list of the user's preferred tags
    */
    public function likeTag($user_id, $tag_id)
    {
        $db = new PDOWrapper();
        $db->init();
        $args = array();
        $args['user_id'] = $db->cleanse($user_id);
        $args['tag_id'] = $db->cleanse($tag_id);
        if ($result = $db->call("userLikeTag", $args)) {
            return $result[0]['result'];
        }
        return 0;
    }

    /*
        The opposite of likeTag
    */
    public function removeTag($user_id, $tag_id)
    {
        $ret = false;
        $db= new PDOWrapper();
        $db->init();
        if ($result = $db->call("removeUserTag", "{$db->cleanse($user_id)},{$db->cleanse($tag_id)}")) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }

    /*
        Get list of tasks in User's notification list
    */
    public function getUserNotificationList($user_id) 
    {
        $ret = null;
        $db = new PDOWrapper();
        $db->init();
        $args = array();
        $args['id'] = $user_id;
        if($return = $db->call('getUserNotifications', $args)) {
            $ret = $return;
        }

        return $ret;
    }

    /*
        returns true if the user has registered for notifications for this task
    */
    public function isSubscribedToTask($user_id, $task_id)
    {
        $ret = false;
        $db = new PDOWrapper();
        $db->init();
        $args = array();
        $args[] = $user_id;
        $args[] = $task_id;
        if($result = $db->call('userSubscribedToTask', $args)) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }

    /*
        Add the task to the user's notification List
    */
    public function trackTask($user_id, $task_id)
    {
        $ret = false;
        $db = new PDOWrapper();
        $db->init();
        $args = array();
        $args['user_id'] = $user_id;
        $args['task_id'] = $task_id;
        if ($result = $db->call("userNotificationsInsertAndUpdate", $args)) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }

    /*
        Remove the task from the user's notification list
    */
    public function ignoreTask($user_id, $task_id)
    {
        $ret = false;
        $db = new PDOWrapper();
        $db->init();
        $args = array();
        $args['user_id'] = $user_id;
        $args['task_id'] = $task_id;
        if($result = $db->call("removeUserNotification", $args)) {
            $ret = $result[0]['result'];
        }

        return $ret;
    }
    
     public function getTrackedTasks($user_id)
    {
        $ret = array();
        $db = new PDOWrapper();
        $db->init();
        $dao = new TaskDao();
        if($result = $db->call("getUserTrackedTasks", "$user_id")) {
            foreach($result as $row) {
                $params = array();
                $params['task_id'] = $row['id'];
                $params['title'] = $row['title'];
                $params['impact'] = $row['impact'];
                $params['reference_page'] = $row['reference_page'];
                $params['organisation_id'] = $row['organisation_id'];
                $params['source_id'] = $row['source_id'];
                $params['target_id'] = $row['target_id'];
                $params['word_count'] = $row['word_count'];
                $params['created_time'] = $row['created_time'];
                $task = new Task($params);
                $task->setStatus($dao->getTaskStatus($task->getTaskId()));
                $ret[] = $task;
            }
        }

        return $ret;
    }

    public function createPasswordReset($user_id){
        if(!$this->hasRequestedPasswordResetID($user_id)) {       
            $uid = md5(uniqid(rand()));
            $this->addPasswordResetRequest($uid, $user_id);   
            Notify::sendPasswordResetEmail($uid, $this->getUser($user_id, null, null, null, null, null, null, null, null));
            return 1;
        }
        return 0;
    }
    
    
    /*
        Add password reset request to DB for this user
    */
    public function addPasswordResetRequest($unique_id, $user_id)
    {
        $db = new PDOWrapper();
        $db->init();
        $db->call("addPasswordResetRequest", "{$db->cleanseWrapStr($unique_id)}, {$db->cleanse($user_id)}");
    }

    public function removePasswordResetRequest($user_id)
    {
        $db = new PDOWrapper();
        $db->init();
        $db->call("removePasswordResetRequest", "{$db->cleanse($user_id)}");
    }

    /*
        Check if a user has requested a password reset
    */
    public function hasRequestedPasswordReset($user)
    {
        return $this->hasRequestedPasswordResetID($user->getUserId());
    }
    
     public function hasRequestedPasswordResetID($user_id)
    {
        $ret = false;
        if($this->getPasswordResetRequests(array('user_id'=>$user_id))) {
            $ret = true;
        }
        return $ret;
    }

    /*
        Get Password Reset Requests
    */
    public function getPasswordResetRequests($args)
    {
        $ret = false;
        $db = new PDOWrapper();
        $db->init();
        if(isset($args['uid']) && $args['uid'] != '') {
            $uid = $args['uid'];
            if($result = $db->call("getPasswordResetRequests", "{$db->cleanseWrapStr($uid)}, null")) {
                $ret = ModelFactory::BuildModel("PasswordResetRequest", $result[0]);
            }
        } elseif(isset($args['user_id']) && $args['user_id'] != '') {
            $user_id = $args['user_id'];
            if($result = $db->call("getPasswordResetRequests", "null, {$db->cleanse($user_id)}")) {
                $ret = ModelFactory::BuildModel("PasswordResetRequest", $result);
            }
        }

        return $ret;
    }
    
     public function passwordReset($password,$key){
                $dao = new UserDao;
                $reset_request = $dao->getPasswordResetRequests(array('uid' => $key));
                if($reset_request->getUserId() == '') {
                    return array("result"=>0, "message"=> "Incorrect Unique ID. Are you sure you copied the URL correctly?");
                }elseif($dao->changePassword($reset_request->getUserId(), $password)) {
                        $dao->removePasswordResetRequest($reset_request->getUserId());
                        return array("result"=>1,"message"=>"You have successfully changed your password");
                     }   
    }
}
