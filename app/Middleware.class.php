<?php

class Middleware
{
    public static function authUserIsLoggedIn()
    {
        $app = Slim::getInstance();
        
        $user_dao = new UserDao();
        if(!is_object($user_dao->getCurrentUser())) {
            $app->flash('error', "Login required to access page");
            $app->redirect($app->urlFor('login'));
        }
        
        return true;
    }

}
