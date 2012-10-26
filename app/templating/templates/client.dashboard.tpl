{include file="header.tpl"}

<div class="page-header">
	<h1>
        Dashboard <small>Overview of your tasks for translation</small>

    </h1>
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
                    <p style="margin-bottom:40px;"></p>
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
                                <font color="Green">Download&nbsp;updated&nbsp;file</font>
                            </a>
                        </td>
                    {elseif $task_dao->taskIsClaimed($task_id)}
                        <td>
                            <p><font color=#153E7E>Pending Translation</font></p>
                        </td>
                    {else}
                        <td>
                            <p><font color="Red">Task not Claimed</font></p>
                        </td>
                    {/if}
                    <td>
                        <form method="post" action="{urlFor name="client-dashboard"}">
                            <input type="hidden" name="task_id" value="{$task_id}" />
                            {if $user_dao->isSubscribedToTask($user->getUserId(), $task_id)}
                                <input class="btn btn-primary" type="submit" name="track" value="Ignore" />
                            {else}
                                <input class="btn" type="submit" name="track" value="Track" />
                            {/if}
                        </form>
                    </td>
                    <td>
                        <a href="{urlFor name="archive-task" options="task_id.$task_id"}" class="btn btn-inverse">Archive</a>
                    </td>
                </tr>
            {/foreach}
        {else}
            <td>
                <p>This organisation has no tasks listed.</p>
            </td>
        {/if}
        </tbody>
    {/foreach}
    </table>
{else}
    <div class="alert alert-warning">
    <strong>What now?</strong> You don't have any tasks uploaded for your organisation. If you have content to be translated, please add a new     task for that content.
    </div>
{/if}
<p style="margin-bottom:60px;"></p>
{include file="footer.tpl"}
