<?php

class GraphViewer
{
    private $model;
    private $xPos;
    private $yPos;
    private $graphBuilder;
    private $taskDao;
    private $projectDao;
    //width of the rectangle that displays the task title and status
    const TASK_RECT_WIDTH = 325;
    //height of the rectangle that displays the task title and status
    const TASK_RECT_HEIGHT = 75;
    //spacing between task id and the vertical bar next to it
    const ID_SPACING = 50;
    //Horizontal spacing between task boxes
    const H_SPACING = 100;
    //Vertical spacing between task boxes
    const V_SPACING = 60;

    public function __construct($graph)
    {
        $this->model = $graph;
        $this->xPos = 10;
        $this->yPos = 10;
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
            $taskDao = new TaskDao();
            while (count($currentLayer) > 0) {
                foreach ($currentLayer as $taskId) {
                    $task = $taskDao->getTask($taskId);
                    $taskTargetLocale = $task->getTargetLocale();
                    $target = $taskTargetLocale->getLanguageCode()."-".$taskTargetLocale->getCountryCode();

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
            $viewWidth = 1675;

            $projectDao = new ProjectDao();
            $project = $projectDao->getProject($this->model->getProjectId());

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
            $projectTitle = $doc->createElement("text", htmlspecialchars($titleText));
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

                $task = $this->taskDao->getTask($rootId);
                $this->drawGraphFromNode($task, $doc, $defs);

                $composite = $doc->createElement("use");
                $composite->setAttribute(
                    'xlink:href',
                    "#sub-graph_".$task->getTargetLocale()->getLanguageCode().
                    "-".$task->getTargetLocale()->getCountryCode()
                );
                $composite->setAttribute(
                    'id',
                    "graph_".$task->getTargetLocale()->getLanguageCode().
                    "-".$task->getTargetLocale()->getCountryCode()
                );
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
            $message = $doc->createElement("div");
            $message->setAttribute("class", "alert alert-info");

            $fragment = $doc->createDocumentFragment();
            $fragment->appendXML(Localisation::getTranslation("project_view_failed_build_graph"));
            $message->appendChild($fragment);
            $doc->appendChild($message);
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
        $subGraph->setAttribute(
            'id',
            "sub-graph_".$rootTask->getTargetLocale()->getLanguageCode().
            "-".$rootTask->getTargetLocale()->getCountryCode()
        );
        
        $maxVNodeCount = 0;
        $verticalNodeCount = 0;
        $horizontalNodeCount = 0;
        while (count($currentLayer) > 0) {
            foreach ($currentLayer as $nodeId) {
                $task = $this->taskDao->getTask($nodeId);
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
                
                $yRaster += self::TASK_RECT_HEIGHT + self::V_SPACING;
            }
            
            $yRaster = 10;
            
            if ($verticalNodeCount > $maxVNodeCount) {
                $maxVNodeCount = $verticalNodeCount;
            }
            $verticalNodeCount = 0;
            $horizontalNodeCount++;
            
            $xRaster += self::TASK_RECT_WIDTH + self::H_SPACING;
            
            $currentLayer = $nextLayer;
            $nextLayer = array();
        }
        $width = ($horizontalNodeCount * (self::TASK_RECT_WIDTH + self::H_SPACING)) - self::H_SPACING / 2;
        $height = $maxVNodeCount * (self::TASK_RECT_HEIGHT + self::V_SPACING);
        $this->yPos += $height + 20;

        $languageBox = $doc->createElement("rect");
        $languageBox->setAttribute(
            'id',
            "language-box_".$rootTask->getTargetLocale()->getLanguageCode().
            "-".$rootTask->getTargetLocale()->getCountryCode()
        );
        $languageBox->setAttribute('x', 5);
        $languageBox->setAttribute('y', $yRaster);
        $languageBox->setAttribute('style', "fill-opacity:0;stroke:black;stroke-width:2");
        $languageBox->setAttribute("width", $width);
        $languageBox->setAttribute("height", $height);
        $defs->appendChild($languageBox);
        
        $component = $doc->createElement("use");
        $att = $doc->createAttribute("xlink:href");
        $att->value = "#language-box_".$rootTask->getTargetLocale()->getLanguageCode().
            "-".$rootTask->getTargetLocale()->getCountryCode();
        $component->appendChild($att);
        $subGraph->appendChild($component);
        $text = $doc->createElement("text", TemplateHelper::getLanguageAndCountry($task->getTargetLocale()));
        $text->setAttribute(
            'id',
            "text_".$rootTask->getTargetLocale()->getLanguageCode().
            "-".$rootTask->getTargetLocale()->getCountryCode()
        );
        $text->setAttribute('x', 10);
        $text->setAttribute('y', 25);
        $defs->appendChild($text);
        
        $component = $doc->createElement("use");
        $component->setAttribute(
            'xlink:href',
            "#text_".$rootTask->getTargetLocale()->getLanguageCode().
            "-".$rootTask->getTargetLocale()->getCountryCode()
        );
        $subGraph->appendChild($component);
        $defs->appendChild($subGraph);
    }

    public function drawNode($task, $doc, &$defs)
    {
        $taskTypeColour = Settings::get("ui.task_".$task->getTaskType()."_colour");
        $thisX = 0;
        $thisY = 0;
        
        $rect = $doc->createElement("rect");
        $rect->setAttribute('id', "rect_".$task->getId());
        $rect->setAttribute('x', $thisX);
        $rect->setAttribute("y", $thisY);
        $rect->setAttribute("rx", 20);
        $rect->setAttribute("ry", 20);
        $rect->setAttribute('width', self::TASK_RECT_WIDTH);
        $rect->setAttribute('height', self::TASK_RECT_HEIGHT);
        $rect->setAttribute('style', "fill:rgb(255, 255, 255);stroke:$taskTypeColour;stroke-width:4");
        $defs->appendChild($rect);

        $vLine = $doc->createElement("line");
        $vLine->setAttribute('id', "v-line_".$task->getId());
        $vLine->setAttribute('x1', $thisX + self::ID_SPACING);
        $vLine->setAttribute('y1', $thisY);
        $vLine->setAttribute('x2', $thisX + self::ID_SPACING);
        $vLine->setAttribute('y2', $thisY + self::TASK_RECT_HEIGHT);
        $vLine->setAttribute('style', "stroke:$taskTypeColour;stroke-width:4");
        $defs->appendChild($vLine);
        
        $hLine = $doc->createElement("line");
        $hLine->setAttribute('id', "h-line_".$task->getId());
        $hLine->setAttribute('x1', $thisX + self::ID_SPACING);
        $hLine->setAttribute('y1', $thisY + (self::TASK_RECT_HEIGHT / 2));
        $hLine->setAttribute('x2', $thisX + self::TASK_RECT_WIDTH);
        $hLine->setAttribute('y2', $thisY + (self::TASK_RECT_HEIGHT / 2));
        $hLine->setAttribute('style', "stroke:$taskTypeColour;stroke-width:4");
        $defs->appendChild($hLine);
        
        $clipPath = $doc->createElement("clipPath");
        $clipPath->setAttribute('id', "title-clip_".$task->getId());
        
        $component = $doc->createElement("rect");
        $component->setAttribute('x', 0);
        $component->setAttribute('y', 0);
        // -5 below prevents from text overlapping with the border
        $component->setAttribute('width', self::TASK_RECT_WIDTH + self::ID_SPACING - 5);
        $component->setAttribute('height', self::TASK_RECT_HEIGHT);
        $clipPath->appendChild($component);
        $defs->appendChild($clipPath);
        
        $text = $doc->createElement("text", $task->getId());
        $text->setAttribute('id', "task-id_".$task->getId());
        $text->setAttribute('x', $thisX + 5);
        $text->setAttribute('y', $thisY + (self::TASK_RECT_HEIGHT / 2) + 3);
        $text->setAttribute('clip-path', "url(#title-clip_".$task->getId().")");
        $defs->appendChild($text);
        
        $text = $doc->createElement("text", htmlspecialchars($task->getTitle()));
        $text->setAttribute('id', "task-title_".$task->getId());
        $text->setAttribute('x', $thisX + self::ID_SPACING + 5);
        $text->setAttribute('y', $thisY + 25);
        $text->setAttribute('clip-path', "url(#title-clip_".$task->getId().")");
        $defs->appendChild($text);

        $status = "";
        $taskStatusColour = "rgb(0, 0, 0)";
        switch ($task->getTaskStatus()) {
            case (TaskStatusEnum::WAITING_FOR_PREREQUISITES):
                $status = "Waiting";
                break;
            case (TaskStatusEnum::PENDING_CLAIM):
                $status = "Pending Claim";
                break;
            case (TaskStatusEnum::IN_PROGRESS):
                $status = "In Progress";
                break;
            case (TaskStatusEnum::COMPLETE):
                $status = "Complete";
                break;
        }
        
        $text = $doc->createElement("text", "Status: $status");
        $text->setAttribute('id', "task-status_".$task->getId());
        $text->setAttribute('x', $thisX + self::ID_SPACING + 5);
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
