{include file="header.tpl"}

<h1 class="page-header">
    {if $task->getTitle() != ''}
        {$task->getTitle()}
    {else}
        Task {$task->getId()}
    {/if}
    <small>
        <b>
            -
            {assign var="type_id" value=$task->getTaskType()}
            {if $type_id == TaskTypeEnum::CHUNKING}
                <span style="color: {$taskTypeColours[TaskTypeEnum::CHUNKING]}">Chunking Task</span>                                    
            {elseif $type_id == TaskTypeEnum::TRANSLATION}
                <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">Translation Task</span> 
            {elseif $type_id == TaskTypeEnum::PROOFREADING}
                <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">Proofreading Task</span> 
            {elseif $type_id == TaskTypeEnum::POSTEDITING}
                <span style="color: {$taskTypeColours[TaskTypeEnum::POSTEDITING]}">Postediting Task</span> 
            {/if}
        </b>
    </small>   
    {assign var="task_id" value=$task->getId()}
    <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class='pull-right btn btn-primary'>
        <i class="icon-wrench icon-white"></i> Edit Task Details
    </a>
</h1>
        
        
<table class="table table-striped">
    <thead>            
        <th style="text-align: left"><b>Project</b></th>

        <th><b>Source Language</b></th>
        <th><b>Target Language</b></th>
        <th><b>Created</b></th> 
        <th><b>Task Deadline</b></th>
        <th><b>Word Count</b></th>
        <th><b>Status</b></th>
    </thead>
    <tbody>
        <tr>
            <td style="text-align: left">
                {if isset($project)}
                    {assign var="projectId" value=$project->getId()}
                    <a href="{urlFor name="project-view" options="project_id.$projectId"}">
                        {$project->getTitle()}
                    </a>
                {/if}
            </td>

            <td>
                {TemplateHelper::getTaskSourceLanguage($task)} 
            </td>
            <td>
                {TemplateHelper::getTaskTargetLanguage($task)}
            </td>
            <td>
                {date("D dS, M Y", strtotime($task->getCreatedTime()))}
            </td>
            <td>
                {date("D dS, M Y", strtotime($task->getDeadline()))}
            </td>
            <td>
                {if $task->getWordCount() != ''}
                    {$task->getWordCount()}
                {else}
                    -
                {/if}              
            </td> 
            <td>                            
                {assign var="status_id" value=$task->getTaskStatus()}
                {if $status_id == TaskStatusEnum::WAITING_FOR_PREREQUISITES}
                    Waiting
                {elseif $status_id == TaskStatusEnum::PENDING_CLAIM}
                    Unclaimed
                {elseif $status_id == TaskStatusEnum::IN_PROGRESS}
                    <a href="{urlFor name="task-feedback" options="task_id.$task_id"}">In Progress</a>
                {elseif $status_id == TaskStatusEnum::COMPLETE}
                    <a href="{urlFor name="api"}v0/tasks/{$task_id}/file/?">Complete</a>
                {/if}
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
            <tr valign="top">
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
    <table width="100%" class="table table-striped">  
        <thead>
        <th>Task Published</th>
        <th>Task Tracked</th>
        </thead>
        <tr align="center">
            <td>
            {assign var="task_id" value=$task->getId()}
            <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                <input type="hidden" name="task_id" value="{$task_id}" />
                {if $task->getPublished() == 1}
                    <input type="hidden" name="published" value="0" />
                    <a href="#" onclick="this.parentNode.submit()" class="btn btn-small">
                        <i class="icon-check icon-black"></i> Published
                    </a>
                {else}                                        
                    <input type="hidden" name="published" value="1" />
                    <a href="#" onclick="this.parentNode.submit()" class="btn btn-small btn-inverse">
                        <i class="icon-remove-circle icon-white"></i> Unpublished
                    </a>
                {/if}
            </form>

        </td>
        <td>
            <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                <input type="hidden" name="task_id" value="{$task_id}" />
                {if $taskMetaData[$task_id]['tracking']}
                    <input type="hidden" name="track" value="Ignore" />
                    <a href="#" onclick="this.parentNode.submit()" class="btn btn-small">
                        <i class="icon-inbox icon-black"></i> Tracked
                    </a>
                {else}
                <input type="hidden" name="track" value="Track" />
                <a href="#" onclick="this.parentNode.submit()" class="btn btn-small btn-inverse">
                    <i class="icon-envelope icon-white"></i> Untracked
                </a>
                {/if}
            </form>
        </td>
        </tr>
    </table>
    <div style="margin-bottom: 40px"></div>        
    <table width="100%">
        <thead>
        <th>Source Document Preview - {$filename}<hr/></th>
        </thead>
        <tbody>
            <tr>
                <td align="center"><iframe src="http://docs.google.com/viewer?url={urlencode($file_preview_path)}&embedded=true" width="800" height="780" style="border: none;"></iframe></td>
            </tr>
        </tbody>
    </table>             
                
{else}
    <p class="alert alert-info">
        Please log in to register for notifications for this task.
    </p>
{/if}

{include file="footer.tpl"}
