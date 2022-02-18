<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__."/../DataAccessObjects/ProjectDao.class.php";
require_once __DIR__."/../DataAccessObjects/TaskDao.class.php";
require_once __DIR__."/../../Common/protobufs/models/Project.php";
require_once __DIR__."/../../Common/protobufs/models/Task.php";

class IO
{
    public static function init()
    {
        global $app;

        $app->delete(
            '/v0/io/projectImage/:orgId/:projectId/',
            '\SolasMatch\API\V0\IO:removeProjectImage')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateOrgAdmin');

        $app->post(
            '/v0/io/contentMime/:filename/',
            '\SolasMatch\API\V0\IO:getMimeFromFileContent')
            ->add('\SolasMatch\API\Lib\Middleware:isLoggedIn');

        $app->get(
            '/v0/io/download/projectImage/:projectId/',
            '\SolasMatch\API\V0\IO:downloadProjectImageFile')
            ->add('\SolasMatch\API\Lib\Middleware:authUserForProjectImage');

        $app->get(
            '/v0/io/download/project/:projectId/',
            '\SolasMatch\API\V0\IO:downloadProjectFile')
            ->add('\SolasMatch\API\Lib\Middleware:isLoggedIn');

        $app->get(
            '/v0/io/download/task/:taskId/',
            '\SolasMatch\API\V0\IO:downloadTaskFile');

        $app->put(
            '/v0/io/upload/project/:projectId/file/:filename/:userId/',
            '\SolasMatch\API\V0\IO:saveProjectFile')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateUserForOrgProject');

        $app->put(
            '/v0/io/upload/project/:projectId/image/:filename/:userId/',
            '\SolasMatch\API\V0\IO:saveProjectImageFile')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateUserForOrgProject');

        $app->put(
            '/v0/io/upload/task/:taskId/:userId/',
            '\SolasMatch\API\V0\IO:saveTaskFile')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateUserForOrgTask');

        $app->put(
            '/v0/io/upload/taskfromproject/:taskId/:userId/',
            '\SolasMatch\API\V0\IO:saveTaskFileFromProject')
            ->add('\SolasMatch\API\Lib\Middleware:authenticateUserForOrgTask');

        $app->put(
            '/v0/io/upload/taskOutput/:taskId/:userId/',
            '\SolasMatch\API\V0\IO:saveOutputFile')
            ->add('\SolasMatch\API\Lib\Middleware:authUserForClaimedTask');

        $app->put(
            '/v0/io/upload/sendTaskUploadNotifications/:taskId/:type/',
            '\SolasMatch\API\V0\IO:sendTaskUploadNotifications');
    }

    public static function getMimeFromFileContent(Request $request, Response $response, $args)
    {
        $filename = $args['filename'];
        $filename = urldecode($filename);
        $fileContent = API\Dispatcher::getDispatcher()->request()->getBody();

        API\Dispatcher::sendResponse(null, self::detectMimeType($fileContent, $filename), null);
    }

    public static function downloadProjectImageFile(Request $request, Response $response, $args)
    {
        $projectId = $args['projectId'];

        $imageFileList = glob(Common\Lib\Settings::get("files.upload_path")."proj-$projectId/image/image.*");
        if (isset($imageFileList[0]))
        {
            $imageFilePath=$imageFileList[0];
            $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
            $mime = finfo_file($finfo, $imageFilePath);
            finfo_close($finfo);
            API\Dispatcher::sendResponse(null, self::setDownloadHeaders($imageFilePath, $mime), null);
        } else {
            API\Dispatcher::sendResponse(null, null, Common\Enums\HttpStatusEnum::NOT_FOUND);
        }
    }

    public static function removeProjectImage(Request $request, Response $response, $args)
    {
        $orgId = $args['orgId'];
        $projectId = $args['projectId'];
        $project = DAO\ProjectDao::getProject($projectId);
        $imageFileList = glob(Common\Lib\Settings::get("files.upload_path")."proj-$projectId/image/image.*");
        if (!empty($imageFileList) && count($imageFileList) > 0) {
            $currentImageFile = $imageFileList[0];
            $currentFileName = pathinfo($currentImageFile, PATHINFO_FILENAME);
            $currentfileExt = pathinfo($currentImageFile, PATHINFO_EXTENSION);
            $currentfileDir = pathinfo($currentImageFile, PATHINFO_DIRNAME);
            $date = date('-d-m-Y-h-i-s-a', time());
            rename($currentImageFile,$currentfileDir."/".$currentFileName.$date.".".$currentfileExt);

            $project->setImageUploaded(0);
            $project->setImageApproved(0);
            DAO\ProjectDao::save($project);
            Lib\Notify::sendProjectImageRemoved($projectId);
            API\Dispatcher::sendResponse(null, null, Common\Enums\HttpStatusEnum::OK);
        }
    }

    public static function downloadProjectFile(Request $request, Response $response, $args)
    {
        $projectId = $args['projectId'];
        $fileInfo = DAO\ProjectDao::getProjectFileInfo($projectId);
        if (!is_null($fileInfo)) {
            $fileName = $fileInfo->getFilename();
            $mime = $fileInfo->getMime();
            //$absoluteFilePath = Common\Lib\Settings::get("files.upload_path")."proj-$projectId/$fileName";
            $absoluteFilePath = DAO\ProjectDao::getPhysicalProjectFilePath($projectId, $fileName);
            if (file_exists($absoluteFilePath)) {
                API\Dispatcher::sendResponse(null, self::setDownloadHeaders($absoluteFilePath, $mime), null);
            } else {
                API\Dispatcher::sendResponse(null, null, Common\Enums\HttpStatusEnum::NOT_FOUND);
            }
        } else {
            API\Dispatcher::sendResponse(null, null, Common\Enums\HttpStatusEnum::NOT_FOUND);
        }

    }

    public static function downloadTaskFile(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        $helper = new Common\Lib\APIHelper(".json");

        $version = API\Dispatcher::clenseArgs('version', Common\Enums\HttpMethodEnum::GET, 0);
        $fileName = DAO\TaskDao::getFilename($taskId, $version);
        $task = DAO\TaskDao::getTask($taskId);
        $projectId = $task->getProjectId();
        //$absoluteFilePath = Common\Lib\Settings::get("files.upload_path").
        //                    "proj-$projectId/task-$taskId/v-$version/$fileName";
        $absoluteFilePath = DAO\ProjectDao::getPhysicalTaskFilePath($projectId, $taskId, $version, $fileName);

        $mime = $helper->getCanonicalMime($fileName);
        if (file_exists($absoluteFilePath)) {
            API\Dispatcher::sendResponse(null, self::setDownloadHeaders($absoluteFilePath, $mime), null);
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
        $headerArray['Content-Disposition'] = "attachment; filename=\"".trim($path_parts["basename"],'"')."\"";
        $headerArray['Content-length'] = $fsize;
        $headerArray['X-Frame-Options'] = "ALLOWALL";
        $headerArray['Pragma'] = "no-cache";
        $headerArray['Cache-control'] = "no-cache, must-revalidate, no-transform"; // e.g. https://developers.google.com/web/fundamentals/performance/optimizing-content-efficiency/http-caching (previous comment See http://goo.gl/3fdIVm)
        $headerArray['X-Sendfile'] = realpath($absoluteFilePath);

        return $headerArray;
    }

    public static function saveTaskFile(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        $userId = $args['userId'];
        $task = DAO\TaskDao::getTask($taskId);
        $version = API\Dispatcher::clenseArgs('version', Common\Enums\HttpMethodEnum::GET, null);
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $projectFile = DAO\ProjectDao::getProjectFileInfo($task->getProjectId(), null, null, null, null);
        $filename = $projectFile->getFilename();
        try {
            self::uploadFile($task, $data, $version, $userId, $filename);
        } catch (Common\Exceptions\SolasMatchException $e) {
            API\Dispatcher::sendResponse(null, $e->getMessage(), $e->getCode());
            return;
        }
        API\Dispatcher::sendResponse(null, null, Common\Enums\HttpStatusEnum::CREATED);
    }

    public static function saveTaskFileFromProject(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        $userId = $args['userId'];
        $task = DAO\TaskDao::getTask($taskId);
        $version = API\Dispatcher::clenseArgs('version', Common\Enums\HttpMethodEnum::GET, null);
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $projectFile = DAO\ProjectDao::getProjectFileInfo($task->getProjectId(), null, null, null, null);
        $filename = $projectFile->getFilename();
        try {
            self::uploadFile($task, $data, $version, $userId, $filename, true);
        } catch (Common\Exceptions\SolasMatchException $e) {
            API\Dispatcher::sendResponse(null, $e->getMessage(), $e->getCode());
            return;
        }
        API\Dispatcher::sendResponse(null, null, Common\Enums\HttpStatusEnum::CREATED);
    }

    public static function saveOutputFile(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        $userId = $args['userId'];
        $task = DAO\TaskDao::getTask($taskId);
        $projectFile = DAO\ProjectDao::getProjectFileInfo($task->getProjectId(), null, null, null, null);
        $filename = $projectFile->getFilename();
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        try {
            error_log("Before uploadOutputFile($taskId..., $userId, $filename)");
        self::uploadOutputFile($task, $data, $userId, $filename);
            error_log("After uploadOutputFile($taskId..., $userId, $filename)");
$task = DAO\TaskDao::getTask($taskId + 1);
if (!empty($task) && $task->getTaskType() == 3) {
    $ts = $task->getTaskStatus();
    error_log("After uploadOutputFile($taskId + 1 getTaskStatus(): $ts");
}
        } catch (Common\Exceptions\SolasMatchException $e) {
            error_log("Catch uploadOutputFile($taskId..., $userId, $filename)");
            API\Dispatcher::sendResponse(null, $e->getMessage(), $e->getCode());
            return;
        }
        API\Dispatcher::sendResponse(null, null, Common\Enums\HttpStatusEnum::CREATED);
    }

    public static function saveProjectFile(Request $request, Response $response, $args)
    {
        $projectId = $args['projectId'];
        $filename = $args['filename'];
        $userId = $args['userId'];
        error_log("saveProjectFile($projectId, $filename, $userId...)");
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        try {
            $token = self::saveProjectFileToFs($projectId, $data, urldecode($filename), $userId);
            error_log('CREATED');
            API\Dispatcher::sendResponse(null, $token, Common\Enums\HttpStatusEnum::CREATED);
        } catch (Exception $e) {
            error_log('Exception: ' . $e->getMessage());
            API\Dispatcher::sendResponse(null, $e->getMessage(), $e->getCode());
        }
    }

    public static function saveProjectImageFile(Request $request, Response $response, $args)
    {
        $projectId = $args['projectId'];
        $filename = $args['filename'];
        $userId = $args['userId'];
        $data = API\Dispatcher::getDispatcher()->request()->getBody();

        try {
            self::saveProjectImageFileToFs($projectId, $data, urldecode($filename), $userId);
            API\Dispatcher::sendResponse(null, null, Common\Enums\HttpStatusEnum::CREATED);
        } catch (Exception $e) {
            API\Dispatcher::sendResponse(null, $e->getMessage(), $e->getCode());
        }
    }

    //! Upload a Task file
    /*!
     Used to store Task file upload details and save the file to the filesystem.
    @param Task $task is a Task object
    @param String $file is the contents of the file (passed as reference)
    @param int $version is the version of the file being uploaded
    @param int $userId is the id of the User uploading the file
    @param String $filename is the name of the uploaded file
    @return No return
    */
    private static function uploadFile($task, &$file, $version, $userId, $filename, $from_project_physical_pointer = false)
    {
        $success = null;
        $success = self::saveTaskFileToFs($task, $userId, $file, $filename, $version, $from_project_physical_pointer);

        if (!$success) {
            throw new Common\Exceptions\SolasMatchException(
                "Failed to write file data.",
                Common\Enums\HttpStatusEnum::INTERNAL_SERVER_ERROR
            );
        }
        return $success;
    }

    //! Upload a new version of a Task file
    /*!
     This uploads a new version of a Task file. It also copies the uploaded file to version 0 of all Tasks that are
    dependant on this Task.
    @param Task $task is a Task object
    @param String $file is the contents of the uploaded file (passed as reference)
    @param int $userId is the id of the User uploading the file
    @param String filename is the name of the file
    @return No Return
    */
    private static function uploadOutputFile($task, &$file, $userId, $filename)
    {
        $physical_pointer = self::uploadFile($task, $file, null, $userId, $filename);
        $graphBuilder = new Lib\APIWorkflowBuilder();
        $graph = $graphBuilder->buildProjectGraph($task->getProjectId());
        if ($graph) {
            $index = $graphBuilder->find($task->getId(), $graph);
            $taskNode = $graph->getAllNodes($index);
            foreach ($taskNode->getNext() as $nextTaskId) {
                $result = DAO\TaskDao::getTasks($nextTaskId);
                $nextTask = $result[0];
                if ($physical_pointer) {
                    self::uploadFile($nextTask, $physical_pointer, 0, $userId, $filename, true);
                } else {
                    self::uploadFile($nextTask, $file, 0, $userId, $filename);
                }
            }
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
    private static function saveProjectFileToFs($projectId, $file, $filename, $userId)
    {
        $destination = Common\Lib\Settings::get("files.upload_path")."proj-$projectId/";
        if (!file_exists($destination)) {
            mkdir($destination, 0755);
        }
        error_log("destination: $destination");

        $mime = self::detectMimeType($file, $filename);
        error_log("detectMimeType: $mime");

        $apiHelper = new Common\Lib\APIHelper('.json');
        $canonicalMime = $apiHelper->getCanonicalMime($filename);
        error_log("getCanonicalMime: $canonicalMime");

        if (!is_null($canonicalMime) && $mime != $canonicalMime) {
            $message = "The content type ($mime) of the file you are trying to upload does not";
            $message .= " match the content type ($canonicalMime) expected from its extension.";
            error_log($message);
            throw new Common\Exceptions\SolasMatchException($message, Common\Enums\HttpStatusEnum::BAD_REQUEST);
        }
            $token = DAO\ProjectDao::recordProjectFileInfo($projectId, $filename, $userId, $mime);
        try {
            //file_put_contents($destination.$token, $file);
            $physical_pointer = DAO\ProjectDao::savePhysicalProjectFile($projectId, $filename, $file);
            if ($physical_pointer !== false) {
                $ret = file_put_contents($destination.$token, $physical_pointer);
                if ($ret === false) $physical_pointer = false;
            }
            if ($physical_pointer === false) {
                $message = "Failed to write file data ($projectId).";
                error_log($message);
                throw new Common\Exceptions\SolasMatchException($message, Common\Enums\HttpStatusEnum::INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $e) {
            $message = "You cannot upload a project file for project ($projectId), as one already exists.";
            error_log($message);
            throw new Common\Exceptions\SolasMatchException($message, Common\Enums\HttpStatusEnum::CONFLICT);
        }

        return $token;
    }

    //! Records a ProjectImageFile upload
    /*!
     Used to keep track of Project Image files. Stores information about a project image file upload so it can be retrieved later.
     if an image already exists in the image folder, it will make a backup of the old image by renaming it
     with a timestamp.
    @param int $projectId is the id of a Project
    @param string $filename is the name of the image file being uploaded
    @param int $userId is the id of the user uploading the file
    @param string $mime is the mime type of the image file being uploaded
    @return Returns the ProjectImageFile info that was saved or null on failure.
    */
    private static function saveProjectImageFileToFs($projectId, $file, $filename, $userId)
    {
        $destination = Common\Lib\Settings::get("files.upload_path")."proj-$projectId/image";
        if (!file_exists($destination)) {
            mkdir($destination, 0755);
        }
        $mime = self::detectMimeType($file, $filename);
        $apiHelper = new Common\Lib\APIHelper('.json');
        $canonicalMime = $apiHelper->getCanonicalMime($filename);
        if (!is_null($canonicalMime) && $mime != $canonicalMime) {
            $message = "The content type ($mime) of the image file you are trying to upload does not";
            $message .= " match the content type ($canonicalMime) expected from its extension.";
            throw new Common\Exceptions\SolasMatchException($message, Common\Enums\HttpStatusEnum::BAD_REQUEST);
        }

        $project = DAO\ProjectDao::getProject($projectId);
        $project->setImageUploaded(1);
        $project->setImageApproved(1); // Automatically approve (was 0)
        $project = DAO\ProjectDao::save($project);

        try {
             $imageFileList = glob(Common\Lib\Settings::get("files.upload_path")."proj-$projectId/image/image.*");
                if (!empty($imageFileList) && count($imageFileList)>0)
                {
                    $currentImageFile = $imageFileList[0];
                    $currentfileName = pathinfo($currentImageFile, PATHINFO_FILENAME);
                    $currentfileExt = pathinfo($currentImageFile, PATHINFO_EXTENSION);
                    $currentfileDir = pathinfo($currentImageFile, PATHINFO_DIRNAME);
                    $date = date('-d-m-Y-h-i-s-a', time());
                    rename($currentImageFile,$currentfileDir."/".$currentfileName.$date.".".$currentfileExt);
                }
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            file_put_contents($destination."/image.$ext", $file);
            //Lib\Notify::sendProjectImageUploaded($projectId); // No notification
        } catch (\Exception $e) {
            $message = "You cannot upload an image file for project ($projectId), as one already exists.";
            throw new Common\Exceptions\SolasMatchException($message, Common\Enums\HttpStatusEnum::CONFLICT);
        }
    }

    private static function saveTaskFileToFs($task, $userId, $file, $filename, $version = null, $from_project_physical_pointer = false)
    {
        $taskId = $task->getId();
        $projId = $task->getProjectId();
        $projectFileInfo = DAO\ProjectDao::getProjectFileInfo($projId);
        $projectFileMime = $projectFileInfo->getMime();
        if ($from_project_physical_pointer) {
            $taskFileMime = $projectFileMime;
        } else {
            $taskFileMime = self::detectMimeType($file, $filename);
        }

        if ($taskFileMime != $projectFileMime) {
            //API\Dispatcher::sendResponse(null, null, Common\Enums\HttpStatusEnum::BAD_REQUEST);
            //throw new Common\Exceptions\SolasMatchException("Mime type does not match.", Common\Enums\HttpStatusEnum::BAD_REQUEST);
            // Previous code "API\" allowed the flow to proceed even though it gave an error, but there may be mismatches so we need to proceed uninterrupted 20180919
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
        //$ret = file_put_contents($destinationPath, $file) ? 1 : 0;
        if ($from_project_physical_pointer) {
            $physical_pointer = $file;
        } else {
            $physical_pointer = DAO\ProjectDao::savePhysicalTaskFile($projId, $taskId, $version, $filename, $file);
        }
        if ($physical_pointer) {
            $ret = file_put_contents($destinationPath, $physical_pointer) ? $physical_pointer : 0;
        } else {
            $ret = 0;
        }
        Lib\Notify::sendTaskUploadNotifications($taskId, $version);
        return $ret;
    }

    public static function sendTaskUploadNotifications(Request $request, Response $response, $args)
    {
        $taskId = $args['taskId'];
        $type = $args['type'];
        try {
            Lib\Notify::sendTaskUploadNotifications($taskId, $type);
            error_log("sendTaskUploadNotifications($taskId, $type)");
        } catch (Common\Exceptions\SolasMatchException $e) {
            API\Dispatcher::sendResponse(null, $e->getMessage(), $e->getCode());
            return;
        }
        API\Dispatcher::sendResponse(null, null, null);
    }

    private static function detectMimeType($file, $filename)
    {
        $result = null;

        $mimeMap = array(
                "xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                ,"xlsm" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
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
                ,"doc"  => "application/msword"
                ,"ppt"  => "application/vnd.ms-powerpoint"
                ,"xls"  => "application/vnd.ms-excel"
        );

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($file);

        $extension = explode(".", $filename);
        $extension = $extension[count($extension)-1];

        if (($mime == "application/octet-stream" || $mime == "application/zip" || $extension == "doc" || $extension == "xlf")
            && (array_key_exists($extension, $mimeMap))) {
            $result = $mimeMap[$extension];
        } elseif ($mime === 'text/plain' && $extension === 'json') {
            $result = 'application/json';
        } elseif ($mime === 'application/zip' && $extension === 'odt') {
            $result = 'application/vnd.oasis.opendocument.text';
        } elseif ($mime === 'text/xml' && $extension === 'xml') {
            $result = 'application/xml';
        } else {
            $result = $mime;
        }

        return $result;
    }
}

IO::init();
