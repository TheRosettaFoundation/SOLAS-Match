<?php

namespace SolasMatch\Common\Enums;

class TaskTypeHourEnum
{
    const TRANSLATION  = 200; // Divide by this to convert Translated words to hours
    const PROOFREADING = 500; // for Revision words
    const APPROVAL     = 800; // for Proofreading and Approval words
}
