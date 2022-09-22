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

    static $enum_to_UI = [
        TaskTypeEnum::SEGMENTATION   => ['text' => 'Segmentation',   'colour' => '#B02323', 'claimed_template' => 'task/task.claimed-segmentation.tpl'],
        TaskTypeEnum::TRANSLATION    => ['text' => 'Translation',    'colour' => '#1D8A11', 'claimed_template' => 'task/task.claimed-translation.tpl'],
        TaskTypeEnum::PROOFREADING   => ['text' => 'Revising',       'colour' => '#1064C4', 'claimed_template' => 'task/task.claimed-proofreading.tpl'],
        TaskTypeEnum::DESEGMENTATION => ['text' => 'Desegmentation', 'colour' => '#B02060', 'claimed_template' => 'task/task.claimed-desegmentation.tpl'],
        TaskTypeEnum::QUALITY        => ['text' => 'QA??',           'colour' => '#B02323', 'claimed_template' => ''],
        TaskTypeEnum::APPROVAL       => ['text' => 'Approval??',     'colour' => '#B02060', 'claimed_template' => 'task/task.claimed-approval.tpl'],
    ];
}

$task_type_to_enum = [
    'Translation'                 => TaskTypeEnum::TRANSLATION,
    'Revision'                    => TaskTypeEnum::PROOFREADING,
    //(**)LexiQA not in Memsource: Language Quality Inspection' => TaskTypeEnum::QUALITY,
    'Proofreading and Approval'   => TaskTypeEnum::APPROVAL,
];
