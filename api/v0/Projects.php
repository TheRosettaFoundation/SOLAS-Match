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
//        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/projects/test/:projectId',
//            function ($projectId) 
//            {
//                $time = microtime();
//                $time = explode(" ", $time);
//                $time = $time[1] + $time[0];
//                $time1 = $time; 
//
//                $builder = new APIWorkflowBuilder();
//                $graph = $builder->buildProjectGraph($projectId);
//                $builder->printGraph($graph);
//
//                $time = microtime();
//                $time = explode(" ", $time);
//                $time = $time[1] + $time[0];
//                $time2 = $time;
//
//                $totaltime = ($time2 - $time1);
//                echo '<BR>Running Time: ' .$totaltime. ' seconds.'; 
//            }, 'test');

        //
        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/projects(:format)/',
            function ($format = '.json') 
            {
                Dispatcher::sendResponce(null, ProjectDao::getProject(), null, $format);
            }, 'getProjects');
		
		//
        Dispatcher::registerNamed(HTTPMethodEnum::POST, '/v0/projects(:format)/',
            function ($format = '.json') 
            {
                $data=Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data = $client->deserialize($data,'Project');
                Dispatcher::sendResponce(null, ProjectDao::createUpdate($data), null, $format);
            }, 'createProject', 'Middleware::authenticateUserMembership');
		
		//
        Dispatcher::registerNamed(HTTPMethodEnum::PUT, '/v0/projects/:projectId/',
            function ($projectId, $format = '.json') 
            {
                if (!is_numeric($projectId) && strstr($projectId, '.')) {
                    $projectId = explode('.', $projectId);
                    $format = '.'.$projectId[1];
                    $projectId = $projectId[0];
                }
                $data=Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data = $client->deserialize($data,'Project');
//                $data = $client->cast('Project', $data);
                Dispatcher::sendResponce(null, ProjectDao::createUpdate($data), null, $format);
            }, 'updateProject', 'Middleware::authenticateUserForOrgProject');
		
		//
        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/projects/:projectId/',
            function ($projectId, $format = '.json') 
            {
                if (!is_numeric($projectId) && strstr($projectId, '.')) {
                    $projectId = explode('.', $projectId);
                    $format = '.'.$projectId[1];
                    $projectId = $projectId[0];
                }

                $data = ProjectDao::getProject($projectId);
                if($data && is_array($data)) {
                    $data = $data[0];
                }
                Dispatcher::sendResponce(null, $data, null, $format);

             }, 'getProject',null);
		
		//
        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/projects/:projectId/',
                                                            function ($projectId, $format = ".json") {
            
            if (!is_numeric($projectId) && strstr($projectId, '.')) {
                $projectId = explode('.', $projectId);
                $format = '.'.$projectId[1];
                $projectId = $projectId[0];
            }
            Dispatcher::sendResponce(null, ProjectDao::delete($projectId), null, $format);
        }, 'deleteProject', 'Middleware::authenticateUserForOrgProject');

		//
        Dispatcher::registerNamed(HTTPMethodEnum::POST, '/v0/projects/:projectId/calculateDeadlines(:format)/',
                function ($projectId, $format = '.json')
                {
                    $ret = null;
                    $ret = ProjectDao::calculateProjectDeadlines($projectId);
                    Dispatcher::sendResponce(null, $ret, null, $format);
                }, 'calculateProjectDeadlines');
		
		//
        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/projects/:projectId/reviews(:format)/',
                function ($projectId, $format = '.json')
                {
                    $reviews = TaskDao::getTaskReviews($projectId);
                    Dispatcher::sendResponce(null, $reviews, null, $format);
                }, 'getProjectTaskReviews', 'Middleware::authenticateUserOrOrgForProjectTask');
		
		//
        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/projects/:projectId/tasks(:format)/',
            function ($projectId, $format = '.json')
            {
                $data = ProjectDao::getProjectTasks($projectId);
                Dispatcher::sendResponce(null, $data, null, $format);
            }, 'getProjectTasks');
		
		//
        Dispatcher::registerNamed(HTTPMethodEnum::PUT, '/v0/projects/archiveProject/:projectId/user/:userId/',
                                                        function ($projectId, $userId, $format = ".json") {
            if (!is_numeric($userId) && strstr($userId, '.')) {
                $userId = explode('.', $userId);
                $format = '.'.$userId[1];
                $userId = $userId[0];
            }
             Dispatcher::sendResponce(null, ProjectDao::archiveProject($projectId, $userId), null, $format);               

            }, 'archiveProject', 'Middleware::authenticateUserForOrgProject'); 
		
		//
        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/archivedProjects(:format)/',
            function ($format = '.json') 
            {
                Dispatcher::sendResponce(null, ProjectDao::getArchivedProject(), null, $format);
            }, 'getArchivedProjects', 'Middleware::authenticateSiteAdmin');
		
		//
        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/archivedProjects/:projectId/',
            function ($projectId, $format = '.json') 
            {
                if (!is_numeric($projectId) && strstr($projectId, '.')) {
                    $projectId = explode('.', $projectId);
                    $format = '.'.$projectId[1];
                    $projectId = $projectId[0];
                }

                $data = ProjectDao::getArchivedProject($projectId);
                if($data && is_array($data)) {
                    $data = $data[0];
                }
                Dispatcher::sendResponce(null, $data, null, $format);
            }, 'getArchivedProject', 'Middleware::authenticateUserForOrgProject');
		
		//
        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/projects/buildGraph/:projectId/',
                function ($projectId, $format = '.json')
                {
                    if (!is_numeric($projectId) && strstr($projectId, '.')) {
                        $projectId = explode('.', $projectId);
                        $format = '.'.$projectId[1];
                        $projectId = $projectId[0];
                    }

                    $builder = new APIWorkflowBuilder();
                    $graph = $builder->buildProjectGraph($projectId);
                    Dispatcher::sendResponce(null, $graph, null, $format);
                }, 'getProjectGraph');
		
		//
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/projects/:projectId/tags(:format)/',
                                                        function ($projectId, $format = ".json") {
            Dispatcher::sendResponce(null, ProjectDao::getTags($projectId), null, $format);
        }, 'getProjectTags',null);
        
		//
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/projects/:projectId/info(:format)/',
                                                        function ($projectId, $format = ".json") {
            Dispatcher::sendResponce(null,ProjectDao::getProjectFileInfo($projectId, null, null, null, null), null, $format);
        }, 'getProjectFileInfo');
        
		//
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/projects/:projectId/file(:format)/',
                                                        function ($projectId, $format = ".json") {
            Dispatcher::sendResponce(null,ProjectDao::getProjectFile($projectId), null, $format);
        }, 'getProjectFile',null);
        
		//
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/projects/:projectId/file/:filename/:userId/',
                                                        function ($projectId, $filename, $userId, $format = ".json") {
                     
            if (!is_numeric($userId) && strstr($userId, '.')) {
                $userId = explode('.', $userId);
                $format = '.'.$userId[1];
                $userId = $userId[0];
            }
            $data=Dispatcher::getDispatcher()->request()->getBody();
            try {
                $token = ProjectDao::saveProjectFile($projectId, $data, urldecode($filename),$userId);
                Dispatcher::sendResponce(null, $token, HttpStatusEnum::CREATED, $format);
            } catch(Exception $e) {
                Dispatcher::sendResponce(null, $e->getMessage(), $e->getCode());
            }
           
        }, 'saveProjectFile', 'Middleware::authenticateUserForOrgProject');
        
		//
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/projects/:projectId/archivedTasks(:format)/',
                                                        function ($projectId, $format = ".json") {
            Dispatcher::sendResponce(null, ProjectDao::getArchivedTask($projectId), null, $format);
        }, 'getArchivedProjectTasks', 'Middleware::authenticateUserForOrgProject');
        
		//
        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/projects/:projectId/deleteTags(:format)/',
                                                        function ($projectId, $format = ".json") {
            Dispatcher::sendResponce(null,ProjectDao::deleteProjectTags($projectId), null, $format);
        }, 'deleteProjectTags', 'Middleware::authenticateUserForOrgProject');
    }
}
Projects::init();
