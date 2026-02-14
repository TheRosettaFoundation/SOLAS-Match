<?php
namespace SolasMatch\UI\Lib;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\Common as Common;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

require_once __DIR__."/../../Common/lib/APIHelper.class.php";

class Middleware
{
    public function SessionCookie(Request $request, RequestHandler $handler)
    {
        if (session_id() === '') {
            session_start();
        }

        $cookies = $request->getCookieParams();
        $secret = Common\Lib\Settings::get('session.site_key');
        if (!empty($cookies['slim_session'])) {
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
        if (!is_null($current_user_id = Common\Lib\UserSession::getCurrentUserID())) {
            $current_user = $userDao->getUser($current_user_id);
            if (!is_null($current_user)) {
              try {
                $template_data = array_merge($template_data, ['user' => $current_user]);
                $adminDao = new DAO\AdminDao();
                $roles = $adminDao->isSiteAdmin_any_or_org_admin_any_for_any_org($current_user_id);
                if ($roles) $template_data = array_merge($template_data, ['dashboard' => 1]);
                $tasks = $userDao->getUserTasks($current_user_id);
                if (!empty($tasks)) $template_data = array_merge($template_data, ['user_has_active_tasks' => 1]);
                if ($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | FINANCE)) $template_data = array_merge($template_data, ['site_admin' => 1]);
                if ($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | FINANCE | NGO_ADMIN)) $template_data = array_merge($template_data, ['show_admin_dashboard' => 1]);                
              } catch (Common\Exceptions\SolasMatchException $e) {
                Common\Lib\UserSession::clearCurrentUserID();
                Common\Lib\UserSession::clearAccessToken();
                unset($template_data['user']);
                error_log('beforeDispatch ERROR on: ' . $request->getUri() . ', ' . $e->getMessage());
                error_log($e->getTraceAsString());
              }
            } else {
                Common\Lib\UserSession::clearCurrentUserID();
                Common\Lib\UserSession::clearAccessToken();
            }
        }
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

        if (!Common\Lib\UserSession::getCurrentUserID()) {
            Common\Lib\UserSession::setReferer($request->getUri());
            \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_login_required_to_access_page'));
            return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('login'));
        }

        if ($this->isUserBanned()) return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));

        if (empty($_SESSION['profile_completed']) || $_SESSION['profile_completed'] == 2) {
            $adminDao = new DAO\AdminDao();
            if (!$adminDao->isSiteAdmin_any_or_org_admin_any_for_any_org($_SESSION['user_id'])) {
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

        if (!Common\Lib\UserSession::getCurrentUserID()) {
            Common\Lib\UserSession::setReferer($request->getUri());
            \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_login_required_to_access_page'));
            return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('login'));
        }

        if ($this->isUserBanned()) return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));

        if (!empty($_SESSION['profile_completed']) && $_SESSION['profile_completed'] == 1 && !strpos((string)$request->getUri(), '/googleregister')) {
            error_log('authUserIsLoggedInNoProfile() redirecting to googleregister, user_id: ' . $_SESSION['user_id']);
            \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', 'You must accept the Code of Conduct before continuing'); // Since they are logged in (via Google)...
            return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('googleregister', array('user_id' => $_SESSION['user_id'])));
        }

        return $handler->handle($request);
    }

    private function user_not_logged_in(Request $request)
    {
        global $app;

        if (!Common\Lib\UserSession::getCurrentUserID()) {
            Common\Lib\UserSession::setReferer($request->getUri());
            \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_login_required_to_access_page'));
            return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('login'));
        }

        if ($this->isUserBanned()) return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));

        if (empty($_SESSION['profile_completed']) || $_SESSION['profile_completed'] == 2) {
            $adminDao = new DAO\AdminDao();
            if (!$adminDao->isSiteAdmin_any_or_org_admin_any_for_any_org($_SESSION['user_id'])) {
                \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', 'You must fill in your profile before continuing');
                return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('user-private-profile', array('user_id' => $_SESSION['user_id'])));
            }
        } elseif ($_SESSION['profile_completed'] == 1) {
            error_log('authUserIsLoggedIn() redirecting to googleregister, user_id: ' . $_SESSION['user_id']);
            \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', 'You must accept the Code of Conduct before continuing'); // Since they are logged in (via Google)...
            return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('googleregister', array('user_id' => $_SESSION['user_id'])));
        }

        return 0;
    }

    public function authIsSiteAdmin(Request $request, RequestHandler $handler, $roles = 0)
    {
        global $app;

        $adminDao = new DAO\AdminDao();
        if (!empty($_SESSION['user_id']) && ($adminDao->get_roles($_SESSION['user_id']) & (SITE_ADMIN | $roles))) return $handler->handle($request);

        \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', 'Site Admin login required to access page.');

        Common\Lib\UserSession::setReferer($request->getUri());
        return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('login'));
    }

    public function authIsSiteAdmin_or_PO(Request $request, RequestHandler $handler)
    {
        return $this->authIsSiteAdmin($request, $handler, PROJECT_OFFICER);
    }

    public function authIsSiteAdmin_or_COMMUNITY(Request $request, RequestHandler $handler)
    {
        return $this->authIsSiteAdmin($request, $handler, COMMUNITY_OFFICER);
    }

    public function authIsSiteAdmin_any(Request $request, RequestHandler $handler)
    {
        return $this->authIsSiteAdmin($request, $handler, PROJECT_OFFICER | COMMUNITY_OFFICER);
    }

    public function authIsSiteAdmin_or_FINANCE(Request $request, RequestHandler $handler)
    {
        return $this->authIsSiteAdmin($request, $handler, FINANCE);
    }

    public function authIsSiteAdmin_any_or_FINANCE(Request $request, RequestHandler $handler)
    {
        return $this->authIsSiteAdmin($request, $handler, PROJECT_OFFICER | COMMUNITY_OFFICER | FINANCE);
    }

    public function authIsSiteAdmin_or_PO_or_FINANCE(Request $request, RequestHandler $handler)
    {
        return $this->authIsSiteAdmin($request, $handler, PROJECT_OFFICER | FINANCE);
    }

    public function authIsSiteAdmin_any_or_org_admin_or_po_for_any_org(Request $request, RequestHandler $handler)
    {
        global $app;

        if (!Common\Lib\UserSession::getCurrentUserID()) {
            Common\Lib\UserSession::setReferer($request->getUri());
            \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_login_required_to_access_page'));
            return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('login'));
        }

        $adminDao = new DAO\AdminDao();
        if ($adminDao->isSiteAdmin_any_or_org_admin_any_for_any_org($_SESSION['user_id'])) return $handler->handle($request);

        \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', 'Admin login required to access page.');

        Common\Lib\UserSession::setReferer($request->getUri());
        return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('login'));
    }

    public function authenticateUserForTask(Request $request, RequestHandler $handler)
    {
        global $app;

        if ($ret = $this->user_not_logged_in($request)) return $ret;
        $user_id = $_SESSION['user_id'];

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $task_id = $route->getArgument('task_id');
        if (empty($task_id)) return $handler->handle($request);

        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $adminDao = new DAO\AdminDao();
        $task = $taskDao->getTask($task_id);
        $project = $projectDao->getProject($task->getProjectId());
        if ($adminDao->get_roles($user_id, $project->getOrganisationId()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER)) return $handler->handle($request);

        if ($claimant = $taskDao->getUserClaimedTask($task_id)) {
            if ($user_id != $claimant->getId()) {
                \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_error_already_claimed'));
                return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
            }
        }
        return $handler->handle($request);
    }

    public function authUserForOrg(Request $request, RequestHandler $handler, $community = 0)
    {
        global $app;

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $org_id = $route->getArgument('org_id');
        if (!empty($_SESSION['user_id']) && !empty($org_id)) {
            $adminDao = new DAO\AdminDao();
            if ($adminDao->get_roles($_SESSION['user_id'], $org_id) & (SITE_ADMIN | PROJECT_OFFICER | $community | NGO_ADMIN | NGO_PROJECT_OFFICER)) return $handler->handle($request);
        }
        \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_error_not_exist'));
        return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
    }

    public function authUserForOrg_incl_community_officer(Request $request, RequestHandler $handler)
    {
        return $this->authUserForOrg($request, $handler, COMMUNITY_OFFICER);
    }

    public function auth_admin_any_or_ngo_admin(Request $request, RequestHandler $handler)
    {
        global $app;

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $org_id = $route->getArgument('org_id');
        if (!empty($_SESSION['user_id']) && !empty($org_id)) {
            $adminDao = new DAO\AdminDao();
            if ($adminDao->get_roles($_SESSION['user_id'], $org_id) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | NGO_ADMIN)) return $handler->handle($request);
        }
        \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_error_not_exist'));
        return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
    }

    /*
     *  Middleware for ensuring the current user belongs to the Org that uploaded the associated Task
     *  Used for altering task details
     */
    public function authUserForOrgTask(Request $request, RequestHandler $handler, $community = 0)
    {
        global $app;

        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $adminDao = new DAO\AdminDao();

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $task_id = $route->getArgument('task_id');
        if (!empty($_SESSION['user_id']) && !empty($task_id)) {
            $task = $taskDao->getTask($task_id);
            $project = $projectDao->getProject($task->getProjectId());
            if ($adminDao->get_roles($_SESSION['user_id'], $project->getOrganisationId()) & (SITE_ADMIN | PROJECT_OFFICER | $community | NGO_ADMIN | NGO_PROJECT_OFFICER)) return $handler->handle($request);
        }
        \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_error_not_exist'));
        return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
    }

    public function authUserForOrgTask_incl_community_officer(Request $request, RequestHandler $handler)
    {
        return $this->authUserForOrgTask($request, $handler, COMMUNITY_OFFICER);
    }

    public function authUserForOrgProject(Request $request, RequestHandler $handler)
    {
        global $app;

        $projectDao = new DAO\ProjectDao();
        $adminDao = new DAO\AdminDao();

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $project_id = $route->getArgument('project_id');
        if (!empty($_SESSION['user_id']) && !empty($project_id)) {
            $project = $projectDao->getProject($project_id);
            if ($adminDao->get_roles($_SESSION['user_id'], $project->getOrganisationId()) & (SITE_ADMIN | PROJECT_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER)) return $handler->handle($request);
        }
        \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_error_not_exist'));
        return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
    }

    public function authUserForProjectImage(Request $request, RequestHandler $handler)
    {
        global $app;

        $projectDao = new DAO\ProjectDao();
        $adminDao = new DAO\AdminDao();

        if (!empty($_SESSION['user_id']) && ($adminDao->get_roles($_SESSION['user_id']) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER))) return $handler->handle($request);
        
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $project_id = $route->getArgument('project_id');
        
        if (!empty($project_id)) {
            $project = $projectDao->getProject($project_id);
            if ($project->getImageApproved() && $project->getImageUploaded()) return $handler->handle($request);
        }
        \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_error_not_exist'));
        return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
    }

    public function authUserForTaskDownload(Request $request, RequestHandler $handler)
    {
        global $app;

        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $adminDao = new DAO\AdminDao();

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $task_id = $route->getArgument('task_id');
        if (!empty($_SESSION['user_id']) && !empty($task_id)) {
            $task = $taskDao->getTask($task_id);
            if ($taskDao->getUserClaimedTask($task_id)) return $handler->handle($request); // weird

            $project = $projectDao->getProject($task->getProjectId());
            if ($adminDao->get_roles($_SESSION['user_id'], $project->getOrganisationId()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER)) return $handler->handle($request);
        }
        \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_error_not_exist'));
        return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
    }
    
    public function isUserBanned()
    {
        $adminDao = new DAO\AdminDao();
        if ($adminDao->isUserBanned(Common\Lib\UserSession::getCurrentUserID())) {
            Common\Lib\UserSession::destroySession();
            \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash('error', Localisation::getTranslation('common_this_user_account_has_been_banned'));
            return true;
        }
        return false;
    }
    
    public function isBlacklisted(Request $request, RequestHandler $handler)
    {
        global $app;

        if ($ret = $this->user_not_logged_in($request)) return $ret;

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $taskId = $route->getArgument('task_id');
            if (!empty($taskId)) {
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
                            if ($task->getTaskType() != Common\Enums\TaskTypeEnum::TRANSLATION) $message = 'You cannot claim the task <a href="%s">%s</a>, because you have previously claimed the matching translation task.';
                            else                                                                $message = 'You cannot claim the task <a href="%s">%s</a>, because you have previously claimed the matching revision or proofreading task.';
                        }
                    } else {
                        //An admin has previously revoked this task from the user.
                        $message = Localisation::getTranslation('common_error_cannot_reclaim_admin_revoked');
                    }
                    \SolasMatch\UI\RouteHandlers\UserRouteHandler::flash(
                        'error',
                        sprintf(
                            $message,
                            (string)$app->getRouteCollector()->getRouteParser()->urlFor("task-claimed", array("task_id" => $taskId)),
                            $task->getTitle()
                        )
                    );
                    return $app->getResponseFactory()->createResponse()->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
                } else {
                    return $handler->handle($request);
                }
            }

        return $handler->handle($request);
    }
}
