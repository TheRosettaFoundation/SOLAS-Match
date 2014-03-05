<?php

namespace SolasMatch\API\Lib;

use \SolasMatch\Common\Lib\WorkflowBuilder;

require_once __DIR__."/../../Common/lib/WorkflowBuilder.class.php";

class APIWorkflowBuilder extends WorkflowBuilder
{
    protected function getProjectTasks($projectId)
    {
        $dao = new \SolasMatch\API\DAO\TaskDao();
        return $dao->getTask(null, $projectId);
    }

    protected function getTaskPreReqs($taskId)
    {
        $dao = new \SolasMatch\API\DAO\TaskDao();
        return $dao->getTaskPreReqs($taskId);
    }
}
