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

{if isset($templateData)}
    <table class="table table-striped">
    {foreach $templateData as  $org=>$tasksData}
        {assign var="org_id" value=$org}
        <thead>
            <tr>
                <th>
                    <p style="margin-bottom:40px;"></p>
                    <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                        <i class="icon-briefcase"></i> {$orgs[$org]->getName()}
                    </a>
                </th>
                <th>Task Status</th>
                <th>Track Status</th>
                <th>
                    <a href="{urlFor name="org-private-profile" options="org_id.$org_id"}" class="btn btn-primary">
                        <i class="icon-wrench icon-white"></i> Edit Organisation
                    </a>
                </th>
                <th>                    
                    <a class="btn btn-success" href="{urlFor name="task-upload" options="org_id.$org"}">
                        <i class="icon-upload icon-white"></i> Add New Task
                    </a>                    
                </th>
            </tr>
        </thead>
        <tbody>
        {if !is_null($tasksData)}
            {foreach from=$tasksData item=data}
                <tr>
                {assign var="taskObject" value=$data['task']}
                {assign var="task_id" value=$taskObject->getTaskId()}
                    <td>
                        <a href="{urlFor name="task-view" options="task_id.$task_id"}">{$taskObject->getTitle()}</a>
                    </td>
                    {if $data['translated']}
                        <td>
                            <a href="{urlFor name="download-task-latest-version" options="task_id.$task_id"}" class="btn btn-small">
                                <i class="icon-download icon-black"></i><font color="Green"> Download&nbsp;updated&nbsp;file</font>
                            </a>
                        </td>
                    {elseif $data['taskClaimed']}
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
                            {if $data['userSubscribedToTask']}
                                <input type="hidden" name="track" value="Ignore" />
                                <a href="#" onclick="this.parentNode.submit()" class="btn btn-primary">
                                    <i class="icon-inbox icon-white"></i> Ignore
                                </a>
                            {else}
                                <input type="hidden" name="track" value="Track" />
                                <a href="#" onclick="this.parentNode.submit()" class="btn btn-small">
                                    <i class="icon-envelope icon-black"></i> Track
                                </a>
                            {/if}
                        </form>
                    </td>
                    <td>
                        <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class="btn btn-small">
                            <i class="icon-wrench icon-black"></i> Edit Task
                        </a>
                    </td>
                    <td>
                        <a href="{urlFor name="archive-task" options="task_id.$task_id"}" class="btn btn-inverse">
                            <i class="icon-fire icon-white"></i> Archive
                        </a>
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
    <strong>What now?</strong> You don't have any tasks uploaded for your organisation. If you have content to be translated, please add a new task for that content.
    </div>
{/if}
<p style="margin-bottom:60px;"></p>
{include file="footer.tpl"}
