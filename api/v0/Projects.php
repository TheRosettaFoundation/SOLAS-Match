<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;

require_once __DIR__."/../DataAccessObjects/ProjectDao.class.php";
require_once __DIR__."/../DataAccessObjects/UserDao.class.php";
require_once __DIR__."/../../Common/protobufs/models/Project.php";
require_once __DIR__."/../../Common/lib/Settings.class.php";
require_once __DIR__."/../lib/APIWorkflowBuilder.class.php";


class Projects
{
    public static function init()
    {
        $app = \Slim\Slim::getInstance();

        $app->group('/v0', function () use ($app) {
            $app->group('/projects', function () use ($app) {
                $app->group('/:projectId', function () use ($app) {

                    /* Routes starting /v0/projects/:projectId */
                    $app->put(
                        '/updateWordCount/:newWordCount/',
                        '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
                        '\SolasMatch\API\V0\Projects::updateProjectWordCount'
                    );

                    $app->put(
                        '/setImageApprovalStatus/:imageStatus/',
                        '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
                        '\SolasMatch\API\V0\Projects::setImageApprovalStatus'
                    );

                    $app->post(
                        '/calculateDeadlines(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Projects::calculateProjectDeadlines'
                    );

                    $app->get(
                        '/reviews(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authenticateUserOrOrgForProjectTask',
                        '\SolasMatch\API\V0\Projects::getProjectTaskReviews'
                    );

                    $app->get(
                        '/tasks(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Projects::getProjectTasks'
                    );

                    $app->get(
                        '/tags(:format)/',
                        '\SolasMatch\API\V0\Projects::getProjectTags'
                    );

                    $app->get(
                        '/info(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Projects::getProjectFileInfo'
                    );

                    $app->get(
                        '/file(:format)/',
                        '\SolasMatch\API\V0\Projects::getProjectFile'
                    );

                    $app->get(
                        '/archivedTasks(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgProject',
                        '\SolasMatch\API\V0\Projects::getArchivedProjectTasks'
                    );

                    $app->delete(
                        '/deleteTags(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgProject',
                        '\SolasMatch\API\V0\Projects::deleteProjectTags'
                    );
                });

                /* Routes starting /v0/projects */
                $app->put(
                    '/archiveProject/:projectId/user/:userId/',
                    '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgProject',
                    '\SolasMatch\API\V0\Projects::archiveProject'
                );

                $app->get(
                    '/buildGraph/:projectId/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Projects::getProjectGraph'
                );

                $app->post(
                        '/getProjectByName/',
                        '\SolasMatch\API\V0\Projects::getProjectByName'
                );

                $app->get(
                        '/getProjectByNameAndOrganisation/:title/organisation/:orgId/',
                        '\SolasMatch\API\V0\Projects::getProjectByNameAndOrganisation'
                );

                $app->get(
                    '/:projectId/',
                    '\SolasMatch\API\V0\Projects::getProject'
                );

                $app->put(
                    '/:projectId/',
                    '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgProject',
                    '\SolasMatch\API\V0\Projects::updateProject'
                );

                $app->delete(
                    '/:projectId/',
                    '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgProject',
                    '\SolasMatch\API\V0\Projects::deleteProject'
                );

            });

            /* Routes starting /v0 */
            $app->get(
                '/archivedProjects/:projectId/',
                '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgProject',
                '\SolasMatch\API\V0\Projects::getArchivedProject'
            );

            $app->get(
                '/projects(:format)/',
                '\SolasMatch\API\Lib\Middleware::isloggedIn',
                '\SolasMatch\API\V0\Projects::getProjects'
            );

            $app->post(
                '/projects(:format)/',
                '\SolasMatch\API\Lib\Middleware::authenticateUserMembership',
                '\SolasMatch\API\V0\Projects::createProject'
            );

            $app->get(
                '/archivedProjects(:format)/',
                '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
                '\SolasMatch\API\V0\Projects::getArchivedProjects'
            );
        });
    }

    public static function updateProjectWordCount($projectId, $newWordCount, $format = ".json")
    {
        if (!is_numeric($newWordCount) && strstr($newWordCount, '.')) {
            $newWordCount = explode('.', $newWordCount);
            $format = '.'.$newWordCount[1];
            $newWordCount = $newWordCount[0];
        }

        $ret = null;
        $ret = DAO\ProjectDao::updateProjectWordCount($projectId, $newWordCount);
        API\Dispatcher::sendResponse(null, $ret, null, $format);
    }

    public static function setImageApprovalStatus($projectId, $imageStatus, $format = ".json")
    {
        if (!is_numeric($imageStatus) && strstr($imageStatus, '.')) {
            $imageStatus = explode('.', $imageStatus);
            $format = '.'.$imageStatus[1];
            $imageStatus = $imageStatus[0];
        }

        $ret = null;
        $ret = DAO\ProjectDao::setImageApprovalStatus($projectId, $imageStatus);
        API\Dispatcher::sendResponse(null, $ret, null, $format);
    }

    public static function calculateProjectDeadlines($projectId, $format = '.json')
    {
        $ret = null;
        $ret = DAO\ProjectDao::calculateProjectDeadlines($projectId);
        API\Dispatcher::sendResponse(null, $ret, null, $format);
    }

    public static function getProjectTaskReviews($projectId, $format = '.json')
    {
        $reviews = DAO\TaskDao::getTaskReviews($projectId);
        API\Dispatcher::sendResponse(null, $reviews, null, $format);
    }

    public static function getProjectTasks($projectId, $format = '.json')
    {
        $data = DAO\ProjectDao::getProjectTasks($projectId);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getProjectTags($projectId, $format = ".json")
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::getTags($projectId), null, $format);
    }

    public static function getProjectFileInfo($projectId, $format = ".json")
    {
        API\Dispatcher::sendResponse(
            null,
            DAO\ProjectDao::getProjectFileInfo($projectId, null, null, null, null),
            null,
            $format
        );
    }

    public static function getProjectFile($projectId, $format = ".json")
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::getProjectFile($projectId), null, $format);
    }

    public static function getArchivedProjectTasks($projectId, $format = ".json")
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::getArchivedTask($projectId), null, $format);
    }

    public static function deleteProjectTags($projectId, $format = ".json")
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::deleteProjectTags($projectId), null, $format);
    }

    public static function archiveProject($projectId, $userId, $format = ".json")
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::archiveProject($projectId, $userId), null, $format);
    }

    public static function getProjectGraph($projectId, $format = '.json')
    {
        if (!is_numeric($projectId) && strstr($projectId, '.')) {
            $projectId = explode('.', $projectId);
            $format = '.'.$projectId[1];
            $projectId = $projectId[0];
        }
        $builder = new Lib\APIWorkflowBuilder();
        $graph = $builder->buildProjectGraph($projectId);
        API\Dispatcher::sendResponse(null, $graph, null, $format);
    }

    public static function getProject($projectId, $format = '.json')
    {
        if (!is_numeric($projectId) && strstr($projectId, '.')) {
            $projectId = explode('.', $projectId);
            $format = '.'.$projectId[1];
            $projectId = $projectId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::getProject($projectId), null, $format);
    }

    public static function getProjectByName($format = ".json")
    {
        $title = API\Dispatcher::getDispatcher()->request()->getBody();

        $data = DAO\ProjectDao::getProjectByName($title);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getProjectByNameAndOrganisation($title, $orgId, $format = ".json")
    {
        if (!is_numeric($orgId) && strstr($orgId, '.')) {
            $orgId = explode('.', $orgId);
            $format = '.'.$orgId[1];
            $orgId = $orgId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::getProjectByNameAndOrganisation($title, $orgId), null, $format);
    }

    public static function updateProject($projectId, $format = '.json')
    {
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
    }

    public static function deleteProject($projectId, $format = ".json")
    {
        if (!is_numeric($projectId) && strstr($projectId, '.')) {
            $projectId = explode('.', $projectId);
            $format = '.'.$projectId[1];
            $projectId = $projectId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::delete($projectId), null, $format);
    }

    public static function getArchivedProject($projectId, $format = '.json')
    {
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
    }

    public static function getProjects($format = '.json')
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::getProjects(), null, $format);
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

    public static function createProject($format = '.json')
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\Project');
        $project = DAO\ProjectDao::save($data);
        if (!is_null($project) && $project->getId() > 0) {
            // Auto track the project for admins
            $admins = self::getAutoFollowAdminIds();
            self::addTrackProjectForUsers($admins, $project->getId());
            API\Dispatcher::sendResponse(null, $project, null, $format);
        } else {
            API\Dispatcher::sendResponse(
                null,
                "Project details conflict with existing data",
                Common\Enums\HttpStatusEnum::CONFLICT,
                $format
            );
        }
    }

    public static function getArchivedProjects($format = '.json')
    {
        API\Dispatcher::sendResponse(null, DAO\ProjectDao::getArchivedProject(), null, $format);
    }
}

Projects::init();
