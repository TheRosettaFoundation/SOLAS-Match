<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;

require_once __DIR__."/../DataAccessObjects/ProjectDao.class.php";
require_once __DIR__."/../DataAccessObjects/TaskDao.class.php";
require_once __DIR__."/../../Common/protobufs/models/Project.php";
require_once __DIR__."/../../Common/protobufs/models/Task.php";

class IO
{
    public static function init()
    {
        $app = \Slim\Slim::getInstance();
    
        $app->group('/v0', function () use ($app) {
            $app->group('/io', function () use ($app) {
                /* Routes starting with /v0/io/download */
                $app->group('/download', function () use ($app) {
                    $app->get(
                        '/project/:projectId(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isLoggedIn',
                        '\SolasMatch\API\V0\IO::downloadProjectFile'
                    );
                
                    $app->get(
                        '/task/:taskId(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isLoggedIn',
                        '\SolasMatch\API\V0\IO::downloadTaskFile'
                    );
                });
                /* Routes starting with /v0/io/upload */
                $app->group('/upload', function () use ($app) {
                    $app->put(
                        '/task/:taskId/:userId(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isLoggedIn',
                        '\SolasMatch\API\V0\IO::saveTaskFile'
                    );
                    
                    $app->put(
                        '/taskOutput/:taskId/:userId(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isLoggedIn',
                        '\SolasMatch\API\V0\IO::saveOutputFile'
                    );
                });
            });
        });
    }
    
    public static function downloadProjectFile($projectId, $format = ".json")
    {
        if (!is_numeric($projectId) && strstr($projectId, '.')) {
            $projectId = explode('.', $projectId);
            $format = '.'.$projectId[1];
            $projectId = $projectId[0];
        }
        
        $fileInfo = DAO\ProjectDao::getProjectFileInfo($projectId);
        if (!is_null($fileInfo)) {
            $fileName = $fileInfo->getFilename();
            $mime = $fileInfo->getMime();
            $absoluteFilePath = Common\Lib\Settings::get("files.upload_path")."proj-$projectId/$fileName";
            API\Dispatcher::sendResponse(null, self::setDownloadHeaders($absoluteFilePath, $mime), null, $format);
        } else {
            API\Dispatcher::sendResponse(null, null, Common\Enums\HttpStatusEnum::NOT_FOUND);
        }
        
    }
    
    public static function downloadTaskFile($taskId, $format = ".json")
    {
        if (!is_numeric($taskId) && strstr($taskId, '.')) {
            $taskId = explode('.', $taskId);
            $format = '.'.$taskId[1];
            $taskId = $taskId[0];
        }
        $helper = new Common\Lib\APIHelper(".json");
        
        $version = API\Dispatcher::clenseArgs('version', Common\Enums\HttpMethodEnum::GET, 0);
        $convert = API\Dispatcher::clenseArgs('convertToXliff', Common\Enums\HttpMethodEnum::GET, false);
        $fileName = DAO\TaskDao::getFilename($taskId, $version);
        $task = DAO\TaskDao::getTask($taskId);
        $projectId = $task->getProjectId();
        $absoluteFilePath = Common\Lib\Settings::get("files.upload_path").
                            "proj-$projectId/task-$taskId/v-$version/$fileName";
        $mime = $helper->getCanonicalMime($fileName);
        if (file_exists($absoluteFilePath)) {
            API\Dispatcher::sendResponse(null, self::setDownloadHeaders($absoluteFilePath, $mime), null, $format);
        } else {
            API\Dispatcher::sendResponse(null, null, Common\Enums\HttpStatusEnum::NOT_FOUND);
        }
    }
    
    private static function setDownloadHeaders($absoluteFilePath, $mime)
    {
        $fsize = filesize($absoluteFilePath);
        $path_parts = pathinfo($absoluteFilePath);
        $headerArray = array();
        $headerArray['Content-type'] = $mime;
        $headerArray['Content-Disposition'] = "attachment; filename='".$path_parts["basename"]."'";
        $headerArray['Content-length'] = $fsize;
        $headerArray['X-Frame-Options'] = "ALLOWALL";
        $headerArray['Pragma'] = "public";
        $headerArray['Cache-control'] = "private"; //See http://goo.gl/3fdIVm
        $headerArray['X-Sendfile'] = realpath($absoluteFilePath);
            
        return $headerArray;
    }
    
    public static function saveTaskFile($taskId, $userId, $format = ".json")
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        $task = DAO\TaskDao::getTask($taskId);
        $version = API\Dispatcher::clenseArgs('version', Common\Enums\HttpMethodEnum::GET, null);
        $convert = API\Dispatcher::clenseArgs('convertFromXliff', Common\Enums\HttpMethodEnum::GET, false);
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $projectFile = DAO\ProjectDao::getProjectFileInfo($task->getProjectId(), null, null, null, null);
        $filename = $projectFile->getFilename();
        try {
            self::uploadFile($task, $convert, $data, $version, $userId, $filename);
        } catch (Common\Exceptions\SolasMatchException $e) {
            API\Dispatcher::sendResponse(null, $e->getMessage(), $e->getCode());
            return;
        }
        API\Dispatcher::sendResponse(null, null, Common\Enums\HttpStatusEnum::CREATED);
    }
    
    public static function saveOutputFile($taskId, $userId, $format = ".json")
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        $task = DAO\TaskDao::getTask($taskId);
        $projectFile = DAO\ProjectDao::getProjectFileInfo($task->getProjectId(), null, null, null, null);
        $filename = $projectFile->getFilename();
        $convert = API\Dispatcher::clenseArgs('convertFromXliff', Common\Enums\HttpMethodEnum::GET, false);
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        self::uploadOutputFile($task, $convert, $data, $userId, $filename);
    }
    
    //! Upload a Task file
    /*!
     Used to store Task file upload details and save the file to the filesystem. If convert is true then the file will
    be converted to XLIFF before being saved.
    @param Task $task is a Task object
    @param bool $convert determines if the file should be converted to XLIFF
    @param String $file is the contents of the file (passed as reference)
    @param int $version is the version of the file being uploaded
    @param int $userId is the id of the User uploading the file
    @param String $filename is the name of the uploaded file
    @return No return
    */
    private static function uploadFile($task, $convert, &$file, $version, $userId, $filename)
    {
        $success = null;
        if ($convert) {
            $success = self::saveTaskFileToFs(
                $task,
                $userId,
                Lib\FormatConverter::convertFromXliff($file),
                $filename,
                $version
             );
        } else {
            $success = self::saveTaskFileToFs($task, $userId, $file, $filename, $version);
        }
        if (!$success) {
            throw new Common\Exceptions\SolasMatchException(
            "Failed to write file data.",
            Common\Enums\HttpStatusEnum::INTERNAL_SERVER_ERROR
            );
        }
    }
    
    //! Upload a new version of a Task file
    /*!
     This uploads a new version of a Task file. It also copies the uploaded file to version 0 of all Tasks that are
    dependant on this Task.
    @param Task $task is a Task object
    @param bool $convert determines if the file should be converted to XLIFF before being saved
    @param String $file is the contents of the uploaded file (passed as reference)
    @param int $userId is the id of the User uploading the file
    @param String filename is the name of the file
    @return No Return
    */
    private static function uploadOutputFile($task, $convert, &$file, $userId, $filename)
    {
        self::uploadFile($task, $convert, $file, null, $userId, $filename);
        $graphBuilder = new Lib\APIWorkflowBuilder();
        $graph = $graphBuilder->buildProjectGraph($task->getProjectId());
        if ($graph) {
            $index = $graphBuilder->find($task->getId(), $graph);
            $taskNode = $graph->getAllNodes($index);
            foreach ($taskNode->getNextList() as $nextTaskId) {
                $result = TaskDao::getTasks($nextTaskId);
                $nextTask = $result[0];
                self::uploadFile($nextTask, $convert, $file, 0, $userId, $filename);
            }
        }
    }
    
    private static function saveTaskFileToFs($task, $userId, $file, $filename, $version = null)
    {
        $taskId = $task->getId();
        $projId = $task->getProjectId();
        $taskFileMime = self::detectMimeType($file, $filename);
        $projectFileInfo = DAO\ProjectDao::getProjectFileInfo($projId);
        $projectFileMime = $projectFileInfo->getMime();
        
    
        if ($taskFileMime != $projectFileMime) {
            API\Dispatcher::sendResponse(null, null, Common\Enums\HttpStatusEnum::BAD_REQUEST);
        }
         
        if (is_null($version)) {
            $version = DAO\TaskDao::recordFileUpload($taskId, $filename, $taskFileMime, $userId);
        } else {
            $version = DAO\TaskDao::recordFileUpload($taskId, $filename, $taskFileMime, $userId, $version);
        }
        
        $uploadFolder = Common\Lib\Settings::get("files.upload_path")."proj-$projId/task-$taskId/v-$version";
        $folderExists = is_dir($uploadFolder);
        if (!$folderExists) {
            mkdir($uploadFolder, 0755, true);
            //Check if folder exists now as it should after calling mkdir()
            $folderExists = is_dir($uploadFolder);
            
            if (!$folderExists) {
                throw new \Exception('Could not create the folder for the file upload. Check permissions.');
            }
        }
        $destinationPath = $uploadFolder."/$filename";
        $ret = file_put_contents($destinationPath, $file) ? 1 : 0;
        Lib\Notify::sendTaskUploadNotifications($taskId, $version);
        return $ret;
    }
    
    private static function detectMimeType($file, $filename)
    {
        $result = null;
    
        $mimeMap = array(
                "xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                ,"xltx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.template"
                ,"potx" => "application/vnd.openxmlformats-officedocument.presentationml.template"
                ,"ppsx" => "application/vnd.openxmlformats-officedocument.presentationml.slideshow"
                ,"pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation"
                ,"sldx" => "application/vnd.openxmlformats-officedocument.presentationml.slide"
                ,"docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                ,"dotx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.template"
                ,"xlam" => "application/vnd.ms-excel.addin.macroEnabled.12"
                ,"xlsb" => "application/vnd.ms-excel.sheet.binary.macroEnabled.12"
                ,"xlf"  => "application/xliff+xml"
        );
    
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($file);
    
        $extension = explode(".", $filename);
        $extension = $extension[count($extension)-1];
    
        if (($mime == "application/zip" || ($extension == "xlf")) && array_key_exists($extension, $mimeMap)) {
            $result = $mimeMap[$extension];
        } else {
            $result = $mime;
        }
        return $result;
    }
}

IO::init();
