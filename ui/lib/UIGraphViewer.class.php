<?php

require_once __DIR__."/../../Common/lib/GraphViewer.class.php";
require_once __DIR__."/UIWorkflowBuilder.class.php";

class UIGraphViewer extends GraphViewer
{
    private $taskDao;
    private $projectDao;

    public function UIGraphViewer($graph)
    {
        parent::__construct($graph);
        $this->graphBuilder = new UIWorkflowBuilder();
        $this->taskDao = new TaskDao();
        $this->projectDao = new ProjectDao();
    }

    protected function getTask($id)
    {
        $task = $this->taskDao->getTask(array('id' => $id));
        return $task;
    }

    protected function getProject($id)
    {
        $project = $this->projectDao->getProject(array('id' => $id));
        return $project;
    }

    protected function getTaskTargetLanguage($task)
    {
        return TemplateHelper::getTaskTargetLanguage($task);
    }
}
