<?php

require_once 'Common/lib/APIHelper.class.php';

class UserDao
{
    private $client;
    private $siteApi;
    
    public function __construct()
    {
        $this->client = new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi = Settings::get("site.api");
    }

    public function getUser($params)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users";

        $id = null;
        $email = null;
        if (isset($params['id'])) {
            $id = $params['id'];
            $request = "$request/$id";
        } elseif(isset($params['email'])) {
            $email = $params['email'];
            $request = "$request/getByEmail/$email";
        }

        $response = $this->client->call($request);
        if (!is_null($id) || !is_null($email)) {
            $ret = $this->client->cast("User", $response);
        } else {
            $ret = $this->client->cast(array("User"), $response);
        }
        
        return $ret;
    }

    public function isSubscribedToTask($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/subscribedToTask/$userId/$taskId";
        $ret = $this->client->call($request);
        return $ret;
    }

    public function isSubscribedToProject($userId, $projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/subscribedToProject/$userId/$projectId";
        $ret = $this->client->call($request);
        return $ret;
    }

    public function getUserOrgs($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/orgs";
        $response = $this->client->call($request);
        $ret = $this->client->cast(array("Organisation"), $response);
        return $ret;
    }

    public function getUserBadges($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/badges";
        $response = $this->client->call($request);
        $ret = $this->client->cast(array("Badge"), $response);
        return $ret;
    }

    public function getUserTags($userId, $limit = null)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tags";

        $args = null;
        if ($limit) {
            $args = array("limit" => $limit);
        }

        $response = $this->client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        $ret = $this->client->cast(array("Tag"), $response);
        return $ret;
    }

    public function getUserTasks($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tasks";
        $response = $this->client->call($request);
        $ret = $this->client->cast(array("Task"), $response);
        return $ret;
    }

    public function getUserTaskStreamNotification($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/taskStreamNotification";
        $ret = get_object_vars($this->client->call($request));
        return $ret;
    }

    public function getUserTopTasks($userId, $limit = null, $filter = array())
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/top_tasks";

        $args = array();
        if ($limit) {
            $args["limit"] = $limit;
        }

        $filterString = "";
        if ($filter) {
            if (isset($filter['taskType']) && $filter['taskType'] != '') {
                $filterString .= "taskType:".$filter['taskType'].';';
            }
            if (isset($filter['sourceLanguage']) && $filter['sourceLanguage'] != '') {
                $filterString .= "sourceLanguage:".$filter['sourceLanguage'].';';
            }
            if (isset($filter['targetLanguage']) && $filter['targetLanguage'] != '') {
                $filterString .= "targetLanguage:".$filter['targetLanguage'].';';
            }
        }

        if ($filterString != '') {
            $args['filter'] = $filterString;
        }

        $response = $this->client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        $ret = $this->client->cast(array("Task"), $response);
        return $ret;
    }

    public function getUserArchivedTasks($userId, $limit = null)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/archived_tasks";

        $args = null;
        if ($limit) {
            $args = array("limit" => $limit);
        }

        $response = $this->client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        $ret = $this->client->cast(array("Task"), $response);
        return $ret;
    }

    public function getUserTrackedTasks($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tracked_tasks";
        $response = $this->client->call($request);
        $ret = $this->client->cast(array("Task"), $response);
        return $ret;
    }

    public function getUserTrackedProjects($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/projects";
        $response = $this->client->call($request);
        $ret = $this->client->cast(array("Project"), $response);
        return $ret;
    }

    public function hasUserRequestedPasswordReset($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/passwordResetRequest";
        $ret = $this->client->call($request);
        return $ret;
    }

    public function getPasswordResetRequestTime($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/passwordResetRequest/time";
        $ret = $this->client->call($request);
        return $ret;
    }

    public function leaveOrganisation($userId, $orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/leaveOrg/$userId/$orgId";
        $ret = $this->client->call($request, HTTP_Request2::METHOD_DELETE);
        return $ret;
    }

    public function addUserBadge($userId, $badge)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/badges";
        $ret = $this->client->call($request, HTTP_Request2::METHOD_POST, $badge);
        return $ret;
    }

    public function addUserBadgeById($userId, $badgeId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/badges/$badgeId";
        $ret = $this->client->call($request, HTTP_Request2::METHOD_PUT);
        return $ret;
    }

    public function removeUserBadge($userId, $badgeId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/badges/$badgeId";
        $ret = $this->client->call($request, HTTP_Request2::METHOD_DELETE);
        return $ret;
    }

    public function claimTask($userId, $task)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tasks";
        $ret = $this->client->call($request, HTTP_Request2::METHOD_POST, $task);
        return $ret;
    }

    public function unclaimTask($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tasks/$taskId";
        $ret = $this->client->call($request, HTTP_Request2::METHOD_DELETE);
        return $ret;
    }

    public function updateUser($user)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/{$user->getUserId()}";
        $response = $this->client->call($request, HTTP_Request2::METHOD_PUT, $user);
        $ret = $this->client->cast("User", $response);
        return $ret;
    }

    public function addUserTag($userId, $tag)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tags";
        $ret = $this->client->call($request, HTTP_Request2::METHOD_POST, $tag);
        return $ret;
    }

    public function addUserTagById($userId, $tagId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tags/$tagId";
        $ret = $this->client->call($request, HTTP_Request2::METHOD_PUT);
        return $ret;
    }

    public function removeUserTag($userId, $tagId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tags/$tagId";
        $ret = $this->client->call($request, HTTP_Request2::METHOD_DELETE);
        return $ret;
    }

    public function trackTask($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tracked_tasks/$taskId";
        $ret = $this->client->call($request, HTTP_Request2::METHOD_PUT);
        return $ret;
    }

    public function untrackTask($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tracked_tasks/$taskId";
        $ret = $this->client->call($request, HTTP_Request2::METHOD_DELETE);
        return $ret;
    }

    public function requestPasswordReset($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/passwordResetRequest";
        $ret = $this->client->call($request, HTTP_Request2::METHOD_POST);
        return $ret;
    }

    public function trackProject($userId, $projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/projects/$projectId";
        $ret = $this->client->call($request, HTTP_Request2::METHOD_PUT);
        return $ret;
    }

    public function untrackProject($userId, $projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/projects/$projectId";
        $ret = $this->client->call($request, HTTP_Request2::METHOD_DELETE);
        return $ret;
    }

    public function login($email, $password)
    {
        $ret = null;
        $login = new Login();
        $login->setEmail($email);
        $login->setPassword($password);
        $request = "{$this->siteApi}v0/login";
        $response = $this->client->call($request, HTTP_Request2::METHOD_POST, $login);
        $ret = $this->client->cast("User", $response);
        return $ret;
    }

    public function getPasswordResetRequest($key)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/password_reset/$key";
        $response = $this->client->call($request);
        $ret = $this->client->cast("PasswordResetRequest", $response);
        return $ret;
    }

    public function resetPassword($password, $key)
    {
        $ret = null;
        $passwordReset = new PasswordReset();
        $passwordReset->setPassword($password);
        $passwordReset->setKey($key);
        $request = "{$this->siteApi}v0/password_reset";
        $ret = $this->client->call($request, HTTP_Request2::METHOD_POST, $passwordReset);
        return $ret;
    }

    public function register($email, $password)
    {
        $ret = null;
        $registerData = new Register();
        $registerData->setEmail($email);
        $registerData->setPassword($password);
        $request = "{$this->siteApi}v0/register";
        $response = $this->client->call($request, HTTP_Request2::METHOD_POST, $registerData);
        $ret = $this->client->cast("User", $response);
        return $ret;
    }
}
