<?php

class UIWorkflowBuilder extends WorkflowBuilder
{
    protected function getProjectTasks($projectId)
    {
        $client = new APIClient();
        $projectTasks = null;
        $request = APIClient::API_VERSION."/projects/$projectId/tasks";
        $response = $client->call($request);
        if ($response) {
            $projectTasks = array();
            foreach ($response as $row) {
                $projectTasks[] = $client->cast("Task", $row);
            }
        }

        return $projectTasks;
    }

    protected function getTaskPreReqs($taskId)
    {
        $client = new APIClient();
        $taskPreReqs = null;

        $request = APIClient::API_VERSION."/tasks/$taskId/prerequisites";
        $response = $client->call($request);
        if ($response) {
            $taskPreReqs = $response;
        }

        return $taskPreReqs;
    }
}

