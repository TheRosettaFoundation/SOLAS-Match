{include file="header.tpl"}

<div class="page-header">
	<h1>
        Organisation Dashboard <small>Overview of projects created by your organisation(s).</small>
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

{if isset($orgs)}
    <table class="table table-striped">
    {foreach $orgs as $org}
        {assign var="org_id" value=$org->getId()}
        <thead>
            <tr>
                <th>
                    <p style="margin-bottom:40px;"></p>
                    <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                        <h4><i class="icon-briefcase"></i> {$org->getName()}</h4>
                    </a>
                </th>
                <th>
                    <center>Deadline</center>
                </th>
                <th>
                    <center>Status</center>
                </th>
                <th>
                    <center>Word Count</center>
                </th>
                <th>
                    <center>Created</center>
                </th>
                <th>
                    <center>
                    <a href="{urlFor name="org-private-profile" options="org_id.$org_id"}" class="btn btn-primary">
                        <i class="icon-wrench icon-white"></i> Edit Organisation
                    </a>
                    </center>
                </th>
                <th>                    
                    <a class="btn btn-success" href="{urlFor name="task-upload" options="org_id.$org_id"}">
                        <i class="icon-upload icon-white"></i> Create Project
                    </a>                    
                </th>
            </tr>
        </thead>
        <tbody>
        {*
        {assign var="projectsData" value=$templateData[$project_id]}
        {if !is_null($projectsData)}
            {foreach from=$projectsData item=data}
                <tr>
                {assign var="projectObject" value=$data['project']}
                {assign var="project_id" value=$projectObject->getId()}
                    <td>
                        <a href="{urlFor name="project-view" options="project_id.$project_id"}">{$projectObject->getTitle()}</a>
                    </td>
                    {if $data['deadline']}
                        <td>
                            <center>2011/12/12 - 24:00</center>
                        </td>
                    {elseif $data['translated']}
                        <td>
                            <center>
                            <a href="{urlFor name="download-task-latest-version" options="task_id.$task_id"}" class="btn btn-small">
                                <i class="icon-download icon-black"></i><font color="Green"> Download&nbsp;updated&nbsp;file</font>
                            </a>
                            </center>
                        </td>
                    {elseif $data['taskClaimed']}
                        <td>
                            <center>
                            <p><font color=#153E7E>Pending Translation</font></p>
                            </center>
                        </td>
                    {else}
                        <td>
                            <center>
                            <p><font color="Red">Task not Claimed</font></p>
                            </center>
                        </td>
                    {/if}
                    <td>
                        <center>
                        <form method="post" action="{urlFor name="org-dashboard"}">
                            <input type="hidden" name="task_id" value="{$task_id}" />
                            {if $data['userSubscribedToTask']}
                                <input type="hidden" name="track" value="Ignore" />
                                <a href="#" onclick="this.parentNode.submit()" class="btn btn-primary">
                                    <i class="icon-inbox icon-white"></i> Disable
                                </a>
                            {else}
                                <input type="hidden" name="track" value="Track" />
                                <a href="#" onclick="this.parentNode.submit()" class="btn btn-small">
                                    <i class="icon-envelope icon-black"></i> Enable
                                </a>
                            {/if}
                        </form>
                        </center>
                    </td>
                    <td>
                        <center>
                        <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class="btn btn-small">
                            <i class="icon-wrench icon-black"></i> Edit Project
                        </a>
                        </center>
                    </td>
                    <td>
                        <a href="{urlFor name="archive-task" options="task_id.$task_id"}" class="btn btn-inverse">
                            <i class="icon-fire icon-white"></i> Archive Project
                        </a>
                    </td>
                </tr>
            {/foreach}
        {else}
            <td colspan="5">
                <p>This organisation has no projects listed.</p>
            </td>
        {/if}
        *}
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
