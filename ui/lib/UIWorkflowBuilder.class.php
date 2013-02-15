<?php

require_once __DIR__."/../../Common/lib/WorkflowBuilder.class.php";

class UIWorkflowBuilder extends WorkflowBuilder
{
    protected function getProjectTasks($projectId)
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");

        $request = "$siteApi/v0/projects/$projectId/tasks";
        $response = $client->call($request);
        $projectTasks = $client->cast(array("Task"), $response);

        return $projectTasks;
    }

    protected function getTaskPreReqs($taskId)
    {
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $taskPreReqs = null;

        $request = "$siteApi/v0/tasks/$taskId/prerequisites";
        $response = $client->call($request);
        if ($response) {
            $taskPreReqs = $response;
        }

        return $taskPreReqs;
    }
}

