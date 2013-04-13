<?php

require_once __DIR__."/../../Common/lib/APIHelper.class.php";

class UserDao
{
    private $client;
    private $siteApi;
    
    public function __construct()
    {
        $this->client = new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi = Settings::get("site.api");
    }
    
    public function getUser($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId"; 
        $ret = $this->client->castCall("User", $request);
        return $ret;
    }
    
    public function getUserByEmail($email)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/getByEmail/$email"; 
        $ret = $this->client->castCall("User", $request);
        return $ret;
    }


    public function isSubscribedToTask($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/subscribedToTask/$userId/$taskId";
        $ret = $this->client->castCall(null, $request);
        return $ret;
    }

    public function isSubscribedToProject($userId, $projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/subscribedToProject/$userId/$projectId";
        $ret = $this->client->castCall(null, $request);
        return $ret;
    }

    public function getUserOrgs($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/orgs";
        $ret = $this->client->castCall(array("Organisation"), $request);
        return $ret;
    }

    public function getUserBadges($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/badges";
        $ret = $this->client->castCall(array("Badge"), $request);
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
        $ret = $this->client->castCall(array("Tag"), $request, HttpMethodEnum::GET, null, $args);
        return $ret;
    }

    public function getUserTasks($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tasks";
        $ret = $this->client->castCall(array("Task"), $request);
        return $ret;
    }

    public function getUserTopTasks($userId, $limit = null)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/top_tasks";

        $args = null;
        if ($limit) {
            $args = array("limit" => $limit);
        }

        $ret = $this->client->castCall(array("Task"), $request, HttpMethodEnum::GET, null, $args);
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

        $ret = $this->client->castCall(array("Task"), $request, HttpMethodEnum::GET, null, $args);
        return $ret;
    }

    public function getUserTrackedTasks($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tracked_tasks";
        $ret = $this->client->castCall(array("Task"), $request);
        return $ret;
    }

    public function getUserTrackedProjects($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/projects";
        $ret = $this->client->castCall(array("Project"), $request);
        return $ret;
    }

    public function hasUserRequestedPasswordReset($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/passwordResetRequest";
        $ret = $this->client->castCall(null, $request);
        return $ret;
    }

    public function getPasswordResetRequestTime($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/passwordResetRequest/time";
        $ret = $this->client->castCall(null, $request);
        return $ret;
    }

    public function leaveOrganisation($userId, $orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/leaveOrg/$userId/$orgId";
        $ret = $this->client->castCall(null, $request, HttpMethodEnum::DELETE);
        return $ret;
    }

    public function addUserBadge($userId, $badge)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/badges";
        $ret = $this->client->castCall(null, $request, HttpMethodEnum::POST, $badge);
        return $ret;
    }

    public function addUserBadgeById($userId, $badgeId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/badges/$badgeId";
        $ret = $this->client->castCall(null, $request, HttpMethodEnum::PUT);
        return $ret;
    }

    public function removeUserBadge($userId, $badgeId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/badges/$badgeId";
        $ret = $this->client->castCall(null, $request, HttpMethodEnum::DELETE);
        return $ret;
    }

    public function claimTask($userId, $task)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tasks";
        $ret = $this->client->castCall(null, $request, HttpMethodEnum::POST, $task);
        return $ret;
    }

    public function unclaimTask($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tasks/$taskId";
        $ret = $this->client->castCall(null, $request, HttpMethodEnum::DELETE);
        return $ret;
    }

    public function updateUser($user)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/{$user->getId()}";
        $ret = $this->client->castCall("User", $request, HttpMethodEnum::PUT, $user);
        return $ret;
    }

    public function addUserTag($userId, $tag)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tags";
        $ret = $this->client->castCall(null, $request, HttpMethodEnum::POST, $tag);
        return $ret;
    }

    public function addUserTagById($userId, $tagId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tags/$tagId";
        $ret = $this->client->castCall(null, $request, HttpMethodEnum::PUT);
        return $ret;
    }

    public function removeUserTag($userId, $tagId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tags/$tagId";
        $ret = $this->client->castCall(null, $request, HttpMethodEnum::DELETE);
        return $ret;
    }

    public function trackTask($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tracked_tasks/$taskId";
        $ret = $this->client->castCall(null, $request, HttpMethodEnum::PUT);
        return $ret;
    }

    public function untrackTask($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tracked_tasks/$taskId";
        $ret = $this->client->castCall(null, $request, HttpMethodEnum::DELETE);
        return $ret;
    }

    public function requestPasswordReset($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/passwordResetRequest";
        $ret = $this->client->castCall(null, $request, HttpMethodEnum::POST);
        return $ret;
    }

    public function trackProject($userId, $projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/projects/$projectId";
        $ret = $this->client->castCall(null, $request, HttpMethodEnum::PUT);
        return $ret;
    }

    public function untrackProject($userId, $projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/projects/$projectId";
        $ret = $this->client->castCall(null, $request, HttpMethodEnum::DELETE);
        return $ret;
    }

    public function login($email, $password)
    {
        $ret = null;
        $login = new Login();
        $login->setEmail($email);
        $login->setPassword($password);
        $request = "{$this->siteApi}v0/login";
        $ret = $this->client->castCall("User", $request, HttpMethodEnum::POST, $login);
        return $ret;
    }

    public function getPasswordResetRequest($key)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/password_reset/$key";
        $ret = $this->client->castCall("PasswordResetRequest", $request);
        return $ret;
    }

    public function resetPassword($password, $key)
    {
        $ret = null;
        $passwordReset = new PasswordReset();
        $passwordReset->setPassword($password);
        $passwordReset->setKey($key);
        $request = "{$this->siteApi}v0/password_reset";
        $ret = $this->client->castCall(null, $request, HttpMethodEnum::POST, $passwordReset);
        return $ret;
    }

    public function register($email, $password)
    {
        $ret = null;
        $registerData = new Register();
        $registerData->setEmail($email);
        $registerData->setPassword($password);
        $request = "{$this->siteApi}v0/register";
        $ret = $this->client->castCall("User", $request, HttpMethodEnum::POST, $registerData);
        return $ret;
    }
}
