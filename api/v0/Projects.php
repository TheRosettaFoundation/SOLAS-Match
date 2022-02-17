<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__."/../DataAccessObjects/ProjectDao.class.php";
require_once __DIR__."/../DataAccessObjects/UserDao.class.php";
require_once __DIR__."/../../Common/protobufs/models/Project.php";
require_once __DIR__."/../../Common/lib/Settings.class.php";
require_once __DIR__."/../lib/APIWorkflowBuilder.class.php";


class Projects
{
    public static function init()
    {
        global $app;

        $app->put(
            '/v0/projects/:projectId/updateWordCount/:newWordCount/',
            '\SolasMatch\API\V0\Projects::updateProjectWordCount'
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
        );

        $app->put(
            '/v0/projects/:projectId/setImageApprovalStatus/:imageStatus/',
            '\SolasMatch\API\V0\Projects::setImageApprovalStatus'
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
        );

        $app->post(
            '/v0/projects/:projectId/calculateDeadlines/',
            '\SolasMatch\API\V0\Projects::calculateProjectDeadlines'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->get(
            '/v0/projects/:projectId/reviews/',
            '\SolasMatch\API\V0\Projects::getProjectTaskReviews'
            '\SolasMatch\API\Lib\Middleware::authenticateUserOrOrgForProjectTask',
        );

        $app->get(
            '/v0/projects/:projectId/tasks/',
            '\SolasMatch\API\V0\Projects::getProjectTasks'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->get(
            '/v0/projects/:projectId/tags/',
            '\SolasMatch\API\V0\Projects::getProjectTags'
        );

        $app->get(
            '/v0/projects/:projectId/info/',
            '\SolasMatch\API\V0\Projects::getProjectFileInfo'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->get(
            '/v0/projects/:projectId/file/',
            '\SolasMatch\API\V0\Projects::getProjectFile'
        );

        $app->get(
            '/v0/projects/:projectId/archivedTasks/',
            '\SolasMatch\API\V0\Projects::getArchivedProjectTasks'
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgProject',
        );

        $app->delete(
            '/v0/projects/:projectId/deleteTags/',
            '\SolasMatch\API\V0\Projects::deleteProjectTags'
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgProject',
        );

        $app->put(
            '/v0/projects/archiveProject/:projectId/user/:userId/',
            '\SolasMatch\API\V0\Projects::archiveProject'
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgProject',
        );

        $app->get(
            '/v0/projects/buildGraph/:projectId/',
            '\SolasMatch\API\V0\Projects::getProjectGraph'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->post(
                '/v0/projects/getProjectByName/',
                '\SolasMatch\API\V0\Projects::getProjectByName'
        );

        $app->get(
                '/v0/projects/getProjectByNameAndOrganisation/:title/organisation/:orgId/',
                '\SolasMatch\API\V0\Projects::getProjectByNameAndOrganisation'
        );

        $app->get(
            '/v0/projects/:projectId/',
            '\SolasMatch\API\V0\Projects::getProject'
        );

        $app->put(
            '/v0/projects/:projectId/',
            '\SolasMatch\API\V0\Projects::updateProject'
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgProject',
        );

        $app->delete(
            '/v0/projects/:projectId/',
            '\SolasMatch\API\V0\Projects::deleteProject'
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgProject',
        );

        $app->get(
            '/v0/archivedProjects/:projectId/',
            '\SolasMatch\API\V0\Projects::getArchivedProject'
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgProject',
        );

        $app->get(
            '/v0/projects/',
            '\SolasMatch\API\V0\Projects::getProjects'
            '\SolasMatch\API\Lib\Middleware::isloggedIn',
        );

        $app->post(
            '/v0/projects/',
            '\SolasMatch\API\V0\Projects::createProject'
            '\SolasMatch\API\Lib\Middleware::authenticateUserMembership',
        );

        $app->get(
            '/v0/archivedProjects/',
            '\SolasMatch\API\V0\Projects::getArchivedProjects'
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
        );
    }

    public static function updateProjectWordCount($projectId, $newWordCount)
    {
        $ret = null;
        $ret = DAO\ProjectDao::updateProjectWordCount($projectId, $newWordCount);
        API\Dispatcher::sendResponse(null, $ret, null);
    }

    public static function setImageApprovalStatus($projectId, $imageStatus)
    {
        $ret = null;
        $ret = DAO\ProjectDao::setImageApprovalStatus($projectId, $imageStatus);
        API\Dispatcher::sendResponse(null, $ret, null);
    }

    public static function calculateProjectDeadlines($projectId)
    {
        $ret = null;
        $ret = DAO\ProjectDao::calculateProjectDeadlines($projectId);
        API\Dispatcher::sendResponse(null, $ret, null);
    }

    public static function getProjectTaskReviews($projectId)
    {
        $reviews = DAO\TaskDao::getTaskReviews($projectId);
        API\Dispatcher::sendResponse(null, $reviews, null);
    }

    public static function getProjectTasks($projectId)
    {
        $data = DAO\ProjectDao::getProjectTasks($projectId);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getProjectTags($projectId)
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::getTags($projectId), null);
    }

    public static function getProjectFileInfo($projectId)
    {
        API\Dispatcher::sendResponse(
            null,
            DAO\ProjectDao::getProjectFileInfo($projectId, null, null, null, null),
            null
        );
    }

    public static function getProjectFile($projectId)
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::getProjectFile($projectId), null);
    }

    public static function getArchivedProjectTasks($projectId)
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::getArchivedTask($projectId), null);
    }

    public static function deleteProjectTags($projectId)
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::deleteProjectTags($projectId), null);
    }

    public static function archiveProject($projectId, $userId)
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::archiveProject($projectId, $userId), null);
    }

    public static function getProjectGraph($projectId)
    {
        $builder = new Lib\APIWorkflowBuilder();
        $graph = $builder->buildProjectGraph($projectId);
        API\Dispatcher::sendResponse(null, $graph, null);
    }

    public static function getProject($projectId)
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::getProject($projectId), null);
    }

    public static function getProjectByName()
    {
        $title = API\Dispatcher::getDispatcher()->request()->getBody();

        $data = DAO\ProjectDao::getProjectByName($title);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getProjectByNameAndOrganisation($title, $orgId)
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::getProjectByNameAndOrganisation($title, $orgId), null);
    }

    public static function updateProject($projectId)
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\Project');
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::save($data), null);

        if (!DAO\ProjectDao::get_memsource_project($data->getId())) DAO\ProjectDao::calculateProjectDeadlines($data->getId());
    }

    public static function deleteProject($projectId)
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::delete($projectId), null);
    }

    public static function getArchivedProject($projectId)
    {
        $data = DAO\ProjectDao::getArchivedProject($projectId);
        if ($data && is_array($data)) {
            $data = $data[0];
        }
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getProjects()
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::getProjects(), null);
    }

    private static function addTrackProjectForUsers($userIds, $projectId)
    {
        foreach($userIds as $userId) {
            try {
                DAO\UserDao::trackProject($projectId, $userId);
                error_log(sprintf('User %s tracks project %s', $userId, $projectId));
            } catch (Exception $e) {
                error_log('Error auto-tracking project ' . $projectId);
            }
        }
    }

    private static function getAutoFollowAdminIds()
    {
        $result = array();
        try {
            $adminIdsString = trim(Common\Lib\Settings::get('site.autofollow_admin_ids'));
            if ($adminIdsString) {
                $result = array_map('intval', explode(',', $adminIdsString));
            }
        } catch(Exception $e) {
            error_log($e->getMessage());
        }
        return $result;
    }

    public static function createProject()
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\Project');
        $project = DAO\ProjectDao::save($data);
        if (!is_null($project) && $project->getId() > 0) {
            // Auto track the project for admins
            $admins = self::getAutoFollowAdminIds();
            self::addTrackProjectForUsers($admins, $project->getId());
            API\Dispatcher::sendResponse(null, $project, null);
        } else {
            API\Dispatcher::sendResponse(
                null,
                "Project details conflict with existing data",
                Common\Enums\HttpStatusEnum::CONFLICT
            );
        }
    }

    public static function getArchivedProjects()
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::getArchivedProject(), null);
    }
}

Projects::init();
