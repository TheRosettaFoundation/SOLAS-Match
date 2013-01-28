{include file="header.tpl"}

<h1 class="page-header">
    {$project->getTitle()}
    <small>Overview of project details.</small>
    {assign var="project_id" value=$project->getId()}
    
    <a href="{urlFor name="project-alter" options="project_id.$project_id"}" class='pull-right btn btn-primary'>
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



<table class="table table-striped">
    <thead>            
        <th style="text-align: left"><b>Organisation</b></th>
        <th><b>Source Language</b></th>
        <th><b>Reference</b></th>
        <th><b>Word Count</b></th>
        <th><b>Created</b></center></th> 
        <th><b>Project Deadline</b></th>
        <th><b>Track</b></th>
          
    </thead>
    <tbody>
        <tr>
            <td style="text-align: left">
                {if isset($org)}
                    {assign var="org_id" value=$org->getId()}
                    <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">{$org->getName()}</a>
                {/if}
            </td>
            <td>
                {TemplateHelper::languageNameFromCode($project->getSourceLanguageCode())}
                ({TemplateHelper::countryNameFromCode($project->getSourceCountryCode())})
            </td>
            <td>
                {if $project->getReference() != ''}
                    <a target="_blank" href="{$project->getReference()}">{$project->getReference()}</a>
                {/if}            
            </td>
            <td>
                {$project->getWordCount()}
            </td>
            <td>
                {$project->getCreatedTime()}
            </td>  
            <td>
                {$project->getDeadline()}
            </td>
            <td>
                <form method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
                    {if isset($registered) && $registered == true}
                        <p>
                            <input type="hidden" name="notify" value="false" />
                            <input type="submit" class="btn btn-small" value="    Tracked" />

                            <i class="icon-inbox icon-black" style="position:relative; right:70px; top:2px;"></i>
                        </p>
                    {else}
                        <p>
                            <input type="hidden" name="notify" value="true" />
                            <input type="submit" class="btn btn-small btn-inverse" value="    Untracked" />

                            <i class="icon-envelope icon-white" style="position:relative; right:81px; top:2px;"></i>
                        </p>
                    {/if}
                </form> 
            </td>
        </tr>
        <tr>
        </tr> 
    </tbody>
</table>
            
            
<div class="well">
    <table width="100%">
        <thead>
        <th align="left">Project Description:<hr/></th>
        </thead>
        <tbody>
            <tr>
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
                
<p style="margin-bottom:40px;"></p>

{if isset($user)}
    <hr />
    
    <h1 class="page-header">
        Tasks
        <small>Overview of tasks created for this project.</small>

        <a class="pull-right btn btn-success" href="{urlFor name="task-create" options="project_id.$project_id"}">
            <i class="icon-upload icon-white"></i> Create Task
        </a>          

       
    </h1> 
        

    {if isset($projectTasks) && count($projectTasks) > 0}
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>
                        <p style="margin-bottom:40px;"></p>
                        Title
                    </th>
                    <th>
                        Status
                    </th>               
                    <th>
                        Type
                    </th> 
                    <th>
                        Task Deadline
                    </th>                  
                    <th>
                        Word Count
                    </th>
                    <th>
                        Published
                    </th>                    
                    <th>
                        Track
                    </th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$projectTasks item=task}
                    {assign var="task_id" value=$task->getId()}
                    <tr>
                        <td>
                            <a href="{urlFor name="task-view" options="task_id.$task_id"}">{$task->getTitle()}</a><br/>
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
                        <td>
                            <b>
                                <small>                                  
                                {assign var="type_id" value=$task->getTaskType()}
                                {if $type_id == TaskTypeEnum::CHUNKING}
                                    <span style="color: {$taskTypeColours[TaskTypeEnum::CHUNKING]}">Chunking Task</span>                                    
                                {elseif $type_id == TaskTypeEnum::TRANSLATION}
                                    <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">Translation Task
                                {elseif $type_id == TaskTypeEnum::PROOFREADING}
                                    <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">Proofreading Task
                                {elseif $type_id == TaskTypeEnum::POSTEDITING}
                                    <span style="color: {$taskTypeColours[TaskTypeEnum::POSTEDITING]}">Postediting Task
                                {/if}
                                </small>
                            </b>
                        </td>
                        <td>
                            {date("D, dS F Y, H:i:s", strtotime($task->getDeadline()))}
                        </td>
                        <td>
                            {$task->getWordCount()}
                        </td>
                        <td>
                            <form method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
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
                            <form method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
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
                {/foreach}
            </tbody>
        </table>
    {else}
        <div class="alert alert-warning">
        <strong>What now?</strong> You don't have any tasks uploaded for your organisation. If you have content to be translated, please add a new task for that content.
        </div>
    {/if}       
        
{else}
    <p class="alert alert-info">
        Please log in to register for notifications for this project.
    </p>
{/if}

{include file="footer.tpl"}
