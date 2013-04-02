<?php

class GraphViewer
{
    private $model;
    private $xPos;
    private $yPos;
    private $iconWidth;
    private $iconHeight;
    private $graphBuilder;
    private $taskDao;
    private $projectDao;

    public function __construct($graph)
    {
        $this->model = $graph;
        $this->xPos = 10;
        $this->yPos = 10;
        $this->iconWidth = 175;
        $this->iconHeight = 75;
        $this->graphBuilder = new UIWorkflowBuilder();
        $this->taskDao = new TaskDao();
        $this->projectDao = new ProjectDao();
    }

    public function generateDataScript()
    {
        $ret = "<script>
              var postReqs = new Array();
              var preReqs = new Array();
              var languageList = new Array();
              var languageTasks = new Array();";
        
        if ($this->model && $this->model->hasRootNode()) {
            $currentLayer = $this->model->getRootNodeList();
            $nextLayer = array();
            
            $foundLanguages = array();
            while (count($currentLayer) > 0) {
                foreach ($currentLayer as $taskId) {
                    $task = $this->taskDao->getTask(array('id' => $taskId));
                    $target = $task->getTargetLanguageCode()."-".$task->getTargetCountryCode();
                    if (!in_array($target, $foundLanguages)) {
                        $ret .= "languageTasks[\"".$target."\"] = new Array();";
                        $ret .= "languageList.push(\"".$target."\");";
                        $foundLanguages[] = $target;
                    }
                    $ret .= "languageTasks[\"".$target."\"].push($taskId);";
                    $ret .= "preReqs[$taskId] = new Array();";
                    $ret .= "postReqs[$taskId] = new Array();";
                    
                    $index = $this->graphBuilder->find($taskId, $this->model);
                    $node = $this->model->getAllNodes($index);
                    foreach ($node->getNextList() as $nextId) {
                        $ret .= "postReqs[$taskId].push($nextId);";
                        if (!in_array($nextId, $nextLayer)) {
                            $nextLayer[] = $nextId;
                        }
                    }
                    foreach ($node->getPreviousList() as $prevId) {
                        $ret .= "preReqs[$taskId].push($prevId);";
                    }
                }
                $currentLayer = $nextLayer;
                $nextLayer = array();
            }
        }
        $ret .= "</script>";
        
        return $ret;
    }

    public function constructView()
    {
        $ret = "";
        $doc = new DOMDocument();
        $doc->formatOutput = true;
        if ($this->model) {
            $viewWidth = 1200;
            $project = $this->projectDao->getProject(array('id' => $this->model->getProjectId()));
            
            $view = $doc->createElement("svg");
            $view->setAttribute('xmlns', "http://www.w3.org/2000/svg");
            $view->setAttribute('xmlns:xlink', "http://www.w3.org/1999/xlink");
            $view->setAttribute('id', "project-view");
            $view->setAttribute('version', "1.1");
            $view->setAttribute('width', $viewWidth);
            
            $border = $doc->createElement("rect");
            $border->setAttribute('x', 1);
            $border->setAttribute('y', 4);
            $border->setAttribute('width', $viewWidth - 2);
            $border->setAttribute('height', 900);
            $border->setAttribute('style', "fill-opacity:0;stroke:black;stroke-width:2");
            
            $titleText = "Project: ".$project->getTitle();
            $projectTitle = $doc->createElement("text", $titleText);
            $projectTitle->setAttribute('x', 10);
            $projectTitle->setAttribute('y', 20);
            $view->appendChild($projectTitle);
            
            $triangle = $doc->createElement("marker");
            $triangle->setAttribute('id', "triangle");
            $triangle->setAttribute('viewBox', "0 0 10 10");
            $triangle->setAttribute('refX', 0);
            $triangle->setAttribute('refY', 5);
            $triangle->setAttribute('markerUnits', "strokeWidth");
            $triangle->setAttribute('markerWidth', 10);
            $triangle->setAttribute('markerHeight', 10);
            $triangle->setAttribute('orient', 'auto');
            
            $path = $doc->createElement("path");
            $path->setAttribute('d', "M 0 0 L 10 5 L 0 10 z");
            $triangle->appendChild($path);
            $view->appendChild($triangle);
            
            $defs = $doc->createElement("defs");
            $defs->setAttribute('id', "svg-definitions");
            
            $roots = $this->model->getRootNodeList();
            foreach ($roots as $rootId) {
                $thisY = $this->yPos + 20;
                $task = $this->taskDao->getTask(array('id' => $rootId));
                $this->drawGraphFromNode($task, $doc, $defs);

                $composite = $doc->createElement("use");
                $composite->setAttribute('xlink:href', "#sub-graph_".$task->getTargetLanguageCode().
                                                        "-".$task->getTargetCountryCode());
                $composite->setAttribute('id', "graph_".$task->getTargetLanguageCode().
                                                "-".$task->getTargetCountryCode());
                $composite->setAttribute('x', 5);
                $composite->setAttribute('y', $thisY);
                $view->appendChild($composite);
            }
            $view->insertBefore($defs, $view->firstChild);
            $view->setAttribute("height", $this->yPos + 20);
            $border->setAttribute("height", $this->yPos + 15);
            $view->appendChild($border);
            
            //create a div to display the graph
            $div = $doc->createElement("div");
            $div->setAttribute("class", "graph-view");
            $div->appendChild($view);
            $doc->appendChild($div);
        } else {
            echo "<p>Unable to build graph, model is null</p>";
        }
        foreach ($doc->childNodes as $child) {
            $ret .= $doc->saveXml($child);
        }
        
        return $ret;
    }

    public function drawGraphFromNode($rootTask, $doc, &$defs)
    {
        $currentLayer = array();
        $nextLayer = array();
        $currentLayer[] = $rootTask->getId();
        
        $xRaster = 10;
        $yRaster = 10;
        
        $subGraph = $doc->createElement("g");
        $subGraph->setAttribute('id', "sub-graph_".$rootTask->getTargetLanguageCode().
                                        "-".$rootTask->getTargetCountryCode());
        
        $languageBox = $doc->createElement("rect");
        $languageBox->setAttribute('id', "language-box_".$rootTask->getTargetLanguageCode().
                                            "-".$rootTask->getTargetCountryCode());
        $languageBox->setAttribute('x', 5);
        $languageBox->setAttribute('y', $yRaster);
        $languageBox->setAttribute('width', 1200);
        $languageBox->setAttribute('height', 900);
        $languageBox->setAttribute('style', "fill-opacity:0;stroke:black;stroke-width:2");
        
        $maxVNodeCount = 0;
        $verticalNodeCount = 0;
        $horizontalNodeCount = 0;
        while (count($currentLayer) > 0) {
            foreach ($currentLayer as $nodeId) {
                $task = $this->taskDao->getTask(array('id' => $nodeId));
                $index = $this->graphBuilder->find($nodeId, $this->model);
                $node = $this->model->getAllNodes($index);
                $verticalNodeCount++;
                foreach ($node->getNextList() as $nextId) {
                    if (!in_array($nextId, $nextLayer)) {
                        $nextLayer[] = $nextId;
                    }
                }
                $this->drawNode($task, $doc, $defs);
                
                $composite = $doc->createElement("use");
                $composite->setAttribute('xlink:href', "#comp_".$node->getTaskId());
                $composite->setAttribute('id', "task_".$node->getTaskId());
                $composite->setAttribute('x', $xRaster + 20);
                $composite->setAttribute('y', $yRaster + 40);
                $subGraph->appendChild($composite);
                
                $yRaster += $this->iconHeight + 60;
            }
            
            $yRaster = 10;
            
            if ($verticalNodeCount > $maxVNodeCount) {
                $maxVNodeCount = $verticalNodeCount;
            }
            $verticalNodeCount = 0;
            $horizontalNodeCount++;
            
            $xRaster += $this->iconWidth + 100;
            
            $currentLayer = $nextLayer;
            $nextLayer = array();
        }
        $width = $horizontalNodeCount * ($this->iconWidth + 100);
        $height = $maxVNodeCount * ($this->iconHeight + 60);
        $this->yPos += $height + 20;
        $languageBox->setAttribute("width", $width);
        $languageBox->setAttribute("height", $height);
        $defs->appendChild($languageBox);
        
        $component = $doc->createElement("use");
        $att = $doc->createAttribute("xlink:href");
        $att->value = "#language-box_".$rootTask->getTargetLanguageCode()."-".$rootTask->getTargetCountryCode();
        $component->appendChild($att);
        $subGraph->appendChild($component);
        
        $text = $doc->createElement("text", TemplateHelper::getTaskTargetLanguage($task));
        $text->setAttribute('id', "text_".$rootTask->getTargetLanguageCode().
                                    "-".$rootTask->getTargetCountryCode());
        $text->setAttribute('x', 10);
        $text->setAttribute('y', 25);
        $defs->appendChild($text);
        
        $component = $doc->createElement("use");
        $component->setAttribute('xlink:href', "#text_".$rootTask->getTargetLanguageCode().
                                                "-".$rootTask->getTargetCountryCode());
        $subGraph->appendChild($component);
        $defs->appendChild($subGraph);
    }

    public function drawNode($task, $doc, &$defs)
    {
        $taskTypeColour = Settings::get("ui.task_".$task->getTaskType()."_colour");
        $thisX = 0;
        $thisY = 0;
        $itemWidth = $this->iconWidth;
        $itemHeight = $this->iconHeight;
        
        $rect = $doc->createElement("rect");
        $rect->setAttribute('id', "rect_".$task->getId());
        $rect->setAttribute('x', $thisX);
        $rect->setAttribute("y", $thisY);
        $rect->setAttribute("rx", 20);
        $rect->setAttribute("ry", 20);
        $rect->setAttribute('width', $itemWidth);
        $rect->setAttribute('height', $itemHeight);
        $rect->setAttribute('style', "fill:rgb(255, 255, 255);stroke:$taskTypeColour;stroke-width:4");
        $defs->appendChild($rect);

        $vLine = $doc->createElement("line");
        $vLine->setAttribute('id', "v-line_".$task->getId());
        $vLine->setAttribute('x1', $thisX + 25);
        $vLine->setAttribute('y1', $thisY);
        $vLine->setAttribute('x2', $thisX + 25);
        $vLine->setAttribute('y2', $thisY + $itemHeight);
        $vLine->setAttribute('style', "stroke:$taskTypeColour;stroke-width:4");
        $defs->appendChild($vLine);
        
        $hLine = $doc->createElement("line");
        $hLine->setAttribute('id', "h-line_".$task->getId());
        $hLine->setAttribute('x1', $thisX + 25);
        $hLine->setAttribute('y1', $thisY + ($itemHeight / 2));
        $hLine->setAttribute('x2', $thisX + $itemWidth);
        $hLine->setAttribute('y2', $thisY + ($itemHeight / 2));
        $hLine->setAttribute('style', "stroke:$taskTypeColour;stroke-width:4");
        $defs->appendChild($hLine);
        
        $clipPath = $doc->createElement("clipPath");
        $clipPath->setAttribute('id', "title-clip_".$task->getId());
        
        $component = $doc->createElement("rect");
        $component->setAttribute('x', 0);
        $component->setAttribute('y', 0);
        $component->setAttribute('width', $this->iconWidth);
        $component->setAttribute('height', $this->iconHeight);
        $clipPath->appendChild($component);
        $defs->appendChild($clipPath);
        
        $text = $doc->createElement("text", $task->getId());
        $text->setAttribute('id', "task-id_".$task->getId());
        $text->setAttribute('x', $thisX + 5);
        $text->setAttribute('y', $thisY + ($itemHeight / 2) + 3);
        $text->setAttribute('clip-path', "url(#title-clip_".$task->getId().")");
        $defs->appendChild($text);
        
        $text = $doc->createElement("text", $task->getTitle());
        $text->setAttribute('id', "task-title_".$task->getId());
        $text->setAttribute('x', $thisX + 30);
        $text->setAttribute('y', $thisY + 25);
        $text->setAttribute('clip-path', "url(#title-clip_".$task->getId().")");
        $defs->appendChild($text);
        
        $status = "";
        $taskStatusColour = "rgb(0, 0, 0)";
        switch ($task->getTaskStatus()) {
            case (TaskStatusEnum::WAITING_FOR_PREREQUISITES):
                $status = "Waiting";
//                $taskStatusColour = "rgb(255, 50, 50)";
                break;
            case (TaskStatusEnum::PENDING_CLAIM):
                $status = "Pending Claim";
//                $taskStatusColour = "rgb(230, 230, 230)";
                break;
            case (TaskStatusEnum::IN_PROGRESS):
                $status = "In Progress";
//                $taskStatusColour = "rgb(150, 150, 255)";
                break;
            case (TaskStatusEnum::COMPLETE):
                $status = "Complete";
//                $taskStatusColour = "rgb(20, 210, 20)";
                break;
        }
        
        $text = $doc->createElement("text", "Status: $status");
        $text->setAttribute('id', "task-status_".$task->getId());
        $text->setAttribute('x', $thisX + 35);
        $text->setAttribute('y', $thisY + 60);
        $text->setAttribute('fill', $taskStatusColour);
        $text->setAttribute('clip-path', "url(#title-clip_".$task->getId().")");
        $defs->appendChild($text);
        
        $compositeElement = $doc->createElement("g");
        $compositeElement->setAttribute('id', "comp_".$task->getId());
        
        $component = $doc->createElement("use");
        $component->setAttribute('xlink:href', "#rect_".$task->getId());
        $compositeElement->appendChild($component);
        
        $component = $doc->createElement("use");
        $component->setAttribute('xlink:href', "#task-id_".$task->getId());
        $compositeElement->appendChild($component);

        $component = $doc->createElement("use");
        $component->setAttribute('xlink:href', "#task-title_".$task->getId());
        $compositeElement->appendChild($component);
        
        $component = $doc->createElement("use");
        $component->setAttribute('xlink:href', "#task-status_".$task->getId());
        $compositeElement->appendChild($component);
        
        $component = $doc->createElement("use");
        $component->setAttribute('xlink:href', "#v-line_".$task->getId());
        $compositeElement->appendChild($component);
        
        $component = $doc->createElement("use");
        $component->setAttribute('xlink:href', "#h-line_".$task->getId());
        $compositeElement->appendChild($component);
        
        $defs->appendChild($compositeElement);
    }
}
