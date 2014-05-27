<?php

namespace SolasMatch\API\Lib;

use \SolasMatch\API\DAO as DAO;
use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/Enums/TaskStatusEnum.class.php";
require_once __DIR__."/../../Common/Enums/TaskTypeEnum.class.php";
require_once __DIR__."/Notify.class.php";

class Upload
{
    public static function maxFileSizeBytes()
    {
        $display_max_size = self::maxUploadSizeFromPHPSettings();

        switch (substr($display_max_size, -1)) {
            case 'G':
                $display_max_size = $display_max_size * 1024;
                // no break
            case 'M':
                $display_max_size = $display_max_size * 1024;
                // no break
            case 'K':
                $display_max_size = $display_max_size * 1024;
                break;
        }
        return $display_max_size;
    }

    /**
     * Return an integer value of the max file size that can be uploaded to the system,
     * denominated in megabytes.
     */
    public static function maxFileSizeMB()
    {
        $bytes = self::maxFileSizeBytes();
        return round(($bytes/1024)/1024, 1);
    }

    private static function maxUploadSizeFromPHPSettings()
    {
        return ini_get('upload_max_filesize');
    }

    public static function validateFileHasBeenSuccessfullyUploaded($field_name)
    {
        if (self::isPostTooLarge()) {
            $max_file_size = ini_get('post_max_size');
            throw new \Exception(
                'Sorry, the file you tried uploading is too large. The max file size is '.
                $max_file_size.'. Please consider saving the file in multiple smaller parts for upload.'
            );
        }

        if (!self::isUploadedFile($field_name)) {
            throw new \Exception('You did not upload a file. Please try again.');
        }

        if (!self::isUploadedWithoutError($field_name)) {
            $error_message = self::fileUploadErrorMessage($_FILES[$form_file_field]['error']);
            throw new \Exception('Sorry, we were not able to upload your file. Error: ' . $error_message);
        }
    }

    /* Thanks to http://andrewcurioso.com/2010/06/detecting-file-size-overflow-in-php/ */
    private static function isPostTooLarge()
    {
        return (
            $_SERVER['REQUEST_METHOD'] == 'POST' &&
            empty($_POST) &&
            empty($_FILES) &&
            $_SERVER['CONTENT_LENGTH'] > 0
        );
    }

    private static function fileUploadErrorMessage($error_code)
    {
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    }

    public static function isUploadedFile($field_name)
    {
        return is_uploaded_file($_FILES[$field_name]['tmp_name']);
    }

    public static function isUploadedWithoutError($field_name)
    {
        return $_FILES[$field_name]['error'] == UPLOAD_ERR_OK;
    }

    public static function apiSaveFile($task, $user_id, $file, $filename, $version = null)
    {
        $taskFileMime = IO::detectMimeType($file, $filename);
        $projectFileInfo = DAO\ProjectDao::getProjectFileInfo($task->getProjectId());
        $projectFileMime = $projectFileInfo->getMime();
        
        if ($taskFileMime != $projectFileMime) {
            throw new Common\Exceptions\SolasMatchException(
                "Invalid file content.",
                Common\Enums\HttpStatusEnum::BAD_REQUEST
            );
        }
       
        if (is_null($version)) {
            $version = DAO\TaskDao::recordFileUpload($task->getId(), $filename, $taskFileMime, $user_id);
        } else {
            $version = DAO\TaskDao::recordFileUpload($task->getId(), $filename, $taskFileMime, $user_id, $version);
        }
        $upload_folder = self::absoluteFolderPathForUpload($task, $version);
        if (!self::folderPathForUploadExists($task, $version)) {
            self::createFolderForUpload($task, $version);
        }
        $destination_path = self::absoluteFilePathForUpload($task, $version, $filename);
        $ret = file_put_contents($destination_path, $file) ? 1 : 0;
        Notify::sendTaskUploadNotifications($task->getId(), $version);
        return $ret;
    }
        
    /*
     * For a named filename, save file that have been uploaded by form submission.
     * The file has been specified in a form element <input type="file" name="myfile">
     * We access that file through PHP's $_FILES array.
     */
    public static function saveSubmittedFile($form_file_field, $task, $user_id)
    {
        /*
         * Right now we're assuming that there's one file, but I think it can also be
         * an array of multiple files.
         */
        if ($_FILES[$form_file_field]['error'] == UPLOAD_ERR_FORM_SIZE) {
            throw new \Exception(
                'Sorry, the file you tried uploading is too large. Please choose a smaller file, '.
                'or break the file into sub-parts.'
            );
        }

        $file_name = $_FILES[$form_file_field]['name'];
        $file_tmp_name = $_FILES[$form_file_field]['tmp_name'];
        $version = DAO\TaskDao::recordFileUpload(
            $task->getId(),
            $file_name,
            $_FILES[$form_file_field]['type'],
            $user_id
        );
        $version = $version[0]['version'];
        $upload_folder = self::absoluteFolderPathForUpload($task, $version);

        self::saveSubmittedFileToFs($task, $file_name, $file_tmp_name, $version);

        return true;
    }

    public static function createFolderPath($task, $version = 0)
    {
        $upload_folder = self::absoluteFolderPathForUpload($task, $version);
        if (!self::folderPathForUploadExists($task, $version)) {
                self::createFolderForUpload($task, $version);
        }
    }

    /*
     * $files_file is the name of the parameter of the file we want to access
     * in the $_FILES global array.
     */
    private static function saveSubmittedFileToFs($task, $file_name, $file_tmp_name, $version)
    {
        $upload_folder = self::absoluteFolderPathForUpload($task, $version);

        if (!self::folderPathForUploadExists($task, $version)) {
            self::createFolderForUpload($task, $version);
        }

        $destination_path = self::absoluteFilePathForUpload($task, $version, $file_name);
        if (move_uploaded_file($file_tmp_name, $destination_path) == false) {
            throw new \Exception('Could not save uploaded file.');
        }
    }

    private static function folderPathForUploadExists($task, $version)
    {
        $folder = self::absoluteFolderPathForUpload($task, $version);
        return is_dir($folder);
    }

    private static function createFolderForUpload($task, $version)
    {
        $upload_folder = self::absoluteFolderPathForUpload($task, $version);
        mkdir($upload_folder, 0755, true);

        if (self::folderPathForUploadExists($task, $version)) {
            return true;
        } else {
            throw new \Exception('Could not create the folder for the file upload. Check permissions.');
        }
    }

    public static function absoluteFilePathForUpload($task, $version, $file_name)
    {
        $folder = self::absoluteFolderPathForUpload($task, $version);
        return $folder . DIRECTORY_SEPARATOR . basename($file_name);
    }

    public static function absoluteFolderPathForUpload($task, $version)
    {
        if (!is_numeric($version) || $version < 0) {
            throw new \InvalidArgumentException(
                "Cannot give an upload folder path as the version number was not specified.version = $version"
            );
        }

        $uploads_folder = Common\Lib\Settings::get('files.upload_path');
        $project_folder = 'proj-' . $task->getProjectId();
        $task_folder = 'task-' . $task->getId();
        $version_folder = 'v-' . $version;

        return $uploads_folder.$project_folder.DIRECTORY_SEPARATOR.
                $task_folder.DIRECTORY_SEPARATOR.$version_folder;
    }
    
    public static function addTaskPreReq($id, $preReqId)
    {
        $builder = new APIWorkflowBuilder();
        $currentTask =  DAO\TaskDao::getTask($id);
        $projectId = $currentTask->getProjectId();
        $taskPreReqs = $builder->calculatePreReqArray($projectId);
        $ret = null;

        if (!empty($taskPreReqs) && !in_array($preReqId, $taskPreReqs[$id])) {
            $taskPreReqs[$id][] = $preReqId;
        
            if ($graph = $builder->parseAndBuild($taskPreReqs)) {
                $index = $builder->find($id, $graph);
                $currentTaskNode = $graph->getAllNodes($index);
                $task = DAO\TaskDao::getTask($id);
                $preReqTask = DAO\TaskDao::getTask($preReqId);
                $ret = DAO\TaskDao::addTaskPreReq($id, $preReqId);

                if ($task->getTaskType() != Common\Enums\TaskTypeEnum::DESEGMENTATION) {
                    foreach ($currentTaskNode->getPreviousList() as $nodeId) {
                        $preReq = DAO\TaskDao::getTask($nodeId);
                        if ($preReq->getTaskStatus() == Common\Enums\TaskStatusEnum::COMPLETE
                                && $preReq->getTaskType() != Common\Enums\TaskTypeEnum::SEGMENTATION) {
                            Upload::copyOutputFile($id, $preReqId);
                        }
                    }
                }
            }
        }
        return $ret;
    }
    
    public static function removeTaskPreReq($id, $preReqId)
    {
        $task = DAO\TaskDao::getTask($id);
        $ret = DAO\TaskDao::removeTaskPreReq($id, $preReqId);
        $taskPreReqs = DAO\TaskDao::getTaskPreReqs($id);
        if (is_array($taskPreReqs) && count($taskPreReqs > 0)) {
            foreach ($taskPreReqs as $taskPreReq) {
                if ($taskPreReq->getTaskStatus() == Common\Enums\TaskStatusEnum::COMPLETE) {
                    Upload::copyOutputFile($id, $taskPreReq->getId());
                }
            }
        } else {
            $projectId = $task->getProjectId();
            $projectFile = DAO\ProjectDao::getProjectFile($projectId);
            $projectFileInfo = DAO\ProjectDao::getProjectFileInfo($projectId, null, null, null, null);

            file_put_contents(
                Common\Lib\Settings::get(
                    "files.upload_path"
                )
                ."proj-$projectId/task-$id/v-0/{$projectFileInfo->getFileName()}",
                $projectFile
            );
        }
        return $ret;
    }
    
    private static function copyOutputFile($id, $preReqId)
    {
        $task = DAO\TaskDao::getTask($id);
        
        $preReqTask = DAO\TaskDao::getTask($preReqId);
        
        $preReqlatestFileVersion = DAO\TaskDao::getLatestFileVersion($preReqId);
        $preReqFileName = DAO\TaskDao::getFilename($preReqId, $preReqlatestFileVersion);
        $projectId= $task->getProjectId();
        file_put_contents(
            Common\Lib\Settings::get("files.upload_path")."proj-$projectId/task-$id/v-0/$preReqFileName",
            file_get_contents(
                Common\Lib\Settings::get("files.upload_path").
                "proj-$projectId/task-$preReqId/v-$preReqlatestFileVersion/$preReqFileName"
            )
        );
    }
}
