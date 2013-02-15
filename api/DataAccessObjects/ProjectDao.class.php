<?php

include_once 'DataAccessObjects/ProjectTags.class.php';
include_once 'DataAccessObjects/TagsDao.class.php';
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

    public function update($params)
    {
        if (!is_array($params) && is_object($params)) {
            $project   = APIHelper::cast("Project", $params);
        } else {
            $project = ModelFactory::buildModel("Project", $params);
        }
        
        $this->save($project);
        return $project;
    }
    
    private function save(&$project)
    {
        $projectId = "null";
        if($project->getId() != '') {
            $projectId = $project->getId();
        }

        $result = PDOWrapper::call("projectInsertAndUpdate", $projectId
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getTitle())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getDescription())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getDeadline())
                .",".PDOWrapper::cleanseNull($project->getOrganisationId())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getReference())
                .",".PDOWrapper::cleanseNull($project->getWordCount())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getCreatedTime())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getSourceCountryCode())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getSourceLanguageCode()));
        $project->setId($result[0]['id']);
        $this->updateTags($project);
        return $project;
    }

    private function updateTags($project)
    {
        ProjectTags::removeAllProjectTags($project->getId());
        $tags = $project->getTagList();
        if (count($tags) > 0) {
            if ($tag_ids = $this->tagsToIds($tags)) {
                ProjectTags::addProjectTags($project->getId(), $tag_ids);
                return 1;
            }
            return 0;
        }
        return 0;
    }
    
    private function tagsToIds($tags)
    {
        $tag_ids = array();
        foreach ($tags as $tag) {
            if ($tag_id = $this->getTagId($tag)) {
                $tag_ids[] = $tag_id;
            } else {
                $tag_ids[] = $this->createTag($tag);
            }
        }
        
        if (count($tag_ids) > 0) {
            return $tag_ids;
        } else {
            return null;
        }
    }
    
    public function getTagId($tag)
    {
        $tDAO = new TagsDao();
        return $tDAO->tagIDFromLabel($tag);
    }
    
    private function createTag($tag)
    {
        $tDAO = new TagsDao();
        return $tDAO->create($tag);
    }
    
    public function getProject($params)
    {
        $args = "";
        if (isset($params['id'])) {
            $args .= PDOWrapper::cleanseNull($params['id']);
        } else {
            $args .= "null";
        }
        if (isset($params['title'])) {
            $args .= ", ".PDOWrapper::cleanseNullOrWrapStr($params['title']);
        } else {
            $args .= ", null";
        }
        if (isset($params['description'])) {
            $args .= ", ".PDOWrapper::cleanseNullOrWrapStr($params['description']);
        } else {
            $args .= ", null";
        }
        if (isset($params['deadline'])) {
            $args .= ", ".PDOWrapper::cleanseNull($params['deadline']);
        } else {
            $args .= ", null";
        }
        if (isset($params['organisation_id'])) {
            $args .= ", ".PDOWrapper::cleanseNull($params['organisation_id']);
        } else {
            $args .= ", null";
        }
        if (isset($params['reference'])) {
            $args .= ", ".PDOWrapper::cleanseNullOrWrapStr($params['reference']);
        } else {
            $args .= ", null";
        }
        if (isset($params['word-count'])) {
            $args .= ", ".PDOWrapper::cleanseNull($params['word-count']);
        } else {
            $args .= ", null";
        }
        if (isset($params['created'])) {
            $args .= ", ".PDOWrapper::cleanseNull($params['created']);
        } else {
            $args .= ", null";
        }
        if (isset($params['language_id'])) {
            $args .= ", ".PDOWrapper::cleanseNull($params['language_id']);
        } else {
            $args .= ", null";
        }
        if (isset($params['country_id'])) {
            $args .= ", ".PDOWrapper::cleanseNull($params['country_id']);
        } else {
            $args .= ", null";
        }

        $projects = array();
        $result = PDOWrapper::call("getProject", $args);
        if($result) {
            foreach($result as $row) {
                $project = ModelFactory::buildModel("Project", $row);
                if(is_object($project)) {
                    $tags = $this->getProjectTags($project->getId());
                    if($tags) {
                        foreach ($tags as $tag) {
                            $project->addTag($tag->getLabel());
                        }
                    }

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
        $args = PDOWrapper::cleanseNull($projectId).",".PDOWrapper::cleanseNull($userId);
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

    private function getProjectTags($projectId)
    {
        return ProjectTags::getTags($projectId);
    }
    
    public function saveProjectFile($projectId,$file,$filename,$userId){
        $destination =realpath(Settings::get("files.upload_path")."proj-$projectId/");
        if(!file_exists($destination)) mkdir ($destination);
        $token=self::recordProjectFile($projectId,$file,$filename,$userId);
        file_put_contents($destination.$token, $file);
        
        
        
    }
    
    public function recordProjectFile($projectId,$file,$filename,$userId){
        $token=$filename;//generate guid in future.
        $args = PDOWrapper::cleanseNull($projectId).",".PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNull($filename).",".PDOWrapper::cleanseNull($token);
        PDOWrapper::call("addProjectFile", $args);
        return $token;
    }
    
    
    
}

