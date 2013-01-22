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


{if isset($org)}
    <h3>Organisation</h3>
    <p>
        <a href="{$org->getHomePage()}">{$org->getName()}</a>
    </p>
{/if}

<h3>Deadline</h3>
<p>
    {$project->getDeadline()}
</p>

{if $project->getReference() != ''}
    <h3>Context Reference</h3>
    <p>
        <a target="_blank" href="{$project->getReference()}">{$project->getReference()}</a>
    </p>
{/if}

<h3>Word Count</h3>
<p>
    {$project->getWordCount()}
</p>

<h3>Created</h3>
<p>
    {$project->getCreatedTime()}
</p>
    
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
        

    {if isset($projectTasks) && count($projectTasks) > 0}
        <table class="table table-striped">
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
                        <center>Comment</title>
                    </th>                    
                    <th>
                        <center>Word Count</title>
                    </th>
                    <th>
                        <center>Published</title>
                    </th>                    
                    <th>
                        <center>Track</center>
                    </th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$projectTasks item=task}
                    {assign var="task_id" value=$task->getId()}
                    <tr>
                        <td>
                            <center>
                                <a href="{urlFor name="task-view" options="task_id.$task_id"}">{$task->getTitle()}</a><br/>
                            </center>
                        </td>
                        <td>
                            <center>
                                {assign var="status_id" value=$task->getTaskStatus()}
                                {if $status_id == TaskStatusEnum::WAITING_FOR_PREREQUISITES}
                                    Waiting
                                {elseif $status_id == TaskStatusEnum::PENDING_CLAIM}
                                    Unclaimed
                                {elseif $status_id == TaskStatusEnum::IN_PROGRESS}
                                    In progress
                                {elseif $status_id == TaskStatusEnum::COMPLETE}
                                    Complete
                                {/if}
                            </center>
                        </td>
                        <td>
                            <center>
                                {assign var="type_id" value=$task->getTaskType()}
                                {if $type_id == TaskTypeEnum::CHUNKING}
                                    Chunking
                                {elseif $type_id == TaskTypeEnum::TRANSLATION}
                                    Translation
                                {elseif $type_id == TaskTypeEnum::PROOFREADING}
                                    Proofreading
                                {elseif $type_id == TaskTypeEnum::POSTEDITING}
                                    Post-Editing
                                {/if}
                            </center>
                        </td>
                        <td>
                            <center>
                                {date("D, dS F Y, H:i:s", strtotime($task->getDeadline()))}
                            </center>
                        </td>
                        <td>
                            <center>
                                {$task->getComment()}
                            </center>
                        </td>
                        <td>
                            <center>
                                {$task->getWordCount()}
                            </center>
                        </td>
                        <td>
                            <center>
                                {if $task->getPublished() == 1}
                                    Yes
                                {else}
                                    No
                                {/if}
                            </center>
                        </td>
                        <td>
                            <center>
                                <form method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
                                    <input type="hidden" name="task_id" value="{$task_id}" />
                                    {if $taskMetaData[$task_id]['tracking']}
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
        Please log in to register for notifications for this task.
    </p>
{/if}

{include file="footer.tpl"}
