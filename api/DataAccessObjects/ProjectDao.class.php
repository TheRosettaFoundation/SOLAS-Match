<?php

include_once __DIR__."/TagsDao.class.php";
include_once __DIR__."/../../api/lib/PDOWrapper.class.php";
include_once __DIR__."/../../Common/lib/ModelFactory.class.php";
include_once __DIR__."/../../Common/models/Project.php";
include_once __DIR__."/../../Common/models/ArchivedProject.php";
include_once __DIR__."/../lib/MessagingClient.class.php";
include_once __DIR__."/../../Common/Requests/CalculateProjectDeadlinesRequest.php";

class ProjectDao
{
    
    public static function createUpdate($project)
    {
        self::save($project);
        return $project;
    }
    
    private static function save(&$project)
    {       
        $sourceLocale = $project->getSourceLocale();
        $args =PDOWrapper::cleanseNull($project->getId())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getTitle())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getDescription())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getImpact())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getDeadline())
                .",".PDOWrapper::cleanseNull($project->getOrganisationId())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getReference())
                .",".PDOWrapper::cleanseNull($project->getWordCount())
                .",".PDOWrapper::cleanseNullOrWrapStr($project->getCreatedTime())
                .",".PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getCountryCode())
                .",".PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getLanguageCode());
        $result = PDOWrapper::call("projectInsertAndUpdate", $args);
        $project->setId($result[0]['id']);

        TagsDao::updateTags($project->getId(), $project->getTagList());
        $project = ModelFactory::buildModel("Project", $result[0]);
        $projectTags = self::getTags($project->getId());
        if($projectTags) {
            foreach($projectTags as $tag) {
                $project->addTag($tag);
            }
        }
        
        return $project;
    }

    public static function calculateProjectDeadlines($projectId)
    {
        $messagingClient = new MessagingClient();
        if ($messagingClient->init()) {
            $proto = new CalculateProjectDeadlinesRequest();
            $proto->setProjectId($projectId);
            $message = $messagingClient->createMessageFromProto($proto);
            $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange,
                    $messagingClient->CalculateProjectDeadlinesTopic);
        }
    }
    
    public static function getProject($id=null, $title=null, $description=null, $impact=null, $deadline=null, $orgId=null,
                                        $reference=null, $wordCount=null, $created=null, $countryCode=null, $languageCode=null)
    {
        $projects = array();
        
        $args = PDOWrapper::cleanseNull($id)
                .",".PDOWrapper::cleanseNullOrWrapStr($title)
                .",".PDOWrapper::cleanseNullOrWrapStr($description)
                .",".PDOWrapper::cleanseNullOrWrapStr($impact)
                .",".PDOWrapper::cleanseNullOrWrapStr($deadline)
                .",".PDOWrapper::cleanseNull($orgId)
                .",".PDOWrapper::cleanseNullOrWrapStr($reference)
                .",".PDOWrapper::cleanseNullOrWrapStr($wordCount)
                .",".PDOWrapper::cleanseNullOrWrapStr($created)
                .",".PDOWrapper::cleanseNullOrWrapStr($countryCode)
                .",".PDOWrapper::cleanseNullOrWrapStr($languageCode);
        
        $result = PDOWrapper::call("getProject", $args);
        if($result) {
            foreach($result as $row) {
                $projects[] = ModelFactory::buildModel("Project", $row);
            }
        }

        if(count($projects) == 0) {
            $projects = null;
        }

        return $projects;
    }

    public static function archiveProject($projectId, $userId)
    {
        $args = PDOWrapper::cleanseNull($projectId)
                .",".PDOWrapper::cleanseNull($userId);
        
        $result = PDOWrapper::call("archiveProject", $args);        
        if($result) {
            return ModelFactory::buildModel("ArchivedProject", $result[0]);
        } else {
            return null;
        }
    }

    public static function getArchivedProject($id=null,$orgId=null, $title=null, $description=null, $impact=null, $deadline=null,  $reference=null,
                                                $wordCount=null, $created=null, $archivedDate=null, $userIdArchived=null)
    {
        $projects = array();
        
        $args = PDOWrapper::cleanseNull($id)
                .",".PDOWrapper::cleanseNullOrWrapStr($title)
                .",".PDOWrapper::cleanseNullOrWrapStr($description)
                .",".PDOWrapper::cleanseNullOrWrapStr($impact)
                .",".PDOWrapper::cleanseNullOrWrapStr($deadline)
                .",".PDOWrapper::cleanseNull($orgId)
                .",".PDOWrapper::cleanseNullOrWrapStr($reference)
                .",".PDOWrapper::cleanseNullOrWrapStr($wordCount)
                .",".PDOWrapper::cleanseWrapStr($created)
                .",".PDOWrapper::cleanseNullOrWrapStr($archivedDate)
                .",".PDOWrapper::cleanseNull($userIdArchived);
        
        $result = PDOWrapper::call("getArchivedProject", $args);
        
        if($result) {           
            foreach($result as $row) {
                $projects[] = ModelFactory::buildModel("ArchivedProject", $row);
            }
        }

        if(count($projects) == 0) {
            $projects = null;
        }
        
        return $projects;
    }

    public static function getProjectTasks($projectId)
    {
        $tasks = null;
        $args = "null"
                .",".PDOWrapper::cleanseNull($projectId)
                .",null, null, null, null, null, null, null, null, null, null, null, null";
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
    
    public static function getTags($project_id)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getProjectTags", PDOWrapper::cleanseNull($project_id))) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Tag", $row);
            }
        }
        return $ret;
    }

    public static function removeProjectTag($projectId, $tagId)
    {
        $args = PDOWrapper::cleanseNull($projectId)
                .",".PDOWrapper::cleanseNull($tagId);
        
        $result = PDOWrapper::call("removeProjectTag", $args);
        if($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }

    public static function removeAllProjectTags($projectId)
    {
        if($tags = self::getTags($projectId)) {
            foreach ($tags as $tag) {
                self::removeProjectTag($projectId, $tag->getId());
            }
        }
    }

    public static function addProjectTags($projectId, $projectTags)
    {
        foreach ($projectTags as $tag) {
            self::addProjectTag($projectId, $tag->getId());
        }
    }

    public static function addProjectTag($projectId, $tagId)
    {
        $args = PDOWrapper::cleanseNull($projectId)
                .",".PDOWrapper::cleanseNull($tagId);
        
        $result = PDOWrapper::call("addProjectTag", $args);
        if($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }
    
    public static function getProjectFileInfo($project_id, $user_id, $filename, $token, $mime)
    {        
        $args = PDOWrapper::cleanseNull($project_id)
                .",".PDOWrapper::cleanseNull($user_id)
                .",".PDOWrapper::cleanseNullOrWrapStr($filename)
                .",".PDOWrapper::cleanseNullOrWrapStr($token)
                .",".PDOWrapper::cleanseNullOrWrapStr($mime);
        
        $result = PDOWrapper::call("getProjectFile", $args);
        
        if($result) {
            return ModelFactory::buildModel("ProjectFile", $result[0]);
        } else {
            return null;
        }        
    }
    
    public static function getProjectFile($projectId)
    {
        $projectFileInfo = self::getProjectFileInfo($projectId, null, null, null, null);
        $filename = $projectFileInfo->getFilename();
        $source = Settings::get("files.upload_path")."proj-$projectId/$filename";
        //return file_get_contents($source);
        IO::downloadFile($source, $projectFileInfo->getMime());
    }
    
    public static function saveProjectFile($projectId,$file,$filename,$userId)
    {
        $destination =Settings::get("files.upload_path")."proj-$projectId/";
        if(!file_exists($destination)) mkdir ($destination);
//        $finfo = new finfo(FILEINFO_MIME_TYPE);
//        $mime= $finfo->buffer($file);
        $mime = IO::detectMimeType($file, $filename);
        $token=self::recordProjectFileInfo($projectId,$filename,$userId,$mime);
        file_put_contents($destination.$token, $file);
        return $token;
    }
    
    public static function recordProjectFileInfo($projectId,$filename,$userId, $mime)
    {
        $token = $filename;//generate guid in future.
        $args = PDOWrapper::cleanseNull($projectId)
                .",".PDOWrapper::cleanseNull($userId)
                .",".PDOWrapper::cleanseNullOrWrapStr($filename)
                .",".PDOWrapper::cleanseNullOrWrapStr($token)
                .",".PDOWrapper::cleanseNullOrWrapStr($mime);
        
        $result = PDOWrapper::call("addProjectFile", $args);        
        if($result[0]['result'] == "1") {            
            return $token;
        } else {
            return null;
        }        
    }
    
    public static function getArchivedTask($projectId, $archiveId=null, $title=null, $comment=null, $deadline=null,
            $wordCount=null, $createdTime=null, $sourceLanguageId=null, $targetLanguageId=null, $sourceCountryId=null,
            $targetCountryId=null, $taskTypeId=null, $taskStatusId=null, $published=null)
    {
        $args = PDOWrapper::cleanseNull($archiveId)
                .",".PDOWrapper::cleanseNull($projectId)
                .",".PDOWrapper::cleanseNullOrWrapStr($title)
                .",".PDOWrapper::cleanseNullOrWrapStr($comment)
                .",".PDOWrapper::cleanseNullOrWrapStr($deadline)
                .",".PDOWrapper::cleanseNull($wordCount)
                .",".PDOWrapper::cleanseNullOrWrapStr($createdTime)
                .",".PDOWrapper::cleanseNull($sourceLanguageId)
                .",".PDOWrapper::cleanseNull($targetLanguageId)
                .",".PDOWrapper::cleanseNull($sourceCountryId)
                .",".PDOWrapper::cleanseNull($targetCountryId)
                .",".PDOWrapper::cleanseNull($taskTypeId)
                .",".PDOWrapper::cleanseNull($taskStatusId)
                .",".PDOWrapper::cleanseNullOrWrapStr($published);
        
        $ret = null;
        if ($result = PDOWrapper::call("getArchivedTask", $args)) { 
           $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("ArchivedTask", $row);
            }
        }
        return $ret; 
    }
    
    public static function deleteProjectTags($projectId)
    {
        $args = PDOWrapper::cleanseNull($projectId);        
           
        if($result = PDOWrapper::call("deleteProjectTags", $args)) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }    
}
