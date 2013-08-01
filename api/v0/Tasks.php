<?php

/**
 * Description of Tasks
 *
 * @author sean
 */

require_once __DIR__."/../DataAccessObjects/TaskDao.class.php";
require_once __DIR__."/../../Common/models/TaskMetadata.php";
require_once __DIR__."/../../Common/protobufs/emails/UserFeedback.php";
require_once __DIR__."/../../Common/protobufs/emails/OrgFeedback.php";
require_once __DIR__."/../lib/IO.class.php";
require_once __DIR__."/../lib/Upload.class.php";
require_once __DIR__."/../lib/FormatConverter.php";

class Tasks {
    
    public static function init()
    {
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks(:format)/',
                                                        function ($format = ".json") {
            
            Dispatcher::sendResponce(null, TaskDao::getTask(), null, $format);
        }, 'getTasks');        
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/tasks(:format)/',
                                                        function ($format = ".json") {
            
            $data=Dispatcher::getDispatcher()->request()->getBody();
            $client = new APIHelper($format);
            $data = $client->deserialize($data, "Task");
            Dispatcher::sendResponce(null, TaskDao::create($data), null, $format);
        }, 'createTask');
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/tasks/:id/',
                                                        function ($id, $format = ".json") {
            
            if (!is_numeric($id) && strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            $data = Dispatcher::getDispatcher()->request()->getBody();
            $client = new APIHelper($format);
            $data = $client->deserialize($data, "Task");
            Dispatcher::sendResponce(null, TaskDao::save($data), null, $format);
        }, 'updateTask');
        
        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/tasks/:id/',
                                                            function ($id, $format = ".json") {
            
            if (!is_numeric($id) && strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            Dispatcher::sendResponce(null, TaskDao::delete($id), null, $format);
        }, 'deleteTask');

        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/prerequisites(:format)/',
            function ($id, $format = ".json") {
                Dispatcher::sendResponce(null, TaskDao::getTaskPreReqs($id), null, $format);
        }, 'getTaskPreReqs');

        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/tasks/:id/prerequisites/:preReqId/',
            function ($id, $preReqId, $format = ".json") {
            if (!is_numeric($preReqId) && strstr($preReqId, '.')) {
                $preReqId = explode('.', $preReqId);
                $format = '.'.$preReqId[1];
                $preReqId = $preReqId[0];
            }
            
            Dispatcher::sendResponce(null, Upload::addTaskPreReq($id, $preReqId), null, $format);
        }, "addTaskPreReq");

        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/tasks/:id/prerequisites/:preReqId/',
        function ($id, $preReqId, $format = ".json") {
            if (!is_numeric($preReqId) && strstr($preReqId, '.')) {
                $preReqId = explode('.', $preReqId);
                $format = '.'.$preReqId[1];
                $preReqId = $preReqId[0];
            }
            Dispatcher::sendResponce(null, Upload::removeTaskPreReq($id, $preReqId), null, $format);
            //Dispatcher::sendResponce(null, Upload::removeTaskPreReq($id, $preReqId), null, $format);
        }, "removeTaskPreReq");
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/tasks/archiveTask/:taskId/user/:userId/',
                                                        function ($taskId, $userId, $format = ".json") {
            if (!is_numeric($userId) && strstr($userId, '.')) {
                $userId = explode('.', $userId);
                $format = '.'.$userId[1];
                $userId = $userId[0];
            }
            Dispatcher::sendResponce(null, TaskDao::moveToArchiveByID($taskId, $userId), null, $format);
        }, 'archiveTask');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/topTasks(:format)/',
                                                        function ($format = ".json") {
            
            $limit = Dispatcher::clenseArgs('limit', HttpMethodEnum::GET, 15);
            $offset = Dispatcher::clenseArgs('offset', HttpMethodEnum::GET, 0);
            Dispatcher::sendResponce(null, TaskDao::getLatestAvailableTasks($limit, $offset), null, $format);
        }, 'getTopTasks');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/',
                                                        function ($id, $format = ".json") {

            if (!is_numeric($id) && strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            $data = TaskDao::getTask($id);
            if ($data && is_array($data)) {
                $data = $data[0];
            }
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getTask');

        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/review(:format)/',
                function ($id, $format = '.json')
                {
                    $review = TaskDao::getTaskReviews(null,$id);
                    Dispatcher::sendResponce(null, $review, null, $format);
                }, 'getTaskReview');

        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/tasks/review(:format)/',
                function ($format = '.json')
                {
                    $data=Dispatcher::getDispatcher()->request()->getBody();
                    $client = new APIHelper($format);
                    $review = $client->deserialize($data,"TaskReview");
                    Dispatcher::sendResponce(null, TaskDao::submitReview($review), null, $format);
                }, 'submitReview');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/tags(:format)/',
                                                        function ($id, $format = ".json") {
            Dispatcher::sendResponce(null, TaskDao::getTags($id), null, $format);
        }, 'getTasksTags');
//        
//        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/tasks/:id/tags(:format)/',
//                                                        function ($id, $format = ".json") {
//            $data = Dispatcher::getDispatcher()->request()->getBody();
//            $client = new APIHelper($format);
//            $data = $client->deserialize($data);
//            $data = $client->cast("Task", $data);
//            $result = TaskDao::updateTags($data);
//            Dispatcher::sendResponce(null, array("result" => $result), null, $format);
//        }, 'setTasksTags');
        
        //Consider Removing
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/status(:format)/',
                                                        function ($id, $format=".json") {
            
            Dispatcher::sendResponce(null, array("status message" => TaskDao::getTaskStatus($id)), null, $format);
        }, 'getTaskStatus');

        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/tasks/:id/orgFeedback(:format)/',
                function ($id, $format = ".json") {
                    $data = Dispatcher::getDispatcher()->request()->getBody();
                    $client = new APIHelper($format);
                    $feedbackData = $client->deserialize($data,"OrgFeedback");
                    Notify::sendOrgFeedback($feedbackData);
                    Dispatcher::sendResponce(null, null, null, $format);
        }, 'sendOrgFeedback');
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/tasks/:id/userFeedback(:format)/',
                function ($id, $format = ".json") {
                    $data = Dispatcher::getDispatcher()->request()->getBody();
                    $client = new APIHelper($format);
                    $feedbackData = $client->deserialize($data,"UserFeedback");
                    Notify::sendUserFeedback($feedbackData);
                    Dispatcher::sendResponce(null, null, null, $format);
        }, 'sendUserFeedback');

        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/file(:format)/',
                                                        function ($id, $format=".json") {
            
            $version = Dispatcher::clenseArgs('version', HttpMethodEnum::GET, 0);
            $convert = Dispatcher::clenseArgs('convertToXliff', HttpMethodEnum::GET, false);
            if($convert&&$convert!==""){
                TaskDao::downloadConvertedTask($id, $version);
            }else{
                TaskDao::downloadTask($id, $version);
            }
        }, 'getTaskFile');
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/tasks/:id/file/:filename/:userId/',
                                                        function ($id, $filename, $userID, $format = ".json") {
            
            if (!is_numeric($userID) && strstr($userID, '.')) {
                $userID = explode('.', $userID);
                $format = '.'.$userID[1];
                $userID = $userID[0];
            }
            $task = TaskDao::getTask($id);
            if (is_array($task)) {
                $task = $task[0];
            }
            $version = Dispatcher::clenseArgs('version', HttpMethodEnum::GET, null);
            $convert = Dispatcher::clenseArgs('convertFromXliff', HttpMethodEnum::GET, false);
            $data=Dispatcher::getDispatcher()->request()->getBody();
            TaskDao::uploadFile($task, $convert,$data,$version,$userID,$filename);
        }, 'saveTaskFile');
        
        
         Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/tasks/uploadOutputFile/:id/:userId/',
                                                        function ($id, $userID, $format = ".json") {
             if (!is_numeric($userID) && strstr($userID, '.')) {
                $userID = explode('.', $userID);
                $format = '.'.$userID[1];
                $userID = $userID[0];
            }
            $task = TaskDao::getTask($id);
            if (is_array($task)) {
                $task = $task[0];
            }
            
            $projectFile = ProjectDao::getProjectFileInfo($task->getProjectId(), null, null, null, null);
            $filename = $projectFile->getFilename();
            
            $convert = Dispatcher::clenseArgs('convertFromXliff', HttpMethodEnum::GET, false);
            $data=Dispatcher::getDispatcher()->request()->getBody();
            TaskDao::uploadOutputFile($task, $convert,$data,$userID,$filename);
        }, 'uploadOutputFile');
        
        
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/version(:format)/',
                                                        function ($id, $format = ".json") {
            
            $userID = Dispatcher::clenseArgs('userID', HttpMethodEnum::GET, null);
            Dispatcher::sendResponce(null, TaskDao::getLatestFileVersion($id, $userID), null, $format);
        }, 'getTaskVersion');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/info(:format)/',
                                                        function ($id, $format = ".json") {
            
            $version = Dispatcher::clenseArgs('version', HttpMethodEnum::GET, 0);
            $taskMetadata = ModelFactory::buildModel("TaskMetadata", TaskDao::getTaskFileInfo($id, $version));
            Dispatcher::sendResponce(null, $taskMetadata, null, $format);
        }, 'getTaskInfo');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/claimed(:format)/', 
                                                        function ($id, $format = ".json") {

            $data = null;
            $userID = Dispatcher::clenseArgs('userID', HttpMethodEnum::GET, null);
            if (is_numeric($userID)) {
                $data = TaskDao::hasUserClaimedTask($userID, $id);
            } else {
                $data = TaskDao::taskIsClaimed($id);
            }

            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getTaskClaimed');
        
        //Consider Removing
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/tasks/addTarget/:languageCode/:countryCode/:userID/',
                                                function ($languageCode, $countryCode, $userID, $format = ".json") {
            
            if (!is_numeric($userID) && strstr($userID, '.')) {
                $userID = explode('.', $userID);
                $format = '.'.$userID[1];
                $userID = $userID[0];
            }
            $data = Dispatcher::getDispatcher()->request()->getBody();
            $client = new APIHelper($format);
            $data = $client->deserializer($data);
            $data = $client->cast("Task", $data);
            $result = TaskDao::duplicateTaskForTarget($data, $languageCode, $countryCode, $userID);
            Dispatcher::sendResponce(null, array("result" => $result), null, $format);
        }, 'addTarget');   
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/user(:format)/',
                                                        function ($id, $format = ".json") {
            
            $data = TaskDao::getUserClaimedTask($id);
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getUserClaimedTask');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/timeClaimed(:format)/',
                                                        function ($id, $format = ".json") {
            
            $data = TaskDao::getClaimedTime($id);
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getClaimedTime');
    }
}
Tasks::init();
