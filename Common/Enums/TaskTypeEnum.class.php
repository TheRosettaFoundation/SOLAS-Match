<?php

namespace SolasMatch\Common\Enums;

class TaskTypeEnum
{
    const SEGMENTATION      = 1;
    const TRANSLATION       = 2;
    const PROOFREADING      = 3;
    const DESEGMENTATION    = 4;

    public static function registerWithSmarty()
    {
        \Slim\Slim::getInstance()->view()->getInstance()->registerClass('TaskTypeEnum', __NAMESPACE__.'\TaskTypeEnum');
    }
}
