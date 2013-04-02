<?php

require_once __DIR__."/../../Common/lib/WorkflowBuilder.class.php";
require_once __DIR__."/../../Common/lib/APIHelper.class.php";

class UIWorkflowBuilder extends WorkflowBuilder
{
    protected function getProjectTasks($projectId)
    {
        $projectDao = new ProjectDao();
        $projectTasks = $projectDao->getProjectTasks($projectId);
        return $projectTasks;
    }

    protected function getTaskPreReqs($taskId)
    {
        $taskDao = new TaskDao();
        $taskPreReqs = $taskDao->getTaskPreReqs($taskId);
        return $taskPreReqs;
    }
}

