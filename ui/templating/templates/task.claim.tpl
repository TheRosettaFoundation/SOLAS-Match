{include file="header.tpl"}

    {assign var="taskType" value=$task->getTaskType()}
    {if $taskType == TaskTypeEnum::SEGMENTATION}
        {include file="task.claim-segmentation.tpl"}
    {elseif $taskType == TaskTypeEnum::TRANSLATION}
        {include file="task.claim-translation.tpl"}
    {elseif $taskType == TaskTypeEnum::PROOFREADING}
        {include file="task.claim-proofreading.tpl"}
    {elseif $taskType == TaskTypeEnum::DESEGMENTATION}
        {include file="task.claim-desegmentation.tpl"}
    {/if}

{include file="footer.tpl"}
