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
                        '/orgFeedback/',
                        '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgTask',
                        '\SolasMatch\API\V0\Tasks::sendOrgFeedback'
                    );

                    $app->put(
                        '/sendOrgFeedbackDeclined/',
                        '\SolasMatch\API\V0\Tasks::sendOrgFeedbackDeclined'
                    );

                    $app->put(
                        '/userFeedback/',
                        '\SolasMatch\API\Lib\Middleware::authUserForClaimedTask',
                        '\SolasMatch\API\V0\Tasks::sendUserFeedback'
                    );
                    
                    $app->get(
                        '/alsoViewedTasks/:limit/:offset/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Tasks::getAlsoViewedTasks'
                    );

                    $app->get(
                        '/prerequisites/',
                        '\SolasMatch\API\Lib\Middleware::authUserOrOrgForClaimedTask',
                        '\SolasMatch\API\V0\Tasks::getTaskPreReqs'
                    );
                    

                    $app->get(
                        '/reviews/',
                        '\SolasMatch\API\Lib\Middleware::authUserOrOrgForClaimedTask',
                        '\SolasMatch\API\V0\Tasks::getTaskReview'
                    );

                    $app->get(
                        '/tags/',
                        '\SolasMatch\API\V0\Tasks::getTasksTags'
                    );
                    
                
                    $app->get(
                        '/version/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Tasks::getTaskVersion'
                    );

                    $app->get(
                        '/info/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Tasks::getTaskInfo'
                    );

                    $app->get(
                        '/claimed/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Tasks::getTaskClaimed'
                    );

                    $app->get(
                        '/user/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Tasks::getUserClaimedTask'
                    );

                    $app->get(
                        '/timeClaimed/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Tasks::getClaimedTime'
                    );
                });

                /* Routes starting /v0/tasks */
                $app->put(
                    '/archiveTask/:taskId/user/:userId/',
                    '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
                    '\SolasMatch\API\V0\Tasks::archiveTask'
                );
                
                $app->put(
                    '/recordView/:taskId/user/:userId/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Tasks::recordTaskView'
                );
            
               
                $app->get(
                    '/proofreadTask/:taskId/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Tasks::getProofreadTask'
                );

                $app->post(
                    '/reviews/',
                    '\SolasMatch\API\Lib\Middleware::authenticateUserToSubmitReview',
                    '\SolasMatch\API\V0\Tasks::submitReview'
                );
                
                $app->get(
                    '/topTasksCount/',
                    '\SolasMatch\API\V0\Tasks::getTopTasksCount'
                );
                
                $app->get(
                    '/topTasks/',
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
                '/tasks/',
                '\SolasMatch\API\V0\Tasks::getTasks'
            );

            $app->post(
                '/tasks/',
                '\SolasMatch\API\Lib\Middleware::authUserOrOrgForTaskCreation',
                '\SolasMatch\API\V0\Tasks::createTask'
            );
        });
    }

    public static function addTaskPreReq($taskId, $preReqId)
    {
        API\Dispatcher::sendResponse(null, Lib\Upload::addTaskPreReq($taskId, $preReqId), null);
    }

    public static function removeTaskPreReq($taskId, $preReqId)
    {
        API\Dispatcher::sendResponse(null, Lib\Upload::removeTaskPreReq($taskId, $preReqId), null);
    }

    // Org Feedback, feedback sent from the organisation to the user who claimed the task
    public static function sendOrgFeedback($taskId)
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $feedbackData = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Emails\OrgFeedback");
        Lib\Notify::sendOrgFeedback($feedbackData);
        API\Dispatcher::sendResponse(null, null, null);
    }

    // If DECLINED status comes from Memsource, notify claimant
    public static function sendOrgFeedbackDeclined($taskId)
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $feedbackData = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Emails\OrgFeedback');
        $task_id     = $feedbackData->getTaskId();
        $claimant_id = $feedbackData->getClaimantId();
        $user_id     = $feedbackData->getUserId();
        $feedback    = $feedbackData->getFeedback();

        $pos = strpos($feedback, '::');
        $data = substr($feedback, 0, $pos);
        $feedback = substr($feedback, $pos + 2);
        $feedbackData->setFeedback($feedback);

        $task_claimant_user = DAO\TaskDao::decrypt_to_verify_integrity($data);
        if ($task_claimant_user === "$task_id,$claimant_id,$user_id") Lib\Notify::sendOrgFeedback($feedbackData);
        else error_log("Security mismatch: $task_claimant_user !== $task_id,$claimant_id,$user_id");

        API\Dispatcher::sendResponse(null, null, null);
    }

    // User Feedback, feedback sent from the user who claimed the task to the organisation
    public static function sendUserFeedback($taskId)
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $feedbackData = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Emails\UserFeedback");
        Lib\Notify::sendUserFeedback($feedbackData);
        API\Dispatcher::sendResponse(null, null, null);
    }
    
    public static function getAlsoViewedTasks(
            $taskId,
            $limit,
            $offset
    ) {
        API\Dispatcher::sendResponse(
            null,
            DAO\TaskDao::getAlsoViewedTasks(
                $taskId,
                $limit,
                $offset
            ),
            null
        );
    }
    
    public static function getTaskPreReqs($taskId)
    {
        API\Dispatcher::sendResponse(null, DAO\TaskDao::getTaskPreReqs($taskId), null);
    }

    public static function getTaskReview($taskId)
    {
        $review = DAO\TaskDao::getTaskReviews(null, $taskId);
        API\Dispatcher::sendResponse(null, $review, null);
    }

    public static function getTasksTags($taskId)
    {
        API\Dispatcher::sendResponse(null, DAO\TaskDao::getTags($taskId), null);
    }

    public static function getTaskVersion($taskId)
    {
        $userId = API\Dispatcher::clenseArgs('userId', Common\Enums\HttpMethodEnum::GET, null);
        API\Dispatcher::sendResponse(null, DAO\TaskDao::getLatestFileVersion($taskId, $userId), null);
    }

    public static function getTaskInfo($taskId)
    {
        $version = API\Dispatcher::clenseArgs('version', Common\Enums\HttpMethodEnum::GET, 0);
        $taskMetadata = Common\Lib\ModelFactory::buildModel(
            "TaskMetadata",
            DAO\TaskDao::getTaskFileInfo($taskId, $version)
        );
        API\Dispatcher::sendResponse(null, $taskMetadata, null);
    }

    public static function getTaskClaimed($taskId)
    {
        $data = null;
        $userId = API\Dispatcher::clenseArgs('userId', Common\Enums\HttpMethodEnum::GET, null);
        if (is_numeric($userId)) {
            $data = DAO\TaskDao::hasUserClaimedTask($userId, $taskId);
        } else {
            $data = DAO\TaskDao::taskIsClaimed($taskId);
        }
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getUserClaimedTask($taskId)
    {
        $data = DAO\TaskDao::getUserClaimedTask($taskId);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function getClaimedTime($taskId)
    {
        $data = DAO\TaskDao::getClaimedTime($taskId);
        API\Dispatcher::sendResponse(null, $data, null);
    }

    public static function archiveTask($taskId, $userId)
    {
        API\Dispatcher::sendResponse(null, DAO\TaskDao::moveToArchiveByID($taskId, $userId), null);
    }

    public static function recordTaskView($taskId, $userId)
    {
        API\Dispatcher::sendResponse(null, DAO\TaskDao::recordTaskView($taskId, $userId), null);
    }   
    
    public static function getProofreadTask($taskId)
    {
        API\Dispatcher::sendResponse(null, DAO\TaskDao::getProofreadTask($taskId), null);
    }   
    
    public static function submitReview()
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $review = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\TaskReview");
        API\Dispatcher::sendResponse(null, DAO\TaskDao::submitReview($review), null);
    }

    public static function getTopTasks()
    {
        $limit = API\Dispatcher::clenseArgs('limit', Common\Enums\HttpMethodEnum::GET, 15);
        $offset = API\Dispatcher::clenseArgs('offset', Common\Enums\HttpMethodEnum::GET, 0);
        API\Dispatcher::sendResponse(null, DAO\TaskDao::getLatestAvailableTasks($limit, $offset), null);
    }
    
    public static function getTopTasksCount()
    {
        API\Dispatcher::sendResponse(null, DAO\TaskDao::getLatestAvailableTasksCount(), null);
    }

    public static function getTask($taskId)
    {
        API\Dispatcher::sendResponse(null, DAO\TaskDao::getTask($taskId), null);
    }

    public static function updateTask($taskId)
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Task");
        API\Dispatcher::sendResponse(null, DAO\TaskDao::save($data), null);
    }

    public static function deleteTask($taskId)
    {
        API\Dispatcher::sendResponse(null, DAO\TaskDao::delete($taskId), null);
    }

    public static function getTasks()
    {
        API\Dispatcher::sendResponse(null, DAO\TaskDao::getTasks(), null);
    }

    public static function createTask()
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Task");
        API\Dispatcher::sendResponse(null, DAO\TaskDao::save($data), null);
    }
}
Tasks::init();
