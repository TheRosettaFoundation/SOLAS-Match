<?php

require_once __DIR__.'/../../Common/lib/PDOWrapper.class.php';
require_once __DIR__.'/../lib/APIWorkflowBuilder.class.php';
require_once __DIR__.'/../lib/Upload.class.php';

class TaskFile {

    public function taskID() // Should be package protected
    {
        return intval($this->task_id);		
    }
    
    public function timesDownloaded() // Should be package protected
    {     
        if ($r = PDOWrapper::call("taskDownloadCount", PDOWrapper::cleanse($this->taskID()))) {
            $ret = $r[0]['times_downloaded'];
            return $ret;
        } else {
            return null;
        }
    }

    /*
     * URL to download the file.
     */
    public function url() // Should be package protected
    {   // Not secure and should be improved.
        return '/task/id/' . $this->task_id . '/download-file/';
    }

    public function urlVersion($version) // Should be package protected
    {
        return $this->url() . 'v/' . intval($version) . '/';
    }
        /*
     * A private file for check if a task has been translated by checking 
     * if a file has been uploaded for it. if user_id is null it will just 
     * check on a task basis. The inclusion of the user_id allows several 
     * people to work on the job at once
     * Returns true if the file has been translated
     */
    public static function checkTaskFileVersion($task_id, $user_id = null)
    {
        $result = PDOWrapper::call("getLatestFileVersion", PDOWrapper::cleanse($task_id)
                                    .",".PDOWrapper::cleanseNull($user_id));
        return $result[0]['latest_version'] > 0;
    }
    
    public static function recordFileUpload($task, $filename, $content_type, $user_id) 
    {
        $args = "";
        $args .= PDOWrapper::cleanse($task->getId());
        $args .= ",".PDOWrapper::cleanseWrapStr($filename);
        $args .= ",".PDOWrapper::cleanseWrapStr($content_type);
        $args .= ",".PDOWrapper::cleanseNull($user_id);
        return PDOWrapper::call("recordFileUpload", $args);
    }
    
    public static function getTaskFileInfo($task, $version = 0)
    {
        return TaskFile::getTaskFileInfoById($task->getId(), $version);
    }
    
    public static function getTaskFileInfoById($taskID, $version = 0)
    {
        $ret = false;
        if ($r = PDOWrapper::call("getTaskFileMetaData", PDOWrapper::cleanse($taskID)
                                                    .",".PDOWrapper::cleanse($version)
                                                    .",null, null, null, null")) {
            $file_info = array();
            foreach ($r[0] as $key => $value) {
                if (!is_numeric($key)) {
                    $file_info[$key] = $value;
                }
            }
            $ret = $file_info;
        }
        return $ret;
    }
    
    public static function getFilename($task, $version)
    {
        if ($r = PDOWrapper::call("getTaskFileMetaData", PDOWrapper::cleanse($task->getId())
                                                    .",".PDOWrapper::cleanse($version)
                                                    .",null, null, null, null")) {
            return $r[0]['filename'];
        } else {
            return null;			
        }
    }  
        
    public static function logFileDownload($task, $version)
    {
        PDOWrapper::call("logFileDownload", PDOWrapper::cleanse($task->getId())
                                            .",".PDOWrapper::cleanse($version).",null");
    }
    
    public static function getLatestFileVersion($task)
    {
        return TaskFile::getLatestFileVersionByTaskID($task->getId());
    }

    public static function getLatestFileVersionByTaskID($task_id,$user_id=null)
    {
        $ret = false;
        if ($r = PDOWrapper::call("getLatestFileVersion", PDOWrapper::cleanse($task_id)
                                    .",".PDOWrapper::cleanseNull($user_id))) {
            if (is_numeric($r[0]['latest_version'])) {
                $ret =  intval($r[0]['latest_version']);
            }
        }
        return $ret;
    }
    
    public static function uploadFile($task,$convert,&$file,$version,$userId,$filename)
    {
       
            
        if($convert){
            Upload::apiSaveFile($task, $userId, 
            FormatConverter::convertFromXliff(Dispatcher::getDispatcher()->request()->getBody()), $filename,$version);
        }else{
            //touch this and you will die painfully sinisterly sean :)
            Upload::apiSaveFile($task, $userId, Dispatcher::getDispatcher()->request()->getBody(), $filename,$version);
        }
         Notify::sendEmailNotifications($task->getId(), NotificationTypes::UPLOAD);
    }
    
    public static function uploadOutputFile($task,$convert,&$file,$userId,$filename)
    {
        TaskFile::uploadFile($task,$convert,$file,null,$userId,$filename);
        $graphBuilder = new APIWorkflowBuilder();
        $graph = $graphBuilder->buildProjectGraph($task->getProjectId());
        if ($graph && $graph->hasRootNode()) {
            $index = $graphBuilder->find($task->getId(), $graph);
            $node = $graph->getAllNodes($index);

            $dependants = array();
            foreach ($node->getNextList() as $nextNodeId) {
                $dependants[] = $nextNodeId;
            }

            foreach ($dependants as $nextTask) {
                $taskDao = new TaskDao();
                $dTask = $taskDao->find(array("id" => $nextTask));
                TaskFile::uploadFile($dTask ,$convert,$file,0,$userId,$filename);
            }
        }
    }
}
