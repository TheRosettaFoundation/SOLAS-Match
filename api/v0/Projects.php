<?php

/*
 * Routes for projects
 *
 * @author Dave
 */
include_once 'DataAccessObjects/ProjectDao.class.php';
include_once '../Common/models/Project.php';

class Projects
{
    public static function init()
    {
        
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
                $data= APIHelper::deserialiser($data, $format);
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
                $data= APIHelper::deserialiser($data, $format);
                if(is_object($data)) {
                    $project = APIHelper::cast("Project", $data);
                } else {
                    $project = ModelFactory::buildModel('Project', $data);
                }
                $dao = new ProjectDao();
                Dispatcher::sendResponce(null, $dao->update($project), null, $format);
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
            $dao = new ProjectDao();
            Dispatcher::sendResponce(null, $dao->archiveProject($projectId, $userId), null, $format);                
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
