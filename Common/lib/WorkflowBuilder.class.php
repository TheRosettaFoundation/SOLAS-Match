<?php

include_once __DIR__."/../models/WorkflowGraph.php";
include_once __DIR__."/../models/WorkflowNode.php";

abstract class WorkflowBuilder
{
    public function buildProjectGraph($projectId)
    {
        $projectTasks = $this->getProjectTasks($projectId);
        $taskPreReqIds = array();

        if ($projectTasks) {
            $taskPreReqIds = array();
            foreach ($projectTasks as $task) {
                $taskPreReqIds[$task->getId()] = $this->getTaskPreReqs($task->getId());
            }
        }

        return $this->parseAndBuild($taskPreReqIds);
    }


    /*
     *  The graphArray parameter is an associative array of 
     *  task objects => task pre req ids
     *  For new tasks you must set its id to 0
     */
    public function parseAndBuild($graphArray)
    {
        $graph = new WorkflowGraph();
        
        foreach ($graphArray as $taskId => $preReqIds) {
            if ($preReqIds == null || count($preReqIds) < 1) {
                $node = new WorkflowNode();
                $node->setTaskId($taskId);
                $graph->addRootNode($node);

                //Remove from list of tasks
                unset($graphArray[$taskId]);
            }
        }

        if ($graph->hasRootNode()) {
            $previousLayer = $graph->getRootNodeList();
            $previousLayerIds = array();    //array to hold previous layer's ids
            $currentLayer = array();
                
            while (count($graphArray) > 0 && count($previousLayer) > 0) {
                foreach($previousLayer as $node) {
                    $previousLayerIds[] = $node->getTaskId();
                }

                foreach ($graphArray as $taskId => $preReqIds) {
                    $satisfiedPreReqs = array();
                    if (is_array($preReqIds) && count($preReqIds) > 0) {
                        foreach ($preReqIds as $preReqId) {
                            if (in_array($preReqId, $previousLayerIds)) {
                                $satisfiedPreReqs[] = $preReqId;

                                $index = array_search($preReqId, $preReqIds);
                                unset($preReqIds[$index]);
                            }
                        }

                        if (count($preReqIds) == 0) {
                            $node = new WorkflowNode();
                            $node->setTaskId($taskId);

                            foreach ($previousLayer as $pNode) {
                                if (in_array($pNode->getTaskId(), $satisfiedPreReqs)) {
                                    $node->addPrevious($pNode);
                                    $pNode->addNext($node);
                                }
                            }
    
                            $currentLayer[] = $node;
                            $index = array_search($node->getTaskId(), $graphArray);
                            unset($graphArray[$taskId]);
                        }
                    }
                }
                $previousLayer = $currentLayer;
                $currentLayer = array();
            }

            if (count($graphArray) > 0) {
                // a deadlock occured
                $graph = null;
            }
        } else {
            //a deadlock has occured
            $graph = null;
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
        if ($node->hasTask()) {
            echo "<p>{$node->getTaskId()}: {$node->getTask()->getTitle()}</p>";
        } else {
            echo "<p>{$node->getTaskId()}</p>";
        }
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
