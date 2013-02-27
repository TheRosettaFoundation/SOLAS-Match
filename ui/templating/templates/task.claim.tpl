{include file="header.tpl"}

    {assign var="taskType" value=$task->getTaskType()}
    {if $taskType == TaskTypeEnum::CHUNKING}
        {include file="task.claim-chunking.tpl"}
    {elseif $taskType == TaskTypeEnum::TRANSLATION}
        {include file="task.claim-translation.tpl"}
    {elseif $taskType == TaskTypeEnum::PROOFREADING}
        {include file="task.claim-proofreading.tpl"}
    {elseif $taskType == TaskTypeEnum::POSTEDITING}
        {include file="task.claim-postediting.tpl"}
    {/if}

{include file="footer.tpl"}
