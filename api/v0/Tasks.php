<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;

require_once __DIR__."/../DataAccessObjects/TaskDao.class.php";
require_once __DIR__."/../../Common/protobufs/models/TaskMetadata.php";
require_once __DIR__."/../../Common/protobufs/emails/UserFeedback.php";
require_once __DIR__."/../../Common/protobufs/emails/OrgFeedback.php";
require_once __DIR__."/../lib/IO.class.php";
require_once __DIR__."/../lib/Upload.class.php";
require_once __DIR__."/../lib/FormatConverter.php";
require_once __DIR__."/../../Common/lib/SolasMatchException.php";

class Tasks
{
    public static function init()
    {
        $app = \Slim\Slim::getInstance();

        $app->group('/v0', function () use ($app) {
            $app->group('/tasks', function () use ($app) {
                $app->group('/:taskId', function () use ($app) {
                    $app->group('/prerequisites', function () use ($app) {

                        /* Routes starting /v0/tasks/:taskId/prerequisites */
                        $app->put(
                            '/:preReqId/',
                            '\SolasMatch\API\Lib\Middleware::authUserOrOrgForTaskCreationPassingTaskId',
                            '\SolasMatch\API\V0\Tasks::addTaskPreReq'
                        );

                        $app->delete(
                            '/:preReqId/',
                            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgTask',
                            '\SolasMatch\API\V0\Tasks::removeTaskPreReq'
                        );
                    });

                    /* Routes starting /v0/tasks/:taskId */
                    $app->put(
                        '/orgFeedback(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgTask',
                        '\SolasMatch\API\V0\Tasks::sendOrgFeedback'
                    );

                    $app->put(
                        '/userFeedback(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserForClaimedTask',
                        '\SolasMatch\API\V0\Tasks::sendUserFeedback'
                    );

                    $app->get(
                        '/prerequisites(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOrOrgForClaimedTask',
                        '\SolasMatch\API\V0\Tasks::getTaskPreReqs'
                    );

                    $app->get(
                        '/reviews(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOrOrgForClaimedTask',
                        '\SolasMatch\API\V0\Tasks::getTaskReview'
                    );

                    $app->get(
                        '/tags(:format)/',
                        '\SolasMatch\API\V0\Tasks::getTasksTags'
                    );

                    $app->get(
                        '/file(:format)/',
                        '\SolasMatch\API\V0\Tasks::getTaskFile'
                    );

                    $app->get(
                        '/version(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Tasks::getTaskVersion'
                    );

                    $app->get(
                        '/info(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Tasks::getTaskInfo'
                    );

                    $app->get(
                        '/claimed(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Tasks::getTaskClaimed'
                    );

                    $app->get(
                        '/user(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Tasks::getUserClaimedTask'
                    );

                    $app->get(
                        '/timeClaimed(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Tasks::getClaimedTime'
                    );
                });

                /* Routes starting /v0/tasks */
                $app->put(
                    '/archiveTask/:taskId/user/:userId/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Tasks::archiveTask'
                );

                $app->put(
                    '/uploadOutputFile/:taskId/:userId/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Tasks::uploadOutputFile'
                );

                $app->put(
                    '/saveFile/:taskId/:userId/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Tasks::saveTaskFile'
                );

                $app->post(
                    '/reviews(:format)/',
                    '\SolasMatch\API\Lib\Middleware::authenticateUserToSubmitReview',
                    '\SolasMatch\API\V0\Tasks::submitReview'
                );

                $app->get(
                    '/topTasks(:format)/',
                    '\SolasMatch\API\V0\Tasks::getTopTasks'
                );

                $app->get(
                    '/:taskId/',
                    '\SolasMatch\API\V0\Tasks::getTask'
                );

                $app->put(
                    '/:taskId/',
                    '\SolasMatch\API\Lib\Middleware::authUserOrOrgForTaskCreationPassingTaskId',
                    '\SolasMatch\API\V0\Tasks::updateTask'
                );

                $app->delete(
                    '/:taskId/',
                    '\SolasMatch\API\Lib\Middleware::authUserOrOrgForTaskCreationPassingTaskId',
                    '\SolasMatch\API\V0\Tasks::deleteTask'
                );
            });

            /* Routes starting /v0 */
            $app->get(
                '/tasks(:format)/',
                '\SolasMatch\API\V0\Tasks::getTasks'
            );

            $app->post(
                '/tasks(:format)/',
                '\SolasMatch\API\Lib\Middleware::authUserOrOrgForTaskCreation',
                '\SolasMatch\API\V0\Tasks::createTask'
            );
        });
    }

    public static function addTaskPreReq($taskId, $preReqId, $format = ".json")
    {
        if (!is_numeric($preReqId) && strstr($preReqId, '.')) {
            $preReqId = explode('.', $preReqId);
            $format = '.'.$preReqId[1];
            $preReqId = $preReqId[0];
        }
        API\Dispatcher::sendResponse(null, Lib\Upload::addTaskPreReq($taskId, $preReqId), null, $format);
    }

    public static function removeTaskPreReq($taskId, $preReqId, $format = ".json")
    {
        if (!is_numeric($preReqId) && strstr($preReqId, '.')) {
            $preReqId = explode('.', $preReqId);
            $format = '.'.$preReqId[1];
            $preReqId = $preReqId[0];
        }
        API\Dispatcher::sendResponse(null, Lib\Upload::removeTaskPreReq($taskId, $preReqId), null, $format);
    }

    // Org Feedback, feedback sent from the organisation to the user who claimed the task
    public static function sendOrgFeedback($taskId, $format = ".json")
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $feedbackData = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Emails\OrgFeedback");
        Lib\Notify::sendOrgFeedback($feedbackData);
        API\Dispatcher::sendResponse(null, null, null, $format);
    }

    // User Feedback, feedback sent from the user who claimed the task to the organisation
    public static function sendUserFeedback($taskId, $format = ".json")
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $feedbackData = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\UserFeedback");
        Lib\Notify::sendUserFeedback($feedbackData);
        API\Dispatcher::sendResponse(null, null, null, $format);
    }

    public static function getTaskPreReqs($taskId, $format = ".json")
    {
        API\Dispatcher::sendResponse(null, DAO\TaskDao::getTaskPreReqs($taskId), null, $format);
    }

    public static function getTaskReview($taskId, $format = '.json')
    {
        $review = DAO\TaskDao::getTaskReviews(null, $taskId);
        API\Dispatcher::sendResponse(null, $review, null, $format);
    }

    public static function getTasksTags($taskId, $format = ".json")
    {
        API\Dispatcher::sendResponse(null, DAO\TaskDao::getTags($taskId), null, $format);
    }

    public static function getTaskFile($taskId, $format = ".json")
    {
        $version = API\Dispatcher::clenseArgs('version', Common\Enums\HttpMethodEnum::GET, 0);
        $convert = API\Dispatcher::clenseArgs('convertToXliff', Common\Enums\HttpMethodEnum::GET, false);
        if ($convert && $convert !== "") {
            DAO\TaskDao::downloadConvertedTask($taskId, $version);
        } else {
            DAO\TaskDao::downloadTask($taskId, $version);
        }
    }

    public static function getTaskVersion($taskId, $format = ".json")
    {
        $userId = API\Dispatcher::clenseArgs('userId', Common\Enums\HttpMethodEnum::GET, null);
        API\Dispatcher::sendResponse(null, DAO\TaskDao::getLatestFileVersion($taskId, $userId), null, $format);
    }

    public static function getTaskInfo($taskId, $format = ".json")
    {
        $version = API\Dispatcher::clenseArgs('version', Common\Enums\HttpMethodEnum::GET, 0);
        $taskMetadata = Common\Lib\ModelFactory::buildModel(
            "TaskMetadata",
            DAO\TaskDao::getTaskFileInfo($taskId, $version)
        );
        API\Dispatcher::sendResponse(null, $taskMetadata, null, $format);
    }

    public static function getTaskClaimed($taskId, $format = ".json")
    {
        $data = null;
        $userId = API\Dispatcher::clenseArgs('userId', Common\Enums\HttpMethodEnum::GET, null);
        if (is_numeric($userId)) {
            $data = DAO\TaskDao::hasUserClaimedTask($userId, $taskId);
        } else {
            $data = DAO\TaskDao::taskIsClaimed($taskId);
        }
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getUserClaimedTask($taskId, $format = ".json")
    {
        $data = DAO\TaskDao::getUserClaimedTask($taskId);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getClaimedTime($taskId, $format = ".json")
    {
        $data = DAO\TaskDao::getClaimedTime($taskId);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function archiveTask($taskId, $userId, $format = ".json")
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\TaskDao::moveToArchiveByID($taskId, $userId), null, $format);
    }

    public static function uploadOutputFile($taskId, $userId, $format = ".json")
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        $task = DAO\TaskDao::getTask($taskId);
        $projectFile = DAO\ProjectDao::getProjectFileInfo($task->getProjectId(), null, null, null, null);
        $filename = $projectFile->getFilename();
        $convert = API\Dispatcher::clenseArgs('convertFromXliff', Common\Enums\HttpMethodEnum::GET, false);
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        DAO\TaskDao::uploadOutputFile($task, $convert, $data, $userId, $filename);
    }

    public static function saveTaskFile($taskId, $userId, $format = ".json")
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        $task = DAO\TaskDao::getTask($taskId);
        $version = API\Dispatcher::clenseArgs('version', Common\Enums\HttpMethodEnum::GET, null);
        $convert = API\Dispatcher::clenseArgs('convertFromXliff', Common\Enums\HttpMethodEnum::GET, false);
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $projectFile = DAO\ProjectDao::getProjectFileInfo($task->getProjectId(), null, null, null, null);
        $filename = $projectFile->getFilename();
        try {
            DAO\TaskDao::uploadFile($task, $convert, $data, $version, $userId, $filename);
        } catch (Common\Exceptions\SolasMatchException $e) {
            API\Dispatcher::sendResponse(null, $e->getMessage(), $e->getCode());
            return;
        }
        API\Dispatcher::sendResponse(null, null, Common\Enums\HttpStatusEnum::CREATED);
    }

    public static function submitReview($format = '.json')
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $review = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\TaskReview");
        API\Dispatcher::sendResponse(null, DAO\TaskDao::submitReview($review), null, $format);
    }

    public static function getTopTasks($format = ".json")
    {
        $limit = API\Dispatcher::clenseArgs('limit', Common\Enums\HttpMethodEnum::GET, 15);
        $offset = API\Dispatcher::clenseArgs('offset', Common\Enums\HttpMethodEnum::GET, 0);
        API\Dispatcher::sendResponse(null, DAO\TaskDao::getLatestAvailableTasks($limit, $offset), null, $format);
    }

    public static function getTask($taskId, $format = ".json")
    {
        if (!is_numeric($taskId) && strstr($taskId, '.')) {
            $taskId = explode('.', $taskId);
            $format = '.'.$taskId[1];
            $taskId = $taskId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\TaskDao::getTask($taskId), null, $format);
    }

    public static function updateTask($taskId, $format = ".json")
    {
        if (!is_numeric($taskId) && strstr($taskId, '.')) {
            $taskId = explode('.', $taskId);
            $format = '.'.$taskId[1];
            $taskId = $taskId[0];
        }
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Task");
        API\Dispatcher::sendResponse(null, DAO\TaskDao::save($data), null, $format);
    }

    public static function deleteTask($taskId, $format = ".json")
    {
        if (!is_numeric($taskId) && strstr($taskId, '.')) {
            $taskId = explode('.', $taskId);
            $format = '.'.$taskId[1];
            $taskId = $taskId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\TaskDao::delete($taskId), null, $format);
    }

    public static function getTasks($format = ".json")
    {
        API\Dispatcher::sendResponse(null, DAO\TaskDao::getTasks(), null, $format);
    }

    public static function createTask($format = ".json")
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Task");
        API\Dispatcher::sendResponse(null, DAO\TaskDao::save($data), null, $format);
    }
}
Tasks::init();
