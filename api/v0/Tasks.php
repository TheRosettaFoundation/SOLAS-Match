<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;

/**
 * Description of Tasks
 *
 * @author sean
 */

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
        /**
         * Get a single task object based on its id
         */
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/tasks/:taskId/',
            function ($taskId, $format = ".json") {
                if (!is_numeric($taskId) && strstr($taskId, '.')) {
                    $taskId = explode('.', $taskId);
                    $format = '.'.$taskId[1];
                    $taskId = $taskId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\TaskDao::getTask($taskId), null, $format);
            },
            'getTask',
            null
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/tasks(:format)/',
            function ($format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\TaskDao::getTasks(), null, $format);
            },
            'getTasks',
            null
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/tasks(:format)/',
            function ($format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Task");
                API\Dispatcher::sendResponse(null, DAO\TaskDao::save($data), null, $format);
            },
            'createTask',
            '\SolasMatch\API\Lib\Middleware::authUserOrOrgForTaskCreation'
        );
        
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/tasks/:taskId/',
            function ($taskId, $format = ".json") {
                if (!is_numeric($taskId) && strstr($taskId, '.')) {
                    $taskId = explode('.', $taskId);
                    $format = '.'.$taskId[1];
                    $taskId = $taskId[0];
                }
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Task");
                API\Dispatcher::sendResponse(null, DAO\TaskDao::save($data), null, $format);
            },
            'updateTask',
            '\SolasMatch\API\Lib\Middleware::authUserOrOrgForTaskCreationPassingTaskId'
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/tasks/:taskId/',
            function ($taskId, $format = ".json") {
                if (!is_numeric($taskId) && strstr($taskId, '.')) {
                    $taskId = explode('.', $taskId);
                    $format = '.'.$taskId[1];
                    $taskId = $taskId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\TaskDao::delete($taskId), null, $format);
            },
            'deleteTask',
            '\SolasMatch\API\Lib\Middleware::authUserOrOrgForTaskCreationPassingTaskId'
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/tasks/:taskId/prerequisites(:format)/',
            function ($taskId, $format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\TaskDao::getTaskPreReqs($taskId), null, $format);
            },
            'getTaskPreReqs',
            '\SolasMatch\API\Lib\Middleware::authUserOrOrgForClaimedTask'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/tasks/:taskId/prerequisites/:preReqId/',
            function ($taskId, $preReqId, $format = ".json") {
                if (!is_numeric($preReqId) && strstr($preReqId, '.')) {
                    $preReqId = explode('.', $preReqId);
                    $format = '.'.$preReqId[1];
                    $preReqId = $preReqId[0];
                }
                API\Dispatcher::sendResponse(null, Lib\Upload::addTaskPreReq($taskId, $preReqId), null, $format);
            },
            'addTaskPreReq',
            '\SolasMatch\API\Lib\Middleware::authUserOrOrgForTaskCreationPassingTaskId'
        );


        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/tasks/:taskId/prerequisites/:preReqId/',
            function ($taskId, $preReqId, $format = ".json") {
                if (!is_numeric($preReqId) && strstr($preReqId, '.')) {
                    $preReqId = explode('.', $preReqId);
                    $format = '.'.$preReqId[1];
                    $preReqId = $preReqId[0];
                }
                API\Dispatcher::sendResponse(null, Lib\Upload::removeTaskPreReq($taskId, $preReqId), null, $format);
            },
            'removeTaskPreReq',
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgTask'
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/tasks/archiveTask/:taskId/user/:userId/',
            function ($taskId, $userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\TaskDao::moveToArchiveByID($taskId, $userId), null, $format);
            },
            'archiveTask'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/tasks/topTasks(:format)/',
            function ($format = ".json") {
                $limit = API\Dispatcher::clenseArgs('limit', Common\Enums\HttpMethodEnum::GET, 15);
                $offset = API\Dispatcher::clenseArgs('offset', Common\Enums\HttpMethodEnum::GET, 0);
                API\Dispatcher::sendResponse(null, DAO\TaskDao::getLatestAvailableTasks($limit, $offset), null, $format);
            },
            'getTopTasks',
            null
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/tasks/:taskId/reviews(:format)/',
            function ($taskId, $format = '.json') {
                $review = DAO\TaskDao::getTaskReviews(null, $taskId);
                API\Dispatcher::sendResponse(null, $review, null, $format);
            },
            'getTaskReview',
            '\SolasMatch\API\Lib\Middleware::authUserOrOrgForClaimedTask'
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/tasks/reviews(:format)/',
            function ($format = '.json') {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $review = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\TaskReview");
                API\Dispatcher::sendResponse(null, DAO\TaskDao::submitReview($review), null, $format);
            },
            'submitReview',
            '\SolasMatch\API\Lib\Middleware::authenticateUserToSubmitReview'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/tasks/:taskId/tags(:format)/',
            function ($taskId, $format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\TaskDao::getTags($taskId), null, $format);
            },
            'getTasksTags',
            null
        );
        
        // Org Feedback, feedback sent from the organisation to the user who claimed the task
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/tasks/:taskId/orgFeedback(:format)/',
            function ($taskId, $format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $feedbackData = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Emails\OrgFeedback");
                Lib\Notify::sendOrgFeedback($feedbackData);
                API\Dispatcher::sendResponse(null, null, null, $format);
            },
            'sendOrgFeedback',
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgTask'
        );
        
        // User Feedback, feedback sent from the user who claimed the task to the organisation
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/tasks/:taskId/userFeedback(:format)/',
            function ($taskId, $format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $feedbackData = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\UserFeedback");
                Lib\Notify::sendUserFeedback($feedbackData);
                API\Dispatcher::sendResponse(null, null, null, $format);
            },
            'sendUserFeedback',
            '\SolasMatch\API\Lib\Middleware::authUserForClaimedTask'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/tasks/:taskId/file(:format)/',
            function ($taskId, $format = ".json") {
                $version = API\Dispatcher::clenseArgs('version', Common\Enums\HttpMethodEnum::GET, 0);
                $convert = API\Dispatcher::clenseArgs('convertToXliff', Common\Enums\HttpMethodEnum::GET, false);
                if ($convert && $convert !== "") {
                    DAO\TaskDao::downloadConvertedTask($taskId, $version);
                } else {
                    DAO\TaskDao::downloadTask($taskId, $version);
                }
            },
            'getTaskFile',
            null
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/tasks/saveFile/:taskId/:userId/',
            function ($taskId, $userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $task = DAO\TaskDao::getTask($taskId);
                if (is_array($task)) {
                    $task = $task[0];
                }
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
            },
            'saveTaskFile'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/tasks/uploadOutputFile/:taskId/:userId/',
            function ($taskId, $userId, $format = ".json") {
                if (!is_numeric($userId) && strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $task = DAO\TaskDao::getTask($taskId);
                if (is_array($task)) {
                    $task = $task[0];
                }
                $projectFile = DAO\ProjectDao::getProjectFileInfo($task->getProjectId(), null, null, null, null);
                $filename = $projectFile->getFilename();
                $convert = API\Dispatcher::clenseArgs('convertFromXliff', Common\Enums\HttpMethodEnum::GET, false);
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                DAO\TaskDao::uploadOutputFile($task, $convert, $data, $userId, $filename);
            },
            'uploadOutputFile'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/tasks/:taskId/version(:format)/',
            function ($taskId, $format = ".json") {
                $userId = API\Dispatcher::clenseArgs('userId', Common\Enums\HttpMethodEnum::GET, null);
                API\Dispatcher::sendResponse(null, DAO\TaskDao::getLatestFileVersion($taskId, $userId), null, $format);
            },
            'getTaskVersion'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/tasks/:taskId/info(:format)/',
            function ($taskId, $format = ".json") {
                $version = API\Dispatcher::clenseArgs('version', Common\Enums\HttpMethodEnum::GET, 0);
                $taskMetadata = Common\Lib\ModelFactory::buildModel(
                    "TaskMetadata",
                    DAO\TaskDao::getTaskFileInfo($taskId, $version)
                );
                API\Dispatcher::sendResponse(null, $taskMetadata, null, $format);
            },
            'getTaskInfo'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/tasks/:taskId/claimed(:format)/',
            function ($taskId, $format = ".json") {
                $data = null;
                $userId = API\Dispatcher::clenseArgs('userId', Common\Enums\HttpMethodEnum::GET, null);
                if (is_numeric($userId)) {
                    $data = DAO\TaskDao::hasUserClaimedTask($userId, $taskId);
                } else {
                    $data = DAO\TaskDao::taskIsClaimed($taskId);
                }
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getTaskClaimed'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/tasks/:taskId/user(:format)/',
            function ($taskId, $format = ".json") {
                $data = DAO\TaskDao::getUserClaimedTask($taskId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUserClaimedTask'
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/tasks/:taskId/timeClaimed(:format)/',
            function ($taskId, $format = ".json") {
                $data = DAO\TaskDao::getClaimedTime($taskId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getClaimedTime'
        );
    }
}
Tasks::init();
