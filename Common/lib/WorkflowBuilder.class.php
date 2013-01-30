<?php

include_once "Common/models/WorkflowGraph.php";
include_once "Common/models/WorkflowNode.php";

abstract class WorkflowBuilder
{
    public function buildGraph($project_id)
    {
        $graph = null;
        $projectTasks = $this->getProjectTasks($project_id);

        if ($projectTasks) {
            //find roots
            $graph = new WorkflowGraph();
            $taskPreReqIds = array();
            foreach ($projectTasks as $task) {
                $taskPreReqIds[$task->getId()] = $this->getTaskPreReqs($task->getId());
                if (!$taskPreReqIds[$task->getId()] || count($taskPreReqIds[$task->getId()]) < 1) {
                    $node = new WorkflowNode();
                    $node->setTaskId($task->getId());
                    $node->setTask($task);
                    $graph->addRootNode($node);

                    //Remove from list of tasks
                    $index = array_search($task, $projectTasks);
                    unset($projectTasks[$index]);
                }
            }

            if ($graph->hasRootNode()) {
                $previousLayer = $graph->getRootNodeList();
                $previousLayerIds = array();    //array to hold previous layer's ids
                $currentLayer = array();
                
                while (count($projectTasks) > 0 && count($previousLayer) > 0) {
                    foreach($previousLayer as $node) {
                        $previousLayerIds[] = $node->getTaskId();
                    }

                    foreach ($projectTasks as $task) {
                        $satisfiedPreReqs = array();
                        foreach ($taskPreReqIds[$task->getId()] as $preReqId) {
                            if (in_array($preReqId, $previousLayerIds)) {
                                $satisfiedPreReqs[] = $preReqId;

                                $index = array_search($preReqId, $taskPreReqIds[$task->getId()]);
                                unset($taskPreReqIds[$task->getId()][$index]);
                            }
                        }

                        if (count($taskPreReqIds[$task->getId()]) == 0) {
                            $node = new WorkflowNode();
                            $node->setTaskId($task->getId());
                            $node->setTask($task);

                            foreach ($previousLayer as $pNode) {
                                if (in_array($pNode->getTaskId(), $satisfiedPreReqs)) {
                                    $node->addPrevious($pNode);
                                    $pNode->addNext($node);
                                }
                            }

                            $currentLayer[] = $node;
                            $index = array_search($node->getTask(), $projectTasks);
                            unset($projectTasks[$index]);
                        }
                    }
                    $previousLayer = $currentLayer;
                    $currentLayer = array();
                }

                if (count($projectTasks) > 0) {
                    // a deadlock occured
                    $graph = null;
                }
            } else {
                //a deadlock has occured
                $graph = null;
            }
        }

        return $graph;
    }

    public function printGraph($graph)
    {
        if ($graph && $graph->hasRootNode()) {
            $currentLayer = $graph->getRootNodeList();
            $nextLayer = array();

            echo "<table>";
            while (count($currentLayer) > 0) {
                echo "<tr>";
                foreach ($currentLayer as $node) {
                    echo "<td>";
                    $this->printNode($node);
                    echo "</td>";
    
                    foreach ($node->getNextList() as $nextNode) {
                        if (!in_array($nextNode, $nextLayer)) {
                            $nextLayer[] = $nextNode;
                        }
                    }
                }
                echo "</td>";
                $currentLayer = $nextLayer;
                $nextLayer = array();
            }
            echo "</table>";
        }
    }

    public function printNode($node)
    {
        echo "<p>{$node->getTaskId()}: {$node->getTask()->getTitle()}</p>";
        echo "<p>IN: [";
        foreach ($node->getPreviousList() as $pNode) {
            echo $pNode->getTaskId().", ";
        }
        echo "]</p>";
        echo "<p>OUT: [";
        foreach ($node->getNextList() as $nNode) {
            echo $nNode->getTaskId().", ";
        }
        echo "]</p>";
    }

    protected abstract function getProjectTasks($projectId);
    protected abstract function getTaskPreReqs($taskId);
}
