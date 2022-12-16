<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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
        global $app;

        $app->put(
            '/api/v0/tasks/{taskId}/orgFeedback/',
            '\SolasMatch\API\V0\Tasks:sendOrgFeedback')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateUserForOrgTask');

        $app->put(
            '/api/v0/tasks/{taskId}/sendOrgFeedbackDeclined/',
            '\SolasMatch\API\V0\Tasks:sendOrgFeedbackDeclined');

        $app->put(
            '/api/v0/tasks/{taskId}/userFeedback/',
            '\SolasMatch\API\V0\Tasks:sendUserFeedback')
            ->add('\SolasMatch\API\Lib\Middleware:authUserForClaimedTask');

        $app->get(
            '/api/v0/tasks/{taskId}/alsoViewedTasks/{limit}/{offset}/',
            '\SolasMatch\API\V0\Tasks:getAlsoViewedTasks')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/api/v0/tasks/{taskId}/prerequisites/',
            '\SolasMatch\API\V0\Tasks:getTaskPreReqs')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOrOrgForClaimedTask');

        $app->get(
            '/api/v0/tasks/{taskId}/reviews/',
            '\SolasMatch\API\V0\Tasks:getTaskReview')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOrOrgForClaimedTask');

        $app->get(
            '/api/v0/tasks/{taskId}/tags/',
            '\SolasMatch\API\V0\Tasks:getTasksTags');

        $app->get(
            '/api/v0/tasks/{taskId}/version/',
            '\SolasMatch\API\V0\Tasks:getTaskVersion')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/api/v0/tasks/{taskId}/info/',
            '\SolasMatch\API\V0\Tasks:getTaskInfo')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/api/v0/tasks/{taskId}/claimed/',
            '\SolasMatch\API\V0\Tasks:getTaskClaimed')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/api/v0/tasks/{taskId}/user/',
            '\SolasMatch\API\V0\Tasks:getUserClaimedTask')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->get(
            '/api/v0/tasks/{taskId}/timeClaimed/',
            '\SolasMatch\API\V0\Tasks:getClaimedTime')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->put(
            '/api/v0/tasks/archiveTask/{taskId}/user/{userId}/',
            '\SolasMatch\API\V0\Tasks:archiveTask')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateSiteAdmin');

        $app->put(
            '/api/v0/tasks/recordView/{taskId}/user/{userId}/',
            '\SolasMatch\API\V0\Tasks:recordTaskView')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');


        $app->get(
            '/api/v0/tasks/proofreadTask/{taskId}/',
            '\SolasMatch\API\V0\Tasks:getProofreadTask')
            ->add('\SolasMatch\API\Lib\Middleware:isloggedIn');

        $app->post(
            '/api/v0/tasks/reviews/',
            '\SolasMatch\API\V0\Tasks:submitReview')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateUserToSubmitReview');

        $app->get(
            '/api/v0/tasks/topTasksCount/',
            '\SolasMatch\API\V0\Tasks:getTopTasksCount');

        $app->get(
            '/api/v0/tasks/topTasks/',
            '\SolasMatch\API\V0\Tasks:getTopTasks');

        $app->get(
            '/api/v0/tasks/{taskId}/',
            '\SolasMatch\API\V0\Tasks:getTask');

        $app->delete(
            '/api/v0/tasks/{taskId}/',
            '\SolasMatch\API\V0\Tasks:deleteTask')
            ->add('\SolasMatch\API\Lib\Middleware:authUserOrOrgForTaskCreationPassingTaskId');
    }

    // Org Feedback, feedback sent from the organisation to the user who claimed the task
    public static function sendOrgFeedback(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $feedbackData = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Emails\OrgFeedback");
        Lib\Notify::sendOrgFeedback($feedbackData);
        return API\Dispatcher::sendResponse($response, null, null);
    }

    // If DECLINED status comes from Memsource, notify claimant
    public static function sendOrgFeedbackDeclined(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        $data = (string)$request->getBody();
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

        return API\Dispatcher::sendResponse($response, null, null);
    }

    // User Feedback, feedback sent from the user who claimed the task to the organisation
    public static function sendUserFeedback(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $feedbackData = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Emails\UserFeedback");
        Lib\Notify::sendUserFeedback($feedbackData);
        return API\Dispatcher::sendResponse($response, null, null);
    }
    
    public static function getAlsoViewedTasks(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        $limit = $args['limit'];
        $offset = $args['offset'];
        return API\Dispatcher::sendResponse($response,
            DAO\TaskDao::getAlsoViewedTasks(
                $taskId,
                $limit,
                $offset
            ),
            null
        );
    }

    public static function getTaskPreReqs(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        return API\Dispatcher::sendResponse($response, DAO\TaskDao::getTaskPreReqs($taskId), null);
    }

    public static function getTaskReview(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        $review = DAO\TaskDao::getTaskReviews(null, $taskId);
        return API\Dispatcher::sendResponse($response, $review, null);
    }

    public static function getTasksTags(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        return API\Dispatcher::sendResponse($response, DAO\TaskDao::getTags($taskId), null);
    }

    public static function getTaskVersion(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        $userId = API\Dispatcher::clenseArgs($request, 'userId', null);
        return API\Dispatcher::sendResponse($response, DAO\TaskDao::getLatestFileVersion($taskId, $userId), null);
    }

    public static function getTaskInfo(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        $version = API\Dispatcher::clenseArgs($request, 'version', 0);
        $taskMetadata = Common\Lib\ModelFactory::buildModel(
            "TaskMetadata",
            DAO\TaskDao::getTaskFileInfo($taskId, $version)
        );
        return API\Dispatcher::sendResponse($response, $taskMetadata, null);
    }

    public static function getTaskClaimed(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        $data = null;
        $userId = API\Dispatcher::clenseArgs($request, 'userId', null);
        if (is_numeric($userId)) {
            $data = DAO\TaskDao::hasUserClaimedTask($userId, $taskId);
        } else {
            $data = DAO\TaskDao::taskIsClaimed($taskId);
        }
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getUserClaimedTask(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        $data = DAO\TaskDao::getUserClaimedTask($taskId);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function getClaimedTime(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        $data = DAO\TaskDao::getClaimedTime($taskId);
        return API\Dispatcher::sendResponse($response, $data, null);
    }

    public static function archiveTask(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        $userId = $args['userId'];
        return API\Dispatcher::sendResponse($response, DAO\TaskDao::moveToArchiveByID($taskId, $userId), null);
    }

    public static function recordTaskView(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        $userId = $args['userId'];
        return API\Dispatcher::sendResponse($response, DAO\TaskDao::recordTaskView($taskId, $userId), null);
    }   
    
    public static function getProofreadTask(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        return API\Dispatcher::sendResponse($response, DAO\TaskDao::getProofreadTask($taskId), null);
    }   
    
    public static function submitReview(Request $request, Response $response)
    {
        $data = (string)$request->getBody();
        $client = new Common\Lib\APIHelper('.json');
        $review = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\TaskReview");
        return API\Dispatcher::sendResponse($response, DAO\TaskDao::submitReview($review), null);
    }

    public static function getTopTasks(Request $request, Response $response)
    {
        $limit = API\Dispatcher::clenseArgs($request, 'limit', 15);
        $offset = API\Dispatcher::clenseArgs($request, 'offset', 0);
        return API\Dispatcher::sendResponse($response, DAO\TaskDao::getLatestAvailableTasks($limit, $offset), null);
    }
    
    public static function getTopTasksCount(Request $request, Response $response)
    {
        return API\Dispatcher::sendResponse($response, DAO\TaskDao::getLatestAvailableTasksCount(), null);
    }

    public static function getTask(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        return API\Dispatcher::sendResponse($response, DAO\TaskDao::getTask($taskId), null);
    }

    public static function deleteTask(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        return API\Dispatcher::sendResponse($response, DAO\TaskDao::delete($taskId), null);
    }
}
Tasks::init();
