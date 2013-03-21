<?php

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
        $this->iconHeight = 100;
    }

    public function constructView()
    {
        $ret = "";
        $doc = new DOMDocument();
        $doc->formatOutput = true;
        if ($this->model) {
            $view = $doc->createElement("svg");
            $att = $doc->createAttribute("xmlns");
            $att->value = "http://www.w3.org/2000/svg";
            $view->appendChild($att);
            $att = $doc->createAttribute("id");
            $att->value = "project-view";
            $view->appendChild($att);
            $att = $doc->createAttribute("version");
            $att->value = "1.1";
            $view->appendChild($att);
            $att = $doc->createAttribute("width");
            $att->value = "1200";
            $view->appendChild($att);
            $att = $doc->createAttribute("height");
            $att->value = "900";
            $view->appendChild($att);

            $defs = $doc->createElement("defs");
            $att = $doc->createAttribute("id");
            $att->value = "svg-definitions";
            $defs->appendChild($att);

            $roots = $this->model->getRootNodeList();
            $taskDao = new TaskDao();
            foreach ($roots as $root) {
                $thisY = $this->yPos;
                $task = $taskDao->getTask(array('id' => $root->getTaskId()));
                $this->drawGraphFromNode($root, $task, $doc, $defs);
                if ($oldDefs = $doc->getElementById("svg-definitions")) {
                        $view->replaceChild($defs, $oldDefs);
                } else {
                        $view->appendChild($defs);
                }
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
            $view->setAttribute("height", $this->yPos + 20);
            $doc->appendChild($view);
        }
        foreach ($doc->childNodes as $child) {
            $ret .= $doc->saveXml($child);
        }

        return $ret;
    }

    public function drawGraphFromNode($node, $rootTask, $doc, &$defs)
    {
        $taskDao = new TaskDao();
        $currentLayer = array();
        $nextLayer = array();
        $currentLayer[] = $node;

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
            foreach ($currentLayer as $node) {
                $task = $taskDao->getTask(array('id' => $node->getTaskId()));
                $verticalNodeCount++;
                foreach ($node->getNextList() as $nextNode) {
                    if (!in_array($nextNode, $nextLayer)) {
                        $nextLayer[] = $nextNode;
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
        $taskDao = new TaskDao();
        $colour = Settings::get("ui.task_".$task->getTaskType()."_colour");

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
        $att->value = "fill:$colour;stroke:black;stroke-width:4";
        $rect->appendChild($att);
        $defs->appendChild($rect);

        $text = $doc->createElement("text", $task->getId()." - ".$task->getTitle());
        $att = $doc->createAttribute("id");
        $att->value = "text_".$task->getId();
        $text->appendChild($att);
        $att = $doc->createAttribute("x");
        $att->value = $thisX + 5;
        $text->appendChild($att);
        $att = $doc->createAttribute("y");
        $att->value = $thisY + 50;
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
        $att->value = "#text_".$task->getId();
        $component->appendChild($att);
        $compositeElement->appendChild($component);

        $defs->appendChild($compositeElement);
    }
}
