<?php

/**
 * Description of Tasks
 *
 * @author sean
 */

require_once 'DataAccessObjects/TaskDao.class.php';
require_once 'DataAccessObjects/TaskTags.class.php';
require_once 'DataAccessObjects/TaskFile.class.php';
require_once 'DataAccessObjects/TaskStream.class.php';
require_once '../Common/models/TaskMetadata.php';
require_once '../Common/protobufs/emails/FeedbackEmail.php';
require_once 'lib/IO.class.php';
require_once 'lib/Upload.class.php';
require_once 'lib/FormatConverter.php';

class Tasks {
    
    public static function init()
    {
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks(:format)/',
                                                        function ($format = ".json") {
            
            $dao = new TaskDao();
            Dispatcher::sendResponce(null, $dao->getTask(null), null, $format);
        }, 'getTasks');        
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/tasks(:format)/',
                                                        function ($format = ".json") {
            
            $data=Dispatcher::getDispatcher()->request()->getBody();
            $data= APIHelper::deserialiser($data, $format);
            $dao = new TaskDao();
            Dispatcher::sendResponce(null, $dao->create($data), null, $format);
        }, 'createTask');
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/tasks/:id/',
                                                        function ($id, $format = ".json") {
            
            if (!is_numeric($id) && strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            $dao = new TaskDao();
            $data = Dispatcher::getDispatcher()->request()->getBody();
            $data = APIHelper::deserialiser($data, $format);
            $data = APIHelper::cast("Task", $data);
            Dispatcher::sendResponce(null, $dao->save($data), null, $format);
        }, 'updateTask');
        
        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/tasks/:id/',
                                                            function ($id, $format = ".json") {
            
            if (!is_numeric($id) && strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            $dao = new TaskDao();
            Dispatcher::sendResponce(null, $dao->delete($id), null, $format);
        }, 'deleteTask');

        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/prerequisites(:format)/',
                function ($id, $format = ".json") {
                    $dao = new TaskDao();
                    Dispatcher::sendResponce(null, $dao->getTaskPreReqs($id), null, $format);
                }, 'getTaskPreReqs');

        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/tasks/:id/prerequisites/:preReqId/',
                function ($id, $preReqId, $format = ".json") {
                    if (!is_numeric($preReqId) && strstr($preReqId, '.')) {
                        $preReqId = explode('.', $preReqId);
                        $format = '.'.$preReqId[1];
                        $preReqId = $preReqId[0];
                    }
                    $dao = new TaskDao();
                    Dispatcher::sendResponce(null, $dao->addTaskPreReq($id, $preReqId), null, $format);
                }, "addTaskPreReq");

        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/tasks/:id/prerequisites/:preReqId/',
                function ($id, $preReqId, $format = ".json") {
                    if (!is_numeric($preReqId) && strstr($preReqId, '.')) {
                        $preReqId = explode('.', $preReqId);
                        $format = '.'.$preReqId[1];
                        $preReqId = $preReqId[0];
                    }
                    $dao = new TaskDao();
                    Dispatcher::sendResponce(null, $dao->removeTaskPreReq($id, $preReqId), null, $format);
                }, "removeTaskPreReq");
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/tasks/archiveTask/:taskId/user/:userId/',
                                                        function ($taskId, $userId, $format = ".json") {
            if (!is_numeric($userId) && strstr($userId, '.')) {
                $userId = explode('.', $userId);
                $format = '.'.$userId[1];
                $userId = $userId[0];
            }
            $dao = new TaskDao();
            Dispatcher::sendResponce(null, $dao->moveToArchiveByID($taskId, $userId), null, $format);
        }, 'archiveTask');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/top_tasks(:format)/',
                                                        function ($format = ".json") {
            
            $limit = Dispatcher::clenseArgs('limit', HttpMethodEnum::GET, null);
            Dispatcher::sendResponce(null, TaskStream::getStream($limit), null, $format);
        }, 'getTopTasks');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/',
                                                        function ($id, $format = ".json") {

            if (!is_numeric($id) && strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            $dao = new TaskDao();
            $data = $dao->getTask(array("id" => $id));
            if ($data && is_array($data)) {
                $data = $data[0];
            }
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getTask');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/tags(:format)/',
                                                        function ($id, $format = ".json") {
            $dao = new TaskTags();
            Dispatcher::sendResponce(null, $dao->getTags($id), null, $format);
        }, 'getTasksTags');
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/tasks/:id/tags(:format)/',
                                                        function ($id, $format = ".json") {
            $dao = new TaskDao();
            $data = Dispatcher::getDispatcher()->request()->getBody();
            $data = APIHelper::deserialiser($data, $format);
            $task = APIHelper::cast(new Task(), $data);
            $result = $dao->updateTags($task);
            Dispatcher::sendResponce(null, array("result" => $result), null, $format);
        }, 'setTasksTags');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/status(:format)/',
                                                        function ($id, $format=".json") {
            
            $dao = new TaskDao();
            Dispatcher::sendResponce(null, array("status message" => $dao->getTaskStatus($id)), null, $format);
        }, 'getTaskStatus');

        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/tasks/:id/feedback(:format)/',
                function ($id, $format = ".json") {
                    $taskDao = new TaskDao();
                    $task = $taskDao->getTask(array('id' => $id));

                    $data = Dispatcher::getDispatcher()->request()->getBody();
                    $data = APIHelper::deserialiser($data, $format);
                    $feedbackData = APIHelper::cast(new FeedbackEmail(), $data);

                    $users = $feedbackData->getUserIdList();
                    if (count($users) > 0) {
                        if (count($users) == 1) {
                            $userDao = new UserDao();
                            $user = $userDao->find(array('user_id' => $users[0]));

                            Notify::sendOrgFeedback($task, $user, $feedbackData->getFeedback());
                        } else {
                            //send user feedback
                            //not implemented
                        }
                    }
                    Dispatcher::sendResponce(null, null, null, $format);
        }, 'sendFeedback');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/file(:format)/',
                                                        function ($id, $format=".json") {
            
            $version = Dispatcher::clenseArgs('version', HttpMethodEnum::GET, 0);
            $convert = Dispatcher::clenseArgs('convertToXliff', HttpMethodEnum::GET, false);
            if($convert){
                TaskDao::downloadConvertedTask($id, $version);
            }else{
                TaskDao::downloadTask($id, $version);
            }
        }, 'getTaskFile');
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/tasks/:id/file/:filename/:userId/',
                                                        function ($id, $filename, $userId) {
            
            $dao = new TaskDao();
            $task = $dao->getTask(array("id" => $id));
            if (is_array($task)) {
                $task = $task[0];
            }
            Notify::sendEmailNotifications($task, NotificationTypes::UPLOAD);
            $convert = Dispatcher::clenseArgs('convertFromXliff', HttpMethodEnum::GET, false);
            if($convert){
                Upload::apiSaveFile($task, $userId, FormatConverter::convertFromXliff(Dispatcher::getDispatcher()->request()->getBody()), $filename);
            }else{
            //touch this and you will die painfully sinisterly sean :)
                Upload::apiSaveFile($task, $userId, Dispatcher::getDispatcher()->request()->getBody(), $filename);
            }
        }, 'saveTaskFile');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/version(:format)/',
                                                        function ($id, $format = ".json") {
            
            $userID = Dispatcher::clenseArgs('userID', HttpMethodEnum::GET, null);
            Dispatcher::sendResponce(null, TaskFile::getLatestFileVersionByTaskID($id, $userID), null, $format);
        }, 'getTaskVersion');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/info(:format)/',
                                                        function ($id, $format = ".json") {
            
            $version = Dispatcher::clenseArgs('version', HttpMethodEnum::GET, 0);
            $taskMetadata = ModelFactory::buildModel("TaskMetadata", TaskFile::getTaskFileInfoById($id, $version));
            Dispatcher::sendResponce(null, $taskMetadata, null, $format);
        }, 'getTaskInfo');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/tasks/:id/claimed(:format)/', 
                                                        function ($id, $format = ".json") {

            $data = null;
            $dao = new TaskDao();
            $userID = Dispatcher::clenseArgs('userID', HttpMethodEnum::GET, null);
            if (is_numeric($userID)) {
                $data = $dao->hasUserClaimedTask($userID, $id);
            } else {
                $data = $dao->taskIsClaimed($id);
            }

            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getTaskClaimed');
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/tasks/addTarget/:languageCode/:countryCode/:userID/',
                                                function ($languageCode, $countryCode, $userID, $format = ".json") {
            
            if (!is_numeric($userID) && strstr($userID, '.')) {
                $userID = explode('.', $userID);
                $format = '.'.$userID[1];
                $userID = $userID[0];
            }
            $dao = new TaskDao();
            $data = Dispatcher::getDispatcher()->request()->getBody();
            $data = APIHelper::deserialiser($data, $format);
            $task = APIHelper::cast(new Task(), $data);

            $result = $dao->duplicateTaskForTarget($task, $languageCode, $countryCode, $userID);
            Dispatcher::sendResponce(null, array("result" => $result), null, $format);
        }, 'addTarget');        
    }
}
Tasks::init();
