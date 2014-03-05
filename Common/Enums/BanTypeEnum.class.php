<?php

namespace SolasMatch\Common\Enums;

class BanTypeEnum
{
    const DAY        = 1;
    const WEEK       = 2;
    const MONTH      = 3;
    const PERMANENT  = 4;
    const HOUR       = 5;

    public static function registerWithSmarty()
    {
        \Slim\Slim::getInstance()->view()->getInstance()->registerClass('BanTypeEnum', __NAMESPACE__.'\BanTypeEnum');
    }
}
