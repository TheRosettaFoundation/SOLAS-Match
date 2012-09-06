<?php

Class UserRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();

        $app->get('/profile/:user_id', array($this, 'userPublicProfile' )
        )->via('POST')->name('user-public-profile');

        $app->get('/profile', array($this, 'userPrivateProfile')
        )->via('POST')->name('user-private-profile');
    }

    public static function userPrivateProfile()
    {
        $app = Slim::getInstance();

        $user_dao = new UserDao();
        $user = $user_dao->getCurrentUser();
        $languages = Languages::getLanguageList();
        $countries = Languages::getCountryList();
        
        if (!is_object($user)) {
            $app->flash('error', 'Login required to access page');
            $app->redirect($app->urlFor('login'));
        }
        
        if($app->request()->isPost()) {
            $displayName = $app->request()->post('name');
            if($displayName != NULL) {
                $user->setDisplayName($displayName);
            }
            
            $userBio = $app->request()->post('bio');
            if($userBio != NULL) {
                $user->setBiography($userBio);
            }
            
            $nativeLang = $app->request()->post('nLanguage');
            $langCountry= $app->request()->post('nLanguageCountry');
            if($nativeLang != NULL&&$langCountry!= NULL) {
                $user->setNativeLanguageID($nativeLang);
                $user->setNativeRegionID($langCountry);
                //assign a badge
                $badge_dao = new BadgeDao();
                $badge = $badge_dao->find(array('badge_id' => Badge::NATIVE_LANGUAGE));
                $badge_dao->assignBadge($user, $badge);
            }
            $user_dao->save($user);
            
            if($user->getDisplayName() != '' && $user->getBiography() != ''
                    && $user->getNativeLanguageID() != '' && $user->getNativeRegionID() != '') {
                $badge_dao = new BadgeDao();
                $badge = $badge_dao->find(array('badge_id' => Badge::PROFILE_FILLER));
                $badge_dao->assignBadge($user, $badge);
            }
            
            $app->redirect($app->urlFor('user-public-profile', array('user_id' => $user->getUserId())));
        }
        
        $app->view()->setData('languages',$languages);
        $app->view()->setData('countries',$countries);
        
       
        $app->render('user-private-profile.tpl');
    }

    public static function userPublicProfile($user_id)
    {
        $app = Slim::getInstance();
        $badge_dao = new BadgeDao();
        $user_dao = new UserDao();
        $user = $user_dao->find(array('user_id' => $user_id));
        
        if($app->request()->isPost()) {
            $post = (object) $app->request()->post();
            
            if(isset($post->badge_id) && $post->badge_id != '') {
                $badge = $badge_dao->find(array('badge_id' => $post->badge_id));
                $badge_dao->removeUserBadge($user, $badge);
            }
                
            if(isset($post->revoke)) {
                $org_id = $post->org_id;
                OrganisationDao::revokeMembership($org_id, $user_id);
            }
        }
                    
        $task_dao = new TaskDao();
        $activeJobs = $task_dao->getUserTasks($user, 10);
                   
        $archivedJobs = $task_dao->getUserArchivedTasks($user, 10);
                   
        $user_tags = $user_dao->getUserTags($user->getUserId());
                    
        $org_dao = new OrganisationDao();
                    
        $orgIds = $user_dao->findOrganisationsUserBelongsTo($user->getUserId());
        $orgList = array();
                    
        if(count($orgIds) > 0) {
            foreach ($orgIds as $orgId) {
                $orgList[] = $org_dao->find(array('id' => $orgId));
            }
        }
                            
        $badgeIds = $user_dao->getUserBadges($user);
        $badges = array();
        $i = 0;
        if(count($badgeIds) > 0) {
            foreach($badgeIds as $badge) {
                $badges[$i] = $badge_dao->find(array('badge_id' => $badge['badge_id']));
                $i++;
            }
        }
            
        $extra_scripts = "<script type=\"text/javascript\" src=\"".$app->urlFor("home");
        $extra_scripts .= "resources/bootstrap/js/confirm-remove-badge.js\"></script>";
              
        $app->view()->setData('orgList',  $orgList);
        $app->view()->appendData(array('badges' => $badges,
                                    'current_page' => 'user-profile',
                                    'activeJobs' => $activeJobs,
                                    'archivedJobs' => $archivedJobs,
                                    'user_tags' => $user_tags,
                                    'this_user' => $user,
                                    'extra_scripts' => $extra_scripts
        ));
                
        if($user_dao->getCurrentUser()->getUserId() === $user_id) {
            $app->view()->appendData(array('private_access' => true));
        }
                    
        $app->render('user-public-profile.tpl');
    }
}
