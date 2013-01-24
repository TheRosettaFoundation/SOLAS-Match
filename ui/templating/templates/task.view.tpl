{include file="header.tpl"}

<h1 class="page-header">
    {if $task->getTitle() != ''}
        {$task->getTitle()}
    {else}
        Task {$task->getId()}
    {/if}
    <small>Task Details</small>
    {assign var="task_id" value=$task->getId()}
    <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class='pull-right btn btn-primary'>
        <i class="icon-wrench icon-white"></i> Edit Details
    </a>
</h1>
        
        
<table class="table table-striped">
    <thead>            
        <th style="text-align: left"><b>Organisation</b></th>
        <th><b>Project</b></th>
        <th><b>Task Deadline</b></th>
        <th><b>Source Language</b></th>
        <th><b>Target Language</b></th>
        <th><b>Word Count</b></th>
        <th><b>Created</b></center></th>            
    </thead>
    <tbody>
        <tr>
            <td style="text-align: left">
                {if isset($task)}
                    {assign var="org_id" value=$task->getId()}
                    <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">{$org->getName()}</a>
                {/if}
            </td>
            <td>
                {if isset($project)}
                    {assign var="projectId" value=$project->getId()}
                    <a href="{urlFor name="project-view" options="project_id.$projectId"}">
                        {$project->getTitle()}
                    </a>
                {/if}
            </td>
            <td>
                {date("D dS, M Y", strtotime($task->getDeadline()))}
            </td>
            <td>
                {TemplateHelper::getTaskSourceLanguage($task)}
            </td>
            <td>
                {TemplateHelper::getTaskTargetLanguage($task)}
            </td>
                
            <td>
                {$task->getWordCount()}                
            </td>
            <td>
                {date("D dS, M Y", strtotime($task->getCreatedTime()))}
            </td>  
        </tr>  
    </tbody>
</table>        
      
<div class="well">
    <table width="100%">
        <thead>
        <th width="48%" align="left">Task Comment:<hr/></th>
        <th></th>
        <th width="48%" align="left">Project Description:<hr/></th>
        </thead>
        <tbody>
            <tr>
                <td>
                    <i>
                    {if $task->getComment() != ''}
                        {$task->getComment()}
                    {else}
                       No comment has been added.
                    {/if}
                    </i>
                </td>
                <td></td>
                <td>
                    <i>
                    {if $project->getDescription() != ''}
                        {$project->getDescription()}
                    {else}
                        No description has been added.
                    {/if}
                    </i>
                </td>
            </tr>
        </tbody>
    </table>
</div>
        

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

{if isset($user)}
    <hr />

    <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}" class="well">
        {if isset($registered) && $registered == true}
            <p>
                <input type="hidden" name="notify" value="false" />
                <input type="submit" class="btn btn-primary" value="    Ignore Task" />
                You are currently receiving notifications about this task.
                <i class="icon-inbox icon-white" style="position:relative; right:430px; top:2px;"></i> 
            </p>
        {else}
            <p>
                <input type="hidden" name="notify" value="true" />
                <input type="submit" class="btn btn-primary" value="    Track Task" />
                You are not currently receiving notifications about this task.
                <i class="icon-envelope icon-white" style="position:relative; right:446px; top:2px;"></i> 
            </p>
        {/if}
    </form>
{else}
    <p class="alert alert-info">
        Please log in to register for notifications for this task.
    </p>
{/if}

{include file="footer.tpl"}
