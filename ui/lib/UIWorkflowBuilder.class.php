<?php

class UIWorkflowBuilder extends WordflowBuilder
{
    protected function getProjectTasks($projectId)
    {
        $client = new APIClient();
        $projectTasks = null
        $request = APIClient::API_VERSION."/projects/$project_id/tasks";
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
        $taskPreReqs[] = null;

        $request = APIClient::API_VERSION."/tasks/$taskId/prerequisites";
        $response = $client->call($request);
        if ($response) {
            $taskPreReqs = array();
            foreach ($response as $obj) {
                $task = $client->cast("Task", $obj);
                $taskPreReqs[] = $task;
            }
        }

        return $taskPreReqs;
    }
}

