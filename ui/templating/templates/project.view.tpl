{include file="header.tpl"}

<h1 class="page-header">
    Project Title Name
    {*$project->getTitle()*}
    <small>Overview of project details.</small>
    {*
    {assign var="project_id" value=$project->getId()}
    
    <a href="{urlFor name="project-alter" options="project_id.$project_id"}" class='pull-right btn btn-primary'>
        <i class="icon-wrench icon-white"></i> Edit Details
    </a> 
    *}
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
    <h3>Organisation</h3>
    <p>
        <a href="{$org->getHomePage()}">{$org->getTitle()}</a>
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
    <!-- Uncomment when projects is working -->
    {*
    <form method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
        {if isset($registered) && $registered == true}
            <p>
                <input type="hidden" name="notify" value="false" />
                <input type="submit" class="btn btn-small" value="    Disable" />

                <i class="icon-inbox icon-black" style="position:relative; right:63px; top:2px;"></i>
            </p>
        {else}
            <p>
                <input type="hidden" name="notify" value="true" />
                <input type="submit" class="btn btn-small" value="    Enable" />

                <i class="icon-envelope icon-black" style="position:relative; right:63px; top:2px;"></i>
            </p>
        {/if}
    </form> 
    *}

<p style="margin-bottom:40px;"></p>

{if isset($user)}
    <hr />
    
    <h1 class="page-header">
        Project Tasks
        <small>Overview of tasks created for this project.</small>

        <a class="pull-right btn btn-success" href="{urlFor name="task-upload"}"> {*options="project_id.$project_id"*}
            <i class="icon-upload icon-white"></i> Create Task
        </a>          

       
    </h1> 
        

    {if isset($orgs)}
        <table class="table table-striped">
        {foreach $orgs as $org}
            {assign var="org_id" value=$org->getId()}
            <thead>
                <tr>
                    <th>
                        <p style="margin-bottom:40px;"></p>
                        <center>Title</center>
                    </th>
                    <th>
                        <center>Status</center>
                    </th>               
                    <th>
                        <center>Type</center>
                    </th> 
                    <th>
                        <center>Deadline</center>
                    </th>
                    <th>
                        <center>Comment</title
                    </th>                    
                    <th>
                        <center>Word Count</title
                    </th>
                    <th>
                        <center>Published</title
                    </th>                    
                    <th>
                        <center>Track</center>
                    </th>
                </tr>
            </thead>
            <tbody>
                {*
            {assign var="tasksData" value=$templateData[$org_id]}
            {if !is_null($tasksData)}
                {foreach from=$tasksData item=data}
                    <tr>
                    {assign var="taskObject" value=$data['task']}
                    {assign var="task_id" value=$taskObject->getId()}
                        <td>
                            <center>
                                <a href="{urlFor name="task-view" options="task_id.$task_id"}">{$taskObject->getTitle()}</a><br/>
                            </center>
                            <i>From:</i> <strong>Ngombe_Democratic_Republic_of_Congo</strong><br/>
                            <i>To:</i> <strong>Ngombe_Democratic_Republic_of_Congo</strong>

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
                                <i class="icon-wrench icon-black"></i> Edit Task
                            </a>
                            </center>
                        </td>
                        <td>
                            <a href="{urlFor name="archive-task" options="task_id.$task_id"}" class="btn btn-inverse">
                                <i class="icon-fire icon-white"></i> Archive Task
                            </a>
                        </td>
                    </tr>
                {/foreach}
            {else}
                <td>
                    <div class="alert-info" align="center">
                        This project has no tasks listed.
                    </div>
                </td>
            {/if}
            </tbody>
            *}
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
