<?php

namespace SolasMatch\API\DAO;

use \SolasMatch\Common as Common;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;

require_once __DIR__."/TagsDao.class.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";
require_once __DIR__."/../../Common/lib/ModelFactory.class.php";
require_once __DIR__."/../../Common/protobufs/models/Project.php";
require_once __DIR__."/../../Common/protobufs/models/ArchivedProject.php";
require_once __DIR__."/../lib/MessagingClient.class.php";
require_once __DIR__."/../../Common/protobufs/Requests/CalculateProjectDeadlinesRequest.php";
require_once __DIR__."/../../Common/lib/SolasMatchException.php";

//! Project Data Access Object for setting getting data about Projects in the API
/*!
  The Project Data Access Object for manipulating data in the Database. It has direct Database access through the use
  of the PDOWrapper. It is used by the API Route Handlers for retrieving and setting data requested through the API.
*/

class ProjectDao
{
    //! Create a new Project or update an existing one
    /*!
      This function takes a Project object as a parameter. If the that object has a valid Project id then that Project
      will be updated in the database with the other values of the Project object parameter. If the input Project does
      not have a valid id then a new Project will be created with the data provided by the Project object. If the new
      Project does not satisfy the unique constraints of the Projects table then the project will not be created.
      @param Project is the Project being updated/created
      @return Returns the updated Project object (with a new id if the Project was created)
    */
    public static function save($project)
    {
        $tagList = $project->getTag(); //Get the project's tags.
        $sourceLocale = $project->getSourceLocale();
        $args = Lib\PDOWrapper::cleanseNullOrWrapStr($project->getId()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($project->getTitle()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($project->getDescription()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($project->getImpact()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($project->getDeadline()).",".
            Lib\PDOWrapper::cleanseNull($project->getOrganisationId()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($project->getReference()).",".
            Lib\PDOWrapper::cleanseNull($project->getWordCount()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($project->getCreatedTime()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getCountryCode()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getLanguageCode()).",".
            Lib\PDOWrapper::cleanseNull($project->getImageUploaded()).",".
            Lib\PDOWrapper::cleanseNull($project->getImageApproved());

        $result = Lib\PDOWrapper::call("projectInsertAndUpdate", $args);
        if ($result) {
            $project = Common\Lib\ModelFactory::buildModel("Project", $result[0]);
        }

        TagsDao::updateTags($project->getId(), $tagList);
        $project->clearTag();

        $projectTags = self::getTags($project->getId());
        if ($projectTags) {
            foreach ($projectTags as $tag) {
                $project->appendTag($tag);
            }
        }

        return $project;
    }

    //! Used to automatically calculate the Deadlines for Project Tasks
    /*!
      When this function is called it generates a CalculateProjectDeadlineRequest object and pushes it to RabbitMQ.
      This gets picked up by the backend which then alters the deadlines of Tasks in the Project to give each volunteer
      enough time to complete their task. This is called when a Project is created or when a Project's deadline is
      updated.
      @param int $projectId is the id of a Project
      @return No return
    */
    public static function calculateProjectDeadlines($projectId)
    {
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            $proto = new Common\Protobufs\Requests\CalculateProjectDeadlinesRequest();
            $proto->setProjectId($projectId);
            $message = $messagingClient->createMessageFromProto($proto);
            error_log('sendTopicMessage CalculateProjectDeadlinesTopic: ' . $projectId);
            $messagingClient->sendTopicMessage(
                $message,
                $messagingClient->MainExchange,
                $messagingClient->CalculateProjectDeadlinesTopic
            );
        }
    }

    //! Retrieve a single Project from the database
    /*!
      Get a single project by its id. If null is passed for the id then this function will return null.
      @param int $id is the id of a project
      @return Returns a Project object
    */
    public static function getProject($id)
    {
        $project = null;
        if (!is_null($id)) {
            $args = Lib\PDOWrapper::cleanseNull($id).", null, null, null, null, null, null, null, null, null, null, null, null";
            $result = Lib\PDOWrapper::call("getProject", $args);
            if ($result) {
                $project = Common\Lib\ModelFactory::buildModel("Project", $result[0]);
            }
        }
        return $project;
    }

    //! Retrieve a single Project from the database
    /*!
     Get a single project by its id. If null is passed for the id then this function will return null.
    @param int $id is the id of a project
    @return Returns a Project object
    */
    public static function getProjectByName($name)
    {
        $project = null;
        if (!is_null($name)) {
            $args = "null, "
                    .Lib\PDOWrapper::cleanseNullOrWrapStr($name).
                    ", null, null, null, null, null, null, null, null, null, null, null";
            $result = Lib\PDOWrapper::call("getProject", $args);
            if ($result) {
                $project = Common\Lib\ModelFactory::buildModel("Project", $result[0]);
            }
        }
        return $project;
    }

    //! Retrieve a single Project from the database
    /*!
      Get a single project by its title and organisation.
      @param string $title is the title of the requested Project
      @param int $orgId is the id of the Organisation the request Project(s) belong to
      @return Returns a Project object. If null is passed for either parameter then this function will return null.
    */
    public static function getProjectByNameAndOrganisation($title, $orgId)
    {
        $project = null;
        if (!is_null($title) && !is_null($orgId)) {
            $args = "null, ".
                Lib\PDOWrapper::cleanseNullOrWrapStr($title).", ".
                "null, ".
                "null, ".
                "null, ".
                Lib\PDOWrapper::cleanseNull($orgId).", ".
                "null, ".
                "null, ".
                "null, ".
                "null, ".
                "null, ".
                "null, ".
                "null";
            $result = Lib\PDOWrapper::call("getProject", $args);
            if ($result) {
                $project = Common\Lib\ModelFactory::buildModel("Project", $result[0]);
            }
        }
        return $project;
    }

    //! Get a Project from the database
    /*!
      Used to retrieve a specific Project(s) from the database. All arguments for this function default to null. if
      null is passed for any argument then it is ignored. If all arguments are null then every Project on the system
      will be returned.
      @param int $id is the id of the requested Project
      @param string $title is the title of the requested Project
      @param string $description is the description of the requested Project
      @param string $impact is the impact of the requested Project
      @param string $deadline is the deadline of the requested Project in the format "YYYY-MM-DD HH:MM:SS"
      @param int $orgId is the id of the Organisation the request Project(s) belong to
      @param string $reference is the reference page of the requested Project
      @param int $wordCount is the word count of the requested Project
      @param string $created is the date and time at which the requested Project was created in the format
      "YYYY-MM-DD HH:MM:SS"
      @param string $countryCode is the country code of the requested Project's source Locale (<b>NOTE</b>: This will
      get converted to a country id on the database)
      @param string $languageCode is the language code of the requested Project's source Locale (<b>NOTE</b>: This will
      get converted to a language id on the database)
      @param bit $imageUploaded represents whether a project image has been uploaded or not for the requested Project
      @param bit $imageApproved represents whether an uploaded project image has been approved by admins or not for the requested Project
      @return Returns an array of Project objects as filtered by the input arguments
    */
    public static function getProjects(
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
        $languageCode = null,
        $imageUploaded = null,
        $imageApproved = null
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
            Lib\PDOWrapper::cleanseNullOrWrapStr($languageCode).",".
            Lib\PDOWrapper::cleanseNull($imageUploaded).",".
            Lib\PDOWrapper::cleanseNull($imageApproved);
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

    //! Move a Project to the ArchivedProjects table
    /*!
      Archiving a Project means copying Project details to the ArchivedProjects table and then deleting th Project from
      the Projects table. Archiving a Project will also archive all the Project's Tasks. An ArchivedProject is for
      information only and should not be manipulated. It should be used by the Task scoring algorithm.
      @param int $projectId is the id of the Project being archived.
      @param int $userId is the id of the User performing the archive
      @return Returns '1' if the Project was successfully archived, '0' otherwise
    */
    public static function archiveProject($projectId, $userId)
    {
        $args = Lib\PDOWrapper::cleanseNull($projectId).",".
            Lib\PDOWrapper::cleanseNull($userId);
error_log("call archiveProject($args)");
error_log('Project: ' . print_r(ProjectDao::getProject($projectId), true));
error_log('ArchivedProject: ' . print_r(ProjectDao::getArchivedProject($projectId), true));
error_log('Tasks: ' . print_r(ProjectDao::getProjectTasks($projectId), true));
error_log('ArchivedTasks: ' . print_r(ProjectDao::getArchivedTask($projectId), true));
//        $result = Lib\PDOWrapper::call("archiveProject", $args);
error_log('Project: ' . print_r(ProjectDao::getProject($projectId), true));
error_log('ArchivedProject: ' . print_r(ProjectDao::getArchivedProject($projectId), true));
error_log('Tasks: ' . print_r(ProjectDao::getProjectTasks($projectId), true));
error_log('ArchivedTasks: ' . print_r(ProjectDao::getArchivedTask($projectId), true));
if (!empty($result)) {
    error_log('result: ' . print_r($result, true));
} else {
    error_log('$result empty()');
}
        if ($result) {
            return $result[0]['result'];
        } else {
            return "0";
        }
    }

    //! Get a list of ArchivedProject objects from the database
    /*!
      Retrieves a list of ArchivedProject objects from the database. All arguments for this function default to null.
      if null is passed for any argument then it is ignored. If all arguments are null then every ArchivedProject on
      the system will be returned.
      @param int $id is the id of the requested ArchivedProject
      @param int $orgId is the id of the Organisation the requested ArchivedProject belongs to
      @param string $title is the title of the requested ArchivedProject
      @param string $description is the description of the requested ArchivedProject provided by the creator
      @param string $impact is the impact of the requested ArchivedProject
      @param deadline $id is the deadline of the requested ArchivedProject in the format "YYYY-MM-DD HH:MM:SS"
      @param string $id is the reference page of the requested ArchivedProject
      @param int $wordCount is the word count of the requested ArchivedProject
      @param string $created is the date and time requested ArchivedProject was created on in the format
      "YYYY-MM-DD HH:MM:SS"
      @param string $archivedDate is the date and time the requested ArchivedProject was archived on in the format
      "YYYY-MM-DD HH:MM:SS"
      @param int $userIdArchived is the id of the User that archived the requested ArchivedProject
      @param string $lCode is the language code of the source Locale for the requested ArchivedProject (<b>NOTE</b>:
      This will get converted to a language id on the database).
      @param string $cCode is the country code of the source Locale for the requested ArchivedProject (<b>NOTE</b>:
      This will get converted to a country id on the database).
      @param bit $imageUploaded represents whether a project image has been uploaded or not for the requested ArchivedProject
      @param bit $imageApproved represents whether an uploaded project image has been approved by admins or not for the requested ArchivedProject
      @return Returns a list of ArchivedProject objects filtered by the input parameters or null
    */
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
        $userIdArchived = null,
        $lCode = null,
        $cCode = null,
        $imageUploaded = null,
        $imageApproved = null
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
            Lib\PDOWrapper::cleanseNull($userIdArchived).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($lCode).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($cCode).",".
            Lib\PDOWrapper::cleanseNull($imageUploaded).",".
            Lib\PDOWrapper::cleanseNull($imageApproved);
        $result = Lib\PDOWrapper::call("getArchivedProject", $args);
        if ($result) {
            foreach ($result as $row) {
                $projects[] = Common\Lib\ModelFactory::buildModel(
                    "ArchivedProject",
                    $row
                );
            }
        }
        if (count($projects) == 0) {
            $projects = null;
        }
        return $projects;
    }

    //! Get the Task objects associated with a specific Project
    /*!
      Retrieves a list Task objects from the database. The returned list only contains Tasks that are associated with
      the specified Project id (i.e. the Task's Project id matches the input parameter).
      @param int $projectId is the id of the Project whose Tasks are being requested
      @return Returns a list of Task objects or null.
    */
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

    //! Get the Tag objects associated with a specific Project
    /*!
      Retrieves a list Tag objects from the database. The returned list only contains Tags that are associated with
      the specified Project id (i.e. there is a tuple in ProjectTags table).
      @param int $projectId is the id of the Project whose Tags are being requested
      @return Returns a list of Tag objects or null.
    */
    public static function getTags($project_id)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($project_id);
        $result = Lib\PDOWrapper::call("getProjectTags", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Tag", $row);
            }
        }
        return $ret;
    }

    //! Remove a Tag from a Project
    /*!
      Remove a Tag from a Project. This means the User's subscribed to this Tag will no longer receive a score bonus
      during User Task score calculation.
      @param int $projectId is the id of the Project from which the Tag is being removed.
      @param int $tagId is the id of the Tag being removed.
      @return Returns '1' if the Tag was successfully remove, '0' otherwise.
    */
    public static function removeProjectTag($projectId, $tagId)
    {
        $args = Lib\PDOWrapper::cleanseNull($projectId).",".
            Lib\PDOWrapper::cleanseNull($tagId);

        $result = Lib\PDOWrapper::call("removeProjectTag", $args);
        if ($result) {
            return $result[0]['result'];
        } else {
            return '0';
        }
    }

    //! Add a list of Tags to a Project
    /*!
      Adds a list of Tags to a Project.
      @param int $projectId is the id of a Project.
      @param int $projectTags is a list of Tag objects.
      @return No return.
    */
    public static function addProjectTags($projectId, $projectTags)
    {
        foreach ($projectTags as $tag) {
            self::addProjectTag($projectId, $tag->getId());
        }
    }

    //! Add a Tag to a Project
    /*!
      Adds a Tag to a Project.
      @param int $projectId is the id of a Project.
      @param int $tagId is the id of a Tag.
      @return Returns '1' if the Tag was added successfully, '0' otherwise
    */
    public static function addProjectTag($projectId, $tagId)
    {
        $args = Lib\PDOWrapper::cleanseNull($projectId).",".
            Lib\PDOWrapper::cleanseNull($tagId);
        $result = Lib\PDOWrapper::call("addProjectTag", $args);
        if ($result) {
            return $result[0]['result'];
        } else {
            return '0';
        }
    }

    //! Get the file info for a Project
    /*!
      Retrieves data on the ProjectFile from the database. The projectId parameter is required, the others are
      optional. If any argument is passed as null then it will be ignored.
      @param int $projectId the id of a Project
      @param int $userId is the id of the User that uploaded the ProjectFile
      @param string $filename is the name of the ProjectFile
      @param string $token is an identifier for the ProjectFile
      @param string $mime is the mime type of the ProjectFile
      @return Returns a ProjectFile object or null
    */
    public static function getProjectFileInfo(
        $projectId,
        $userId = null,
        $filename = null,
        $token = null,
        $mime = null
    ) {
        $args = Lib\PDOWrapper::cleanseNull($projectId).",".
            Lib\PDOWrapper::cleanseNull($userId).",".
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

    //! Download the ProjectFile
    /*!
      This function returns the contents of the ProjectFile in the body of the HTTP Response.
      @param int $projectId is the id of a Project
      @return Returns the project file content.
    */
    public static function getProjectFile($projectId)
    {
        $projectFileInfo = self::getProjectFileInfo($projectId, null, null, null, null);
        $filename = $projectFileInfo->getFilename();
        $source = Common\Lib\Settings::get('files.upload_path')."proj-$projectId/$filename";

        if (file_exists($source)) {
            return file_get_contents($source);
        }
    }

    //! Records a ProjectFile upload
    /*!
      Used to keep track of Project files. Stores information about a project file upload so it can be retrieved later.
      @param int $projectId is the id of a Project
      @param string $filename is the name of the file being uploaded
      @param int $userId is the id of the user uploading the file
      @param string $mime is the mime type of the file being uploaded
      @return Returns the ProjectFile info that was saved or null on failure.
    */
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

    //! Get an ArchivedTask from the database
    /*!
      Get a list of ArchivedTask objects from the database. It will only return ArchivedTasks that are a part of the
      specified Project/ArchivedProject.
      @param int $projectId is the id of a Project or ArchivedProject
      @return Returns a list of ArchivedTask objects or null
    */
    public static function getArchivedTask($projectId)
    {
        $args = "null, ".Lib\PDOWrapper::cleanseNull($projectId).",".
            "null, null, null, null, null, null, null, null, null, null, null, null";
        $ret = null;
        $result = Lib\PDOWrapper::call("getArchivedTask", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("ArchivedTask", $row);
            }
        }
        return $ret;
    }

    //! Remove all Project Tags
    /*!
      Remove all Tags from a Project.
      @param int $projectId is the id of a Project
      @return Returns '1' if all Project Tags were removed successfully, '0' if there were no Tags to remove or it
      failed
    */
    public static function deleteProjectTags($projectId)
    {
        $args = Lib\PDOWrapper::cleanseNull($projectId);
        $result = Lib\PDOWrapper::call("deleteProjectTags", $args);
        if ($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }

    //! Delete a Project
    /*!
      Permanently delete a Project. This will <b>not</b> move the Project to the ArchivedProjects table.
      @param int $projectId is the id of the Project being deleted
      @return Returns '1' if the Project was deleted successfully, '0' otherwise
    */
    public static function delete($projectId)
    {
        $args = Lib\PDOWrapper::cleanseNull($projectId);
        $result = Lib\PDOWrapper::call("deleteProject", $args);
        if ($result) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }

    //! Update word counts in Projects and Tasks tables
    /*!
      This function updates the word count of a given Project and Tasks. Before updating it checks
      1) whether the project has no tasks, if so update the project's word count to new word count
      2) whether the project has segmentation or desegmentation tasks, if so return 2 and do not perform any updates;
      3) whether the word-count of all the tasks of the project are unique, if not return 2
      4) whether the word-count of all the tasks of the project are unique, if so update the word counts
         of tasks and the project and return 1
      The status 2 indicates that either the project has (de)segmentation tasks associated with it
      or tasks have different word-counts
      therefore automated update cannot be performed.
      @param int $projectId is the id of the Project.
      @param int $newWordCount is the new word count that needs to be set
      @return Returns '1' if the word counts were successfully updated,
      '2' if project has segmentation tasks or individual tasks have different
       word counts, '0' otherwise (i.e. if an error occurs during the update)
    */
    public static function updateProjectWordCount($projectId, $newWordCount)
    {
        $args = Lib\PDOWrapper::cleanseNull($projectId).",".
            Lib\PDOWrapper::cleanseNull($newWordCount);
        $result = Lib\PDOWrapper::call("updateProjectWordCount", $args);
        if ($result) {
            return $result[0]['result'];
        } else {
            return "0";
        }
    }
    /*!
     This function updates the approval status of a project image and triggers and email with the
     updated status. Status 1 means approved where as status 0 means dissaproved.
     */
    public static function setImageApprovalStatus($projectId, $imageStatus)
    {
        $status = (int) $imageStatus;
        $project = self::getProject((int)$projectId);
        $project->setImageApproved($status);
        $result = self::save($project);
        if ($result)
        {
            if ($status)
            {
                Lib\Notify::sendProjectImageApprovedEmail($projectId);
            } else
            {
                Lib\Notify::sendProjectImageDisapprovedEmail($projectId);
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getPhysicalProjectFilePath($project_id, $filename) {
        $path = file_get_contents(Common\Lib\Settings::get('files.upload_path') . "proj-$project_id/$filename");
        if ($path === false) $path = 'BADPATH';
        return Common\Lib\Settings::get('files.upload_path') . $path;
    }

    public static function savePhysicalProjectFile($project_id, $filename, $file) {
        $dir_pointer = "files/proj-$project_id";
        $physical_pointer = "$dir_pointer/$filename";

        mkdir(Common\Lib\Settings::get('files.upload_path') . $dir_pointer, 0755);

        $ret = file_put_contents(Common\Lib\Settings::get('files.upload_path') . $physical_pointer, $file);
        if ($ret === false) $physical_pointer = false;

        return $physical_pointer;
    }

    public static function getPhysicalTaskFilePath($project_id, $task_id, $version, $filename) {
        $path = file_get_contents(Common\Lib\Settings::get('files.upload_path') . "proj-$project_id/task-$task_id/v-$version/$filename");
        if ($path === false) $path = 'BADPATH';
        return Common\Lib\Settings::get('files.upload_path') . $path;
    }

    public static function savePhysicalTaskFile($project_id, $task_id, $version, $filename, $file) {
        $dir_pointer = "files/proj-$project_id/task-$task_id/v-$version";
        $physical_pointer = "$dir_pointer/$filename";
        if (file_exists(Common\Lib\Settings::get('files.upload_path') . $physical_pointer)) {
            // I think this should never happen
            $revision = 0;
            while (true) {
                $dir_pointer = "files/proj-$project_id/task-$task_id/v-$version/r-$revision";
                $physical_pointer = "$dir_pointer/$filename";
                if (!file_exists(Common\Lib\Settings::get('files.upload_path') . $physical_pointer)) break;
                $revision++;
            }
        }

        mkdir(Common\Lib\Settings::get('files.upload_path') . $dir_pointer, 0755, true);

        $ret = file_put_contents(Common\Lib\Settings::get('files.upload_path') . $physical_pointer, $file);
        if ($ret === false) $physical_pointer = false;

        return $physical_pointer;
    }
}
