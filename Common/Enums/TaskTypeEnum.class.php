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

    public static function init () {
        $task_type_details = \SolasMatch\UI\DAO\ProjectDao::get_task_type_details();
error_log(print_r($task_type_details, true));
        $enum_to_UI = [];
        $task_type_to_enum = [];
        foreach ($task_type_details as $task_type_detail) {
            $enum_to_UI[$task_type_detail['type_enum']] = $task_type_detail;
            if ($task_type_detail['enabled']) $task_type_to_enum[$task_type_detail['memsource_name']] = $task_type_detail['type_enum'];
        }
error_log(print_r($enum_to_UI, true));
error_log(print_r($task_type_to_enum, true));
    }
}
