<?php

include_once '../Common/lib/PDOWrapper.class.php';
include_once '../Common/lib/ModelFactory.class.php';
include_once '../Common/models/Project.php';
include_once '../Common/models/ArchivedProject.php';

class ProjectDao
{
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

    /*
     *  Create a new project or update an existing
     *  Modifies the project passed by adding an id for new projects
     */
    public function insertProject(&$project)
    {
        $args = "";
        if(is_null($project->getId())) {
            $args .= "null";
        } else {
            $args .= PDOWrapper::cleanseNull($project->getId());
        }
        $args .= PDOWrapper::cleanseNullOrWrapStr($project->getTitle());
        $args .= PDOWrapper::cleanseNullOrWrapStr($project->getDescription());
        $args .= PDOWrapper::cleanseNull($project->getDeadline());
        $args .= PDOWrapper::cleanseNull($project->getOrganisationId());
        $args .= PDOWrapper::cleanseNullOrWrapStr($project->getReference());
        $args .= PDOWrapper::cleanseNull($project->getWordCount());
        $args .= PDOWrapper::cleanseNull($project->getCreatedTime());

        $result = PDOWrapper::call("projectInsertAndUpdate", $args);
        $project->setId($result[0]['id']);
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
}

