<?php

/*
 * Routes for projects
 *
 * @author Dave
 */
require_once __DIR__."/../DataAccessObjects/ProjectDao.class.php";
require_once __DIR__."/../../Common/models/Project.php";
require_once __DIR__."/../lib/APIWorkflowBuilder.class.php";

class Projects
{
    public static function init()
    {
        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/projects/test/:projectId',
            function ($projectId) 
            {
                $time = microtime();
                $time = explode(" ", $time);
                $time = $time[1] + $time[0];
                $time1 = $time; 

                $builder = new APIWorkflowBuilder();
                $graph = $builder->buildProjectGraph($projectId);
                $builder->printGraph($graph);

                $time = microtime();
                $time = explode(" ", $time);
                $time = $time[1] + $time[0];
                $time2 = $time;

                $totaltime = ($time2 - $time1);
                echo '<BR>Running Time: ' .$totaltime. ' seconds.'; 
            }, 'test');

        
        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/projects(:format)/',
            function ($format = '.json') 
            {
                Dispatcher::sendResponce(null, ProjectDao::getProject(), null, $format);
            }, 'getProjects');

        Dispatcher::registerNamed(HTTPMethodEnum::POST, '/v0/projects(:format)/',
            function ($format = '.json') 
            {
                $data=Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data = $client->deserialize($data,'Project');
                Dispatcher::sendResponce(null, ProjectDao::createUpdate($data), null, $format);
            }, 'createProject');

        Dispatcher::registerNamed(HTTPMethodEnum::PUT, '/v0/projects/:id/',
            function ($id, $format = '.json') 
            {
                if (!is_numeric($id) && strstr($id, '.')) {
                    $id = explode('.', $id);
                    $format = '.'.$id[1];
                    $id = $id[0];
                }
                $data=Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data = $client->deserialize($data,'Project');
//                $data = $client->cast('Project', $data);
                Dispatcher::sendResponce(null, ProjectDao::createUpdate($data), null, $format);
            }, 'updateProject');

        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/projects/:id/',
            function ($id, $format = '.json') 
            {
                if (!is_numeric($id) && strstr($id, '.')) {
                    $id = explode('.', $id);
                    $format = '.'.$id[1];
                    $id = $id[0];
                }

                $data = ProjectDao::getProject($id);
                if($data && is_array($data)) {
                    $data = $data[0];
                }
                Dispatcher::sendResponce(null, $data, null, $format);

             }, 'getProject',null);
            
        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/projects/:id/',
                                                            function ($id, $format = ".json") {
            
            if (!is_numeric($id) && strstr($id, '.')) {
                $id = explode('.', $id);
                $format = '.'.$id[1];
                $id = $id[0];
            }
            Dispatcher::sendResponce(null, ProjectDao::delete($id), null, $format);
        }, 'deleteProject');


        Dispatcher::registerNamed(HTTPMethodEnum::POST, '/v0/projects/:id/calculateDeadlines(:format)/',
                function ($id, $format = '.json')
                {
                    $ret = null;
                    $ret = ProjectDao::calculateProjectDeadlines($id);
                    Dispatcher::sendResponce(null, $ret, null, $format);
                }, 'calculateProjectDeadlines');

        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/projects/:id/reviews(:format)/',
                function ($id, $format = '.json')
                {
                    $reviews = TaskDao::getTaskReviews($id);
                    Dispatcher::sendResponce(null, $reviews, null, $format);
                }, 'getProjectTaskReviews');

        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/projects/:id/tasks(:format)/',
            function ($id, $format = '.json')
            {
                $data = ProjectDao::getProjectTasks($id);
                Dispatcher::sendResponce(null, $data, null, $format);
            }, 'getProjectTasks');

        Dispatcher::registerNamed(HTTPMethodEnum::PUT, '/v0/projects/archiveProject/:projectId/user/:userId/',
                                                        function ($projectId, $userId, $format = ".json") {
            if (!is_numeric($userId) && strstr($userId, '.')) {
                $userId = explode('.', $userId);
                $format = '.'.$userId[1];
                $userId = $userId[0];
            }
             Dispatcher::sendResponce(null, ProjectDao::archiveProject($projectId, $userId), null, $format);               

            }, 'archiveProject'); 

        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/archivedProjects(:format)/',
            function ($format = '.json') 
            {
                Dispatcher::sendResponce(null, ProjectDao::getArchivedProject(), null, $format);
            }, 'getArchivedProjects');

        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/archivedProjects/:id/',
            function ($id, $format = '.json') 
            {
                if (!is_numeric($id) && strstr($id, '.')) {
                    $id = explode('.', $id);
                    $format = '.'.$id[1];
                    $id = $id[0];
                }

                $data = ProjectDao::getArchivedProject($id);
                if($data && is_array($data)) {
                    $data = $data[0];
                }
                Dispatcher::sendResponce(null, $data, null, $format);
            }, 'getArchivedProject');

        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/projects/buildGraph/:id/',
                function ($id, $format = '.json')
                {
                    if (!is_numeric($id) && strstr($id, '.')) {
                        $id = explode('.', $id);
                        $format = '.'.$id[1];
                        $id = $id[0];
                    }

                    $builder = new APIWorkflowBuilder();
                    $graph = $builder->buildProjectGraph($id);
                    Dispatcher::sendResponce(null, $graph, null, $format);
                }, 'getProjectGraph');

        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/projects/:id/tags(:format)/',
                                                        function ($id, $format = ".json") {
            Dispatcher::sendResponce(null, ProjectDao::getTags($id), null, $format);
        }, 'getProjectTags',null);
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/projects/:id/info(:format)/',
                                                        function ($id, $format = ".json") {
            Dispatcher::sendResponce(null,ProjectDao::getProjectFileInfo($id, null, null, null, null), null, $format);
        }, 'getProjectFileInfo');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/projects/:id/file(:format)/',
                                                        function ($id, $format = ".json") {
            Dispatcher::sendResponce(null,ProjectDao::getProjectFile($id), null, $format);
        }, 'getProjectFile');
        
         Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/projects/:id/file/:filename/:userId/',
                                                        function ($id, $filename, $userID, $format = ".json") {
                     
            if (!is_numeric($userID) && strstr($userID, '.')) {
                $userID = explode('.', $userID);
                $format = '.'.$userID[1];
                $userID = $userID[0];
            }
            $data=Dispatcher::getDispatcher()->request()->getBody();
            try {
                $token = ProjectDao::saveProjectFile($id, $data, urldecode($filename),$userID);
                Dispatcher::sendResponce(null, $token, HttpStatusEnum::CREATED, $format);
            } catch(Exception $e) {
                Dispatcher::sendResponce(null, $e->getMessage(), $e->getCode());
            }
           
        }, 'saveProjectFile');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/projects/:id/archivedTasks(:format)/',
                                                        function ($id, $format = ".json") {
            Dispatcher::sendResponce(null, ProjectDao::getArchivedTask($id), null, $format);
        }, 'getArchivedProjectTasks');
        
        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/projects/:id/deleteTags(:format)/',
                                                        function ($projectId, $format = ".json") {
            Dispatcher::sendResponce(null,ProjectDao::deleteProjectTags($projectId), null, $format);
        }, 'deleteProjectTags');
    }
}
Projects::init();
