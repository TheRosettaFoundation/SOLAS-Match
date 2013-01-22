<?php

include_once '../Common/lib/PDOWrapper.class.php';
include_once '../Common/lib/ModelFactory.class.php';
include_once '../Common/models/Project.php';
include_once '../Common/models/ArchivedProject.php';

class ProjectDao
{
    
    public function create($params)
    {
        if (!is_array($params) && is_object($params)) {
            $project   = APIHelper::cast("Project", $params);
        } else {
            $project = ModelFactory::buildModel("Project", $params);
        }

        $this->save($project);
        return $project;
    }
    
    public function save(&$project)
    {
        $result = PDOWrapper::call("projectInsertAndUpdate", "null"
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getTitle())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getDescription())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getDeadline())
                .",".PDOWrapper::cleanseNull($project->getOrganisationId())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getReference())
                .",".PDOWrapper::cleanseNull($project->getWordCount())
                .",".PDOWrapper::cleanseNull($project->getCreatedTime()));
        $project->setId($result[0]['id']);     
        return $project;
    }
    
    public function getProject($params)
    {
        $args = "";
        if(isset($params['id'])) {
            $args .= PDOWrapper::cleanseNull($params['id']);
        } else {
            $args .= "null";
        }
        if(isset($params['title'])) {
            $args .= ", ".PDOWrapper::cleanseNullOrWrapStr($params['title']);
        } else {
            $args .= ", null";
        }
        if(isset($params['description'])) {
            $args .= ", ".PDOWrapper::cleanseNullOrWrapStr($params['description']);
        } else {
            $args .= ", null";
        }
        if(isset($params['deadline'])) {
            $args .= ", ".PDOWrapper::cleanseNull($params['deadline']);
        } else {
            $args .= ", null";
        }
        if(isset($params['organisation_id'])) {
            $args .= ", ".PDOWrapper::cleanseNull($params['organisation_id']);
        } else {
            $args .= ", null";
        }
        if(isset($params['reference'])) {
            $args .= ", ".PDOWrapper::cleanseNullOrWrapStr($params['reference']);
        } else {
            $args .= ", null";
        }
        if(isset($params['word-count'])) {
            $args .= ", ".PDOWrapper::cleanseNull($params['word-count']);
        } else {
            $args .= ", null";
        }
        if(isset($params['created'])) {
            $args .= ", ".PDOWrapper::cleanseNull($params['created']);
        } else {
            $args .= ", null";
        }

        $projects = array();
        $result = PDOWrapper::call("getProject", $args);
        if($result) {
            foreach($result as $row) {
                $project = ModelFactory::buildModel("Project", $row);
                if(is_object($project)) {
                    $projects[] = $project;
                }
            }
        }

        if(count($projects) == 0) {
            $projects = null;
        }

        return $projects;
    }

    public function archiveProject($projectId, $userId)
    {
        $args = $projectId.", ".$userId;
        PDOWrapper::call("archiveProject", $args);
    }

    public function getArchivedProject($params)
    {
        $args = "";
        if(isset($params['id'])) {
            $args .= PDOWrapper::cleanseNull($params['id']);
        } else {
            $args .= "null";
        }
        if(isset($params['title'])) {
            $args .= ", ".PDOWrapper::cleanseNullOrWrapStr($params['title']);
        } else {
            $args .= ", null";
        }
        if(isset($params['description'])) {
            $args .= ", ".PDOWrapper::cleanseNullOrWrapStr($params['description']);
        } else {
            $args .= ", null";
        }
        if(isset($params['deadline'])) {
            $args .= ", ".PDOWrapper::cleanseNull($params['deadline']);
        } else {
            $args .= ", null";
        }
        if(isset($params['organisation_id'])) {
            $args .= ", ".PDOWrapper::cleanseNull($params['organisation_id']);
        } else {
            $args .= ", null";
        }
        if(isset($params['reference'])) {
            $args .= ", ".PDOWrapper::cleanseNullOrWrapStr($params['reference']);
        } else {
            $args .= ", null";
        }
        if(isset($params['word-count'])) {
            $args .= ", ".PDOWrapper::cleanseNull($params['word-count']);
        } else {
            $args .= ", null";
        }
        if(isset($params['created'])) {
            $args .= ", ".PDOWrapper::cleanseNull($params['created']);
        } else {
            $args .= ", null";
        }
        if(isset($params['archived-date'])) {
            $args .= ", ".PDOWrapper::cleanseNull($params['archived-date']);
        } else {
            $args .= ", null";
        }
        if(isset($params['user_id-archived'])) {
            $args .= ", ".PDOWrapper::cleanseNull($params['user_id-archived']);
        } else {
            $args .= ", null";
        }

        $projects = array();
        $result = PDOWrapper::call("getArchivedProject", $args);
        if($result) {
            foreach($result as $row) {
                $project = ModelFactory::buildModel("ArchivedProject", $row);
                if(is_object($project)) {
                    $projects[] = $project;
                }
            }
        }

        if(count($projects) == 0) {
            $projects = null;
        }

        return $projects;
    }

    public function getProjectTasks($projectId)
    {
        $tasks = array();
        $args = "null, ";
        $args .= PDOWrapper::cleanseNull($projectId).", ";
        $args .= "null, null, null, null, null, null, null, null, null, null, null, null";
        $result = PDOWrapper::call("getTask", $args);
        if($result) {
            foreach($result as $row) {
                $task = ModelFactory::buildModel("Task", $row);
                if(is_object($task)) {
                    $tasks[] = $task;
                }
            }
        }

        return $tasks;
    }
}

