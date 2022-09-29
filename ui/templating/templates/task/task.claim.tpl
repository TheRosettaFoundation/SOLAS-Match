{include file="header.tpl"}

    {assign var="taskType" value=$task->getTaskType()}
    {if $taskType == TaskTypeEnum::SEGMENTATION}
        {include file="task/task.claim-segmentation.tpl"}
    {elseif $taskType == TaskTypeEnum::TRANSLATION}
        {include file="task/task.claim-translation.tpl"}
    {elseif $taskType == TaskTypeEnum::PROOFREADING}
        {include file="task/task.claim-proofreading.tpl"}
    {elseif $taskType == TaskTypeEnum::DESEGMENTATION}
        {include file="task/task.claim-desegmentation.tpl"}
    {elseif $taskType == TaskTypeEnum::APPROVAL}
        {include file="task/task.claim-approval.tpl"}
    {/if}

{include file="footer.tpl"}
