<?php

namespace SolasMatch\UI\Lib;

use \SolasMatch\Common\Lib\WorkflowBuilder;
use \SolasMatch\UI\DAO as DAO;

require_once __DIR__."/../../Common/lib/WorkflowBuilder.class.php";
require_once __DIR__."/../../Common/lib/APIHelper.class.php";

class UIWorkflowBuilder extends WorkflowBuilder
{
    protected function getProjectTasks($projectId)
    {
        $projectDao = new DAO\ProjectDao();
        $projectTasks = $projectDao->getProjectTasks($projectId);
        return $projectTasks;
    }

    protected function getTaskPreReqs($taskId)
    {
        $taskDao = new DAO\TaskDao();
        $taskPreReqs = $taskDao->getTaskPreReqs($taskId);
        return $taskPreReqs;
    }
}
