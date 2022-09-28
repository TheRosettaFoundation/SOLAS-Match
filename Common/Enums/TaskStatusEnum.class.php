<?php

namespace SolasMatch\Common\Enums;

class TaskStatusEnum
{
    const WAITING_FOR_PREREQUISITES = 1;
    const PENDING_CLAIM             = 2;
    const CLAIMED                   = 10;
    const IN_PROGRESS               = 3;
    const COMPLETE                  = 4;
}
