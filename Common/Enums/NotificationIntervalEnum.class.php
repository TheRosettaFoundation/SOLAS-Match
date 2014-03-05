<?php

namespace SolasMatch\Common\Enums;

class NotificationIntervalEnum
{
    const DAILY = 1;
    const WEEKLY = 2;
    const MONTHLY = 3;

    public static function registerWithSmarty()
    {
        \Slim\Slim::getInstance()->view()->getInstance()->registerClass(
            'NotificationIntervalEnum',
            __NAMESPACE__.'\NotificationIntervalEnum'
        );
    }
}
