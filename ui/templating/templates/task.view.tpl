{include file="header.tpl"}

<h1 class="page-header">
    Project Title Name
    {*
    {if $project->getTitle() != ''}
        {$project->getTitle()}
    {else}
        Task {$project->getId()}
    {/if}
    *}
    <small>Project Details</small>
    {assign var="task_id" value=$task->getId()}
    
    <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class='pull-right btn btn-primary'>
        <i class="icon-wrench icon-white"></i> Edit Details
    </a> 
</h1>

{if isset($flash['success'])}
    <p class="alert alert-success">
        {$flash['success']}
    </p>
{/if}

{if isset($flash['error'])}
    <p class="alert alert-error">
        <b>Warning!</b> {$flash['error']}
    </p>
{/if}

{if isset($org)}
    <h3>Description</h3>
    <p>
        Bla bla bla bla
    </p>
{/if}

{*{if $task->getImpact() != ''}

    <p>{$task->getImpact()}</p>
{/if}
*}
<h3>Deadline</h3>
<p>
    10/5/2012 24:00
</p>

{*
{if $task->getReferencePage() != ''}
    <h3>Context Reference</h3>
    <p>
        <a target="_blank" href="{$task->getReferencePage()}">{$task->getReferencePage()}</a>
    </p>
{/if}
*}

<h3>Word Count</h3>
<p>
    2378905
</p>

<h3>Reference</h3>
<p>
    some url www.google.com
</p>

<h3>Created</h3>
<p>
    10/5/2012 24:00
</p>


    <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}" class="well">
        {if isset($registered) && $registered == true}
            <p>
                <input type="hidden" name="notify" value="false" />
                <input type="submit" class="btn btn-primary" value="    Ignore Task" />

                <i class="icon-inbox icon-black" style="position:relative; right:430px; top:2px;"></i> z
            </p>
        {else}
            <p>
                <input type="hidden" name="notify" value="true" />
                <input type="submit" class="btn btn-primary" value="    Track Task" />

                <i class="icon-envelope icon-black" style="position:relative; right:446px; top:2px;"></i> z
            </p>
        {/if}
    </form> 


{if isset($user)}
    <hr />
    
    <h1 class="page-header">
        Project Tasks
        <small>Project Details</small>
    </h1>    
        

    {if isset($orgs)}
        <table class="table table-striped">
        {foreach $orgs as $org}
            {assign var="org_id" value=$org->getId()}
            <thead>
                <tr>
                    <th>
                        <p style="margin-bottom:40px;"></p>
                        <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                            <i class="icon-briefcase"></i> {$org->getName()}
                        </a>
                    </th>
                    <th>
                        <center>Deadline</center>
                    </th>
                    <th>
                        <center>Status</center>
                    </th>
                    <th>
                        <center>Track</center>
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
            {assign var="tasksData" value=$templateData[$org_id]}
            {if !is_null($tasksData)}
                {foreach from=$tasksData item=data}
                    <tr>
                    {assign var="taskObject" value=$data['task']}
                    {assign var="task_id" value=$taskObject->getId()}
                        <td>
                            <a href="{urlFor name="task-view" options="task_id.$task_id"}">{$taskObject->getTitle()}</a>
                        </td>
                        <td>
                            <center>2011/12/12 - 24:00</center>
                        </td>
                        {if $data['translated']}
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
                            <form method="post" action="{urlFor name="client-dashboard"}">
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
            </tbody>
        {/foreach}
        </table>
    {else}
        <div class="alert alert-warning">
        <strong>What now?</strong> You don't have any tasks uploaded for your organisation. If you have content to be translated, please add a new task for that content.
        </div>
    {/if}        
        
        
        
{else}
    <p class="alert alert-info">
        Please log in to register for notifications for this task.
    </p>
{/if}

{include file="footer.tpl"}
