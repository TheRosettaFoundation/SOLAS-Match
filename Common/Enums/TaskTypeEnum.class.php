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
    'Translation'                 => Common\Enums\TaskTypeEnum::TRANSLATION,
    'Revision'                    => Common\Enums\TaskTypeEnum::PROOFREADING,
    //(**)LexiQA not in Memsource: Language Quality Inspection' => Common\Enums\TaskTypeEnum::QUALITY,
    'Proofreading and Approval'   => Common\Enums\TaskTypeEnum::APPROVAL,
];
