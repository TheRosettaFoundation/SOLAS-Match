<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;

/*
 * Routes for projects
 *
 * @author Dave
 */
require_once __DIR__."/../DataAccessObjects/ProjectDao.class.php";
require_once __DIR__."/../../Common/protobufs/models/Project.php";
require_once __DIR__."/../lib/APIWorkflowBuilder.class.php";

class Projects
{
    public static function init()
    {
        /**
         * Gets a single project object based on its id
         */
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/projects/:projectId/',
            function ($projectId, $format = '.json') {
                if (!is_numeric($projectId) && strstr($projectId, '.')) {
                    $projectId = explode('.', $projectId);
                    $format = '.'.$projectId[1];
                    $projectId = $projectId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\ProjectDao::getProject($projectId), null, $format);
            },
            'getProject',
            null
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/projects(:format)/',
            function ($format = '.json') {
                API\Dispatcher::sendResponse(null, DAO\ProjectDao::getProjects(), null, $format);
            },
            'getProjects'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/projects(:format)/',
            function ($format = '.json') {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\Project');
                $project = DAO\ProjectDao::save($data);
                if (!is_null($project) && $project->getId() > 0) {
                    API\Dispatcher::sendResponse(null, $project, null, $format);
                } else {
                    API\Dispatcher::sendResponse(
                        null,
                        "Project details conflict with existing data",
                        Common\Enums\HttpStatusEnum::CONFLICT,
                        $format
                    );
                }
            },
            'createProject',
            '\SolasMatch\API\Lib\Middleware::authenticateUserMembership'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/projects/:projectId/',
            function ($projectId, $format = '.json') {
                if (!is_numeric($projectId) && strstr($projectId, '.')) {
                    $projectId = explode('.', $projectId);
                    $format = '.'.$projectId[1];
                    $projectId = $projectId[0];
                }
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\Project');
                API\Dispatcher::sendResponse(null, DAO\ProjectDao::save($data), null, $format);
                DAO\ProjectDao::calculateProjectDeadlines($data->getId());
            },
            'updateProject',
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgProject'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/projects/:projectId/',
            function ($projectId, $format = ".json") {
                if (!is_numeric($projectId) && strstr($projectId, '.')) {
                    $projectId = explode('.', $projectId);
                    $format = '.'.$projectId[1];
                    $projectId = $projectId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\ProjectDao::delete($projectId), null, $format);
            },
            'deleteProject',
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgProject'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/projects/:projectId/calculateDeadlines(:format)/',
            function ($projectId, $format = '.json') {
                $ret = null;
                $ret = DAO\ProjectDao::calculateProjectDeadlines($projectId);
                API\Dispatcher::sendResponse(null, $ret, null, $format);
            },
            'calculateProjectDeadlines'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/projects/:projectId/reviews(:format)/',
            function ($projectId, $format = '.json') {
                $reviews = DAO\TaskDao::getTaskReviews($projectId);
                API\Dispatcher::sendResponse(null, $reviews, null, $format);
            },
            'getProjectTaskReviews',
            '\SolasMatch\API\Lib\Middleware::authenticateUserOrOrgForProjectTask'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/projects/:projectId/tasks(:format)/',
            function ($projectId, $format = '.json') {
                $data = DAO\ProjectDao::getProjectTasks($projectId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getProjectTasks'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/projects/archiveProject/:projectId/user/:userId/',
            function ($projectId, $userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\ProjectDao::archiveProject($projectId, $userId), null, $format);
            },
            'archiveProject',
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgProject'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/archivedProjects(:format)/',
            function ($format = '.json') {
                API\Dispatcher::sendResponse(null, DAO\ProjectDao::getArchivedProject(), null, $format);
            },
            'getArchivedProjects',
            '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/archivedProjects/:projectId/',
            function ($projectId, $format = '.json') {
                if (!is_numeric($projectId) && strstr($projectId, '.')) {
                    $projectId = explode('.', $projectId);
                    $format = '.'.$projectId[1];
                    $projectId = $projectId[0];
                }
                $data = DAO\ProjectDao::getArchivedProject($projectId);
                if ($data && is_array($data)) {
                    $data = $data[0];
                }
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getArchivedProject',
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgProject'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/projects/buildGraph/:projectId/',
            function ($projectId, $format = '.json') {
                if (!is_numeric($projectId) && strstr($projectId, '.')) {
                    $projectId = explode('.', $projectId);
                    $format = '.'.$projectId[1];
                    $projectId = $projectId[0];
                }
                $builder = new Lib\APIWorkflowBuilder();
                $graph = $builder->buildProjectGraph($projectId);
                API\Dispatcher::sendResponse(null, $graph, null, $format);
            },
            'getProjectGraph'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/projects/:projectId/tags(:format)/',
            function ($projectId, $format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\ProjectDao::getTags($projectId), null, $format);
            },
            'getProjectTags',
            null
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/projects/:projectId/info(:format)/',
            function ($projectId, $format = ".json") {
                API\Dispatcher::sendResponse(
                    null,
                    DAO\ProjectDao::getProjectFileInfo($projectId, null, null, null, null),
                    null,
                    $format
                );
            },
            'getProjectFileInfo'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/projects/:projectId/file(:format)/',
            function ($projectId, $format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\ProjectDao::getProjectFile($projectId), null, $format);
            },
            'getProjectFile',
            null
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/projects/:projectId/file/:filename/:userId/',
            function ($projectId, $filename, $userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                try {
                    $token = DAO\ProjectDao::saveProjectFile($projectId, $data, urldecode($filename), $userId);
                    API\Dispatcher::sendResponse(null, $token, Common\Enums\HttpStatusEnum::CREATED, $format);
                } catch (Exception $e) {
                    API\Dispatcher::sendResponse(null, $e->getMessage(), $e->getCode());
                }
            },
            'saveProjectFile',
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgProject'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/projects/:projectId/archivedTasks(:format)/',
            function ($projectId, $format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\ProjectDao::getArchivedTask($projectId), null, $format);
            },
            'getArchivedProjectTasks',
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgProject'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/projects/:projectId/deleteTags(:format)/',
            function ($projectId, $format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\ProjectDao::deleteProjectTags($projectId), null, $format);
            },
            'deleteProjectTags',
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgProject'
        );
    }
}
Projects::init();
