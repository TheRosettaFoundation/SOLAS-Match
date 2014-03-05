<?php

namespace SolasMatch\API\DAO;

use \SolasMatch\Common as Common;
use \SolasMatch\API\Lib as Lib;

include_once __DIR__."/TagsDao.class.php";
include_once __DIR__."/../../api/lib/PDOWrapper.class.php";
include_once __DIR__."/../../Common/lib/ModelFactory.class.php";
include_once __DIR__."/../../Common/protobufs/models/Project.php";
include_once __DIR__."/../../Common/protobufs/models/ArchivedProject.php";
include_once __DIR__."/../lib/MessagingClient.class.php";
include_once __DIR__."/../../Common/protobufs/Requests/CalculateProjectDeadlinesRequest.php";
include_once __DIR__."/../../Common/lib/SolasMatchException.php";

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
        $args = Lib\PDOWrapper::cleanseNull($project->getId()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($project->getTitle()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($project->getDescription()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($project->getImpact()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($project->getDeadline()).",".
            Lib\PDOWrapper::cleanseNull($project->getOrganisationId()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($project->getReference()).",".
            Lib\PDOWrapper::cleanseNull($project->getWordCount()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($project->getCreatedTime()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getCountryCode()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getLanguageCode());
        $result = Lib\PDOWrapper::call("projectInsertAndUpdate", $args);
        $project->setId($result[0]['id']);

        TagsDao::updateTags($project->getId(), $project->getTagList());
        $project = Common\Lib\ModelFactory::buildModel("Project", $result[0]);
        $projectTags = self::getTags($project->getId());
        if ($projectTags) {
            foreach ($projectTags as $tag) {
                $project->addTag($tag);
            }
        }
        
        return $project;
    }

    public static function calculateProjectDeadlines($projectId)
    {
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            $proto = new \CalculateProjectDeadlinesRequest();
            $proto->setProjectId($projectId);
            $message = $messagingClient->createMessageFromProto($proto);
            $messagingClient->sendTopicMessage(
                $message,
                $messagingClient->MainExchange,
                $messagingClient->CalculateProjectDeadlinesTopic
            );
        }
    }
    
    public static function getProject(
        $id = null,
        $title = null,
        $description = null,
        $impact = null,
        $deadline = null,
        $orgId = null,
        $reference = null,
        $wordCount = null,
        $created = null,
        $countryCode = null,
        $languageCode = null
    ) {
        $projects = array();
        $args = Lib\PDOWrapper::cleanseNull($id).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($title).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($description).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($impact).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($deadline).",".
            Lib\PDOWrapper::cleanseNull($orgId).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($reference).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($wordCount).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($created).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($countryCode).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($languageCode);
        $result = Lib\PDOWrapper::call("getProject", $args);
        if ($result) {
            foreach ($result as $row) {
                $projects[] = Common\Lib\ModelFactory::buildModel("Project", $row);
            }
        }
        if (count($projects) == 0) {
            $projects = null;
        }
        return $projects;
    }

    public static function archiveProject($projectId, $userId)
    {
        $args = Lib\PDOWrapper::cleanseNull($projectId).",".
            Lib\PDOWrapper::cleanseNull($userId);
        $result = Lib\PDOWrapper::call("archiveProject", $args);
        if ($result) {
            return Common\Lib\ModelFactory::buildModel("ArchivedProject", $result[0]);
        } else {
            return null;
        }
    }

    public static function getArchivedProject(
        $id = null,
        $orgId = null,
        $title = null,
        $description = null,
        $impact = null,
        $deadline = null,
        $reference = null,
        $wordCount = null,
        $created = null,
        $archivedDate = null,
        $userIdArchived = null
    ) {
        $projects = array();
        $args = Lib\PDOWrapper::cleanseNull($id).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($title).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($description).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($impact).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($deadline).",".
            Lib\PDOWrapper::cleanseNull($orgId).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($reference).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($wordCount).",".
            Lib\PDOWrapper::cleanseWrapStr($created).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($archivedDate).",".
            Lib\PDOWrapper::cleanseNull($userIdArchived);
        $result = Lib\PDOWrapper::call("getArchivedProject", $args);
        if ($result) {
            foreach ($result as $row) {
                $projects[] = Common\Lib\ModelFactory::buildModel("ArchivedProject", $row);
            }
        }
        if (count($projects) == 0) {
            $projects = null;
        }
        return $projects;
    }

    public static function getProjectTasks($projectId)
    {
        $tasks = null;
        $args = "null, ".Lib\PDOWrapper::cleanseNull($projectId).
            ", null, null, null, null, null, null, null, null, null, null, null, null";
        $result = Lib\PDOWrapper::call("getTask", $args);
        if ($result) {
            $tasks = array();
            foreach ($result as $row) {
                $task = Common\Lib\ModelFactory::buildModel("Task", $row);
                if (is_object($task)) {
                    $tasks[] = $task;
                }
            }
        }
        return $tasks;
    }
    
    public static function getTags($project_id)
    {
        $ret = null;
        if ($result = Lib\PDOWrapper::call("getProjectTags", Lib\PDOWrapper::cleanseNull($project_id))) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Tag", $row);
            }
        }
        return $ret;
    }

    public static function removeProjectTag($projectId, $tagId)
    {
        $args = Lib\PDOWrapper::cleanseNull($projectId).",".
            Lib\PDOWrapper::cleanseNull($tagId);
        
        $result = Lib\PDOWrapper::call("removeProjectTag", $args);
        if ($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }

    public static function removeAllProjectTags($projectId)
    {
        if ($tags = self::getTags($projectId)) {
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
        $args = Lib\PDOWrapper::cleanseNull($projectId).",".
            Lib\PDOWrapper::cleanseNull($tagId);
        $result = Lib\PDOWrapper::call("addProjectTag", $args);
        if ($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }
    
    public static function getProjectFileInfo(
        $project_id,
        $user_id = null,
        $filename = null,
        $token = null,
        $mime = null
    ) {
        $args = Lib\PDOWrapper::cleanseNull($project_id).",".
            Lib\PDOWrapper::cleanseNull($user_id).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($filename).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($token).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($mime);
        $result = Lib\PDOWrapper::call("getProjectFile", $args);
        if ($result) {
            return Common\Lib\ModelFactory::buildModel("ProjectFile", $result[0]);
        } else {
            return null;
        }
    }
    
    public static function getProjectFile($projectId)
    {
        $projectFileInfo = self::getProjectFileInfo($projectId, null, null, null, null);
        $filename = $projectFileInfo->getFilename();
        $source = Common\Lib\Settings::get("files.upload_path")."proj-$projectId/$filename";
        Lib\IO::downloadFile($source, $projectFileInfo->getMime());
    }
    
    public static function saveProjectFile($projectId, $file, $filename, $userId)
    {
        $destination = Common\Lib\Settings::get("files.upload_path")."proj-$projectId/";
        if (!file_exists($destination)) {
            mkdir($destination);
        }
        $mime = Lib\IO::detectMimeType($file, $filename);
        $apiHelper = new Common\Lib\APIHelper(Common\Lib\Settings::get("ui.api_format"));
        $canonicalMime = $apiHelper->getCanonicalMime($filename);
        if (!is_null($canonicalMime) && $mime != $canonicalMime) {
            $message = "The content type ($mime) of the file you are trying to upload does not";
            $message .= " match the content type ($canonicalMime) expected from its extension.";
            throw new Common\Exceptions\SolasMatchException($message, Common\Enums\HttpStatusEnum::BAD_REQUEST);
        }
        $token = self::recordProjectFileInfo($projectId, $filename, $userId, $mime);
        try {
            file_put_contents($destination.$token, $file);
        } catch (Exception $e) {
            $message = "You cannot upload a project file for project ($projectId), as one already exists.";
            throw new Common\Exceptions\SolasMatchException($message, Common\Enums\HttpStatusEnum::CONFLICT);
        }

        return $token;
    }
    
    public static function recordProjectFileInfo($projectId, $filename, $userId, $mime)
    {
        $token = $filename;//generate guid in future.
        $args = Lib\PDOWrapper::cleanseNull($projectId).",".
            Lib\PDOWrapper::cleanseNull($userId).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($filename).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($token).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($mime);
        $result = Lib\PDOWrapper::call("addProjectFile", $args);
        if ($result[0]['result'] == "1") {
            return $token;
        } else {
            return null;
        }
    }
    
    public static function getArchivedTask(
        $projectId,
        $archiveId = null,
        $title = null,
        $comment = null,
        $deadline = null,
        $wordCount = null,
        $createdTime = null,
        $sourceLanguageId = null,
        $targetLanguageId = null,
        $sourceCountryId = null,
        $targetCountryId = null,
        $taskTypeId = null,
        $taskStatusId = null,
        $published = null
    ) {
        $args = Lib\PDOWrapper::cleanseNull($archiveId).",".
            Lib\PDOWrapper::cleanseNull($projectId).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($title).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($comment).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($deadline).",".
            Lib\PDOWrapper::cleanseNull($wordCount).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($createdTime).",".
            Lib\PDOWrapper::cleanseNull($sourceLanguageId).",".
            Lib\PDOWrapper::cleanseNull($targetLanguageId).",".
            Lib\PDOWrapper::cleanseNull($sourceCountryId).",".
            Lib\PDOWrapper::cleanseNull($targetCountryId).",".
            Lib\PDOWrapper::cleanseNull($taskTypeId).",".
            Lib\PDOWrapper::cleanseNull($taskStatusId).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($published);
        $ret = null;
        if ($result = Lib\PDOWrapper::call("getArchivedTask", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("ArchivedTask", $row);
            }
        }
        return $ret;
    }
    
    public static function deleteProjectTags($projectId)
    {
        $args = Lib\PDOWrapper::cleanseNull($projectId);
        if ($result = Lib\PDOWrapper::call("deleteProjectTags", $args)) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }
    
    public static function delete($projectId)
    {
        $args = Lib\PDOWrapper::cleanseNull($projectId);
        if ($result = Lib\PDOWrapper::call("deleteProject", $args)) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }
}
