<?php

class APIWorkflowBuilder extends WorkflowBuilder
{
    protected function getProjectTasks($projectId)
    {
        $dao = new TaskDao();
        return $dao->getTask(array('project_id' => $projectId));
    }

    protected function getTaskPreReqs($taskId)
    {
        $dao = new TaskDao();
        return $dao->getTaskPreReqs($taskId);
    }
}
