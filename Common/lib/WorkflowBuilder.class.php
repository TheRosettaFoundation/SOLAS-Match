<?php

include_once "Common/models/WorkflowGraph.php";
include_once "Common/models/WorkflowNode.php";

abstract class WorkflowBuilder
{
    public function buildTree($project_id)
    {
        $tree = new WorkflowGraph();
        $client = new APIClient();
        $projectTasks = $this->getProjectTasks($project_id);

        if ($projectTasks) {
            //pick a root
            $potentialRoots = array();
            $taskPreReqIds = array();
            foreach ($projectTasks as $task) {
                $taskPreReqs = $this->getTaskPreReqs($task->getId());
                if ($taskPreReqs && count($taskPreReqs) > 0) {
                    $taskPreReqIds[$task->getId()] = array();
                    foreach ($taskPreReqs as $preReqTask) {
                        $taskPreReqIds[$task->getId()][] = $preReqTask->getId();
                    }
                } else {
                    $potentialRoots[] = $task;
                }
            }

            if (count($potentialRoots) == 0) {
                //A deadlock has occured
                //Report an error
            } elseif (count($potentialRoots) == 1) {
                $tree->setRoot($potentialRoots[0]);

                //remove element from project tasks
                $index = array_search($potentialRoots[0], $projectTasks);
                unset($projectTasks[$index]);
            } else {
                //multiple roots
                //create dummy root to point to real roots
            }
        }

        return $tree;
    }

    protected abstract function getProjectTasks($projectId);
    protected abstract function getTaskPreReqs($taskId);
}
