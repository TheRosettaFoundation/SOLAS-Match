{include file="header.tpl"}

<div class="page-header">
	<h1>Dashboard <small>Overview of your tasks for translation</small></h1>
</div>

{if isset($org_tasks)}
    <a class="btn btn-primary" href="{urlFor name="task-upload"}"><i class="icon-upload icon-white"></i> Add new task</a>
    <table class="table table-striped">
    {foreach from=$org_tasks  key=org item=tasks}
        {if count($tasks) > 0}
            <thead>
                <tr>
                    <th>{$org}</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$tasks item=task}
                <tr>
                {assign var="task_id" value=$task->getTaskId()}
                {if $task_dao->getLatestFileVersion($task) > 0}
                    <td>
                        <a href="{urlFor name="task" options="task_id.$task_id"}">{$task->getTitle()}</a>
                    </td>
                    <td>
                        <a href="{urlFor name="download-task-latest-version" options="task_id.$task_id"}" class="btn btn-small">
                            Download&nbsp;updated&nbsp;file
                        </a>
                    </td>
                    <td>
                        <a href="{urlFor name="archive-task" options="task_id.$task_id"}" class="btn btn-small">Archive</a>
                    </td>
                {else}
                    <td>
                        <a href="{urlFor name="task" options="task_id.$task_id"}">{$task->getTitle()}</a>
                    </td>
                    <td>
                    </td>
                    <td>
                        <a href="{urlFor name="archive-task" options="task_id.$task_id"}" class="btn btn-small">Archive</a>
                    </td>
                {/if}
                </tr>
            {/foreach}
            </tbody>
        {/if}
    {/foreach}
    </table>
{else}
    <div class="alert alert-warning">
    <strong>What now?</strong> You don't have any tasks uploaded for your organisation. If you have content to be translated, please add a new     task for that content.
    </div>
       
    <a class="btn btn-primary" href="{urlFor name="task-upload"}"><i class="icon-upload icon-white"></i> Add new task</a>
{/if}

{include file="footer.tpl"}
