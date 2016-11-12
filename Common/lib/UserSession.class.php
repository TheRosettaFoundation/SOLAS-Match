<?php

namespace SolasMatch\Common\Lib;

use \SolasMatch\UI\Lib as Lib;

class UserSession
{
    public static function setSession($user_id)
    {
        $_SESSION['user_id'] = $user_id;
    }

    public static function destroySession()
    {
        unset($_SESSION['user_id']);
        self::clearAccessToken();
    }

    public static function getCurrentUserID()
    {
        if (isset($_SESSION['user_id']) && self::isValidUserId($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        } else {
            return null;
        }
    }
    
    public static function clearCurrentUserID()
    {
        if (isset($_SESSION['user_id'])) {
            unset($_SESSION['user_id']);
        }
    }

    public static function isValidUserId($user_id)
    {
        return (is_numeric($user_id) && $user_id > 0);
    }
    
    public static function setUserLanguage($lang)
    {
        $_SESSION['siteLanguage'] = $lang;
    }
    
    public static function getUserLanguage()
    {
        if (isset($_SESSION['siteLanguage'])) {
            return $_SESSION['siteLanguage'];
        } else {
            return null;
        }
    }
    
    public static function setReferer($ref)
    {
        $_SESSION['ref'] = $ref;
    }

    public static function getReferer()
    {
        if (isset($_SESSION['ref'])) {
            return $_SESSION['ref'];
        } else {
            return null;
        }
    }

    public static function clearReferer()
    {
        if (isset($_SESSION['ref'])) {
            unset($_SESSION['ref']);
        }
    }

    public static function setAccessToken($token)
    {
        $_SESSION['AccessToken'] = $token;
    }
    
    public static function getAccessToken()
    {
        if (isset($_SESSION['AccessToken'])) {
            return $_SESSION['AccessToken'];
        } else {
            return null;
        }
    }

    public static function clearAccessToken()
    {
        if (isset($_SESSION['AccessToken'])) {
            unset($_SESSION['AccessToken']);
        }
    }

    public static function registerWithSmarty()
    {
        \Slim\Slim::getInstance()->view()->getInstance()->registerClass('UserSession', __NAMESPACE__.'\UserSession');
    }

    /**
     * Get Key for using in Form Posts and subsequent (when POST recieved) security check against CSRF.
     *
     * @return string
     */
    public static function getCSRFKey() {
        if (empty($_SESSION['SESSION_CSRF_KEY'])) {
            $_SESSION['SESSION_CSRF_KEY'] = self::random_string(10);
        }
        return $_SESSION['SESSION_CSRF_KEY']; // This is a check against CSRF (Posts should come back with same sesskey)
    }

    /**
     * Check Key returned from Post matches $_SESSION['SESSION_CSRF_KEY'].
     *
     * @param string $post sesskey returned from browser by GET | array $post POST Parameters
     * @param string $location.
     * @return void, will error_log() and redirect if test fails.
     */
    public static function checkCSRFKey($post, $location) {
        $is_a_post = false;

        if (is_array($post)) {
            if (!empty($post['sesskey'])) $postKey = $post['sesskey'];
            else                          $postKey = '9999999999';

            $is_a_post = true;
        }
        elseif (is_string($post)) {
            $postKey = $post;
        }
        else {
            $postKey = '8888888888';
        }

        if (empty($postKey) || $postKey !== $_SESSION['SESSION_CSRF_KEY']) {
            $expected = $_SESSION['SESSION_CSRF_KEY'];
            error_log("CSRF attempt identified!: $location, sesskey: $postKey ($expected)");
            if (is_array($post)) {
                error_log('POST...');
                foreach ($post as $key => $item) {
                    error_log("$key => $item");
                }
            }

            if (!empty($_FILES) && is_array($_FILES)) {
                error_log('FILES...');
                foreach ($_FILES as $file) {
                    if (is_array($file)) {
                        error_log('FILE...');
                        foreach ($file as $key => $item) {
                            error_log("$key => $item");
                        }
                    }
                }
            }

            error_log($_SERVER['REQUEST_URI']);

            $app = \Slim\Slim::getInstance();
            $app->flash('error', Lib\Localisation::getTranslation('common_error_partial_upload')); // This is most likely reason: FILE UPLOAD error => 3, The uploaded file was only partially uploaded
            if ($is_a_post) {
                $app->redirect($_SERVER['REQUEST_URI']); // Will be a GET
            }
            else {
                $app->redirect($app->urlFor('home'));
            }

//            throw new \Exception("CSRF attempt identified!: $location");
        }
    }

    /**
     * Generate and return a random string of the specified length.
     *
     * @param int $length The length of the string to be created.
     * @return string
     */
    public static function random_string($length=15) {
        $pool  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pool .= 'abcdefghijklmnopqrstuvwxyz';
        $pool .= '0123456789';
        $poollen = strlen($pool);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($pool, (mt_rand()%($poollen)), 1);
        }
        return $string;
    }
}
