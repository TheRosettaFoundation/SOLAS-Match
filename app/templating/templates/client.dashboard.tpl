{include file="header.tpl"}

<div class="page-header">
	<h1>Dashboard <small>Overview of your tasks for translation</small></h1>
</div>

{if isset($flash['success'])}
    <p class="alert alert-success">
        {$flash['success']}
    </p>
{/if}

{if isset($flash['error'])}
    <p class="alert alert-error">
        {$flash['error']}
    </p>
{/if}

{if isset($org_tasks)}
    <table class="table table-striped">
    {foreach from=$org_tasks  key=org item=tasks}
        <thead>
            <tr>
                <th>
                    <a href="{urlFor name="org-public-profile" options="org_id.$org"}">
                        <i class="icon-briefcase"></i> {$orgs[$org]->getName()}
                    </a>
                </th>
                <th>Task Status</th>
                <th>Track Status</th>
                <th>
                    <a class="btn btn-primary" href="{urlFor name="task-upload" options="org_id.$org"}">
                        <i class="icon-upload icon-white"></i> Add new task
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
        {if !is_null($tasks)}
            {foreach from=$tasks item=task}
                <tr>
                {assign var="task_id" value=$task->getTaskId()}
                    <td>
                        <a href="{urlFor name="task-view" options="task_id.$task_id"}">{$task->getTitle()}</a>
                    </td>
                    {if TaskFile::getLatestFileVersion($task) > 0}
                        <td>
                            <a href="{urlFor name="download-task-latest-version" options="task_id.$task_id"}" class="btn btn-small">
                                Download&nbsp;updated&nbsp;file
                            </a>
                        </td>
                    {elseif $task_dao->taskIsClaimed($task_id)}
                        <td>
                            <p>Awaiting Translation</p>
                        </td>
                    {else}
                        <td>
                            <p>Task not Claimed</p>
                        </td>
                    {/if}
                    <td>
                        <form method="post" action="{urlFor name="client-dashboard"}">
                            <input type="hidden" name="task_id" value="{$task_id}" />
                            {if $user_dao->isSubscribedToTask($user->getUserId(), $task_id)}
                                <input class="btn" type="submit" name="track" value="Ignore" />
                            {else}
                                <input class="btn" type="submit" name="track" value="Track" />
                            {/if}
                        </form>
                    </td>
                    <td>
                        <a href="{urlFor name="archive-task" options="task_id.$task_id"}" class="btn btn-small">Archive</a>
                    </td>
                </tr>
            {/foreach}
        {/if}
        </tbody>
    {/foreach}
    </table>
{else}
    <div class="alert alert-warning">
    <strong>What now?</strong> You don't have any tasks uploaded for your organisation. If you have content to be translated, please add a new     task for that content.
    </div>
       
    <a class="btn btn-primary" href="{urlFor name="task-upload"}"><i class="icon-upload icon-white"></i> Add new task</a>
{/if}

{include file="footer.tpl"}
