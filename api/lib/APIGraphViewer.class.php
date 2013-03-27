<?php

require_once __DIR__."/../../Common/lib/GraphViewer.class.php";
require_once __DIR__."/APIWorkflowBuilder.class.php";

class APIGraphViewer extends GraphViewer
{
    private $taskDao;
    private $projectDao;
    
    public function APIGraphViewer($graph)
    {
        parent::__construct($graph);
        $this->graphBuilder = new APIWorkflowBuilder();
        $this->taskDao = new TaskDao();
        $this->projectDao = new ProjectDao();
    }
    
    protected function getTask($id)
    {
        $task = $this->taskDao->getTask(array('id' => $id));
        return $task[0];
    }
     
    protected function getProject($id)
    {
        $project = $this->projectDao->getProject(array('id' => $id));
        return $project[0];
    }

    protected function getTaskTargetLanguage($task)
    {
        $ret = "";
        $use_language_codes = Settings::get("ui.language_codes");
        
        if($use_language_codes == "y") {
            $ret = $task->getTargetLanguageCode()."-".$task->getTargetCountryCode();
        } else if($use_language_codes == "n") {
            $langObj = Languages::getLanguage(null, $task->getTargetLanguageCode(), null);
            $language = $langObj->getName();
            $region = Languages::countryNameFromCode($task->getTargetCountryCode());
            $ret = $language." - ".$region;
        } else if($use_language_codes == "h") {
            $langObj = Languages::getLanguage(null, $task->getTargetLanguageCode(), null);
            $ret = $langObj->getName()." - ".Languages::countryNameFromCode($task->getTargetCountryCode())
                        ." (".$task->getTargetLanguageCode()."-".$task->getTargetCountryCode().")";
        }
        return $ret;
    }
}
