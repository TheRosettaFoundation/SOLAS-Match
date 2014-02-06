<?php

require_once __DIR__."/../lib/TipSelector.class.php";

class Tips
{
    public static function init()
    {
        Dispatcher::registerNamed(
            HttpMethodEnum::GET,
            '/v0/tips(:format)/',
            function ($format = ".json") {
                Dispatcher::sendResponce(null, TipSelector::selectTip(), null, $format);
            },
            'getTip'
        );
    }
}
Tips::init();
