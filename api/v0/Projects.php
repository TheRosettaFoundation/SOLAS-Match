<?php

/*
 * Routes for projects
 *
 * @author Dave
 */
require_once 'DataAccessObjects/ProjectDao.class.php';
require_once '../Common/models/Project.php';
require_once 'lib/APIWorkflowBuilder.class.php';

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
                $dao = new ProjectDao();
                Dispatcher::sendResponce(null, $dao->getProject(null), null, $format);
            }, 'getProjects');

        Dispatcher::registerNamed(HTTPMethodEnum::POST, '/v0/projects(:format)/',
            function ($format = '.json') 
            {
                $data=Dispatcher::getDispatcher()->request()->getBody();
                $client = new APIHelper($format);
                $data = $client->deserialize($data);
                $data = $client->cast('Project', $data);
                $dao = new ProjectDao();
                Dispatcher::sendResponce(null, $dao->create($data), null, $format);
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
                $data = $client->deserialize($data);
                $data = $client->cast('Project', $data);
                $dao = new ProjectDao();
                Dispatcher::sendResponce(null, $dao->update($data), null, $format);
            }, 'updateProject');

        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/projects/:id/',
            function ($id, $format = '.json') 
            {
                if (!is_numeric($id) && strstr($id, '.')) {
                    $id = explode('.', $id);
                    $format = '.'.$id[1];
                    $id = $id[0];
                }

                $dao = new ProjectDao();
                $params = array();
                $params['id'] = $id;
                $data = $dao->getProject($params);
                if($data && is_array($data)) {
                    $data = $data[0];
                }
                Dispatcher::sendResponce(null, $data, null, $format);
            }, 'getProject');

        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/projects/:id/tasks(:format)/',
            function ($id, $format = '.json')
            {
                if(!is_numeric($id) && strstr($id, '.')) {
                    $id = explode('.', $id);
                    $format = '.'.$id[1];
                    $id = $id[0];
                }
                
                $dao = new ProjectDao();
                $data = $dao->getProjectTasks($id);
                Dispatcher::sendResponce(null, $data, null, $format);
            }, 'getProjectTasks');

        Dispatcher::registerNamed(HTTPMethodEnum::PUT, '/v0/projects/archiveProject/:projectId/user/:userId/',
                                                        function ($projectId, $userId, $format = ".json") {
            if (!is_numeric($userId) && strstr($userId, '.')) {
                $userId = explode('.', $userId);
                $format = '.'.$userId[1];
                $userId = $userId[0];
            }
            $projectDao = new ProjectDao();
            $taskDao = new TaskDao();
            $projectTasks = $projectDao->getProjectTasks($projectId);
            foreach ($projectTasks as $task) {
                $taskDao->moveToArchiveById($task->getId(), $userId);
            }
            Dispatcher::sendResponce(null, $projectDao->archiveProject($projectId, $userId), null, $format);                
            }, 'archiveProject');

        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/archivedProjects(:format)/',
            function ($format = '.json') 
            {
                $dao = new ProjectDao();
                Dispatcher::sendResponce(null, $dao->getArchivedProject(null), null, $format);
            }, 'getArchivedProjects');

        Dispatcher::registerNamed(HTTPMethodEnum::GET, '/v0/archivedProjects/:id/',
            function ($id, $format = '.json') 
            {
                if (!is_numeric($id) && strstr($id, '.')) {
                    $id = explode('.', $id);
                    $format = '.'.$id[1];
                    $id = $id[0];
                }

                $dao = new ProjectDao();
                $params = array();
                $params['id'] = $id;
                $data = $dao->getArchivedProject($params);
                if($data && is_array($data)) {
                    $data = $data[0];
                }
                Dispatcher::sendResponce(null, $data, null, $format);
            }, 'getArchivedProject');
            
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/projects/:id/tags(:format)/',
                                                        function ($id, $format = ".json") {
            $dao = new ProjectTags();
            Dispatcher::sendResponce(null, $dao->getTags($id), null, $format);
        }, 'getProjectTags');
    }
}
Projects::init();
