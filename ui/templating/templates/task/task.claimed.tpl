{include file="header.tpl"}

    {assign var="taskTypeId" value=$task->getTaskType()}
    {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
        {if $taskTypeId == $task_type}
            {include file=$ui['claimed_template'] task=$task}
        {/if}
    {/foreach}

{include file="footer.tpl"}
