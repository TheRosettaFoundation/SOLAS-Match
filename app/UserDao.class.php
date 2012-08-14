<?php

require('models/User.class.php');

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
                'native_language' => $r[0]['native_language']
			);
			$ret = new User($user_data);
		}
		return $ret;
	}

	public function create($email, $clear_password) {
		if (!User::isValidEmail($email)) {
			throw new InvalidArgumentException('Please check the email provided, and try again. It was not found to be valid.');
		}
		else if (!User::isValidPassword($clear_password)) {
			throw new InvalidArgumentException('Please check the password provided, and try again. It was not found to be valid.');
		}
		else if (is_object($this->find(array('email' => $email)))) {
			throw new InvalidArgumentException('Oops, you already have an account here with that email address. Please log in instread.');
		}

		$nonce = Authentication::generateNonce();
		$password = Authentication::hashPassword($clear_password, $nonce);
		
		$user_data = array(
			'email' => $email,
			'nonce' => $nonce,
			'password' => $password
		);
		$user = new User($user_data);
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
                $result = $db->call('userInsertAndUpdate', "{$db->cleanseNullOrWrapStr($user->getEmail())},{$db->cleanse($user->getNonce())},{$db->cleanseNullOrWrapStr($user->getPassword())},{$db->cleanseNullOrWrapStr($user->getBiography())},{$db->cleanseNullOrWrapStr($user->getDisplayName())},{$db->cleanseNullOrWrapStr($user->getNativeLanguage())},{$db->cleanse($user->getUserId())}");
                return $result[0]['user_id'];
	}

	private function _insert($user) {
		$db = new PDOWrapper();
		$db->init();
		if ($user_id = $db->call('userInsertAndUpdate', "{$db->cleanseNullOrWrapStr($user->getEmail())},{$db->cleanse($user->getNonce())},{$db->cleanseNullOrWrapStr
                        ($user->getPassword())},NULL,NULL,NULL,NULL")) {
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
			throw new InvalidArgumentException('Sorry, we could not find an account for that email address. Please check the provided address, or register for an account.');
		}

		if (!$this->clearPasswordMatchesUsersPassword($user, $clear_password)) {
			throw new InvalidArgumentException('Sorry, that password is incorrect. Please try again.');
		}

                if ($clear_password === '') {
                    throw new InvalidArgumentException('Sorry, an empty password is not allowed. Please contact the site administrator for details');
                }

		UserSession::setSession($user->getUserId());

		return true;
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
                        $badge = $badge_dao->find(array('badge_id' => Badge::REGISTERED));
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

	public function findOrganisationsUserBelongsTo($user_id) {
		$ret = null;
		$db = new PDOWrapper();
		$db->init();
		if ($result = $db->call("findOrganisationsUserBelongsTo", $db->cleanse($user_id))) {
			$ret = array();
			foreach ($result as $row) {
				$ret[] = $row['organisation_id'];
			}
		}
		return $ret;
	}

	public function getUserBadges(User $user) {
		$ret = NULL;
		$db = new PDOWrapper();
		$db->init();
		if ($result = $db->call("getUserBadges", $db->cleanse($user->getUserId()))) {
			$ret = $result;
		}

		return $ret;
	}

    public function getUserTags($user_id)
    {
        $ret = null;
        $db = new PDOWrapper();
        $db->init();
        if($result = $db->call("getUserTags", $db->cleanse($user_id))) {
            $ret = array();
            foreach($result as $row) {
                $ret[] = $row['label'];
            }
        }

        return $ret;
    }

    /*
        Add the tag to a list of the user's preferred tags
    */
    public function likeTag($user_id, $tag_id)
    {
        $ret = false;
        $db = new PDOWrapper();
        $db->init();
        if ($result = $db->call("userLikeTag", "{$db->cleanse($user_id)},{$db->cleanse($tag_id)}")) {
            $ret = $result[0]['result'];
        }
        return $ret;
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
}
