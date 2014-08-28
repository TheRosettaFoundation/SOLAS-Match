<?php

namespace SolasMatch\API\Lib;

use \SolasMatch\API\DAO as DAO;
use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/Enums/TaskStatusEnum.class.php";
require_once __DIR__."/../../Common/Enums/TaskTypeEnum.class.php";
require_once __DIR__."/Notify.class.php";

class Upload
{
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
                    foreach ($currentTaskNode->getPrevious() as $nodeId) {
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
                ."proj-$projectId/task-$id/v-0/{$projectFileInfo->getFilename()}",
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
