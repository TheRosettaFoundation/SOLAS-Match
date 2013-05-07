{include file="header.tpl"}

    {assign var="taskTypeId" value=$task->getTaskType()}
    {if $taskTypeId == TaskTypeEnum::SEGMENTATION}
         {include file="task/task.claimed-segmentation.tpl" task=$task}
    {else if $taskTypeId == TaskTypeEnum::TRANSLATION}
         {include file="task/task.claimed-translation.tpl" task=$task}
    {else if $taskTypeId == TaskTypeEnum::PROOFREADING}
         {include file="task/task.claimed-proofreading.tpl" task=$task}
    {else if $taskTypeId == TaskTypeEnum::DESEGMENTATION}
         {include file="task/task.claimed-desegmentation.tpl" task=$task}
    {/if}

{include file="footer.tpl"}
