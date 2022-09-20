<?php

namespace SolasMatch\Common\Enums;

class TaskTypeEnum
{
    const SEGMENTATION      = 1;
    const TRANSLATION       = 2;
    const PROOFREADING      = 3;
    const DESEGMENTATION    = 4;
    const QUALITY           = 5;
    const APPROVAL          = 6;
}

$task_type_to_enum = [
    'Translation'                 => TaskTypeEnum::TRANSLATION,
    'Revision'                    => TaskTypeEnum::PROOFREADING,
    //(**)LexiQA not in Memsource: Language Quality Inspection' => TaskTypeEnum::QUALITY,
    'Proofreading and Approval'   => TaskTypeEnum::APPROVAL,
];

$enum_to_UI = [
    TaskTypeEnum::SEGMENTATION   => ['text' => 'Segmentation', 'colour' => '#B02323'],
    TaskTypeEnum::TRANSLATION    => ['text' => 'Translation', 'colour' => '#1D8A11'],
    TaskTypeEnum::PROOFREADING   => ['text' => 'Revising', 'colour' => '#1064C4'],
    TaskTypeEnum::DESEGMENTATION => ['text' => 'Desegmentation', 'colour' => '#B02060'],
    TaskTypeEnum::QUALITY        => ['text' => 'QA??', 'colour' => '#B02323'],
    TaskTypeEnum::APPROVAL       => ['text' => 'Approval??', 'colour' => '#B02060'],
];
