<?php

class UserDao
{
    public function getUser($params)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users";

        $id = null;
        $email = null;
        if (isset($params['id'])) {
            $id = $params['id'];
            $request = "$request/$id";
        } elseif(isset($params['email'])) {
            $email = $params['email'];
            $request = "$request/getByEmail/$email";
        }

        $response = $client->call($request);
        $ret = $client->cast(array("User"), $response);
        
        if ((!is_null($id) || !is_null($email)) && is_array($ret)) {
            $ret = $ret[0];
        }
        
        return $ret;
    }

    public function isSubscribedToTask($userId, $taskId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/subscribedToTask/$userId/$taskId";
        $ret = $client->call($request);
        return $ret;
    }

    public function isSubscribedToProject($userId, $projectId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/subscribedToProject/$userId/$projectId";
        $ret = $client->call($request);
        return $ret;
    }

    public function getUserOrgs($userId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/orgs";
        $response = $client->call($request);
        $ret = $client->cast(array("Organisation"), $response);
        return $ret;
    }

    public function getUserBadges($userId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/badges";
        $response = $client->call($request);
        $ret = $client->cast(array("Badge"), $response);
        return $ret;
    }

    public function getUserTags($userId, $limit = null)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/tags";

        $args = null;
        if ($limit) {
            $args = array("limit" => $limit);
        }

        $response = $client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        $ret = $client->cast(array("Tag"), $response);
        return $ret;
    }

    public function getUserTasks($userId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/tasks";
        $response = $client->call($request);
        $ret = $client->cast(array("Task"), $response);
        return $ret;
    }

    public function getUserTopTasks($userId, $limit = null)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/top_tasks";

        $args = null;
        if ($limit) {
            $args = array("limit" => $limit);
        }

        $response = $client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        $ret = $client->cast(array("Task"), $response);
        return $ret;
    }

    public function getUserArchivedTasks($userId, $limit = null)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/archived_tasks";

        $args = null;
        if ($limit) {
            $args = array("limit" => $limit);
        }

        $response = $client->call($request, HTTP_Request2::METHOD_GET, null, $args);
        $ret = $client->cast(array("Task"), $response);
        return $ret;
    }

    public function getUserTrackedTasks($userId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/tracked_tasks";
        $response = $client->call($request);
        $ret = $client->cast(array("Task"), $response);
        return $ret;
    }

    public function getUserTrackedProjects($userId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/projects";
        $response = $client->call($request);
        $ret = $client->cast(array("Project"), $response);
        return $ret;
    }

    public function hasUserRequestedPasswordReset($userId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/passwordResetRequest";
        $ret = $client->call($request);
        return $ret;
    }

    public function getPasswordResetRequestTime($userId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/passwordResetRequest/time";
        $ret = $client->call($request);
        return $ret;
    }

    public function leaveOrganisation($userId, $orgId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/leaveOrg/$userId/$orgId";
        $ret = $client->call($request, HTTP_Request2::METHOD_DELETE);
        return $ret;
    }

    public function addUserBadge($userId, $badge)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/badges";
        $ret = $client->call($request, HTTP_Request2::METHOD_POST, $badge);
        return $ret;
    }

    public function addUserBadgeById($userId, $badgeId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/badges/$badgeId";
        $ret = $client->call($request, HTTP_Request2::METHOD_PUT);
        return $ret;
    }

    public function removeUserBadge($userId, $badgeId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/badges/$badgeId";
        $ret = $client->call($request, HTTP_Request2::METHOD_DELETE);
        return $ret;
    }

    public function claimTask($userId, $task)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/tasks";
        $ret = $client->call($request, HTTP_Request2::METHOD_POST, $task);
        return $ret;
    }

    public function unclaimTask($userId, $taskId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/tasks/$taskId";
        $ret = $client->call($request, HTTP_Request2::METHOD_DELETE);
        return $ret;
    }

    public function updateUser($user)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/{$user->getId()}";
        $response = $client->call($request, HTTP_Request2::METHOD_PUT, $user);
        $ret = $client->cast("User", $response);
        return $ret;
    }

    public function addUserTag($userId, $tag)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/tags";
        $ret = $client->call($request, HTTP_Request2::METHOD_POST, $tag);
        return $ret;
    }

    public function addUserTagById($userId, $tagId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/tags/$tagId";
        $ret = $client->call($request, HTTP_Request2::METHOD_PUT);
        return $ret;
    }

    public function removeUserTag($userId, $tagId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/tags/$tagId";
        $ret = $client->call($request, HTTP_Request2::METHOD_DELETE);
        return $ret;
    }

    public function trackTask($userId, $taskId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/tracked_tasks/$taskId";
        $ret = $client->call($request, HTTP_Request2::METHOD_PUT);
        return $ret;
    }

    public function untrackTask($userId, $taskId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/tracked_tasks/$taskId";
        $ret = $client->call($request, HTTP_Request2::METHOD_DELETE);
        return $ret;
    }

    public function requestPasswordReset($userId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/passwordResetRequest";
        $ret = $client->call($request, HTTP_Request2::METHOD_POST);
        return $ret;
    }

    public function trackProject($userId, $projectId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/projects/$projectId";
        $ret = $client->call($request, HTTP_Request2::METHOD_PUT);
        return $ret;
    }

    public function untrackProject($userId, $projectId)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/users/$userId/projects/$projectId";
        $ret = $client->call($request, HTTP_Request2::METHOD_DELETE);
        return $ret;
    }

    public function login($loginData)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/login";
        $ret = $client->call($request, HTTP_Request2::METHOD_POST, $loginData);
        return $ret;
    }

    public function getPasswordResetRequest($key)
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/password_reset/$key";
        $response = $client->call($request);
        $ret = $client->cast("PasswordResetRequest", $response);
        return $ret;
    }

    public function resetPassword($password, $key)
    {
        $ret = null;
        $passwordReset = new PasswordReset();
        $passwordReset->setPassword($password);
        $passwordReset->setKey($key);
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/password_reset";
        $ret = $client->call($request, HTTP_Request2::METHOD_POST, $passwordReset);
        return $ret;
    }

    public function register($email, $password)
    {
        $ret = null;
        $registerData = new Register();
        $registerData->setEmail($email);
        $registerData->setPassword($password);
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/register";
        $ret = $client->call($request, HTTP_Request2::METHOD_POST, $registerData);
        return $ret;
    }
}
