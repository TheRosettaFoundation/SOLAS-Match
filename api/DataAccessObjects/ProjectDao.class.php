<?php

include_once __DIR__.'/ProjectTags.class.php';
include_once __DIR__.'/TagsDao.class.php';
include_once __DIR__.'/../../Common/lib/PDOWrapper.class.php';
include_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
include_once __DIR__.'/../../Common/models/Project.php';
include_once __DIR__.'/../../Common/models/ArchivedProject.php';

class ProjectDao
{
    
    public function createUpdate($project)
    {
        $this->save($project);
        return $project;
    }
    
    private function save(&$project)
    {        
        $args =PDOWrapper::cleanseNull($project->getId())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getTitle())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getDescription())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getImpact())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getDeadline())
                .",".PDOWrapper::cleanseNull($project->getOrganisationId())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getReference())
                .",".PDOWrapper::cleanseNull($project->getWordCount())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getCreatedTime())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getSourceCountryCode())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getSourceLanguageCode());
        $result = PDOWrapper::call("projectInsertAndUpdate", $args);
        $project->setId($result[0]['id']);
        $tagsDao = new TagsDao();
        $tagsDao->updateTags($project);
        $project = ModelFactory::buildModel("Project", $result[0]);
        $project->setTag(ProjectTags::getTags($project->getId()));
        return $project;
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
        if (isset($params['impact'])) {
            $args .= ", ".PDOWrapper::cleanseNullOrWrapStr($params['impact']);
        } else {
            $args .= ", null";
        }
        if (isset($params['deadline'])) {
            $args .= ", ".PDOWrapper::cleanseNullOrWrapStr($params['deadline']);
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
            $args .= ", ".PDOWrapper::cleanseNullOrWrapStr($params['created']);
        } else {
            $args .= ", null";
        }
        
        if (isset($params['country_id'])) {
            $args .= ", ".PDOWrapper::cleanseNullOrWrapStr($params['country_id']);
        } else {
            $args .= ", null";
        }
        if (isset($params['language_id'])) {
            $args .= ", ".PDOWrapper::cleanseNullOrWrapStr($params['language_id']);
        } else {
            $args .= ", null";
        }

        $projects = array();
        $result = PDOWrapper::call("getProject", $args);
        if($result) {
            foreach($result as $row) {
                $project = ModelFactory::buildModel("Project", $row);
                if(is_object($project)) {
                    $tagsDao = new ProjectTags();
                    $tags = $tagsDao->getTags($project->getId());
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
        $result = PDOWrapper::call("archiveProject", $args);        
        if($result) {
            return ModelFactory::buildModel("ArchivedProject", $result[0]);
        } else {
            return null;
        }
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
        if(isset($params['impact'])) {
            $args .= ", ".PDOWrapper::cleanseNullOrWrapStr($params['impact']);
        } else {
            $args .= ", null";
        }
        if(isset($params['deadline'])) {
            $args .= ", ".PDOWrapper::cleanseNullOrWrapStr($params['deadline']);
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
            $args .= ", ".PDOWrapper::cleanseNullOrWrapStr($params['created']);
        } else {
            $args .= ", null";
        }
        if(isset($params['archived-date'])) {
            $args .= ", ".PDOWrapper::cleanseNullOrWrapStr($params['archived-date']);
        } else {
            $args .= ", null";
        }
        if(isset($params['user_id-archived'])) {
            $args .= ", ".PDOWrapper::cleanseNull($params['user_id-archived']);
        } else {
            $args .= ", null";
        }

        $projects = null;
        $result = PDOWrapper::call("getArchivedProject", $args);
        if($result) {
            $projects = array();
            foreach($result as $row) {
                $project = ModelFactory::buildModel("ArchivedProject", $row);
                
                if(is_object($project)) {
                    $projects[] = $project;
                }
            }
        }

        if(count ($projects == 1)) {
            return $projects[0];
        } else {
            return $projects;
        }
    }

    public function getProjectTasks($projectId)
    {
        $tasks = null;
        $args = "null, ";
        $args .= PDOWrapper::cleanseNull($projectId).", ";
        $args .= "null, null, null, null, null, null, null, null, null, null, null, null";
        $result = PDOWrapper::call("getTask", $args);
        if($result) {
            $tasks = array();
            foreach($result as $row) {
                $task = ModelFactory::buildModel("Task", $row);
                if(is_object($task)) {
                    $tasks[] = $task;
                }
            }
        }

        return $tasks;
    }
    
    public function getProjectFileInfo($project_id, $user_id, $filename, $token, $mime) {
        
        $args = PDOWrapper::cleanseNull($project_id).",".PDOWrapper::cleanseNull($user_id)
                .",".PDOWrapper::cleanseNullOrWrapStr($filename).",".PDOWrapper::cleanseNullOrWrapStr($token)
                .",".PDOWrapper::cleanseNullOrWrapStr($mime);
        $result = PDOWrapper::call("getProjectFile", $args);
        
        if($result) {
            return ModelFactory::buildModel("ProjectFile", $result[0]);
        } else {
            return null;
        }        
    }
    
    public function getProjectFile($projectId) {
        $projectFileInfo = $this->getProjectFileInfo($projectId, null, null, null, null);
        $filename = $projectFileInfo->getFilename();
        $source = Settings::get("files.upload_path")."proj-$projectId/$filename";
        return file_get_contents($source);
        //IO::downloadFile($source, $projectFileInfo->getMime());
    }
    
    public function saveProjectFile($projectId,$file,$filename,$userId){
        $destination =Settings::get("files.upload_path")."proj-$projectId/";
        if(!file_exists($destination)) mkdir ($destination);
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime= $finfo->buffer($file);
        $token=self::recordProjectFileInfo($projectId,$filename,$userId,$mime);
        file_put_contents($destination.$token, $file);
        return $token;        
    }
    
    public function recordProjectFileInfo($projectId,$filename,$userId, $mime){
        $token = $filename;//generate guid in future.
        $args = PDOWrapper::cleanseNull($projectId).",".PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNullOrWrapStr($filename).",".PDOWrapper::cleanseNullOrWrapStr($token)
                .",".PDOWrapper::cleanseNullOrWrapStr($mime);
        $result = PDOWrapper::call("addProjectFile", $args);        
        if($result[0]['result'] == "1") {            
            return $token;
        } else {
            return null;
        }        
    }
    
}

