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
            /* Routes starting with /v0/io */
            $app->group('/io', function () use ($app) {
                $app->get(
                    '/download/project/:projectId(:format)/',
                    '\SolasMatch\API\Lib\Middleware::isLoggedIn',
                    '\SolasMatch\API\V0\IO::downloadProjectFile'
                );
                
                $app->get(
                    '/download/task/:taskId(:format)/',
                    '\SolasMatch\API\Lib\Middleware::isLoggedIn',
                    '\SolasMatch\API\V0\IO::downloadTaskFile'
                );
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
}

IO::init();
