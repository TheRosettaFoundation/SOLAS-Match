{include file="header.tpl"}

    {assign var="taskTypeId" value=$task->getTaskType()}
    {if $taskTypeId == TaskTypeEnum::CHUNKING}
         {include file="task.claimed-chunking.tpl" task=$task}
    {else if $taskTypeId == TaskTypeEnum::TRANSLATION}
         {include file="task.claimed-translation.tpl" task=$task}
    {else if $taskTypeId == TaskTypeEnum::PROOFREADING}
         {include file="task.claimed-proofreading.tpl" task=$task}
    {else if $taskTypeId == TaskTypeEnum::POSTEDITING}
         {include file="task.claimed-postediting.tpl" task=$task}
    {/if}

{include file="footer.tpl"}
