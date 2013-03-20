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

            $currentLayer = $this->model->getRootNodeList();
            $nextLayer = array();
            $maxVNodeCount = 0;
            $verticalNodeCount = 0;
            $horizontalNodeCount = 0;
            while (count($currentLayer) > 0) {
                foreach ($currentLayer as $node) {
                    $verticalNodeCount++;
                    foreach ($node->getNextList() as $nextNode) {
                        if (!in_array($nextNode, $nextLayer)) {
                            $nextLayer[] = $nextNode;
                        }
                    }
                    $this->drawNode($node, $doc, $defs);

                    if ($oldDefs = $doc->getElementById("svg-definitions")) {
                        $view->replaceChild($defs, $oldDefs);
                    } else {
                        $view->appendChild($defs);
                    }

                    $composite = $doc->createElement("use");
                    $att = $doc->createAttribute("xlink:href");
                    $att->value = "#comp_".$node->getTaskId();
                    $composite->appendChild($att);
                    $att = $doc->createAttribute("id");
                    $att->value = "task_".$node->getTaskId();
                    $composite->appendChild($att);
                    $att = $doc->createAttribute("x");
                    $att->value = $this->xPos;
                    $composite->appendChild($att);
                    $att = $doc->createAttribute("y");
                    $att->value = $this->yPos;
                    $composite->appendChild($att);
                    $view->appendChild($composite);

                    $this->yPos += $this->iconHeight + 30;
                }

                if ($verticalNodeCount > $maxVNodeCount) {
                    $maxVNodeCount = $verticalNodeCount;
                }
                $verticalNodeCount = 0;
                $horizontalNodeCount++;

                $this->yPos = 10;
                $this->xPos += $this->iconWidth + 100;

                $currentLayer = $nextLayer;
                $nextLayer = array();
            }

            $width = $horizontalNodeCount * ($this->iconWidth + 100);
            $height = $maxVNodeCount * ($this->iconHeight + 30);
            $view->setAttribute("width", $width);
            $view->setAttribute("height", $height);
            $doc->appendChild($view);
        }
        foreach ($doc->childNodes as $child) {
            $ret .= $doc->saveXml($child);
        }

        return $ret;
    }

    public function drawNode($node, $doc, &$defs)
    {
        $taskDao = new TaskDao();
        $task = $taskDao->getTask(array('id' => $node->getTaskId()));
        $colour = Settings::get("ui.task_".$task->getTaskType()."_colour");

        $thisX = 0;
        $thisY = 0;
        $itemWidth = $this->iconWidth;
        $itemHeight = $this->iconHeight;

        $rect = $doc->createElement("rect");
        $att = $doc->createAttribute("id");
        $att->value = "rect_".$node->getTaskId();
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
        $att->value = "text_".$node->getTaskId();
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
        $att->value = "comp_".$node->getTaskId();
        $compositeElement->appendChild($att);

        $component = $doc->createElement("use");
        $att = $doc->createAttribute("xlink:href");
        $att->value = "#rect_".$node->getTaskId();
        $component->appendChild($att);
        $compositeElement->appendChild($component);

        $component = $doc->createElement("use");
        $att = $doc->createAttribute("xlink:href");
        $att->value = "#text_".$node->getTaskId();
        $component->appendChild($att);
        $compositeElement->appendChild($component);

        $defs->appendChild($compositeElement);
    }
}
