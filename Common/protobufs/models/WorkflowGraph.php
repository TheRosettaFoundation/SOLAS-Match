<?php
namespace SolasMatch\Common\Protobufs\Models;

class WorkflowGraph
{
  public $rootNode;
  public $projectId;
  public $allNodes;

  public function __construct() {
    $this->rootNode = array();
    $this->projectId = null;
    $this->allNodes = array();
  }

  public function getRootNode($index = null) {
    if (!is_null($index)) return $this->rootNode[$index];
    return $this->rootNode;
  }

  public function hasRootNode() {
    return count($this->rootNode) > 0;
  }

  public function setRootNode($rootNode, $index) {
    $this->rootNode[$index] = $rootNode;
  }

  public function clearRootNode() {
    $this->rootNode = array();
  }

  public function addRootNode($rootNode) {
    $this->rootNode[] = $rootNode;
  }

  public function appendRootNode($rootNode) {
    $this->rootNode[] = $rootNode;
  }

  public function getProjectId() {
    return $this->projectId;
  }

  public function setProjectId($projectId) {
    $this->projectId = $projectId;
  }

  public function getAllNodes($index = null) {
    if (!is_null($index)) return $this->allNodes[$index];
    return $this->allNodes;
  }

  public function hasAllNodes() {
    return count($this->allNodes) > 0;
  }

  public function setAllNodes($allNodes, $index) {
    $this->allNodes[$index] = $allNodes;
  }

  public function clearAllNodes() {
    $this->allNodes = array();
  }

  public function addAllNodes($allNodes) {
    $this->allNodes[] = $allNodes;
  }

  public function appendAllNodes($allNodes) {
    $this->allNodes[] = $allNodes;
  }

}
