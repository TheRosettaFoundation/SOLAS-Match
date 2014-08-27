<?php

namespace SolasMatch\Common\Lib;

use \SolasMatch\Common as Common;

include_once __DIR__."/../protobufs/models/WorkflowGraph.php";
include_once __DIR__."/../protobufs/models/WorkflowNode.php";

abstract class WorkflowBuilder
{
    public function buildProjectGraph($projectId)
    {
        $model = $this->parseAndBuild($this->calculatePreReqArray($projectId));
        if ($model) {
            $model->setProjectId($projectId);
        }

        return $model;
    }
    
    public function calculatePreReqArray($projectId)
    {
        $projectTasks = $this->getProjectTasks($projectId);
        $taskPreReqIds = array();
        if ($projectTasks) {
            foreach ($projectTasks as $task) {
                $taskPreReqs = $this->getTaskPreReqs($task->getId());
                $taskPreReqIds[$task->getId()] = array();
                if ($taskPreReqs) {
                    foreach ($taskPreReqs as $preReq) {
                        $taskPreReqIds[$task->getId()][] = $preReq->getId();
                    }
                }
            }
        }
        
        return $taskPreReqIds;
    }
    
    public function find($taskId, $graph)
    {
        $ret = false;
        $allNodes = $graph->getAllNodes();
        $i = 0;
        while ($i < count($allNodes) && $ret === false) {
            if ($taskId == $allNodes[$i]->getTaskId()) {
                $ret = $i;
            }
            $i++;
        }
        return $ret;
    }


    /*
     *  The graphArray parameter is an associative array of 
     *  task objects => task pre req ids
     *  For new tasks you must set its id to 0
     */
    public function parseAndBuild($graphArray)
    {
        $graph = new Common\Protobufs\Models\WorkflowGraph();
        
        foreach ($graphArray as $taskId => $preReqIds) {
            if ($preReqIds == null || count($preReqIds) < 1) {
                $node = new Common\Protobufs\Models\WorkflowNode();
                $node->setTaskId($taskId);
                $this->insertNode($node, $graph);

                //Remove from list of tasks
                unset($graphArray[$taskId]);
            }
        }

        if ($graph->hasRootNode()) {
            $previousLayer = $graph->getRootNode();    //array to hold previous layer's ids
            $currentLayer = array();
                
            while (count($graphArray) > 0 && count($previousLayer) > 0) {
                foreach ($graphArray as $taskId => $preReqIds) {
                    $satisfiedPreReqs = array();
                    if (is_array($preReqIds) && count($preReqIds) > 0) {
                        foreach ($preReqIds as $preReqId) {
                            if (in_array($preReqId, $previousLayer)) {
                                $satisfiedPreReqs[] = $preReqId;

                                $index = array_search($preReqId, $preReqIds);
                                unset($preReqIds[$index]);
                            }
                        }

                        if (count($preReqIds) == 0) {
                            $node = new Common\Protobufs\Models\WorkflowNode();
                            $node->setTaskId($taskId);

                            foreach ($previousLayer as $pId) {
                                if (in_array($pId, $satisfiedPreReqs)) {
                                    $node->appendPrevious($pId);

                                    $index = $this->find($pId, $graph);
                                    $pNode = $graph->getAllNodes($index);
                                    $pNode->appendNext($taskId);
                                    $this->updateNode($pNode, $graph);
                                }
                            }
                            $this->insertNode($node, $graph);
    
                            $currentLayer[] = $taskId;
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

    public function insertNode($node, $graph)
    {
        $ret = false;
        $index = $this->find($node->getTaskId(), $graph);
        if ($index === false) {
            if (count($node->getPrevious()) == 0) {
                $graph->appendRootNode($node->getTaskId());
            }
            $graph->appendAllNodes($node);
            $ret = true;
        }
        return $ret;
    }

    public function updateNode($node, $graph)
    {
        $ret = false;
        $index = $this->find($node->getTaskId(), $graph);
        if ($index !== false) {
            if (count($node->getPrevious()) == 0) {
                if (!in_array($node->getTaskId(), $graph->getRootNode())) {
                    $graph->appendRootNode($node->getTaskId());
                }
            } else {
                if (in_array($node->getTaskId(), $graph->getRootNode())) {
                    $roots = $graph->getRootNode();
                    $graph->clearRootNode();
                    foreach ($roots as $rootId) {
                        if ($rootId != $node->getTaskId()) {
                            $graph->appendRootNode($rootId);
                        }
                    }
                }
            }
            $graph->setAllNodes($node, $index);
            $ret = true;
        }

        return $ret;
    }

    public function printGraph($graph)
    {
        if ($graph && $graph->hasRootNode()) {
            $currentLayer = $graph->getRootNode();
            $nextLayer = array();

            echo "<table>";
            while (count($currentLayer) > 0) {
                echo "<tr>";
                foreach ($currentLayer as $taskId) {
                    echo "<td>";
                    $index = $this->find($taskId, $graph);
                    $node = $graph->getAllNodes($index);
                    $this->printNode($node);
                    echo "</td>";
    
                    foreach ($node->getNext() as $nextId) {
                        if (!in_array($nextId, $nextLayer)) {
                            $nextLayer[] = $nextId;
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
            echo "<p>Node: {$node->getTaskId()}: {$node->getTask()->getTitle()}</p>";
        } else {
            echo "<p>Node: {$node->getTaskId()}</p>";
        }
        echo "<p>IN: [";
        foreach ($node->getPrevious() as $prevId) {
            echo $prevId.", ";
        }
        echo "]</p>";
        echo "<p>OUT: [";
        foreach ($node->getNext() as $nextId) {
            echo $nextId.", ";
        }
        echo "]</p>";
    }

    abstract protected function getProjectTasks($projectId);
    abstract protected function getTaskPreReqs($taskId);
}
