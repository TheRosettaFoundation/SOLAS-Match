<?php

require_once __DIR__."/UIWorkflowBuilder.class.php";

class GraphViewer
{
    private $model;
    private $xPos;
    private $yPos;
    private $iconWidth;
    private $iconHeight;

    public function GraphViewer($graph)
    {
        $this->model = $graph;
        $this->xPos = 10;
        $this->yPos = 10;
        $this->iconWidth = 175;
        $this->iconHeight = 75;
    }

    public function generateDataScript()
    {
        $graphBuilder = new UIWorkflowBuilder();
        $ret = "<script>
            var postReqs = new Array();
            var preReqs = new Array();
            var languageList = new Array();
            var languageTasks = new Array();";

        if ($this->model && $this->model->hasRootNode()) {
            $currentLayer = $this->model->getRootNodeList();
            $nextLayer = array();
            
            $taskDao = new TaskDao();
            $foundLanguages = array();
            while (count($currentLayer) > 0) {
                foreach ($currentLayer as $taskId) {
                    $task = $taskDao->getTask(array('id' => $taskId));
                    $target = $task->getTargetLanguageCode()."-".$task->getTargetCountryCode();
                    if (!in_array($target, $foundLanguages)) {
                        $ret .= "languageTasks[\"".$target."\"] = new Array();";
                        $ret .= "languageList.push(\"".$target."\");";
                        $foundLanguages[] = $target;
                    }
                    $ret .= "languageTasks[\"".$target."\"].push($taskId);";
                    $ret .= "preReqs[$taskId] = new Array();";
                    $ret .= "postReqs[$taskId] = new Array();";

                    $index = $graphBuilder->find($taskId, $this->model);
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
            $projectDao = new ProjectDao();
            $project = $projectDao->getProject(array("id" => $this->model->getProjectId()));

            $view = $doc->createElement("svg");
            $att = $doc->createAttribute("xmlns");
            $att->value = "http://www.w3.org/2000/svg";
            $view->appendChild($att);
            $att = $doc->createAttribute("xmlns:xlink");
            $att->value = "http://www.w3.org/1999/xlink";
            $view->appendChild($att);
            $att = $doc->createAttribute("id");
            $att->value = "project-view";
            $view->appendChild($att);
            $att = $doc->createAttribute("version");
            $att->value = "1.1";
            $view->appendChild($att);
            $att = $doc->createAttribute("width");
            $att->value = $viewWidth;
            $view->appendChild($att);

            $border = $doc->createElement("rect");
            $att = $doc->createAttribute("x");
            $att->value = 1;
            $border->appendChild($att);
            $att = $doc->createAttribute("y");
            $att->value = 4;
            $border->appendChild($att);
            $att = $doc->createAttribute("width");
            $att->value = $viewWidth - 2;
            $border->appendChild($att);
            $att = $doc->createAttribute("height");
            $att->value = 900;
            $border->appendChild($att);
            $att = $doc->createAttribute("style");
            $att->value = "fill-opacity:0;stroke:black;stroke-width:2";
            $border->appendChild($att);

            $titleText = "Project: ".$project->getTitle();
            $projectTitle = $doc->createElement("text", $titleText);
            $att = $doc->createAttribute("x");
            $att->value = 10;
            $projectTitle->appendChild($att);
            $att = $doc->createAttribute("y");
            $att->value = 20;
            $projectTitle->appendChild($att);
            $view->appendChild($projectTitle);

            $triangle = $doc->createElement("marker");
            $att = $doc->createAttribute("id");
            $att->value = "triangle";
            $triangle->appendChild($att);
            $att = $doc->createAttribute("viewBox");
            $att->value = "0 0 10 10";
            $triangle->appendChild($att);
            $att = $doc->createAttribute("refX");
            $att->value = 0;
            $triangle->appendChild($att);
            $att = $doc->createAttribute("refY");
            $att->value = 5;
            $triangle->appendChild($att);
            $att = $doc->createAttribute("markerUnits");
            $att->value = "strokeWidth";
            $triangle->appendChild($att);
            $att = $doc->createAttribute("markerWidth");
            $att->value = 10;
            $triangle->appendChild($att);
            $att = $doc->createAttribute("markerHeight");
            $att->value = 10;
            $triangle->appendChild($att);
            $att = $doc->createAttribute("orient");
            $att->value = "auto";
            $triangle->appendChild($att);

            $path = $doc->createElement("path");
            $att = $doc->createAttribute("d");
            $att->value = "M 0 0 L 10 5 L 0 10 z";
            $path->appendChild($att);
            $triangle->appendChild($path);
            $view->appendChild($triangle);

            $defs = $doc->createElement("defs");
            $att = $doc->createAttribute("id");
            $att->value = "svg-definitions";
            $defs->appendChild($att);

            $roots = $this->model->getRootNodeList();
            $taskDao = new TaskDao();
            foreach ($roots as $rootId) {
                $thisY = $this->yPos + 20;
                $task = $taskDao->getTask(array('id' => $rootId));
                $this->drawGraphFromNode($task, $doc, $defs);
                $composite = $doc->createElement("use");
                $att = $doc->createAttribute("xlink:href");
                $att->value = "#sub-graph_".$task->getTargetLanguageCode()."-".$task->getTargetCountryCode();
                $composite->appendChild($att);
                $att = $doc->createAttribute("id");
                $att->value = "graph_".$task->getTargetLanguageCode()."-".$task->getTargetCountryCode();
                $composite->appendChild($att);
                $att = $doc->createAttribute("x");
                $att->value = 5;
                $composite->appendChild($att);
                $att = $doc->createAttribute("y");
                $att->value = $thisY;
                $composite->appendChild($att);
                $view->appendChild($composite);
            }
            $view->insertBefore($defs, $view->firstChild);
            $view->setAttribute("height", $this->yPos + 20);
            $border->setAttribute("height", $this->yPos + 15);
            $view->appendChild($border);

            //create a div to display the graph
            $div = $doc->createElement("div");
            $att = $doc->createAttribute("class");
            $att->value = "graph-view";
            $div->appendChild($att);
            $div->appendChild($view);
            $doc->appendChild($div);
        }
        foreach ($doc->childNodes as $child) {
            $ret .= $doc->saveXml($child);
        }

        return $ret;
    }

    public function drawGraphFromNode($rootTask, $doc, &$defs)
    {
        $graphBuilder = new UIWorkflowBuilder();
        $taskDao = new TaskDao();
        $currentLayer = array();
        $nextLayer = array();
        $currentLayer[] = $rootTask->getId();

        $xRaster = 10;
        $yRaster = 10;

        $subGraph = $doc->createElement("g");
        $att = $doc->createAttribute("id");
        $att->value = "sub-graph_".$rootTask->getTargetLanguageCode()."-".$rootTask->getTargetCountryCode();
        $subGraph->appendChild($att);

        $languageBox = $doc->createElement("rect");
        $att = $doc->createAttribute("id");
        $att->value = "language-box_".$rootTask->getTargetLanguageCode()."-".$rootTask->getTargetCountryCode();
        $languageBox->appendChild($att);
        $att = $doc->createAttribute("x");
        $att->value = 5;
        $languageBox->appendChild($att);
        $att = $doc->createAttribute("y");
        $att->value = $yRaster;
        $languageBox->appendChild($att);
        $att = $doc->createAttribute("width");
        $att->value = 1200;
        $languageBox->appendChild($att);
        $att = $doc->createAttribute("height");
        $att->value = 900;
        $languageBox->appendChild($att);
        $att = $doc->createAttribute("style");
        $att->value = "fill-opacity:0;stroke:black;stroke-width:2";
        $languageBox->appendChild($att);

        $maxVNodeCount = 0;
        $verticalNodeCount = 0;
        $horizontalNodeCount = 0;
        while (count($currentLayer) > 0) {
            foreach ($currentLayer as $nodeId) {
                $task = $taskDao->getTask(array('id' => $nodeId));
                $index = $graphBuilder->find($nodeId, $this->model);
                $node = $this->model->getAllNodes($index);
                $verticalNodeCount++;
                foreach ($node->getNextList() as $nextId) {
                    if (!in_array($nextId, $nextLayer)) {
                        $nextLayer[] = $nextId;
                    }
                }
                $this->drawNode($task, $doc, $defs);

                $composite = $doc->createElement("use");
                $att = $doc->createAttribute("xlink:href");
                $att->value = "#comp_".$node->getTaskId();
                $composite->appendChild($att);
                $att = $doc->createAttribute("id");
                $att->value = "task_".$node->getTaskId();
                $composite->appendChild($att);
                $att = $doc->createAttribute("x");
                $att->value = $xRaster + 20;
                $composite->appendChild($att);
                $att = $doc->createAttribute("y");
                $att->value = $yRaster + 40;
                $composite->appendChild($att);
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

        $text = $doc->createElement("text", TemplateHelper::getTaskTargetLanguage($rootTask));
        $att = $doc->createAttribute("id");
        $att->value = "text_".$rootTask->getTargetLanguageCode()."-".$rootTask->getTargetCountryCode();
        $text->appendChild($att);
        $att = $doc->createAttribute("x");
        $att->value = 10;
        $text->appendChild($att);
        $att = $doc->createAttribute("y");
        $att->value = 25;
        $text->appendChild($att);
        $defs->appendChild($text);

        $component = $doc->createElement("use");
        $att = $doc->createAttribute("xlink:href");
        $att->value = "#text_".$rootTask->getTargetLanguageCode()."-".$rootTask->getTargetCountryCode();
        $component->appendChild($att);
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
        $att = $doc->createAttribute("id");
        $att->value = "rect_".$task->getId();
        $rect->appendChild($att);
        $att = $doc->createAttribute("x");
        $att->value = $thisX;
        $rect->appendChild($att);
        $att = $doc->createAttribute("y");
        $att->value = $thisY;
        $rect->appendChild($att);
        $att = $doc->createAttribute("rx");
        $att->value = "20";
        $rect->appendChild($att);
        $att = $doc->createAttribute("ry");
        $att->value = "20";
        $rect->appendChild($att);
        $att = $doc->createAttribute("width");
        $att->value = $itemWidth;
        $rect->appendChild($att);
        $att = $doc->createAttribute("height");
        $att->value = $itemHeight;
        $rect->appendChild($att);
        $att = $doc->createAttribute("style");
        $att->value = "fill:rgb(255, 255, 255);stroke:$taskTypeColour;stroke-width:4";
        $rect->appendChild($att);
        $defs->appendChild($rect);

        $vLine = $doc->createElement("line");
        $att = $doc->createAttribute("id");
        $att->value = "v-line_".$task->getId();
        $vLine->appendChild($att);
        $att = $doc->createAttribute("x1");
        $att->value = $thisX + 25;
        $vLine->appendChild($att);
        $att = $doc->createAttribute("y1");
        $att->value = $thisY;
        $vLine->appendChild($att);
        $att = $doc->createAttribute("x2");
        $att->value = $thisX + 25;
        $vLine->appendChild($att);
        $att = $doc->createAttribute("y2");
        $att->value = $thisY + $itemHeight;
        $vLine->appendChild($att);
        $att = $doc->createAttribute("style");
        $att->value = "stroke:$taskTypeColour;stroke-width:4";
        $vLine->appendChild($att);
        $defs->appendChild($vLine);

        $hLine = $doc->createElement("line");
        $att = $doc->createAttribute("id");
        $att->value = "h-line_".$task->getId();
        $hLine->appendChild($att);
        $att = $doc->createAttribute("x1");
        $att->value = $thisX + 25;
        $hLine->appendChild($att);
        $att = $doc->createAttribute("y1");
        $att->value = $thisY + ($itemHeight / 2);
        $hLine->appendChild($att);
        $att = $doc->createAttribute("x2");
        $att->value = $thisX + $itemWidth;
        $hLine->appendChild($att);
        $att = $doc->createAttribute("y2");
        $att->value = $thisY + ($itemHeight / 2);
        $hLine->appendChild($att);
        $att = $doc->createAttribute("style");
        $att->value = "stroke:$taskTypeColour;stroke-width:4";
        $hLine->appendChild($att);
        $defs->appendChild($hLine);

        $clipPath = $doc->createElement("clipPath");
        $att = $doc->createAttribute("id");
        $att->value = "title-clip_".$task->getId();
        $clipPath->appendChild($att);

        $component = $doc->createElement("rect");
        $att = $doc->createAttribute("x");
        $att->value = 0;
        $component->appendChild($att);
        $att = $doc->createAttribute("y");
        $att->value = 0;
        $component->appendChild($att);
        $att = $doc->createAttribute("width");
        $att->value = $this->iconWidth;
        $component->appendChild($att);
        $att = $doc->createAttribute("height");
        $att->value = $this->iconHeight;
        $component->appendChild($att);
        $clipPath->appendChild($component);
        $defs->appendChild($clipPath);

        $text = $doc->createElement("text", $task->getId());
        $att = $doc->createAttribute("id");
        $att->value = "task-id_".$task->getId();
        $text->appendChild($att);
        $att = $doc->createAttribute("x");
        $att->value = $thisX + 5;
        $text->appendChild($att);
        $att = $doc->createAttribute("y");
        $att->value = $thisY + ($itemHeight / 2) + 3;
        $text->appendChild($att);
        $att = $doc->createAttribute("clip-path");
        $att->value = "url(#title-clip_".$task->getId().")";
        $text->appendChild($att);
        $defs->appendChild($text);

        $text = $doc->createElement("text", $task->getTitle());
        $att = $doc->createAttribute("id");
        $att->value = "task-title_".$task->getId();
        $text->appendChild($att);
        $att = $doc->createAttribute("x");
        $att->value = $thisX + 30;
        $text->appendChild($att);
        $att = $doc->createAttribute("y");
        $att->value = $thisY + 25;
        $text->appendChild($att);
        $att = $doc->createAttribute("clip-path");
        $att->value = "url(#title-clip_".$task->getId().")";
        $text->appendChild($att);
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
        $att = $doc->createAttribute("id");
        $att->value = "task-status_".$task->getId();
        $text->appendChild($att);
        $att = $doc->createAttribute("x");
        $att->value = $thisX + 35;
        $text->appendChild($att);
        $att = $doc->createAttribute("y");
        $att->value = $thisY + 60;
        $text->appendChild($att);
        $att = $doc->createAttribute("fill");
        $att->value = $taskStatusColour;
        $text->appendChild($att);
        $att = $doc->createAttribute("clip-path");
        $att->value = "url(#title-clip_".$task->getId().")";
        $text->appendChild($att);
        $defs->appendChild($text);

        $compositeElement = $doc->createElement("g");
        $att = $doc->createAttribute("id");
        $att->value = "comp_".$task->getId();
        $compositeElement->appendChild($att);

        $component = $doc->createElement("use");
        $att = $doc->createAttribute("xlink:href");
        $att->value = "#rect_".$task->getId();
        $component->appendChild($att);
        $compositeElement->appendChild($component);

        $component = $doc->createElement("use");
        $att = $doc->createAttribute("xlink:href");
        $att->value = "#task-id_".$task->getId();
        $component->appendChild($att);
        $compositeElement->appendChild($component);

        $component = $doc->createElement("use");
        $att = $doc->createAttribute("xlink:href");
        $att->value = "#task-title_".$task->getId();
        $component->appendChild($att);
        $compositeElement->appendChild($component);

        $component = $doc->createElement("use");
        $att = $doc->createAttribute("xlink:href");
        $att->value = "#task-status_".$task->getId();
        $component->appendChild($att);
        $compositeElement->appendChild($component);

        $component = $doc->createElement("use");
        $att = $doc->createAttribute("xlink:href");
        $att->value = "#v-line_".$task->getId();
        $component->appendChild($att);
        $compositeElement->appendChild($component);

        $component = $doc->createElement("use");
        $att = $doc->createAttribute("xlink:href");
        $att->value = "#h-line_".$task->getId();
        $component->appendChild($att);
        $compositeElement->appendChild($component);

        $defs->appendChild($compositeElement);
    }
}
