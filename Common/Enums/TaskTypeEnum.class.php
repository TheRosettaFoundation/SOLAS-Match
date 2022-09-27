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

    static $enum_to_UI;
    static $task_type_to_enum;

    static $x=999;
    public static function init () {
        $task_type_details = \SolasMatch\UI\DAO\ProjectDao::get_task_type_details();
        $enum_to_UI = [];
        $task_type_to_enum = [];
        foreach ($task_type_details as $task_type_detail) {
            $enum_to_UI[$task_type_detail['type_enum']] = $task_type_detail;
            if ($task_type_detail['enabled']) $task_type_to_enum[$task_type_detail['memsource_name']] = $task_type_detail['type_enum'];
        }
error_log(print_r($enum_to_UI, true));
error_log("x: $x");
$a = \SolasMatch\Common\Enums\TaskTypeEnum::$x;
error_log("\SolasMatch\Common\Enums\TaskTypeEnum::x: $a");
$a = TaskTypeEnum::$x;
error_log("TaskTypeEnum::x: $a");
error_log(print_r(\SolasMatch\Common\Enums\TaskTypeEnum::$enum_to_UI, true));
    }
}
