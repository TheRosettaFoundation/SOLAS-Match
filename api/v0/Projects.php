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


class Projects
{
    public static function init()
    {
        global $app;

        $app->put(
            '/api/v0/projects/{projectId}/updateWordCount/{newWordCount}/',
            '\SolasMatch\API\V0\Projects:updateProjectWordCount')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateSiteAdmin');

        $app->put(
            '/api/v0/projects/{projectId}/setImageApprovalStatus/{imageStatus}/',
            '\SolasMatch\API\V0\Projects:setImageApprovalStatus')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateSiteAdmin');

        $app->get(
            '/api/v0/projects/{projectId}/tasks/',
            '\SolasMatch\API\V0\Projects:getProjectTasks')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/api/v0/projects/{projectId}/tags/',
            '\SolasMatch\API\V0\Projects:getProjectTags');

        $app->get(
            '/api/v0/projects/{projectId}/info/',
            '\SolasMatch\API\V0\Projects:getProjectFileInfo')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/api/v0/projects/{projectId}/file/',
            '\SolasMatch\API\V0\Projects:getProjectFile');

        $app->get(
            '/api/v0/projects/{projectId}/archivedTasks/',
            '\SolasMatch\API\V0\Projects:getArchivedProjectTasks')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateUserForOrgProject');

        $app->delete(
            '/api/v0/projects/{projectId}/deleteTags/',
            '\SolasMatch\API\V0\Projects:deleteProjectTags')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateUserForOrgProject');

        $app->put(
            '/api/v0/projects/archiveProject/{projectId}/user/{userId}/',
            '\SolasMatch\API\V0\Projects:archiveProject')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateUserForOrgProject');

        $app->post(
                '/api/v0/projects/getProjectByName/',
                '\SolasMatch\API\V0\Projects:getProjectByName');

        $app->get(
                '/api/v0/projects/getProjectByNameAndOrganisation/{title}/organisation/{orgId}/',
                '\SolasMatch\API\V0\Projects:getProjectByNameAndOrganisation');

        $app->get(
            '/api/v0/projects/{projectId}/',
            '\SolasMatch\API\V0\Projects:getProject');

        $app->put(
            '/api/v0/projects/{projectId}/',
            '\SolasMatch\API\V0\Projects:updateProject')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateUserForOrgProject');

        $app->delete(
            '/api/v0/projects/{projectId}/',
            '\SolasMatch\API\V0\Projects:deleteProject')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateUserForOrgProject');

        $app->get(
            '/api/v0/projects/',
            '\SolasMatch\API\V0\Projects:getProjects')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');
    }

    public static function updateProjectWordCount(Request $request, Response $response, $args)
    {
        $projectId = $args['projectId'];
        $newWordCount = $args['newWordCount'];
        $ret = null;
        $ret = DAO\ProjectDao::updateProjectWordCount($projectId, $newWordCount);
        return API\Dispatcher::sendResponse($response, $ret, null);
    }

    public static function setImageApprovalStatus(Request $request, Response $response, $args)
    {
        $projectId = $args['projectId'];
        $imageStatus = $args['imageStatus'];
        $ret = null;
        $ret = DAO\ProjectDao::setImageApprovalStatus($projectId, $imageStatus);
        return API\Dispatcher::sendResponse($response, $ret, null);
    }

    public static function getProjectTasks(Request $request, Response $response, $args)
    {
        $projectId = $args['projectId'];
        $data = DAO\ProjectDao::getProjectTasks($projectId);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getProjectTags(Request $request, Response $response, $args)
    {
        $projectId = $args['projectId'];
        return API\Dispatcher::sendResponse($response, DAO\ProjectDao::getTags($projectId), null);
    }

    public static function getProjectFileInfo(Request $request, Response $response, $args)
    {
        $projectId = $args['projectId'];
        return API\Dispatcher::sendResponse($response,
            DAO\ProjectDao::getProjectFileInfo($projectId, null, null, null, null),
            null
        );
    }

    public static function getProjectFile(Request $request, Response $response, $args)
    {
        $projectId = $args['projectId'];
        return API\Dispatcher::sendResponse($response, DAO\ProjectDao::getProjectFile($projectId), null);
    }

    public static function getArchivedProjectTasks(Request $request, Response $response, $args)
    {
        $projectId = $args['projectId'];
        return API\Dispatcher::sendResponse($response, DAO\ProjectDao::getArchivedTask($projectId), null);
    }

    public static function deleteProjectTags(Request $request, Response $response, $args)
    {
        $projectId = $args['projectId'];
        return API\Dispatcher::sendResponse($response, DAO\ProjectDao::deleteProjectTags($projectId), null);
    }

    public static function archiveProject(Request $request, Response $response, $args)
    {
        $projectId = $args['projectId'];
        $userId = $args['userId'];
        return API\Dispatcher::sendResponse($response, DAO\ProjectDao::archiveProject($projectId, $userId), null);
    }

    public static function getProject(Request $request, Response $response, $args)
    {
        $projectId = $args['projectId'];
        return API\Dispatcher::sendResponse($response, DAO\ProjectDao::getProject($projectId), null);
    }

    public static function getProjectByName(Request $request, Response $response)
    {
        $title = (string)$request->getBody();

        $data = DAO\ProjectDao::getProjectByName($title);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getProjectByNameAndOrganisation(Request $request, Response $response, $args)
    {
        $title = $args['title'];
        $orgId = $args['orgId'];
        return API\Dispatcher::sendResponse($response, DAO\ProjectDao::getProjectByNameAndOrganisation($title, $orgId), null);
    }

    public static function updateProject(Request $request, Response $response, $args)
    {
        $projectId = $args['projectId'];
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\Project');
        return API\Dispatcher::sendResponse($response, DAO\ProjectDao::save($data), null);
    }

    public static function deleteProject(Request $request, Response $response, $args)
    {
        $projectId = $args['projectId'];
        return API\Dispatcher::sendResponse($response, DAO\ProjectDao::delete($projectId), null);
    }

    public static function getProjects(Request $request, Response $response)
    {
        return API\Dispatcher::sendResponse($response, DAO\ProjectDao::getProjects(), null);
    }
}

Projects::init();
