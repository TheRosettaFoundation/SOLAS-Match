<?php

namespace SolasMatch\UI\Lib;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\Common as Common;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require_once __DIR__."/../../Common/lib/APIHelper.class.php";

class Middleware
{
    public function SessionCookie(Request $request, RequestHandler $handler)
    {
        if (session_id() === '') {
            error_log('session_start()');
            session_start();
        }

        $cookies = $request->getCookieParams();
        if (!empty($cookies['slim_session'])) {
            $secret = Common\Lib\Settings::get('session.site_key');
            $value = explode('|', $cookies['slim_session']);
            if (count($value) === 3 && ((int)$value[0] === 0 || (int)$value[0] > time())) {
                $key = hash_hmac('sha1', $value[0], $secret);
                $iv = self::getIv($value[0], $secret);
                $plainString = '';
                $data = base64_decode($value[1]);
                if (!empty($data)) {
                    $ivSize = 16;
                    if (strlen($iv) > $ivSize) {
                        $iv = substr($iv, 0, $ivSize);
                    }
                    $keySize = 16;
                    if (strlen($key) > $keySize) {
                        $key = substr($key, 0, $keySize);
                    }
                    $plainString = openssl_decrypt($data, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
                    $plainString = rtrim($plainString, "\0");
                }
                $verificationString = hash_hmac('sha1', $value[0] . $plainString, $key);
                if ($verificationString === $value[2]) {
                    try {
                        $_SESSION = unserialize($plainString);
                    } catch (\Exception $e) {
                        $_SESSION = [];
                    }
                } else {
                    $_SESSION = [];
                }
            } else {
                $_SESSION = [];
            }
        } else {
            $_SESSION = [];
        }

        $response = $handler->handle($request);

        $plainString = serialize($_SESSION);

        if (strlen($plainString) > 4096) {
            error_log('WARNING! SessionCookie data size is larger than 4KB. Content save failed.');
            $value = '';
        } else {
            $expires = strtotime('12 hours');
            $key = hash_hmac('sha1', $expires, $secret);
            $iv = self::getIv($expires, $secret);
            $ivSize = 16;
            if (strlen($iv) > $ivSize) {
                $iv = substr($iv, 0, $ivSize);
            }
            $keySize = 16;
            if (strlen($key) > $keySize) {
                $key = substr($key, 0, $keySize);
            }
            $secureString = base64_encode(openssl_encrypt($plainString, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv));
            $verificationString = hash_hmac('sha1', $expires . $plainString, $key);
            $value = implode('|', array($expires, $secureString, $verificationString));
        }
        return $response->withAddedHeader('Set-Cookie', 'slim_session=' . urlencode($value) . '; path=/; expires=' . gmdate('D, d-M-Y H:i:s e', $expires));
    }

    private static function getIv($expires, $secret)
    {
        $data1 = hash_hmac('sha1', 'a' . $expires . 'b', $secret);
        $data2 = hash_hmac('sha1', 'z' . $expires . 'y', $secret);

        return pack("h*", $data1 . $data2);
    }

    public function beforeDispatch(Request $request, RequestHandler $handler)
    {
       global $template_data;

        if (!is_null($token = Common\Lib\UserSession::getAccessToken()) && $token->getExpires() <  time()) {
            Common\Lib\UserSession::clearCurrentUserID();
        }

        $userDao = new DAO\UserDao();
        if (!is_null(Common\Lib\UserSession::getCurrentUserID())) {
            $current_user = $userDao->getUser(Common\Lib\UserSession::getCurrentUserID());
            if (!is_null($current_user)) {
                $template_data = array_merge($template_data, ['user' => $current_user]);
                $org_array = $userDao->getUserOrgs(Common\Lib\UserSession::getCurrentUserID());
                if ($org_array && count($org_array) > 0) {
                    $template_data = array_merge($template_data, ['user_is_organisation_member' => true]);
                }

                $tasks = $userDao->getUserTasks(Common\Lib\UserSession::getCurrentUserID());
                if ($tasks && count($tasks) > 0) {
                    $template_data = array_merge($template_data, ['user_has_active_tasks' => true]);
                }
                $adminDao = new DAO\AdminDao();
                $isAdmin = $adminDao->isSiteAdmin(Common\Lib\UserSession::getCurrentUserID());
                if ($isAdmin) {
                    $template_data = array_merge($template_data, ['site_admin' => true]);
                }
            } else {
                Common\Lib\UserSession::clearCurrentUserID();
                Common\Lib\UserSession::clearAccessToken();
            }
        }
        $template_data = array_merge($template_data, ['locs' => Lib\Localisation::loadTranslationFiles()]);

        return $handler->handle($request);
    }

    public function Flash(Request $request, RequestHandler $handler)
    {
        global $flash_messages;
        $flash_messages = [
            'prev' => [], // Flash messages from prev request (loaded when middleware called)
            'next' => [], // Flash messages for next request
            'now'  => []  // Flash messages for current request
        ];

        // Read flash messaging from previous request if available
        if (isset($_SESSION['slim.flash'])) {
            $flash_messages['prev'] = $_SESSION['slim.flash'];
        }

        $response = $handler->handle($request);

        $_SESSION['slim.flash'] = $flash_messages['next'];

        return $response;
    }

    public function authUserIsLoggedIn(Request $request, RequestHandler $handler)
    {
        global $app;

        $this->isUserBanned();
        if (!Common\Lib\UserSession::getCurrentUserID()) {
            Common\Lib\UserSession::setReferer($request->getUri());
            \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_login_required_to_access_page'));
            return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('login'));
        }

        if (empty($_SESSION['profile_completed']) || $_SESSION['profile_completed'] == 2) {
            $userDao = new DAO\UserDao();
            if (!$userDao->is_admin_or_org_member($_SESSION['user_id'])) {
                \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', 'You must fill in your profile before continuing');
                return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('user-private-profile', array('user_id' => $_SESSION['user_id'])));
            }
        } elseif ($_SESSION['profile_completed'] == 1) {
            error_log('authUserIsLoggedIn() redirecting to googleregister, user_id: ' . $_SESSION['user_id']);
            \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', 'You must accept the Code of Conduct before continuing'); // Since they are logged in (via Google)...
            return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('googleregister', array('user_id' => $_SESSION['user_id'])));
        }

        return $handler->handle($request);
    }

    public function authUserIsLoggedInNoProfile(Request $request, RequestHandler $handler)
    {
        global $app;

        $this->isUserBanned();
        if (!Common\Lib\UserSession::getCurrentUserID()) {
            Common\Lib\UserSession::setReferer($request->getUri());
            \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_login_required_to_access_page'));
            return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('login'));
        }

        if (!empty($_SESSION['profile_completed']) && $_SESSION['profile_completed'] == 1 && !strpos($app->request()->getPathInfo(), '/googleregister')) {
            error_log('authUserIsLoggedInNoProfile() redirecting to googleregister, user_id: ' . $_SESSION['user_id']);
            \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', 'You must accept the Code of Conduct before continuing'); // Since they are logged in (via Google)...
            return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('googleregister', array('user_id' => $_SESSION['user_id'])));
        }

        return $handler->handle($request);
    }
    
    public function isSiteAdmin()
    {
        $this->isUserBanned();
        if (is_null(Common\Lib\UserSession::getCurrentUserID())) {
            return false;
        }
        $adminDao = new DAO\AdminDao();
        return $adminDao->isSiteAdmin(Common\Lib\UserSession::getCurrentUserID());
    }

    public function authIsSiteAdmin(Request $request, RequestHandler $handler)
    {
        global $app;

        if ($this->isSiteAdmin()) {
            return $handler->handle($request);
        }

        \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_login_required_to_access_page'));

        Common\Lib\UserSession::setReferer($request->getUri());
        return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('login'));
    }

    public function authenticateUserForTask(\Slim\Route $route;;;;;;;;;;; Request $request, RequestHandler $handler)
    {
        global $app;

        if ($this->isSiteAdmin()) {
            return $handler->handle($request);
        }

        $taskDao = new DAO\TaskDao();
        $params = $route->getParams();

        $this->authUserIsLoggedIn();
        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $claimant = null;
        if ($params !== null) {
            $task_id = $params['task_id'];
            $claimant = $taskDao->getUserClaimedTask($task_id);
        }
        if ($claimant) {
            if ($user_id != $claimant->getId()) {
//error_log("Already claimed... task_id: $task_id, user_id: $user_id, claimant: " . $claimant->getId());
                \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_error_already_claimed'));
                return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
            }
        }
    }

    public function authUserForOrg(\Slim\Route $route;;;;;;;   Request $request, RequestHandler $handler)
    {
        global $app;

        if ($this->isSiteAdmin()) {
            return $handler->handle($request);
        }
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();

        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $params = $route->getParams();
        if ($params !== null) {
            $org_id = $params['org_id'];
            if ($user_id) {
                $user_orgs = $userDao->getUserOrgs($user_id);
                if (!is_null($user_orgs)) {
                    foreach ($user_orgs as $orgObject) {
                        if ($orgObject->getId() == $org_id) {
                            return $handler->handle($request);
                        }
                    }
                }
            }
        }
        
        \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_error_not_exist'));
        return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
    }

    /*
     *  Middleware for ensuring the current user belongs to the Org that uploaded the associated Task
     *  Used for altering task details
     */

    public function authUserForOrgTask(\Slim\Route $route;;;;;;;;;; Request $request, RequestHandler $handler)
    {
        global $app;

        if ($this->isSiteAdmin()) {
            return $handler->handle($request);
        }

        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $userDao = new DAO\UserDao();

        $params= $route->getParams();
        if ($params != null) {
            $task_id = $params['task_id'];
            $task = $taskDao->getTask($task_id);
            $project = $projectDao->getProject($task->getProjectId());
            
            $org_id = $project->getOrganisationId();
            $user_id = Common\Lib\UserSession::getCurrentUserID();

            if ($user_id) {
                $user_orgs = $userDao->getUserOrgs($user_id);
                if (!is_null($user_orgs)) {
                    foreach ($user_orgs as $orgObject) {
                        if ($orgObject->getId() == $org_id) {
                            return $handler->handle($request);
                        }
                    }
                }
            }
        }
       
        \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_error_not_exist'));
        return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
    }
    

    public function authUserForOrgProject(\Slim\Route $route;;;;;;;;;;;;;   Request $request, RequestHandler $handler)
    {
        global $app;

        if ($this->isSiteAdmin()) {
            return $handler->handle($request);
        }

        $params = $route->getParams();
        $userDao = new DAO\UserDao();
        $projectDao = new DAO\ProjectDao();
        
        if ($params != null) {
            $user_id = Common\Lib\UserSession::getCurrentUserID();
            $project_id = $params['project_id'];
            $userOrgs = $userDao->getUserOrgs($user_id);
            $project = $projectDao->getProject($project_id);
            $project_orgid = $project->getOrganisationId();

            if ($userOrgs) {
                foreach ($userOrgs as $org) {
                    if ($org->getId() == $project_orgid) {
                        return $handler->handle($request);
                    }
                }
            }
        }
        \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_error_not_exist'));
        return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
    }

    public function authUserForProjectImage(\Slim\Route $route;;;;;;;;; Request $request, RequestHandler $handler)
    {
        global $app;

        if ($this->isSiteAdmin()) {
            return $handler->handle($request);
        }
        
        $params = $route->getParams();
        $userDao = new DAO\UserDao();
        $projectDao = new DAO\ProjectDao();
        
        if ($params != null) {
            $project_id = $params['project_id'];
            $project = $projectDao->getProject($project_id);
            $projectImageApprovedAndUploaded = $project->getImageApproved() && $project->getImageUploaded();
            
            if ($projectImageApprovedAndUploaded) {
                return $handler->handle($request);
            }
        }
        \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_error_not_exist'));
        return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
    }

    public function authUserForTaskDownload(\Slim\Route $route;;;;;;;;;;;; Request $request, RequestHandler $handler)
    {
        global $app;

        if ($this->isSiteAdmin()) {
            return $handler->handle($request);
        }

        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $userDao = new DAO\UserDao();

        $params= $route->getParams();
        if ($params != null) {
            $task_id = $params['task_id'];
            $task = $taskDao->getTask($task_id);
            if ($taskDao->getUserClaimedTask($task_id)) {
                return $handler->handle($request);
            }

            $project = $projectDao->getProject($task->getProjectId());
            $org_id = $project->getOrganisationId();
            $user_id = Common\Lib\UserSession::getCurrentUserID();

            if ($user_id) {
                $user_orgs = $userDao->getUserOrgs($user_id);
                if (!is_null($user_orgs)) {
                    foreach ($user_orgs as $orgObject) {
                        if ($orgObject->getId() == $org_id) {
                            return $handler->handle($request);
                        }
                    }
                }
            }
        }
       
        \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_error_not_exist'));
        return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
    }
    
    public function isUserBanned()
    {
        global $app;

        $adminDao = new DAO\AdminDao();
        if ($adminDao->isUserBanned(Common\Lib\UserSession::getCurrentUserID())) {
            Common\Lib\UserSession::destroySession();
            \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_this_user_account_has_been_banned'));
            return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }
    }
    
    public function isBlacklisted(\Slim\Route $route ;;;; Request $request, RequestHandler $handler)
    {
        global $app;

        $isLoggedIn = $this->authUserIsLoggedIn();
        if ($isLoggedIn) {
            $params = $route->getParams();
            if (!is_null($params)) {
                $taskId = $params['task_id'];
                $userId = Common\Lib\UserSession::getCurrentUserID();
                $userDao = new DAO\UserDao();
                $isBlackListed = $userDao->isBlacklistedForTask($userId, $taskId);
                
                //Is the user blacklisted for the task?
                if ($isBlackListed) {
                    $taskDao = new DAO\TaskDao();
                    $task = $taskDao->getTask($taskId);
                    $message = null;
                    
                    $isBlackListedByAdmin = $userDao->isBlacklistedForTaskByAdmin($userId, $taskId);
                    if (!$isBlackListedByAdmin) {
                        //If it is a desegmentation task, user must have been blacklisted for it because they
                        //have worked on a prerequisite task for it.
                        if ($task->getTaskType() == Common\Enums\TaskTypeEnum::DESEGMENTATION) {
                            $message = Localisation::getTranslation("common_error_cannot_claim_desegmentation");
                        } else {
                            $message = Localisation::getTranslation('common_error_cannot_reclaim');
                        }
                    } else {
                        //An admin has previously revoked this task from the user.
                        $message = Localisation::getTranslation('common_error_cannot_reclaim_admin_revoked');
                    }
                    \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash(
                        'error',
                        sprintf(
                            $message,
                            $app->urlFor("task-claimed", array("task_id" => $taskId)),
                            $task->getTitle()
                        )
                    );
                    $app->response()->redirect($app->urlFor('home'));
                } else {
                    return $handler->handle($request);
                }
            }
        }
    }
}
