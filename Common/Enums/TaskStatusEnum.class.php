<?php

namespace SolasMatch\Common\Enums;

class TaskStatusEnum
{
    const WAITING_FOR_PREREQUISITES = 1;
    const PENDING_CLAIM             = 2;
    const IN_PROGRESS               = 3;
    const COMPLETE                  = 4;

    public static function registerWithSmarty()
    {
        \Slim\Slim::getInstance()->view()->getInstance()->registerClass('TaskStatusEnum', __NAMESPACE__.'\TaskStatusEnum');
    }
}
